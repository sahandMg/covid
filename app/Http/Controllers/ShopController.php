<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Repo;
use App\ShopItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    //    ============ Adding new Item to shop ============

    /*
     * Data Needed : name,price,img,desc,token
     * Data returns : data
     *
     */
    public function addItem(Request $request){


        $this->validate($request,[
            'p_name'=>'required|unique:shop_items',
            'img'=>'required|mimes:jpeg,bmp,png,jpg|max:1000',
            'price'=>'required',
            'desc'=>'required',
            'p_title'=>'required'
        ]);

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

    //    ============ Updating an Item information ============

    /*
     * Data Needed : p_name_old,p_name,price,img,desc,token
     * Data returns : data
     *
     */
    public function updateItem(Request $request){

        $this->validate($request,[
            'p_name'=>'required',
        ]);

        if($request->password != env('ADMIN_PASS')){

            return redirect()->back()->with(['error'=>'کد عبور نادرست است']);
        }
        $item = ShopItem::where('p_name',$request->p_name_old)->first();

        if(is_null($item)){

//            $resp = ['status'=>500,'body'=>['type'=>'error','message'=> ['err'=>'محصول یافت نشد']]];
//            return $resp;
            return redirect()->back()->with(['error'=>'محصول یافت نشد']);
        }
        try{


            if($request->has('p_name')){

                $item->update(['p_name'=>$request->p_name]);
            }
            if($request->has('title')){

                $item->update(['title'=>$request->p_title]);
            }
            if($request->has('price')){

                $item->update(['price'=>$request->price]);
            }
            if($request->has('desc')){

                $item->update(['desc'=>$request->desc]);
            }
            if($request->has('available')){

                $item->update(['available'=>$request->available]);
            }
            if($request->has('img')){

                $extension = $request->file('img')->getClientOriginalExtension();

                $extensions = ['jpeg','bmp','png','jpg'];

                if($request->file('img')->getSize()/1000 > 1000){

                    $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'حجم عکس حداکثر باید ۱۰۰۰ کیلوبایت باشد']]];

                    return $resp;
                }
                if(!in_array($extension,$extensions)){

                    $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>'باشد jpg,jpeg,bmp,png  عکس باید به یکی از فرمت های  ']]];

                    return $resp;
                }
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

    //    ============ Removing an Item  ============

    /*
     * Data Needed : p_name,token
     * Data returns : data
     *
     */

    public function removeItem(Request $request,$name){


        try{

            $item = ShopItem::where('p_name',$name)->first();

            if(is_null($item)){

                return redirect()->back()->with(['error'=>'محصول یافت نشد']);

            }else{

                if(file_exists(public_path('images/'.$item->img))) {

                    unlink(public_path('images/' . $item->img));
                }

                $item->delete();

                return redirect()->back()->with(['message'=>'محصول حذف شد']);
            }

        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];

            return $resp;
        }
    }


    //    ============ Return Shop Items list to user & admin  ============

    /*
     * Data Needed : p_name,token
     * Data returns : data
     *
     */

    public function itemsList(Request $request,Repo $repo){

        $validator = Validator::make($request->all(),[

            'token'=>'required',
        ]);
        if($validator->fails()){

            $errResp =  $repo->responseFormatter($validator->errors()->getMessages());
            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>['err'=>$errResp[0]]]];
            return $resp;
        }

        try{

            $items = ShopItem::select('p_name','price','desc','img','title','available')->get();
            if (count($items) == 0){

                return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'محصولی برای نمایش وجود ندارد']]];
            }else{

                return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$items]];
            }


        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];

            return $resp;
        }
    }

    //    ============ Getting User Address for cart  ============

    /*
     * Data Needed : token
     * Data returns : data
     *
     */
    public function userAddress(Request $request,Repo $repo){

        $user = Auth::guard('user')->user();
        if(!is_null($user->address)){

            return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>[$user->address]]];

        }else{

            return $resp = ['status'=>404,'body'=>['type'=>'data','message'=>['آدرس یافت نشد']]];
        }
    }


    //    ============ Return Previous Shopping list  ============

    /*
     * Data Needed : token
     * Data returns : data
     *
     */
    public function shoppingList(Request $request){

        try{

            $carts = Cart::where('user_id',Auth::guard('user')->id())->where('completed',1)->get();

            if(count($carts) != 0){

                foreach ($carts as $cart){

                    $cart->cart = unserialize($cart->cart);
                }

                return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$carts]];
            }else{

                return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['لیست خرید وجود ندارد']]];
            }

        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];

            return $resp;
        }
    }
}
