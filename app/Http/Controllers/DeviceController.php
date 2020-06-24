<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Device;
use App\DeviceLog;
use App\Repo;
use App\Report;
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
    //    ============ Adding new Device ============
    /*
     * Data Needed : name,ssid,w_ssid,region,city,unique_id,token
     * Data returns : name,token,message
     */
    public function add(Request $request,AuthController $authController,Repo $repo){

        $validator = Validator::make($request->all(),[
//            'd_name'=>'required',
//            'ssid'=>'required',
//            'w_ssid'=>'required',
//            'city'=>'required',
//            'region'=>'required',
            'unique_id'=>'required'
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$validator->errors()]];
            return $resp;
        }

        try{

            $device = Device::where('unique_id',$request->unique_id)->first();
            if(is_null($device)){
                $resp = ['status'=>404,'body'=>['type'=>'data','message'=>['err'=>'دستگاه یافت نشد']]];
                return $resp;
            }else{
                $token = $authController->switchAccountType($repo);
                if($token == 400){

                    return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['دسترسی به این قسمت محدود شده است']]];
                }
                $device->update(['d_name'=>$request->d_name]);
                $device->update(['ssid'=>$request->ssid]);
                $device->update(['w_ssid'=>$request->w_ssid]);
                $device->update(['region'=>$request->region]);
                $device->update(['city'=>$request->city]);
                $device->update(['admin_id'=>Auth::guard('admin')->id()]);
                if($request->has('password')){
                    $device->update(['password'=>$request->password]);
                }

                DeviceLog::where('device_id',$device->id)->whereNull('admin_id')->update(['admin_id'=> Auth::guard('admin')->id()]);
            }
            DB::table('admin_device')->insert([
                'admin_id'=>Auth::guard('admin')->id(),
                'device_id'=>$device->id,
                'created_at'=>Carbon::now()
            ]);
            $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['scc'=>'دستگاه با موفقیت ثبت شد','token'=>$token]]];
        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
        }
        return $resp;
    }

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
//                if($request->has('ssid')){
//
//                    $device->update(['ssid'=>$request->ssid]);
//                }
//                if($request->has('w_ssid')){
//
//                    $device->update(['w_ssid'=>$request->w_ssid]);
//                }
                if($request->has('city')){

                    $device->update(['city'=>$request->city]);
                }
                if($request->has('region')){

                    $device->update(['region'=>$request->region]);
                }
                $device->update(['admin_id'=> Auth::guard('admin')->id()]);

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

    public function remove(Request $request){

        $validator = Validator::make($request->all(),[
            'unique_id'=>'required'
        ]);

        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$validator->errors()]];
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
//    TODO Under 20% send Notif. consider type of the message
    public function sendData(Request $request){

        $device = Device::where('unique_id',$request->unique_id)->first();

        if(is_null($device)){

            $device = new Device();
            $device->unique_id = $request->unique_id;
        //                $device->name = $request->name;
        //                $device->ssid = $request->ssid;
        //                $device->w_ssid = $request->w_ssid;
        //                $device->city = $request->city;
        //                $device->region = $request->region;
            $device->created_at = Carbon::now();
            $device->save();
        }
        $d_log = new DeviceLog();
        $d_log->power = $request->power;
        $d_log->capacity = $request->capacity;
        $d_log->push = $request->push;
        $d_log->device_id = $device->id;

        if(!is_null($device->admin)){

            $d_log->admin_id = $device->admin->id;
        }
            $d_log->save();
            $dateTime = Jalalian::fromCarbon(Carbon::now())->toString();
            $date = explode(' ',$dateTime)[0];
            $time = explode(' ',$dateTime)[1];

        if($request->power < 20){
// TODO Send Notification to app
        }
        if($request->capacity < 20){

        }
            return ['status'=>200,'date'=>$date,'time'=>$time];
    }

    //    ============ send device list to related admin and user (if admin key has been registered before)  ============

    /*
     * Data Needed : token
     * Data returns : devices_list
     */

    public function get_Devices(Request $request,Repo $repo){

        try{

            if(Auth::guard('user')->check()){
                $user = Auth::guard('user')->user();
                $admin_record = DB::table('shared_keys')->where('user_id',$user->id)->first();
                if(is_null($admin_record)){

                    $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'نیاز به تایید ادمین دارید']]];
                    return $resp;
                }
                $admin_id = $admin_record->admin_id;

                $adminDevices = Device::where('admin_id',$admin_id)->get();
                $resp = [];
                foreach ($adminDevices as $adminDevice){

                    $last = $adminDevice->deviceLogs->first();
                    array_push($resp,['d_name'=>$adminDevice->d_name,'power'=>$last->power,'capacity'=>$last->capacity,'region'=>$adminDevice->region,'city'=>$adminDevice->city]);
                }


                if(is_null($adminDevices)){

                    $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'هیچ دستگاهی ثبت نشده است']]];
                    return $resp;
                }
                $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$resp]];
                return $resp;
            }
            if(Auth::guard('admin')->check()){

                $admin = Auth::guard('admin')->user();
//                $devices = DB::table('devices')
//                    ->join('device_logs',function($query){
//                        $query->on('devices.id', '=', 'device_logs.device_id');
//                    })
//                    ->where('devices.admin_id',$admin->id)->orderBy('device_logs.id','desc')->select('d_name','power','capacity','region','city')->get();
                $adminDevices = Device::where('admin_id',$admin->id)->get();
                $resp = [];
                foreach ($adminDevices as $adminDevice){

                    $last = $adminDevice->deviceLogs->first();

                    array_push($resp,['unique_id'=>$last->unique_id,'d_name'=>$adminDevice->d_name,'power'=>$last->power,'capacity'=>$last->capacity,'region'=>$adminDevice->region,'city'=>$adminDevice->city]);
                }



                if(is_null($adminDevices)){

                    $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'هیچ دستگاهی ثبت نشده است']]];
                    return $resp;
                }
                $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$resp]];
                return $resp;
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
    public function sharing(Request $request){

        $validator = Validator::make($request->all(),[
            'key'=>'required'
        ]);

        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$validator->errors()]];
            return $resp;
        }

        try{

            $sharedAdmin = Admin::where('key',$request->key)->first();
            if(is_null($sharedAdmin)){

                $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'کد معتبر نیست']]];
                return $resp;
            }else{
                $registered_user = DB::table('shared_keys')->where('user_id',Auth::guard('user')->id())->first();
                if(!is_null($registered_user)){

                    return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'شما پیش از این، یک کد ادمین ثبت کرده اید']]];
                }

                if(Auth::guard('admin')->check()){

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


    public function liquidChart(Request $request){

//        return [Auth::guard('user')->id(),Auth::guard('admin')->id()];
        $validator = Validator::make($request->all(),[
            'filter_name'=>'required',
            'unique_id'=>'required',
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$validator->errors()]];
            return $resp;
        }
        try{

            $device = Device::where('unique_id',$request->unique_id)->first();
            if(is_null($device)){

                return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['دستگاهی یافت نشد']]];
            }
            $deviceReports = DB::table('reports')->where('device_id',$device->id)
                ->wheredate('created_at','>',Carbon::now()->subMonths(6))
                ->orderBy('created_at','desc')->select('id','total_pushed','created_at')
                ->get();
            if(count($deviceReports) == 0){

                return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['اطلاعاتی برای این دستگاه وجود ندارد']]];
            }
            if($request->filter_name == 'day'){

                $result = [];

                foreach ($deviceReports as $deviceReport){


                    array_push($result,['total_pushed'=>$deviceReport->total_pushed,'date'=> Jalalian::fromCarbon(Carbon::parse($deviceReport->created_at))->format('%A %d %B %y') ]);
                }

                return $resp = ['status'=>200,'body'=>['type'=>'day','message'=>$result]];

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
                    array_push($result,['total_pushed'=>$total_push,'date'=>Jalalian::fromCarbon($today2)->format('%d %B %y').'*'.Jalalian::fromCarbon($today2)->subDays(7)->format('%d %B %y')]);
                    $total_push = 0;
                    $today2->subDays(7);
                    $i += 1;
                }

                return $resp = ['status'=>200,'body'=>['type'=>'week','message'=>$result]];
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
                    array_push($result,['total_pushed'=>$total_push,'date'=>Jalalian::fromCarbon($today2)->format('%B %y')]);
                    $total_push = 0;
                    $today2->subMonths(1);
                    $i += 1;
                }
                return $resp = ['status'=>200,'body'=>['type'=>'month','message'=>$result]];
            }
