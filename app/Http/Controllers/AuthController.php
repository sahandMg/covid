<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AuthResponsables\AdminSignUp;
use App\Http\Controllers\AuthResponsables\GoogleLogin;
use App\Http\Controllers\AuthResponsables\PasswordRecovery;
use App\Http\Controllers\AuthResponsables\UpdateUserProfile;
use App\Http\Controllers\AuthResponsables\UserLogin;
use App\Http\Controllers\AuthResponsables\UserSignUp;
use App\Repo;
use App\Services\Auth\RequestValidationService;
use App\Services\ReturnMsgFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    private $formatter;

    public function __construct(ReturnMsgFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    //    ============ Recovering Password ============
    /*
     * Data Needed : email
     * Data returns : data
     */

    public function passwordRecover(Request $request,RequestValidationService $rq){

        $val = $rq->passwordRecover($request);
        if(!is_null($val)){

            return $val;
        }
        return new PasswordRecovery($this->formatter);
    }

//    ============ Singing Up The User (user guard) ============
    /*
     * Data Needed : name,email,Password,phone,password_confirmation
     * Data returns : name,token
     */
    public function signup(Request $request,RequestValidationService $rq){

        $val = $rq->signup($request);
        if(!is_null($val)){

            return $val;
        }

        return new UserSignUp($this->formatter);


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

       return new AdminSignUp($repo,$this->formatter);
    }


    //    ============ Singing In The User (user|admin|master guard will be checked) ============
    /*
     * Data Needed : email,Password
     * Data returns : name,token
     */

    public function login(Request $request,Repo $repo,RequestValidationService $rq){


        $val = $rq->login($request);
        if(!is_null($val)){
            return $val;
        }
        return new UserLogin($repo,$this->formatter);
    }
//
//    /**
//     * @return mixed
//     * Google Register & Login
//     */
    public function handleProviderCallback(Request $request,Repo $repo)
    {

        return new GoogleLogin($this->formatter,$repo);

    }
//
//        ============ Logging out the user ============
    /*
     * Data Needed : token
     * Data returns :
     */

    public function logout(){

        Auth::guard('user')->user()->update(['fcm_token'=> null]);

        JWTAuth::parseToken()->invalidate();

        return $this->formatter->create($status = 200, $type = 'data',$message = ['scc'=>'حساب بسته شد']);
    }


    //    ============ Updating User profile ============
    /*
     * Data Needed : token,old_pass,password,password_confirmation,name,address,phone
     * Data returns :
     */
    public function updateProfile(Request $request){


        return new UpdateUserProfile($this->formatter);
    }

    //    ============ Switching user to admin ============
    /*
     * Data Needed : token
     * Data returns :
     */

    public function switchAccountType($user){

        $repo = new Repo();

        $user->update(['role_id'=>$repo->findRoleId('admin')]);

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

        $data = ['name'=>$user->name,'address'=>$user->address,'phone'=>$user->phone];

        return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$data]];
    }

}
