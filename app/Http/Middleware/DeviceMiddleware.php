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


        if(!$request->has('key') || $request->key != env('DEVICE_CODE')){

            return response(['status'=>404,'body'=>'Fake Device!']);
        }


        if(!$request->has('unique_id') || !$request->has('power') || !$request->has('capacity') || !$request->has('push')){

            return response(['status'=>404,'body'=>'Wrong Packet !']);
        }

        return $next($request);
    }
}
