<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;

class CheckUserOwnRequest
{

    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if($request->user()->id != $request->route('user')->id){
          return   $this->errorResponse("you have't access to perform this operation",404);
        }
        return $next($request);
    }
}
