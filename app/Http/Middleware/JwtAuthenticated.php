<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\JwtAuth;

class JwtAuthenticated
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
        $jwt = new JwtAuth();
        $checkToken = $jwt->checkToken($hash);
        if(!$checkToken){
          dd("INDEX SPPOAMDRE AUTENTICADO");
        }
        
        return $next($request);
    }
}
