<?php

namespace App\Services\Shop;

use Illuminate\Support\Facades\Validator;

class RequestValidationService
{

    public function image($request){


        $extension = $request->file('img')->getClientOriginalExtension();

        $extensions = ['jpeg','bmp','png','jpg'];

        if($request->file('img')->getSize()/1000 > 1000){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'حجم عکس حداکثر باید ۱۰۰۰ کیلوبایت باشد']]];

            return $resp;
        }
        if(!in_array($extension,$extensions)){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'باشد jpg,jpeg,bmp,png  عکس باید به یکی از فرمت های  ']]];

            return response($resp);
        }


    }
}