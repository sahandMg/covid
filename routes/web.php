<?php

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


    $user = \App\User::find(3);
    $user->notify(new \App\Notifications\PowerNotification());
//    AIzaSyDPZ5pawLuPhIhjQf6bhJhCzvAdP-axT5Q
//client id : 294272939478-ogckpr8p76m60sfsghms0kfi52j1n0ql.apps.googleusercontent.com
//    client secret

     $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,  'https://fcm.googleapis.com/v1/%5BPARENT%5D/messages:send?key=AIzaSyDPZ5pawLuPhIhjQf6bhJhCzvAdP-axT5Q');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer 294272939478-ogckpr8p76m60sfsghms0kfi52j1n0ql.apps.googleusercontent.com'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    $return["allresponses"] = $response;
    $return = json_encode( $return);

//    $queries =  DB::table('devices')->join('device_logs', 'devices.id', '=', 'device_logs.device_id')
//        ->where('device_logs.created_at','<',Carbon::today())->where('device_logs.created_at','>',Carbon::yesterday())
//        ->select('device_id','push','devices.user_id','device_logs.created_at')->get();
//    $deviceArr = [];
//    $deviceOwner = [];
//    try{
//
//        foreach ($queries as $query){
//
//            if(!in_array($query->device_id,array_keys($deviceArr))){
//                $deviceArr[$query->device_id] = $query->push;
//            }else{
//                $deviceArr[$query->device_id] = $deviceArr[$query->device_id] + $query->push;
//            }
//            if(!in_array($query->device_id,array_keys($deviceOwner))){
//
//                $deviceOwner[$query->device_id] = $query->user_id;
//            }
//        }
//
//        foreach($deviceArr as $key=>$item){
//
//            $report = new Report();
//            $report->total_pushed = $item;
//            $report->device_id = $key;
//            $report->user_id = $deviceOwner[$key];
//            $report->save();
//        }
//    }catch (\Exception $exception){
//
//        dd($exception->getMessage());
//    }


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

//    $curl = curl_init();
//
//    curl_setopt_array($curl, array(
//        CURLOPT_URL => "https://onesignal.com/api/v1/notifications?app_id=".env('ONESIGNAL_APP_ID'),
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_ENCODING => "",
//        CURLOPT_MAXREDIRS => 10,
//        CURLOPT_TIMEOUT => 30,
//        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//        CURLOPT_CUSTOMREQUEST => "GET",
//        CURLOPT_HTTPHEADER => array(
//            "Authorization: Basic ".env('ONESIGNAL_REST_API_KEY'),
//        ),
//    ));
//
//    $response = curl_exec($curl);
//    $err = curl_error($curl);
//
//    curl_close($curl);
//
//     dd($response,200);

});

Route::get('notif',function(){

    $content      = array(
        "en" => 'اخطار باتری ! ',
        "content"=>'سطح باتری زیر ۲۰ درصد است'
    );
    $hashes_array = array();
    array_push($hashes_array, array(
        "id" => "like-button",
        "text" => "Like",
        "icon" => "http://i.imgur.com/N8SN8ZS.png",
        "url" => "http//covid.sahand-moghadam.ir"
    ));
    array_push($hashes_array, array(
        "id" => "like-button-2",
        "text" => "Like2",
        "icon" => "http://i.imgur.com/N8SN8ZS.png",
        "url" => "http//covid.sahand-moghadam.ir"
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

Route::get('notif2',function(){

    $url = "https://app.najva.com/api/v1/notifications/";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'cache-control' => 'no-cache',
        'content-type' => 'application/json',
        'authorization' => 'Token 535de69c6613f810ab193ac011fca5241211b1b8'
    ]);
    $fields = json_encode([
        "api_key"=>"ebcc1665-cdb8-4c7b-8e42-835e24c7a8fd",
        "title"=>"اخطاریه ۲",
        "body"=>"بدو باتریت تموم شد",
        "onclick_action"=>"open-app",
        "url"=>"http://covid.sahand-moghadam.ir",
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    dd($response);

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