<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Zarrin;
use App\ZarrinTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    //    ============ Create New Transaction ============

    /*
     * Data Needed : cart,token,address
     * cart = [
     * {name:A,num:20,price pre part:1000},
     * {name:A,num:20,price pre part:1000},
     * ]
     * Data returns : data
     *
     */
    public function create(Request $request){

        $validator = Validator::make($request->all(),[

            'cart'=>'required',
            'address'=>'required',
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=> $validator->errors()]];
            return $resp;
        }

        try{

            $zarrin = new Zarrin($request->all());
            $result = $zarrin->create();
            if($result["Status"] != 404){
// TODO How to redirect user to a webpage in app

                return $resp = ['status'=>200,'body'=>['type'=>'link','message'=> ['https://www.zarinpal.com/pg/StartPay/' . $result["Authority"]]]];
            }else{

                return $resp = ['status'=>500,'body'=>['type'=>'error','message'=> ['err'=>'مشکلی در ارتباط با درگاه پیش آمده است، لطفا دوباره تلاش کنید']]];
            }

        }catch (\Exception $exception){

            return $resp = ['status'=>500,'body'=>['type'=>'error','message'=> $exception->getMessage()]];
        }
    }

    public function ZarrinCallback(Request $request){


        $zarrin = new Zarrin($request->all());

        return $zarrin->verify();

//        TODO How to return back to app
    }

    public function failedPage(Request $request){

        $transactionId = $request->Authority;
        $trans = Transaction::where('trans_id',$transactionId)->first();
        if(is_null($trans)){

            return 'تراکنش یافت نشد';
        }
        $id = $trans->trans_id;
        return view('paymentFailed',compact('id'));

    }
    public function successPage(Request $request){

        $transactionId = $request->Authority;
        $trans = Transaction::where('trans_id',$transactionId)->first();
        if(is_null($trans)){

            return 'تراکنش یافت نشد';
        }
        $id = $trans->trans_id;
        return view('paymentSuccess',compact('id'));

    }
}

