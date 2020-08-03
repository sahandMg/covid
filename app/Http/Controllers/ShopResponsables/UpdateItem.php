<?php

/**
 * Created by PhpStorm.
 * User: Sahand
 * Date: 8/3/20
 * Time: 7:21 PM
 */

namespace App\Http\Controllers\ShopResponsables;

use App\ShopItem;
use Illuminate\Contracts\Support\Responsable;

class UpdateItem implements Responsable {

    public function __construct()
    {
        
    }
    
    public function toResponse($request){

        try{

            $item = ShopItem::where('p_name',$request->p_name_old)->firstOrFail();
        }
        catch (\Exception $exception){

            return redirect()->back()->with(['error'=>'محصول یافت نشد']);
        }

        try{

            $item->update($request->except('img','p_title','p_name_old','password'));

            if($request->has('title')){

                $item->update(['title'=>$request->p_title]);
            }
// Image file size and format is validated by RequestValidationService
            if($request->has('img')){

                if(file_exists(public_path('images/'.$item->img))){

                    unlink(public_path('images/'.$item->img));
                }
                $name = time().'.'.$request->file('img')->getClientOriginalExtension();

                $request->file('img')->move(public_path('images'),$name);

                $item->update(['img'=>$name]);


            }
        }
        catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
            return $resp;
        }

        return redirect()->route('productList')->with(['message'=>'اطلاعات محصول به روز رسانی شد']);
    }
}

?>