<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('@admin/signup','AuthController@adminSignup')->name('adminSignup');
Route::post('signup','AuthController@post_adminSignup')->name('adminSignup');
Route::get('zarrin/callback','TransactionController@ZarrinCallback');
Route::get('zarrin/failed','TransactionController@failedPage')->name('PaymentCanceled');
Route::get('zarrin/success','TransactionController@successPage')->name('PaymentSuccess');
Route::get('invoice',function(){


    $trans = \App\Transaction::where('id',3)->first();
    $cart = \App\Cart::where('trans_id',3)->first();

    $cart->update(['completed' => 1]);
    $cart->cart = unserialize($cart->cart);
    if(!is_null($cart->user_id)){
        $user = $cart->user;
    }else{
        $user = $cart->admin;
    }
    $data = ['cart'=>$cart,'trans'=>$trans,'user'=>$user];

//    return view('email.invoiceMail',compact('cart','trans','user'));
    Mail::send('email.invoiceMail',$data,function($message)use($cart){

        $message->to($cart->email);
        $message->from(env('NoReply'));
        $message->subject('فاکتور خرید');
    });

    Mail::send('email.invoiceMail',$data,function($message)use($cart){
//
//        $message->to(env('SAILS_MAIL'));
//        $message->from(env('NoReply'));
//        $message->subject('فاکتور خرید');
    });

});