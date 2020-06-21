<?php

use Illuminate\Support\Facades\DB;
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

//    $cartData = DB::table('carts')->join('carts', 'carts.trans_id', '=', 'transactions.id')->get();
    return view('email.issueMail');
//    return view('email.invoiceMail');
});