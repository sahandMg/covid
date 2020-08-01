<?php

namespace App\Http\Controllers\AuthResponsables;


use App\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;

class UserLogin implements Responsable
{
    private $repo;
    private $formatter;

    public function __construct($repo,$formatter)
    {
        $this->repo = $repo;
        $this->formatter = $formatter;
    }
    public function toResponse($request)
    {
        try{

            if($token = Auth::guard('user')->attempt(['email'=>$request->email,'password'=>$request->password])){

                $user = User::where('email',$request->email)->first();
                $user->update(['token'=>$token]);
                $user->update(['fcm_token'=>$request->fcm_token]);

                $respMsg = ['name'=>$user->name,'token'=>$token,'code'=>$user->key,'phone'=>$user->phone,'address'=>$user->address];

                $user->role_id == $this->repo->findRoleId('user')?
                    $respMsg['role'] = 'user':
                    $respMsg['role'] = 'admin';

                $resp = $this->formatter->create($status = 200, $type = 'data',$message = $respMsg);

            }
            else{

                $resp = $this->formatter->create($status = 404, $type = 'error',$message = ['err'=>'ایمیل و یا کلمه عبور نادرست است']);
            }
        }catch (\Exception $exception){

            $resp = $this->formatter->create($status = 500, $type = 'error',$message = $exception->getMessage());
        }
        return $resp;
    }
}
