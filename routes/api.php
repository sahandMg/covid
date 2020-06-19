<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

// ======== Authentication Routes ========

Route::post('signup','AuthController@signup');
Route::post('login','AuthController@login');
Route::post('logout','AuthController@logout');
Route::post('user/check','AuthController@userCheck')->middleware('token');

Route::post('test',function(Request $request){


});
// ======== Device Manager Routes ========

Route::group(['prefix'=>'device','middleware'=>['token','guest:admin']],function(){

    Route::post('add','DeviceController@add');
    Route::post('update','DeviceController@update');
    Route::post('remove','DeviceController@remove');
});

// ======== Device Connections Routes ========

Route::post('device/send','DeviceController@sendData')->middleware('device');

// ======== Device General Routes (admin & user) ========
Route::group(['prefix'=>'device','middleware'=>'token'],function() {

    Route::post('chart/liquid', 'DeviceController@liquidChart');
    Route::post('list', 'DeviceController@get_Devices');
    Route::post('sharing', 'DeviceController@sharing');
});

Route::group(['prefix'=>'shop','middleware'=>['token','guest:admin']],function (){

    Route::post('add','ShopController@addItem');
    Route::post('update','ShopController@updateItem');
    Route::post('remove','ShopController@removeItem');
});
Route::post('shop/item/list','ShopController@itemsList')->middleware('token');

Route::group(['prefix'=>'trans','middleware'=>'token'],function (){

    Route::post('create','TransactionController@create');
});