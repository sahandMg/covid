<?php


namespace App\Http\Controllers\DeviceResponsables;;

use App\Device;
use App\Repo;
use App\SharedKey;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;

class deviceReport implements Responsable {

    public function __construct()
    {

    }

    public function toResponse($request){

        try{
            $temp = [];$repo = new Repo();
            $user = Auth::guard('user')->user();
            $devices = Device::where('user_id',$user->id)->get();
            //                checks shared key, if admin code hasn't been shared, user can't see devices
            try{
                    if($user->role_id == $repo->findRoleId('user')){
                        $sharedAdmin = SharedKey::where('user_id',$user->id)->firstOrFail();
                        $devices = Device::where('user_id',$sharedAdmin->admin_id)->get();
                    }
            }
            catch (\Exception $exception){
                return  ['status'=>404,'body'=>['type'=>'data','message'=>[]]];
            }
            if(count($devices) == 0){
                return ['status'=>200,'body'=>['type'=>'data','message'=>[]]];
            }
            foreach ($devices as $device){
                $totalUsage = $device->reports->sum('total_pushed');
                array_push($temp,['name'=>$device->d_name,'total_usage'=>$totalUsage]);
            }
            return ['status'=>200,'body'=>['type'=>'data','message'=>$temp]];


        }catch (\Exception $exception){
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage().$exception->getLine()]];
            return $resp;
        }
    }
}

?>
