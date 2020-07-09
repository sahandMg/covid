<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use App\Repo;
use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

//        Check Token privileges
        $repo = new Repo();

        if (Auth::guard('user')->user()->role_id != $repo->findRoleId('admin')) {
            return response(['status'=>404,'body'=>['type'=>'error','message'=>['دسترسی به این قسمت محدود شده است']]]);
        }

        return $next($request);
    }
}
