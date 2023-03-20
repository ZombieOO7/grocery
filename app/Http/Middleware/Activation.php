<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Model\UsersDeviceTokens;
use App\oAuthAccessTokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Closure;

class Activation
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
        // $userToken = @$request->user()->token();
        // $userId = $userToken != null ? $userToken->user_id : (int) "";

        $offlineObj = User::find($request->user()->token()->user_id);
        if (@$offlineObj->status == 1 ) {
            return $next($request);
        } else {
            return response()->json([
                'data' => (object) [],
                'status' => false,
                'status_code' => 403,
                'message' => 'Your account is inactivated or closed by admin',
            ], 200);
        }
    }
}
