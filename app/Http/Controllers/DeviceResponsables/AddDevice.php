<?php

namespace App\Http\Controllers\DeviceResponsables;

use App\Device;
use App\DeviceLog;
use App\Repo;
use App\SharedKey;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

class AddDevice implements Responsable {

    private $deviceLog;
    public function __construct($deviceLog)
    {
        $this->deviceLog = $deviceLog;
    }
    
    public function toResponse($request){

        try{

            $resp = (new Repo())->parseDataToArray($request->all());

            $device = Device::where('unique_id',$resp['unique_id'])->first();
            try{

                $user = User::where('key',$resp['owner_key'])->firstOrFail();

                if(!is_null(SharedKey::where('user_id',$user->id)->first())){

                    return 'اجازه اضافه کردن دستگاه ندارید';
                }
            }catch (\Exception $exception){

                return 404;
            }

            Cache::put('data',[$resp,Carbon::now()->format("Y-m-d H:i:s"),$user],200000);

//            Switching account role takes place at DeviceObserver

            $resp['user_id'] = $user->id;

            if(is_null($device)){

//          Creating New Device
                $device = Device::create([
                    'd_name'=>$resp['name'],
                    'city'=>$resp['location'],
                    'region'=>$resp['region'],
                    'unique_id'=>$resp['unique_id'],
                    'user_id'=>$resp['user_id'],
                    'power_off'=>$resp['power_off']
                ]);
            }else{
                try{
                    if(isset($resp['region']) && isset($resp['location'])){

                        $resp['city'] = $resp['location'];
                        unset($resp['location']);

//                        Updating Existing Device Data
                        $user->id == $device->user_id ? (new UpdateDeviceData())->toResponse($resp) : (new UpdateDeviceData())->toResponse($resp,$updateUser = 1);
//                        Device owner may have changed
                        $device = Device::where('unique_id',$resp['unique_id'])->first();

                    }
//                       Creating DeviceLog + Sendig Notifications if Needed

                }catch (\Exception $exception){

                    return ($exception->getMessage().' '.$exception->getFile());
                }
            }

            (new AddDeviceLog($device))->toResponse($resp);

//           Power and Capacity Notifications are handled by DeviceLogObserver

            $dateTime = Jalalian::fromCarbon(Carbon::now())->toString();
            $date = explode(' ',$dateTime)[0];
            $time = explode(' ',$dateTime)[1];
            return ['status'=>200,'date'=>$date,'time'=>$time,'power_off'=> $device->power_off];

        }catch (\Exception $exception){

            return $exception->getMessage().' '.$exception->getLine();
        }
    }
}

?>