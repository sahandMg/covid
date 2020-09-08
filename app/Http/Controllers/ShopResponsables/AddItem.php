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
//            dd($request->file('page_img')->getFilename());
            $shop = new ShopItem();
            $shop->p_name = $request->p_name;
            $shop->desc = $request->desc;
            $shop->price = $request->price;
            $shop->title = $request->p_title;
            $shop->available = $request->available;
            $name = time().'.'.$request->file('img')->getClientOriginalExtension();
            $name2 = time().$request->file('page_img')->getFilename().'.'.$request->file('page_img')->getClientOriginalExtension();
            $request->file('img')->move(public_path('images'),$name);
            $request->file('page_img')->move(public_path('images'),$name2);
            $shop->img = $name;
            $shop->page_img = $name2;
            $shop->save();
        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
            return $resp;
        }

        return redirect()->back()->with(['message'=>'محصول ثبت شد']);
    }
}

?>