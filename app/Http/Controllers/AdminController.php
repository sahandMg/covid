<?php

namespace App\Http\Controllers;

use App\Device;
use App\ShopItem;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function add(){

        return view('admin.addItem');
    }

    public function update($name){

        $product = ShopItem::where('p_name',$name)->first();
        if(!$name && !is_null($product)){

            return 'نام محصول را ارسال کنید';
        }else{

            return view('admin.updateItem',compact('product'));
        }
    }

    public function itemslist(){

        $products = ShopItem::get();

        return view('admin.itemsList',compact('products'));
    }

    public function management(){

        $devices = Device::get();

        return view('admin.deviceManagement',compact('devices'));

    }
}
