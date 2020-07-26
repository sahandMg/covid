<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
Route::post('google/login/callback','AuthController@handleProviderCallback');
Route::post('logout','AuthController@logout')->middleware('token');
Route::post('password/recover','AuthController@passwordRecover');
Route::post('user/check','AuthController@userCheck')->middleware('token');
Route::post('user/data','AuthController@userData')->middleware('token');
Route::post('user/update/profile','AuthController@updateProfile')->middleware('token');
Route::post('user/address','ShopController@userAddress')->middleware('token');
Route::post('switch-account','AuthController@switchAccountType')->middleware('token');

Route::post('test',function(Request $request){

    Cache::forget('data');
    Cache::put('data',$request->all(),2000);
    return response($request->all());



});
Route::get('test2',function (){

    return response(Cache::get('data'));
});

// ======== Device Manager Routes ========

Route::post('device/add','DeviceController@add')->middleware('token');

Route::group(['prefix'=>'device','middleware'=>['token','guest:admin']],function(){

    Route::post('update','DeviceController@update');
    Route::post('remove','DeviceController@remove');
});

// ======== Device Connections Routes ========

//Route::post('device/send','DeviceController@sendData');
Route::post('device/send','DeviceController@sendData')->middleware('device');

// ======== Device General Routes (admin & user) ========

Route::group(['prefix'=>'device','middleware'=>'token'],function() {

    Route::post('chart/liquid', 'DeviceController@liquidChart');
    Route::post('list', 'DeviceController@get_Devices_update');
//    Route::post('list/update', 'DeviceController@get_Devices_update');
    Route::post('sharing', 'DeviceController@sharing');
});

// ======== Shopping Routes ========
Route::group(['prefix'=>'shop','middleware'=>'token'],function (){

    Route::post('add','ShopController@addItem');
    Route::post('update','ShopController@updateItem');
    Route::post('remove','ShopController@removeItem');
});
Route::post('shop/item/list','ShopController@itemsList')->middleware('token');

Route::group(['prefix'=>'trans','middleware'=>'token'],function (){

    Route::post('create','TransactionController@create');

    Route::post('test/create','TransactionController@test_create');
});

Route::post('issue','IssueController@create')->middleware('token');

Route::post('sync/user','AuthController@SyncUser');