//            return $deviceReports;

        }catch (\Exception $exception){

            return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage().' '.$exception->getLine()]];
        }


    }


//     TODO !!!!!!!!!!!!  Naqese !!!!!!!!!!
    public function liquidChart2(Request $request){


        $validator = Validator::make($request->all(),[
            'filter_name'=>'required',
            'unique_id'=>'required',
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$validator->errors()]];
            return $resp;
        }
        $id = Device::where('unique_id',$request->unique_id)->first()->id;
        $deviceLogs = DB::table('device_logs')->where('device_id',$id)->get();

        if($request->filter_name == 'week'){

            $logs = DeviceLog::where('created_at','>',Carbon::now()->subDays(7))
                ->where('created_at','<',Carbon::now())
                ->get();

        }
        elseif($request->filter_name == 'month') {

            $logs = $deviceLogs->pluck('created_at')->toArray();
            $sum = 0;
            $respArr = [];
            $years = [];
            $months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
            for ($i = 0; $i < count($logs); $i++) {

                $date = Carbon::parse($logs[$i])->format('Y');
                array_push($years, $date);
            }
            $years = array_values(array_unique($years));
            for ($y = 0; $y < count($years); $y++) {
                for ($m = 0; $m < 12; $m++) {

                    foreach ($deviceLogs as $deviceLog) {

                        if (explode('-', $deviceLog->created_at)[0] == $years[$y]) {
                            if (explode('-', $deviceLog->created_at)[1] == $months[$m]) {

                                $sum = $sum + $deviceLog->push;
                            }
                        }
                    }
                    array_push($respArr, ['date' => $years[$y] . ' ' . $months[$m], 'count' => $sum]);
                    $sum = 0;
                }
            }
            dd($respArr);
        }
        elseif($request->filter_name == 'year'){

            $logs = $deviceLogs->pluck('created_at')->toArray();
            $years = [];
            for($i=0;$i<count($logs);$i++){

                $date = Carbon::parse($logs[$i])->format('Y');
                array_push($years,$date);
            }
            $years = array_values(array_unique($years));
            $sum = 0;
            $respArr = [];
            for($t=0;$t<count($years);$t++){

                foreach ($deviceLogs as $deviceLog){

                    if(explode('-',$deviceLog->created_at)[0] == $years[$t]){

                        $sum = $sum + $deviceLog->push;
                    }
                }
                array_push($respArr,['date'=>$years[$t],'count'=>$sum]);
                $sum = 0;
            }
            dd($respArr);

        }else{

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'ورودی باید هفته،ماه یا سال باشد']]];
            return $resp;
        }
//        return array_values(array_unique($temp));
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
