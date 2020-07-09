<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Master;
use App\Repo;
use App\SharedKey;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    //    ============ Recovering Password ============
    /*
     * Data Needed : email
     * Data returns : data
     */

    public function passwordRecover(Request $request,Repo $repo){

        $validator = Validator::make($request->all(),[
            'email'=>'required'
        ]);
        if($validator->fails()){

            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;

        }
        try{

//            $admin = Admin::where('email',$request->email)->first();

            $user = User::where('email',$request->email)->first();

            if(is_null($user)){
//            if(is_null($admin) && is_null($user)){

                return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['ایمیل در سیستم ثبت نشده است']]];

            }else{

                $str = Str::random();

//                if(!is_null($admin)){
//
//                    $admin->update(['password'=>Hash::make($str)]);
////                    TODO SEND EMAIL
//                }
                if(!is_null($user)){

                    $user->update(['password'=>Hash::make($str)]);
//                    TODO SEND EMAIL
                }

                Mail::send('email.recover',['pass'=>$str],function($message)use($user){

                    $message->to($user->email);
                    $message->from(env('NoReply'));
                    $message->subject('فراموشی کلمه عبور');
                });

                return $resp = ['status'=>200,'body'=>['type'=>'error','message'=>['کلمه عبور جدید به ایمیل شما ارسال شد']]];

            }

        }catch (\Exception $exception){

            return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
        }


    }


//    ============ Singing Up The User (user guard) ============
    /*
     * Data Needed : name,email,Password,phone,password_confirmation
     * Data returns : name,token
     */
    public function signup(Request $request,Repo $repo){



        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users|unique:admins',
            'password'=>'required|confirmed|min:6',
//            'role'=>'required'
//            'phone'=>'required|numeric'
        ]);
        if($validator->fails()){

            $temp = [];

            for($t = 0 ; $t < count($validator->errors()->keys()); $t++){

                array_push($temp,$validator->errors()->get($validator->errors()->keys()[$t]));
            }
            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }

        try{
            $user = new User();
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->email = $request->email;
            $user->key = strtoupper(str_shuffle('HABIBI').uniqid());
//            $user->phone = $repo->convertp2e($request->phone);
            $user->save();
            $token = JWTAuth::fromUser($user);
            $user->update(['token'=>$token]);
            $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['scc'=>'کاربر با موفقیت ثبت شد']]];
        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
        }

        return $resp;

    }

//    ============ Admin Signup Page View ============

    public function adminSignup(){

        return view('adminSignup');
    }


    //    ============ Singing Up The Admin (admin guard) from web page ============
    /*
     * Data Needed : name,email,Password,phone
     * Data returns : name,token
     */
    public function post_adminSignup($request,Repo $repo){

        $admin = new User();
        $admin->name = $request->name;
        $admin->password = Hash::make($request->password);
        $admin->email = $request->email;
//        $admin->phone = $repo->convertp2e($request->phone);
        $admin->key = strtoupper(str_shuffle('HABIBI').uniqid());
        $admin->role_id = $repo->findRoleId('admin');
        $admin->save();
// !!!!!!!!!! DO NOT CHANGE THE RETURN FORMAT !!!!!!!!!!!!
        return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['scc'=>'ادمین با موفقیت ثبت شد']]];
    }


    //    ============ Singing In The User (user|admin|master guard will be checked) ============
    /*
     * Data Needed : email,Password
     * Data returns : name,token
     */

    public function login(Request $request,Repo $repo){


        $validator = Validator::make($request->all(),[

            'email'=>'required|email',
            'password'=>'required|min:6'
        ]);
        if($validator->fails()){

            $temp = [];

//            for($t = 0 ; $t < count($validator->errors()->keys()); $t++){
//
//                array_push($temp,$validator->errors()->get($validator->errors()->keys()[$t]));
//            }
            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
        try{

//            if($token = Auth::guard('user')->attempt(['email'=>$request->email,'password'=>$request->password])){
            if($token = Auth::guard('user')->attempt(['email'=>$request->email,'password'=>$request->password])){

                $user = User::where('email',$request->email)->first();
                $user->update(['token'=>$token]);
                if($user->role_id == $repo->findRoleId('user') ){

                    $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['name'=>$user->name,'token'=>$token,
                        'role'=>'user','code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address]]];
                }else{

                    $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['name'=>$user->name,'token'=>$token,
                        'role'=>'admin','code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address]]];
                }

            }
//            elseif($token = Auth::guard('admin')->attempt(['email'=>$request->email,'password'=>$request->password])){
//
//                $user = Admin::where('email',$request->email)->first();
//
//                $user->update(['token'=>$token]);
//                $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['name'=>$user->name,'token'=>$token,
//                    'role'=>'admin','code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address]]];
//            }
//            elseif($token = Auth::guard('master')->attempt(['email'=>$request->email,'password'=>$request->password])){
//
//                $user = Master::where('email',$request->email)->first();
//                $user->update(['token'=>$token]);
//                $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['name'=>$user->name,'token'=>$token,
//                    'role'=>'master','code'=>0,'phone'=>$user->phone,'address'=>$user->address]]];
//            }
            else{
                $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'ایمیل و یا کلمه عبور نادرست است']]];
            }
        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
        }
        return $resp;
    }

    //      Google login

    public function redirectToProvider()
    {

        return Socialite::driver('google')->redirect();
    }
