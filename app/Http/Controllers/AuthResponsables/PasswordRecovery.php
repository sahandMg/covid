<?php
namespace App\Http\Controllers\AuthResponsables;

use App\Notifications\PasswordMailNotification;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordRecovery implements \Illuminate\Contracts\Support\Responsable
{

    private $formatter;

    public function __construct($formatter)
    {
        $this->formatter = $formatter;
    }

    public function toResponse($request)
    {
        try{

            $user = User::where('email',$request->email)->firstOrFail();

            $str = Str::random();

            $user->update(['password'=>Hash::make($str)]);

            // SENDING EMAIL NOTIFICATION TO USER

            $user->notify(new PasswordMailNotification($user,$str));

            return $this->formatter->create($status = 200, $type = 'success',$message = ['کلمه عبور جدید به ایمیل شما ارسال شد']);

        }catch (\Exception $exception){

            return $this->formatter->create($status = 500, $type = 'error',$message = ['ایمیل در سیستم ثبت نشده است']);
        }
    }

}