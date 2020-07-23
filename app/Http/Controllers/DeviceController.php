<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Device;
use App\DeviceLog;
use App\Repo;
use App\Report;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Morilog\Jalali\Jalalian;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeviceController extends Controller
{

    //    ============ Updating Device Information ============
    /*
     * Data Needed : name|ssid|w_ssid|region|city and token and unique_id to find the device
     * Data returns : name,token,message
     */
    public function update(Request $request){

        try{
            $device = Device::where('unique_id',$request->unique_id)->first();
            if(is_null($device)){
                $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err' =>'دستگاه مورد نظر یافت نشد']]];
                return $resp;
            }else{
                if($request->has('d_name')){

                    $device->update(['d_name'=>$request->d_name]);
                }
                if($request->has('city')){

                    $device->update(['city'=>$request->city]);
                }
                if($request->has('region')){

                    $device->update(['region'=>$request->region]);
                }
                $device->update(['user_id'=> Auth::guard('user')->id()]);

                $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc' =>'اطلاعات دستگاه به روز رسانی شد']]];
            }

        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
        }
        return $resp;
    }

    //    ============ Removing Device form Database ============
    /*
     * Data Needed : unique_id,token
     * Data returns : name,token,message
     */

    public function remove(Request $request,Repo $repo){

        $validator = Validator::make($request->all(),[
            'unique_id'=>'required'
        ]);

        if($validator->fails()){


            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
        try{

            $device = Device::where('unique_id',$request->unique_id)->first();

            if(is_null($device)){

                $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err' => 'دستگاه موردنظر پیدا نشد']]];

            }else{

                $device->delete();
                $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc' =>'دستگاه موردنظر حذف شد']]];
            }

        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];

        }

        return $resp;
    }


    //    ============ Receiving Data From Devices and store them in database ============

    /*
     * Data Needed : power,capacity,push,unique_id,type
     * Data returns : time and date
     * Device Middleware will check the entry data and validate them
     */
    public function sendData(Request $request,AuthController $authController,Repo $repo){

        try{

            $resp = $repo->parseDataToArray($request->all());

            $device = Device::where('unique_id',$resp['unique_id'])->first();

            $user = User::where('key',$resp['owner_key'])->first();

            if(is_null($user)){
                return 404;
            }
//        Parsing Data From Device, from json to array

//        $key = array_keys($request->all())[0];
//        $segments = explode(',',$key);
//        $resp = [];
//        for($i=0;$i<count($segments);$i++){
//            $resp[explode(':',$segments[$i])[0]] = explode(':',$segments[$i])[1];
//        }
//      ========================

            Cache::put('data',[$resp,$user],2000);

            $admin = $authController->switchAccountType($user);

            if(is_null($device)){

                $device = new Device();
                $device->unique_id = $resp->unique_id;
                $device->d_name = $resp->name;
                $device->ssid = $resp->wifi_password;
                $device->user_id = $admin->id;
                $device->w_ssid = $resp->wifi_ssid;
                $device->city = $resp->location;
                $device->region = $resp->region;
//                $device->created_at = Carbon::now();
                $device->save();
            }else{

                $device->update(['d_name'=>$resp->name]);
                $device->update(['ssid'=>$resp->wifi_password]);
                $device->update(['w_ssid'=>$resp->wifi_ssid]);
                $device->update(['city'=>$resp->location]);
                $device->update(['region'=>$resp->region]);
                $device->update(['user_id'=>$admin->id]);

            }
            if(isset($resp['power'])){

                $d_log = new DeviceLog();
                $d_log->power = $resp->power;
                $d_log->capacity = $resp->capacity;
                $d_log->push = $resp->push;
                $d_log->device_id = $device->id;
                $d_log->user_id = $device->user->id;
                $d_log->save();
            }

            $dateTime = Jalalian::fromCarbon(Carbon::now())->toString();
            $date = explode(' ',$dateTime)[0];
            $time = explode(' ',$dateTime)[1];

            if($resp['capacity'] < env('CAPACITY_THRESHOLD')){
                $device_name = $device->d_name;
                $body = " حجم مایع دستگاه $device_name زیر ۲۰ درصد است ";
                $title = "اخطار حجم مایع";
                \App\Events\DeviceNotificationEvent::dispatch($title,$body,$device->user->fcm_token);

            }
            if($resp['power'] < env('POWER_THRESHOLD')){
                $device_name = $device->d_name;
                $body = " ظرفیت باتری دستگاه $device_name زیر ۲۰ درصد است ";
                $title = "اخطار ظرفیت باتری";
                \App\Events\DeviceNotificationEvent::dispatch($title,$body,$device->user->fcm_token);

            }
            return ['status'=>200,'date'=>$date,'time'=>$time,'power_off'=> $device->power_off];

        }catch (\Exception $exception){

            return $exception->getMessage();
        }

    }


    //    ============ send device list to related admin and user (if admin key has been registered before)  ============

    /*
     * Data Needed : token,date
     * Data returns : message
     */

    public function get_Devices_update(Request $request,Repo $repo){


        $validator = Validator::make($request->all(),[
            'date'=>'required'
        ]);

        if($validator->fails()){


            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }

        try{

            $date = $request->date;

//            USER SIDE

            if(Auth::guard('user')->user()->role_id == $repo->findRoleId('user')){

                $user = Auth::guard('user')->user();
                $admin_record = DB::table('shared_keys')->where('user_id',$user->id)->first();

//                checks shared key, if admin code hasn't been shared, user can't see devices

                if(is_null($admin_record)){

                    $resp = ['status'=>404,'body'=>['type'=>'data','message'=>[],'date'=>Carbon::now()->format("Y-m-d H:i:s")]];

                    return $resp;
                }
                $admin_id = $admin_record->admin_id;

                $adminDevices = Device::where('user_id',$admin_id)->where('updated_at','>',Carbon::parse($date))->get();
                $resp = [];

//                Means that there is no new devices on the app

                if(count($adminDevices->toArray()) == 0){

                    $devices =  DB::table('devices')->where('devices.user_id',$admin_id)->where('device_logs.updated_at','>',Carbon::parse($date))
                        ->join('device_logs','device_logs.device_id','=','devices.id')->select('unique_id','d_name','power','capacity','region','city')->get();

//               Means that there is no new data for devices

                    if(count($devices->toArray()) == 0){

                        $resp = ['status'=>404,'body'=>['type'=>'error','message'=>[],'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
                        return $resp;
                    }else{

                        $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$devices,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];

                        return $resp;
                    }
                }
                else{

                    foreach ($adminDevices as $adminDevice){

                        $last = $adminDevice->deviceLogs->first();

                        array_push($resp,[

                            'unique_id'=>$adminDevice->unique_id,'d_name'=>$adminDevice->d_name,
                            'power'=>$last->power,'capacity'=>$last->capacity,
                            'region'=>$adminDevice->region,'city'=>$adminDevice->city
                        ]);
                    }

                    $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$resp,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
                    return $resp;
                }
            }

    //        ADMIN SIDE

            else if(Auth::guard('user')->user()->role_id == $repo->findRoleId('admin')){

                $admin = Auth::guard('user')->user();
                $adminDevices = Device::where('user_id',$admin->id)->where('updated_at','>',Carbon::parse($date))->get();


//                No Update for devices. Check device props instead

                if(count($adminDevices->toArray()) == 0){

                    $devices =  DB::table('devices')->where('devices.user_id',$admin->id)->where('device_logs.updated_at','>',Carbon::parse($date))
                        ->join('device_logs','device_logs.device_id','=','devices.id')->select('unique_id','d_name','power','capacity','region','city')->get();


                    if(count($devices->toArray()) == 0){

                        $resp = ['status'=>404,'body'=>['type'=>'error','message'=>[],'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
                        return $resp;
                    }else{

                        $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$devices,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];

                        return $resp;
                    }
                }
                else{

                    $resp = [];

                    try{
                        foreach ($adminDevices as $adminDevice){

                            $last = $adminDevice->deviceLogs->first();

                            array_push($resp,['unique_id'=>$adminDevice->unique_id,'d_name'=>$adminDevice->d_name,'power'=>$last->power,'capacity'=>$last->capacity,'region'=>$adminDevice->region,'city'=>$adminDevice->city]);
                        }
                    }catch (\Exception $exception){

                        return ($exception->getMessage());
                    }
                    $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$resp,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
                    return $resp;
                }
            }
        }catch (\Exception $exception){
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage().$exception->getLine()]];
            return $resp;
        }
    }

    //    ============ Registering admin key by user  ============

    /*
     * Data Needed : token,key
     * Data returns : message
     */
    public function sharing(Request $request,Repo $repo){

        $validator = Validator::make($request->all(),[
            'key'=>'required'
        ]);

        if($validator->fails()){

            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }

        try{

            $sharedAdmin = User::where('key',$request->key)->where('role_id',$repo->findRoleId('admin'))->first();
            if(is_null($sharedAdmin)){

                $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'کد معتبر نیست']]];
                return $resp;
            }else{

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
            }

        }catch (\Exception $exception){

            return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
        }

    }

    //    ============ Sending device liquid usage for app chart ============

    /*
     * Data Needed : filter_name(week,month,year),token,unique_id
     * Data returns : data
     *
     */


    public function liquidChart(Request $request,Repo $repo){

//        return [Auth::guard('user')->id(),Auth::guard('admin')->id()];
        $validator = Validator::make($request->all(),[
            'filter_name'=>'required',
            'unique_id'=>'required',
            'date'=>'required'
        ]);
        if($validator->fails()){

            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
        $date = $request->date;
        try{

            $device = Device::where('unique_id',$request->unique_id)->first();
            if(is_null($device)){

                return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['دستگاهی یافت نشد']]];
            }
            $deviceReports = DB::table('reports')->where('device_id',$device->id)
                ->where('updated_at','>',Carbon::parse($date))
                ->orderBy('created_at','desc')->select('id','total_pushed','created_at')
                ->get();

            if(count($deviceReports) == 0){

                return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>[],'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
            }
            if($request->filter_name == 'day'){

                $result = [];

                foreach ($deviceReports as $deviceReport){


                    array_push($result,['total_pushed'=>$deviceReport->total_pushed,'date'=> $repo->converte2p(Jalalian::fromCarbon(Carbon::parse($deviceReport->created_at))->format('Y-m-d')) ]);
                }

                return $resp = ['status'=>200,'body'=>['type'=>'day','message'=>$result,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];

            }elseif ($request->filter_name == 'week'){

                $total_push = 0;
                $endDate = Carbon::parse($deviceReports[count($deviceReports)-1]->created_at);
                $today = Carbon::now();
                $today2 = Carbon::now();
                $result = [];
                $i = 1;
                while ($today->greaterThanOrEqualTo($endDate)){

                    foreach ($deviceReports as $deviceReport){

                        $queryDate = Carbon::parse($deviceReport->created_at);
                        $today = Carbon::now();

                        if($queryDate->greaterThanOrEqualTo($today->subDays(7*$i)) && $queryDate->lessThanOrEqualTo(Carbon::now()->subDays(7*($i-1)))){

                            $total_push = $total_push + $deviceReport->total_pushed;

                        }else{

                        }
                    }
//                    array_push($result,['total_pushed'=>$total_push,'date'=>$repo->converte2p(Jalalian::fromCarbon($today2)->format('Y-m-d')).'*'.$repo->converte2p(Jalalian::fromCarbon($today2)->subDays(7)->format('Y-m-d'))]);
                    array_push($result,['total_pushed'=>$total_push,'date'=>$repo->converte2p(Jalalian::fromCarbon($today2)->format('Y-m-d'))]);
                    $total_push = 0;
                    $today2->subDays(7);
                    $i += 1;
                }

                return $resp = ['status'=>200,'body'=>['type'=>'week','message'=>$result,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
            }

            elseif ($request->filter_name == 'month'){

                $total_push = 0;
                $endDate = Carbon::parse($deviceReports[count($deviceReports)-1]->created_at)->firstOfMonth();
                $today2 = Carbon::now();
                $result = [];
                $i = 1;
                while ($today2->greaterThanOrEqualTo($endDate)){

                    foreach ($deviceReports as $deviceReport){

                        $queryDate = Carbon::parse($deviceReport->created_at);
                            if($today2->firstOfMonth()->equalTo($queryDate->firstOfMonth())){

                                $total_push = $total_push + $deviceReport->total_pushed;
                        }else{

                        }
                    }
                    array_push($result,['total_pushed'=>$total_push,'date'=>$repo->converte2p(Jalalian::fromCarbon($today2)->format('%B %y'))]);
                    $total_push = 0;
                    $today2->subMonths(1);
                    $i += 1;
                }
                return $resp = ['status'=>200,'body'=>['type'=>'month','message'=>$result,'date'=>Carbon::now()->format("Y-m-d H:i:s")]];
            }
//            return $deviceReports;

        }catch (\Exception $exception){

            return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage().' '.$exception->getLine()]];
        }
    }
    //    ============ Getting Transactions List  ============

    /*
     * Data Needed : token
     * Data returns : data
     */
//    TODO How makes Transactions ???
    public function transList(){

    }

}
