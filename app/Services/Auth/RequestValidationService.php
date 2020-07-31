<?php

namespace App\Services\Auth;

use App\Repo;
use Illuminate\Support\Facades\Validator;

class RequestValidationService
{
    public $repo;
    public function __construct(Repo $repo)
    {
        $this->repo = $repo;
    }

    public function passwordRecover($request){

        $validator = Validator::make($request->all(),[
            'email'=>'required|email'
        ]);
        if($validator->fails()){

            $errResp =   $this->repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }

    public function signup($request){

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6',
        ]);
        if($validator->fails()){

            $temp = [];

            for($t = 0 ; $t < count($validator->errors()->keys()); $t++){

                array_push($temp,$validator->errors()->get($validator->errors()->keys()[$t]));
            }
            $errResp =  $this->repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }

    public function login($request){

        $validator = Validator::make($request->all(),[

            'email'=>'required|email',
            'password'=>'required|min:6',
            'fcm_token'=>'required'
        ]);
        if($validator->fails()){

            $errResp =  $this->repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }
}