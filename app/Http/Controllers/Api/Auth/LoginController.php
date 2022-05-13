<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Requests\Api\Auth\LoginRequest;

use App\Services\AuthService;


class LoginController extends BaseController
{
    private $authService;
    /**
     * RegisterController constructor.
     */
    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    /**
     * ログイン試行してから、トークン返す
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    final public function login(LoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;

        $token = $this->authService->authorizeEmail($email, $password);

        return $this->respondWithToken($token);
    }

    /**
     * ログインしているかのチェックに使うAPI
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    final public function noLogin(Request $request)
    {
        $request_authorization = explode(':', $request->header('Authorization'));

        if (!empty($request_authorization[0]) && !empty($request_authorization[1])) {
            return response()->json(
                [
                    'code' => '20',
                    'message' => 'login'
                ]
            );

        }
        return response()->json(
            [
                'code' => '10',
                'message' => 'no login'
            ]
        );

    }
}
