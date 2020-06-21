<?php

namespace App\Http\Controllers;

use App\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IssueController extends Controller
{


    //    ============ Save issues that users are reporting ============

    /*
     * Data Needed : token,title,img,desc
     * Data returns : message
     *
     */

    public function create(Request $request){

        $validator = Validator::make($request->all(),[

            'title'=>'required',
//            'img'=>'required|mimes:jpeg,bmp,png,jpg|max:1000',
            'desc'=>'required'
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=> $validator->errors()]];
            return $resp;
        }
        try{
            $issue = new Issue();
            $issue->title = $request->title;
            $issue->desc = $request->desc;
//            $name = time().'.'.$request->file('img')->getClientOriginalExtension();
//            $request->file('img')->move(public_path('images'),$name);
//            $issue->img = $name;
            if(Auth::guard('admin')->check()){

                $issue->admin_id = Auth::guard('admin')->id();

            }elseif(Auth::guard('user')->check()){

                $issue->user_id = Auth::guard('user')->id();
            }

            $issue->save();
        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
            return $resp;
        }

        $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc'=>'مشکل شما ثبت شد']]];
//        TODO Send Email To ???
        return $resp;
    }
}
