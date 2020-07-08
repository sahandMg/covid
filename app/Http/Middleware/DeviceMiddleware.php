<?php

namespace App\Http\Middleware;

use App\Device;
use Closure;

class DeviceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        if(!$request->has('key') || $request->key != 'raiwan2020'){

            return response(['status'=>404,'body'=>'Fake Device!']);
        }

        if(!$request->has('unique_id')){

            return response(['status'=>404,'body'=>'Send the device unique_id']);
        }
        if(!$request->has('power')){

            return response(['status'=>404,'body'=>'Send the device power']);
        }
        if(!$request->has('capacity')){

            return response(['status'=>404,'body'=>'Send the device capacity']);
        }
        if(!$request->has('push')){

            return response(['status'=>404,'body'=>'Send the device pushed number']);
        }

//        $id = $request->unique_id;
//        $device = Device::where('unique_id',$id)->first();
//
//        if(is_null($device)){
//
//            return response('device not found');
//        }

        return $next($request);
    }
}
