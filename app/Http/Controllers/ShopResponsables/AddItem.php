<?php



namespace App\Http\Controllers\ShopResponsables;

use App\ShopItem;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Request;

class AddItem implements Responsable {

    public function __construct()
    {
        
    }
    
    public function toResponse($request){


        if($request->password != env('ADMIN_PASS')){

            return redirect()->back()->with(['error'=>'کد عبور نادرست است']);
        }
        try{
            $shop = new ShopItem();
            $shop->p_name = $request->p_name;
            $shop->desc = $request->desc;
            $shop->price = $request->price;
            $shop->title = $request->p_title;
            $shop->available = $request->available;
            $name = time().'.'.$request->file('img')->getClientOriginalExtension();
            $request->file('img')->move(public_path('images'),$name);
            $shop->img = $name;
            $shop->save();
        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
            return $resp;
        }

        return redirect()->back()->with(['message'=>'محصول ثبت شد']);
    }
}

?>