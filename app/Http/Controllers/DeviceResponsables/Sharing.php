<?php


namespace App\Http\Controllers\DeviceResponsables;

use App\Repo;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Sharing implements Responsable {

    public function __construct()
    {
        
    }
    
    public function toResponse($request){

        try{
            $repo = new Repo();
            $sharedAdmin = User::where('key',$request->key)->where('role_id',$repo->findRoleId('admin'))->firstOrFail();

            $registered_user = DB::table('shared_keys')->where('user_id',Auth::guard('user')->id())->first();

            if(!is_null($registered_user)){

                return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'شما پیش از این، یک کد ادمین ثبت کرده اید']]];
            }

            if(Auth::guard('user')->user()->role_id == $repo->findRoleId('admin')){

                return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'برای ثبت کد باید کاربر عادی باشید']]];
            }
            DB::table('shared_keys')->insert([
                'admin_id'=> $sharedAdmin->id,
                'user_id'=> Auth::guard('user')->id(),
                'key'=>$request->key,
                'created_at'=>Carbon::now()
            ]);
            return $resp = ['status'=>200,'body'=>['type'=>'message','message'=>['scc'=>'کد ثبت شد']]];

        }catch (\Exception $exception){

            return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'کد معتبر نیست']]];
        }
    }
}

?>