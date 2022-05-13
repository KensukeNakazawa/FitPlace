<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;

//use App\Lib\Cache\RedisInterface;
//use App\Services\AuthService;

class BaseController extends Controller
{
//    protected
//    protected AuthService $authService;
    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json(
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        );
    }
}
