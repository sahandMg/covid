<?php

namespace App\Http\Middleware;

use App\Device;
use App\Repo;
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

        $repo = new Repo();
        $resp = $repo->parseDataToArray($request->all());

        if(!isset($resp->key) || $resp->key != env('DEVICE_CODE')){

            return response(['status'=>404,'body'=>'Fake Device!']);
        }


        if(!isset($resp->unique_id) || !isset($resp->power) || !isset($resp->capacity) || !isset($resp->push)){

            return response(['status'=>404,'body'=>'Wrong Packet !']);
        }

        return $next($request);
    }
}
