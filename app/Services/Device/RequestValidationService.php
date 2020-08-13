<?php

namespace  App\Services\Device;
use App\Repo;
use Illuminate\Support\Facades\Validator;

class RequestValidationService
{

    private $repo;

    public function __construct(\App\Repo $repo)
    {
        $this->repo = $repo;
    }

    public function removeDevice($request){

        $validator = Validator::make($request->all(),[
            'unique_id'=>'required'
        ]);

        if($validator->fails()){


            $errResp =  $this->repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }

    public function deviceList($request){

        $validator = Validator::make($request->all(),[
            'date'=>'required'
        ]);

        if($validator->fails()){


            $errResp =  $this->repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }

    public function sharing($request){

        $validator = Validator::make($request->all(),[
            'key'=>'required'
        ]);

        if($validator->fails()){

            $errResp =  (new Repo())->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }
    public function chart($request){

        $validator = Validator::make($request->all(),[
            'filter_name'=>'required',
            'unique_id'=>'required',
            'date'=>'required'
        ]);
        if($validator->fails()){

            $errResp =  (new Repo())->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }
    public function report($request){

        $validator = Validator::make($request->all(),[
            'unique_id'=>'required',
        ]);
        if($validator->fails()){

            $errResp =  (new Repo())->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }
    }
}