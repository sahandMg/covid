<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenMiddleware
{
    /**
     * Checks Token presentce on request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try{
            JWTAuth::parseToken();
        }catch (\Exception $exception){

            return response(['status'=>404,'body'=>['type'=>'error','message'=>[$exception->getMessage()]]]);
        }


//        Check Token Validity
        try{

            JWTAuth::parseToken()->checkOrFail();

        }
        catch (TokenBlacklistedException $exception){

            return response(['status'=>404,'body'=>['type'=>'error','message'=>['توکن منقضی شده است. لطفا مجددا وارد شوید']]]);
        }
        catch (TokenExpiredException $exception){

            return response(['status'=>404,'body'=>['type'=>'error','message'=>['توکن منقضی شده است. لطفا مجددا وارد شوید']]]);
        }

        return $next($request);
    }
}
