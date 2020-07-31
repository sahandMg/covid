<?php

namespace App\Http\Controllers\DeviceResponsables;

use App\Device;
use App\DeviceLog;
use App\Repo;
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
            }catch (\Exception $exception){

                return 404;
            }

            Cache::put('data',[$resp,$user],200000);

//            Switching account role takes place at DeviceObserver

            $resp['user_id'] = $user->id;

            $resp['city'] = $resp['location'];
            unset($resp['location']);

            if(is_null($device)){

//          Creating New Device


                $device = Device::create($resp);

            }else{
                try{
                    if(isset($resp['region']) && isset($resp['city'])){

//                        Updating Existing Device Data

                        (new UpdateDeviceData())->toResponse($resp);
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