//
//    /**
//     * @return mixed
//     * Google Register & Login
//     */
    public function handleProviderCallback(Request $request,Repo $repo)
    {

//        $client =  Socialite::driver('google')->stateless()->user();
        $user = User::where('email',$request->email)->first();
//        $admin = Admin::where('email',$request->email)->first();
        if(!is_null($user)){
            $token = Auth::guard('user')->login($user);

            if($user->role_id == $repo->findRoleId('user') ){

                $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['name'=>$user->name,'token'=>$token,
                    'role'=>'user','code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address]]];
            }else{

                $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['name'=>$user->name,'token'=>$token,
                    'role'=>'admin','code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address]]];
            }

            return $resp;

        }
//        elseif(!is_null($admin)){
//
//            $token = Auth::guard('admin')->login($admin);
//            $admin->update(['token'=>$token]);
//            return $resp = $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['name'=>$user->name,'token'=>$token,
//                'role'=>'admin','code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address]]];
//
//        }
        else{

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
//            $user->avatar = $client->avatar;
            $user->key = strtoupper(str_shuffle('HABIBI'));
            $user->save();
            $token = Auth::guard('user')->login($user);
            $user->update(['token'=>$token]);
            $resp = ['status'=>200,'body'=>['type'=>'data','message'=>['name'=>$user->name,'token'=>$token,
                'role'=>'admin','code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address]]];
            return $resp;
        }

    }
//
//        ============ Logging out the user ============
    /*
     * Data Needed : token
     * Data returns :
     */

    public function logout(){

        JWTAuth::parseToken()->invalidate();
        return  ['status'=>200,'body'=>['type'=>'success','message'=> ['scc'=>'حساب بسته شد']]];
    }


    //    ============ Updating User profile ============
    /*
     * Data Needed : token,old_pass,password,password_confirmation,name,address,phone
     * Data returns :
     */
    public function updateProfile(Request $request, Repo $repo){


        $resp = $this->updateUser($repo->getGuard(),$request);

        if($resp == 200){

            return $resp = ['status'=>200,'body'=>['type'=>'message','message'=>['اطلاعات کاربر به روز شد']]];
        }else{

            return $resp;
        }

    }
// ================ ^^^^^^^^^ ================
    private function updateUser($guard,$request){

        try{

            $user = Auth::guard($guard)->user();

            if($request->has('name')){

                $user->update(['name'=>$request->name]);
            }

            if($request->has('address')){

                $user->update(['address'=>$request->address]);
            }

            if($request->has('phone')){

                $user->update(['phone'=>$request->phone]);
            }

            if($request->has('old_password') && $request->has('password')){

                if(Hash::check($request->old_password,$user->password)){

                    $user->update(['password'=>Hash::make($request->password)]);
                }else{

                    return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['کلمه عبور فعلی نادرست است']]];
                }
            }
            return 200;

        }catch (\Exception $exception){

            return $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
        }

    }

    //    ============ Switching user to admin ============
    /*
     * Data Needed : token
     * Data returns :
     */

    public function switchAccountType($user){

        $repo = new Repo();

//        $admin = new Admin();
//        $admin->name = $user->name;
//        $admin->password = $user->password;
//        $admin->email = $user->email;
//        $admin->key = $user->key;
//        $admin->save();
//        $token = JWTAuth::fromUser($admin);
        $user->update(['role_id'=>$repo->findRoleId('admin')]);
//        $user->delete();
//        $token = Auth::guard('user')->login($admin);
        return $user;

    }

    //    ============ Checks if user is authenticated or not. uses token middleware, defined on route ============
    /*
     * Data Needed : token
     * Data returns :
     */

    public function userCheck(){

        return  ['status'=>200,'body'=>['type'=>'success','message'=> ['ok']]];
    }

    //    ============ Return User Profile Data ============
    /*
     * Data Needed : token
     * Data returns :
     */
    public function userData(Request $request,Repo $repo){

        $user = Auth::guard('user')->user();

//        if(Auth::guard('admin')->check()){
//
//            $data = ['name'=>$user->name,'address'=>$user->address,'phone'=>$user->phone,'key'=>$user->key];
//        }
//        else{
//            $data = ['name'=>$user->name,'address'=>$user->address,'phone'=>$user->phone];
//        }
        $data = ['name'=>$user->name,'address'=>$user->address,'phone'=>$user->phone];

        return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$data]];
    }


}
