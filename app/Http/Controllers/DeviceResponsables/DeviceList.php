<?php

namespace App\Http\Controllers\DeviceResponsables;

use App\Device;
use App\DeviceEvent;
use App\DeviceLog;
use App\Repo;
use App\SharedKey;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class DeviceList implements Responsable {

    public function __construct()
    {
        
    }
    
    public function toResponse($request){

        try{
            $repo = new Repo();
//            $date = $repo->convertJalali($request->date);
            $date = $request->date;
            $user = Auth::guard('user')->user();

//                checks shared key, if admin code hasn't been shared, user can't see devices
            try{
                $admin_id = $user->role_id == $repo->findRoleId('user')?
                    SharedKey::where('user_id',$user->id)->firstOrFail()->admin_id:
                    Auth::guard('user')->id();
            }
            catch (\Exception $exception){

                return  ['status'=>404,'body'=>['type'=>'data','message'=>[],'date'=>Jalalian::now()->format("Y-m-d H:i:s")]];
            }

            $deviceEvents = DeviceEvent::where('created_at','>',Carbon::parse($date))->where('user_id',$admin_id)->select('unique_id','type')->get();
//            $admin_id = $shared->admin_id;

            $adminDevices = Device::where('user_id',$admin_id)->where('updated_at','>',Carbon::parse($date))->with('deviceLogs')->get();

//                Means that there is no new devices on the app
            $deviceLogs = DeviceLog::where('user_id',$admin_id)->orderBy('id','desc')->where('created_at','>',Carbon::parse($date))->with('device')->get();

            $resp2 = [];
            try {
//                foreach ($adminDevices as $adminDevice) {
//                    $last = $adminDevice->deviceLogs->last();
//                    array_push($resp, [
//                        'unique_id' => $adminDevice->unique_id,
//                        'd_name' => $adminDevice->d_name,
//                        'power' => $last->power,
//                        'push' => $last->push,
//                        'capacity' => $last->capacity,
//                        'region' => $adminDevice->region,
//                        'city' => $adminDevice->city
//                    ]);
//                }
                $check = [];
                foreach ($deviceLogs as $deviceLog) {
                    if(!in_array($deviceLog->device_id,$check)){

                        $deviceData = $deviceLog->device;
                        try{
                            $lastUsage = $deviceData->reports->first()->total_pushed;
                        }catch(\Exception $e){
                            $lastUsage = 0;
                        }
                        array_push($resp2, [
                            'unique_id' => $deviceData->unique_id,
                            'd_name' => $deviceData->d_name,
                            'power' => $deviceLog->power,
                            'push' => $deviceLog->push,
                            'last_usage'=>$lastUsage,
                            'capacity' => $deviceLog->capacity,
                            'region' => $deviceData->region,
                            'city' => $deviceData->city,
//                            'date'=>Jalalian::fromCarbon($deviceLog->created_at)->format("Y-m-d H:i:s")
                            'date'=>Carbon::parse($deviceLog->created_at)->format("Y-m-d H:i:s")
                        ]);
                        array_push($check,$deviceLog->device_id);
                    }

                }
            }catch (\Exception $exception){}


            if(count($adminDevices->toArray()) == 0 && count($deviceLogs->toArray()) == 0) {
//                return ['status' => 404, 'body' => ['type' => 'error', 'message' => [], 'date' => Jalalian::now()->format("Y-m-d H:i:s")]];
                return ['status' => 404, 'body' => ['type' => 'error', 'message' => [],'log'=>$deviceEvents, 'date' => Carbon::now()->format("Y-m-d H:i:s")]];

            }
            else if(count($adminDevices->toArray()) == 0 && count($deviceLogs->toArray()) != 0) {

                return ['status'=>200,'body'=>['type'=>'data','message'=>$resp2,'log'=>$deviceEvents,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
//                return ['status'=>200,'body'=>['type'=>'data','message'=>$resp2,'date'=>Jalalian::now()->format("Y-m-d H:i:s")]];
            }
//            else if(count($adminDevices->toArray()) != 0 && count($deviceLogs->toArray()) == 0){
//
//                dd('3');
//                return ['status'=>200,'body'=>['type'=>'data','message'=>$resp2,'date'=>Jalalian::now()->format("Y-m-d H:i:s")]];
//
//            }
            else{

//                return ['status'=>200,'body'=>['type'=>'data','message'=>$resp2,'date'=>Jalalian::now()->format("Y-m-d H:i:s")]];
                return ['status'=>200,'body'=>['type'=>'data','message'=>$resp2,'log'=>$deviceEvents,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
            }

        }catch (\Exception $exception){
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage().$exception->getLine()]];
            return $resp;
        }
    }
}

?>