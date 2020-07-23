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

    return view('welcome');

});

Route::get('notif2',function(){

    $device = \App\Device::find(14);
    $device_name = $device->d_name;
    $body = " حجم مایع دستگاه سهند زیر ۲۰ درصد است ";
    $title = "اخطار حجم مایع";
    \App\Events\DeviceNotificationEvent::dispatch($title,$body,$device->user->id);

    $app_id = env('ONESIGNAL_APP_ID');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players?app_id=" . $app_id);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
        'Authorization: Basic '.env('ONESIGNAL_AUTH_KEY')));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;

});

Route::get('notif3',function(){

    \Berkayk\OneSignal\OneSignalFacade::sendNotificationToUser("Some Message", '290d9ccc-c3df-419c-80ca-68914ce43d1d', $url = null, $data = null);
});

Route::get('notif4',function (){

    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    $token='diWhHpEdy1k:APA91bHfaE_zy4FUJ_GGDmO3XuJNz5qshyMeyjbIvvdLKI-DkR5rzhS00k9Hwc49yKzJLUraUPbu9-H-XOv8hbT-q-omtzXa8-uAv8Ewej52zO1gH0maKoGP4FLCu9FwVlLSpwBDC_3T';

    $notification = [
        'title' => 'Coming From Firebase',
        'sound' => true,
    ];

    $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

    $fcmNotification = [
        //'registration_ids' => $tokenList, //multple token array
        'to'        => $token, //single token
        'notification' => $notification,
        'data' => $extraNotificationData
    ];

    $headers = [
        'Authorization: key=AAAARIQItdY:APA91bGkOqsrx0pvQ3w-IKLe9WzpDs4aUa48ea8WZoRhb1O9h81V7Wj7hJPqMZ7vr8xv0TTTSOSpgSAXw9WPfH_PbSl1ABZqZsQPTRbCxm5ln1Mq4CcQpZ2_Yio5fVSAtzYwf90DdPvP',
        'Content-Type: application/json'
    ];


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);
});

Route::get('logs',function(){

    $repo = new \App\Repo();
//    "{\"name\":\"Devic\",\"location\":\"Istanbol\",\"owner_key\":\"SDKMDLSAAA\",\"region\":\"Anc\",\"key\":\"Raiwan@2020\",\"unique_id\":\"FSDFEERGFSDFEERG\",\"power\":\"0\",\"push\":\"0\",\"capacity\":\"0\"}": null;
//    $repo->parseDataToArray();


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

//Route::get('@admin/remove-product/{name}','AdminController@remove')->name('removeItem');
Route::get('@admin/remove-product/{name}','ShopController@removeItem')->name('removeItem');

Route::get('product',function (){

    return view('admin.addItem');
});
Route::get('issue',function(){

    $issue = \App\Issue::where('user_id',1)->first();
    $user = $issue->user;
//    return view('email.responseMail',compact('issue','user'));
    $data = ['issue'=>$issue,'user'=>$user];
    Mail::send('email.responseMail',$data,function($message)use($user){

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