<?php

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
Route::get('zarin/callback','TransactionController@ZarrinCallback');
Route::get('zarin/failed/{id}','TransactionController@failedPage')->name('PaymentCanceled');
Route::get('zarin/success/{id}','TransactionController@successPage')->name('PaymentSuccess');