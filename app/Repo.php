<?php
/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 6/16/20
 * Time: 10:54 PM
 */

namespace App;


use Illuminate\Support\Facades\DB;

class Repo
{
    public function convertp2e($input){

        $persian = ['۰', '۱', '۲', '۳', '۴', '٤', '۵', '٥', '٦', '۶', '۷', '۸', '۹'];
        $english = [ 0 ,  1 ,  2 ,  3 ,  4 ,  4 ,  5 ,  5 ,  6 ,  6 ,  7 ,  8 ,  9 ];
        return str_replace($persian, $english, $input);
    }
    public function converte2p($input){

        $persian = ['۰', '۱', '۲', '۳', '۴', '٤', '۵', '٥', '٦', '۶', '۷', '۸', '۹'];
        $english = [ 0 ,  1 ,  2 ,  3 ,  4 ,  4 ,  5 ,  5 ,  6 ,  6 ,  7 ,  8 ,  9 ];
        return str_replace($english,$persian , $input);
    }

    public function responseFormatter($resp){

        $respArr = [];
        foreach ($resp as $key=>$item){

            for($t=0;$t<count($item);$t++){

                array_push($respArr,$item[$t]);
            }
        }
        return $respArr;
    }

    public function getGuard(){

        foreach(array_keys(config('auth.guards')) as $guard){

            if(auth()->guard($guard)->check()) return $guard;

        }
    }
    public function findRoleId($roleName){

        return DB::table('role_user')->where('role',$roleName)->first()->id;
    }
}