<?php

/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 8/1/20
 * Time: 12:00 AM
 */

namespace App\Http\Controllers\IssueResponsables;

use App\Issue;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateIssue implements Responsable {

    public function __construct()
    {
        
    }
    
    public function toResponse($request){

        try{
            $issue = new Issue();
            $issue->title = $request->title;
            $issue->desc = $request->desc;
//            $name = time().'.'.$request->file('img')->getClientOriginalExtension();
//            $request->file('img')->move(public_path('images'),$name);
//            $issue->img = $name;
//            if(Auth::guard('user')->role_id == $repo->findRoleId('admin')){
//
//                $user = Auth::guard('user')->user();
//
//            }else{
//
//                $issue->user_id = Auth::guard('user')->id();
//                $user = Auth::guard('user')->user();
//            }
            $issue->user_id = Auth::guard('user')->id();
            $user = Auth::guard('user')->user();
            $issue->save();
            $data = ['issue'=>$issue,'user'=>$user];

            Mail::send('email.responseMail',$data,function($message)use($user){

                $message->to($user->email);
                $message->from(env('NoReply'));
                $message->subject('پیام پشتیبانی');
            });
            Mail::send('email.issueMail',$data,function($message)use($user){

                $message->to(env('SUPPORT_MAIL'));
                $message->from(env('NoReply'));
                $message->subject('پیام کاربر');
            });

        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
            return $resp;
        }

        $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc'=>'پیام شما ثبت شد']]];

        return $resp;
    }
}

?>