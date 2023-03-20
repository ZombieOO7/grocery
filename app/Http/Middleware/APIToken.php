<?php

namespace App\Http\Middleware;

use Closure;

class APIToken
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
        dd($request->all());
        $request->headers->set('Accept', 'application/json');
        // if($request->header('Authorization') != ""){
        return $next($request);
        // }
        // return response()->json([
        //     'data' => (object) [],
        //     'status' => false,
        //     'status_code' => 403,
        //     'message' => 'Please Login again',
        // ]);  
    }
}
