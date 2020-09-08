<?php

use App\Device;
use App\DeviceLog;
use App\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;

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

});

Route::get('@admin/signup','AuthController@adminSignup')->name('adminSignup');
Route::post('signup','AuthController@post_adminSignup')->name('adminSignup');
Route::get('zarrin/callback','TransactionController@ZarrinCallback');
Route::get('zarrin/test/callback','TransactionController@test_ZarrinCallback');
Route::get('zarrin/failed','TransactionController@failedPage')->name('PaymentCanceled');
Route::get('zarrin/success','TransactionController@successPage')->name('PaymentSuccess');
Route::get('google/login','AuthController@redirectToProvider');

Route::get('@admin/product-list','AdminController@itemslist')->name('productList');

Route::get('@admin/add-product','AdminController@add')->name('addproduct');
Route::post('@admin/add-product','ShopController@addItem')->name('addproduct');

Route::get('@admin/update-product/{name}','AdminController@update')->name('updateProduct');
Route::post('@admin/update-product/{name}','ShopController@updateItem')->name('updateProduct');

Route::get('@admin/device-management','AdminController@management');

Route::get('@admin/remove-product/{name}','ShopController@removeItem')->name('removeItem');

Route::get('product',function (){

    return view('admin.addItem');
});
Route::get('issue',function(){

    $issue = \App\Issue::where('user_id',1)->first();
    $user = $issue->user;
    return view('email.responseMail',compact('issue','user'));
    $data = ['issue'=>$issue,'user'=>$user];
    Mail::send('email.responseMail',$data,function($message)use($user){

        $message->to('s23.moghadam@gmail.com');
        $message->from(env('NoReply'));
        $message->subject('فاکتور خرید');
    });

});

Route::get('invoice',function(){


    $trans = \App\Transaction::where('id',5)->first();
    $cart = \App\Cart::where('trans_id',5)->first();
    $cart->update(['completed' => 1]);
    $cart->cart = unserialize($cart->cart);
    if(!is_null($cart->user_id)){
        $user = $cart->user;
    }else{
        $user = $cart->admin;
    }
    $data = ['cart'=>$cart,'trans'=>$trans,'user'=>$user];

    Mail::send('email.invoiceMail',$data,function($message)use($cart){

        $message->to($cart->email);
        $message->from(env('NoReply'));
        $message->subject('فاکتور خرید');
    });

//    Mail::send('email.invoiceMail',$data,function($message)use($cart){
//
//        $message->to(env('SAILS_MAIL'));
//        $message->from(env('NoReply'));
//        $message->subject('فاکتور خرید');
//    });

});


Route::get('mohsen',function(){

        $deviceLogs = DeviceLog::orderBy('id','desc')->with('device')->get();

        $resp2 = [];
        try {
//
            $check = [];
            foreach ($deviceLogs as $deviceLog) {
                if (!in_array($deviceLog->device_id, $check)) {
                    $deviceData = $deviceLog->device;
                    try {
                        $lastUsage = $deviceData->reports->last()->total_pushed;
                    } catch (\Exception $e) {
                        $lastUsage = 0;
                    }
                    array_push($resp2, [
                        'unique_id' => $deviceData->unique_id,
                        'd_name' => $deviceData->d_name,
                        'power' => $deviceLog->power,
                        'push' => $deviceLog->push,
                        'last_usage' => $lastUsage,
                        'capacity' => $deviceLog->capacity,
                        'region' => $deviceData->region,
                        'city' => $deviceData->city,
//                            'date'=>Jalalian::fromCarbon($deviceLog->created_at)->format("Y-m-d H:i:s")
                        'date' => Carbon::parse($deviceLog->created_at)->format("Y-m-d H:i:s")
                    ]);
                    array_push($check, $deviceLog->device_id);
                }


            }
        }catch (\Exception $e){

        }
        return $resp2;

});
