<?php
/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 7/31/20
 * Time: 10:57 AM
 */

namespace App\Services;


class ReturnMsgFormatter
{

    public function create($status,$type,$message){

        return  ['status'=>$status,'body'=>['type'=>$type,'message'=>$message]];
    }
}