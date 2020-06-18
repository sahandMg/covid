<?php
/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 6/16/20
 * Time: 10:54 PM
 */

namespace App;


class Repo
{
    public function convertp2e($input){

        $persian = ['۰', '۱', '۲', '۳', '۴', '٤', '۵', '٥', '٦', '۶', '۷', '۸', '۹'];
        $english = [ 0 ,  1 ,  2 ,  3 ,  4 ,  4 ,  5 ,  5 ,  6 ,  6 ,  7 ,  8 ,  9 ];
        return str_replace($persian, $english, $input);
    }

    public function responseFormatter($resp){

        foreach ($resp->errors() as $key=>$item){
            dd($key);
        }
    }
}