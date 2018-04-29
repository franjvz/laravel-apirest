<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\JwtAuth;

class CheckApiToken
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
        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if ($checkToken){
            return $next($request);
        }else{
            return response()->json('Unauthorized', 401);
        }
    }
}
