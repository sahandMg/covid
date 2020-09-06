<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Http\Controllers\ShopResponsables\AddItem;
use App\Http\Controllers\ShopResponsables\UpdateItem;
use App\Repo;
use App\Services\Shop\RequestValidationService;
use App\ShopItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    //    ============ Adding new Item to shop ============

    /*
     * Data Needed : name,price,img,desc,token
     * Data returns : data
     *
     */
    public function addItem(Request $request,RequestValidationService $rq){


        $this->validate($request,[
            'p_name'=>'required|unique:shop_items',
            'img'=>'required|mimes:jpeg,bmp,png,jpg|max:1000',
            'price'=>'required',
            'desc'=>'required',
            'p_title'=>'required'
        ]);

      return new AddItem();
    }

    //    ============ Updating an Item information ============

    /*
     * Data Needed : p_name_old,p_name,price,img,desc,token
     * Data returns : data
     *
     */
    public function updateItem(Request $request, RequestValidationService $rq){

        $this->validate($request,[
            'p_name'=>'required',
        ]);

        $val = $rq->image($request);

        if(!is_null($val)){

            return $val;
        }

        if($request->password != env('ADMIN_PASS')){

            return redirect()->back()->with(['error'=>'کد عبور نادرست است']);
        }else{

            return new UpdateItem();
        }

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
            // Sorting shopping list based on notification title
            if($request->has('s_title')){
                Str::contains($request->s_title,'باتری')?$searchedItem = 'باتری':(Str::contains($request->s_title,'الکل')?
                    $searchedItem = 'الکل':$searchedItem = 0);
                if($searchedItem == 0){

                    $temp = [];
                    foreach ($items as $item){

                        if(Str::contains($item->p_name,$searchedItem)){
                            array_unshift($temp,$item);
                        }else{
                            array_push($temp,$item);
                        }
                    }
                    $items = $temp;
                }

            }


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
