<?php


namespace App\Http\Controllers\AuthResponsables;

use App\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSignUp implements Responsable
{
    private $formatter;

    public function __construct($formatter)
    {
        $this->formatter = $formatter;
    }

    public function toResponse($request){

        try{
            $user = new User();
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->email = $request->email;
            $user->key = strtoupper(str_shuffle('ZINOVAA').Str::random(7));
            $user->save();
            $resp = $this->formatter->create($status = 200, $type = 'success',$message = ['scc'=>'کاربر با موفقیت ثبت شد']);

        }catch (\Exception $exception){

            $resp = $this->formatter->create($status = 500, $type = 'error',$message = $exception->getMessage());
        }

        return $resp;
    }
}