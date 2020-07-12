<?php

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

Route::get('/', function ($notification_id) {


//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications/{notification_id}?app_id=".env('ONESIGNAL_APP_ID'));
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
//        'Authorization: Basic '.env('ONESIGNAL_REST_API_KEY')));
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//    curl_setopt($ch, CURLOPT_HEADER, FALSE);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//
//    $response = curl_exec($ch);
//    curl_close($ch);
//    $return["allresponses"] = $response;
//    $return = json_encode( $return);
//
//    print("\n\nJSON received:\n");
//    print($return);
//    print("\n");

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://onesignal.com/api/v1/notifications?app_id=".env('ONESIGNAL_APP_ID'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic ".env('ONESIGNAL_REST_API_KEY'),
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

     dd($response,200);

});

Route::get('notif',function(){

    $content      = array(
        "en" => 'English Message'
    );
    $hashes_array = array();
    array_push($hashes_array, array(
        "id" => "like-button",
        "text" => "Like",
        "icon" => "http://i.imgur.com/N8SN8ZS.png",
        "url" => "https//covid.sahand-moghadam.ir"
    ));
    array_push($hashes_array, array(
        "id" => "like-button-2",
        "text" => "Like2",
        "icon" => "http://i.imgur.com/N8SN8ZS.png",
        "url" => "https//covid.sahand-moghadam.ir"
    ));
    $fields = array(
        'app_id' => env("ONESIGNAL_APP_ID"),
        'included_segments' => array(
            'All'
        ),
        'data' => array(
            "foo" => "bar"
        ),
        'contents' => $content,
        'web_buttons' => $hashes_array
    );

    $fields = json_encode($fields);
    print("\nJSON sent:\n");
    print($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.env('ONESIGNAL_REST_API_KEY')
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);


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


//Route::get('@admin/remove-product/{name}','AdminController@remove')->name('removeItem');
Route::get('@admin/remove-product/{name}','ShopController@removeItem')->name('removeItem');

Route::get('product',function (){

    return view('admin.addItem');
});
Route::get('issue',function(){

    $issue = \App\Issue::where('user_id',1)->first();
    $user = $issue->user;
    return view('email.responseMail',compact('issue','user'));
    $data = ['issue'=>$issue,'user'=>$user];
    Mail::send('email.issueMail',$data,function($message)use($user){

        $message->to('s23.moghadam@gmail.com');
        $message->from(env('NoReply'));
        $message->subject('فاکتور خرید');
    });

});

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

    return view('email.invoiceMail',compact('cart','trans','user'));
//    Mail::send('email.invoiceMail',$data,function($message)use($cart){
//
//        $message->to($cart->email);
//        $message->from(env('NoReply'));
//        $message->subject('فاکتور خرید');
//    });
//
//    Mail::send('email.invoiceMail',$data,function($message)use($cart){
////
////        $message->to(env('SAILS_MAIL'));
////        $message->from(env('NoReply'));
////        $message->subject('فاکتور خرید');
//    });

});