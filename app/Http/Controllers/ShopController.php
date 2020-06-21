<?php

namespace App\Http\Controllers;

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

        $validator = Validator::make($request->all(),[

            'p_name'=>'required|unique:shop_items',
            'img'=>'required|mimes:jpeg,bmp,png,jpg|max:1000',
            'price'=>'required',
            'desc'=>'required'
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=> $validator->errors()]];
            return $resp;
        }
        try{
            $shop = new ShopItem();
            $shop->p_name = $request->p_name;
            $shop->desc = $request->desc;
            $shop->price = $request->price;
            $name = time().'.'.$request->file('img')->getClientOriginalExtension();
            $request->file('img')->move(public_path('images'),$name);
            $shop->img = $name;
            $shop->admin_id = Auth::guard('admin')->id();
            $shop->save();
        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
            return $resp;
        }
        $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc'=>'محصول ثبت شد']]];
        return $resp;

    }

    //    ============ Updating an Item information ============

    /*
     * Data Needed : p_name_old,p_name,price,img,desc,token
     * Data returns : data
     *
     */
    public function updateItem(Request $request){

        $validator = Validator::make($request->all(),[

            'p_name'=>'required',
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=> $validator->errors()]];
            return $resp;
        }

        $item = ShopItem::where('p_name',$request->p_name_old)->first();

        if(is_null($item)){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=> ['err'=>'محصول یافت نشد']]];
            return $resp;
        }
        try{

            if($request->has('p_name')){

                $item->update(['p_name'=>$request->p_name]);
            }

            if($request->has('price')){

                $item->update(['price'=>$request->price]);
            }
            if($request->has('desc')){

                $item->update(['desc'=>$request->desc]);
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
                unlink(public_path('images/'.$item->img));
                $name = time().'.'.$request->file('img')->getClientOriginalExtension();

                $request->file('img')->move(public_path('images'),$name);

                $item->update(['img'=>$name]);


            }
        }
        catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];
            return $resp;
        }

        $resp = ['status'=>200,'body'=>['type'=>'success','message'=>['scc'=>'اطلاعات محصول به روز رسانی شد']]];
        return $resp;
    }

    //    ============ Removing an Item  ============

    /*
     * Data Needed : p_name,token
     * Data returns : data
     *
     */

    public function removeItem(Request $request){

        $validator = Validator::make($request->all(),[

            'p_name'=>'required',
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=> $validator->errors()]];
            return $resp;
        }
        try{

            $item = ShopItem::where('p_name',$request->p_name)->first();

            if(is_null($item)){

                $resp = ['status'=>500,'body'=>['type'=>'error','message'=> ['err'=>'محصول یافت نشد']]];
                return $resp;

            }else{
                unlink(public_path('images/'.$item->img));
                $item->delete();
                $resp = ['status'=>200,'body'=>['type'=>'success','message'=> ['scc'=>'محصول حذف شد']]];
                return $resp;
            }

        }catch (\Exception $exception){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=>$exception->getMessage()]];

            return $resp;
        }
    }

    //    ============ Removing an Item  ============

    /*
     * Data Needed : p_name,token
     * Data returns : data
     *
     */

    public function itemsList(Request $request){

        $validator = Validator::make($request->all(),[

            'token'=>'required',
        ]);
        if($validator->fails()){

            $resp = ['status'=>500,'body'=>['type'=>'error','message'=> $validator->errors()]];
            return $resp;
        }

        try{

            if(Auth::guard('admin')->check()){

                $items = ShopItem::where('admin_id',Auth::guard('admin')->id())->select('p_name','price','desc','img')->get();
                if (count($items) == 0){

                    return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'محصولی برای نمایش وجود ندارد']]];
                }else{

                    return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$items]];
                }
            }elseif (Auth::guard('user')->check()){

                $shared_user = DB::table('shared_keys')->where('user_id',Auth::guard('user')->id())->first();
                if(is_null($shared_user)){
// TODO ???????????
                    return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'دسترسی به محصولات محدود شده است ']]];

                }else{
                    $admin_id = $shared_user->admin_id;
                    $items = ShopItem::where('admin_id',$admin_id)->select('p_name','price','desc','img')->get();
                    if (count($items) == 0){

                        return $resp = ['status'=>404,'body'=>['type'=>'error','message'=>['err'=>'محصولی برای نمایش وجود ندارد']]];
                    }else{

                        return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>$items]];
                    }

                }
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

        $user = Auth::guard($repo->getGuard())->user();
        if(!is_null($user->address)){

            return $resp = ['status'=>200,'body'=>['type'=>'data','message'=>[$user->address]]];

        }else{

            return $resp = ['status'=>404,'body'=>['type'=>'data','message'=>['آدرس یافت نشد']]];
        }
    }
}
