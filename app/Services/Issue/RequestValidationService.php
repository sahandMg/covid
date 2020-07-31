<?php

namespace  App\Services\Issue;
use Illuminate\Support\Facades\Validator;

/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 8/1/20
 * Time: 12:03 AM
 */
class RequestValidationService
{

    public function create($request){

        $repo = new \App\Repo();
        $validator = Validator::make($request->all(),[

            'title'=>'required',
//            'img'=>'required|mimes:jpeg,bmp,png,jpg|max:1000',
            'desc'=>'required'
        ]);
        if($validator->fails()){

            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }
}