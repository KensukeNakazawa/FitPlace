<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\AuthCodeRequest;

use App\Services\AuthService;


class RegisterController extends BaseController
{
    private AuthService $authService;

    /**
     * RegisterController constructor.
     */
    public function __construct(
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    /**
     * ユーザー登録
     * @param RegisterRequest $request
     * @return string[]
     * @throws \Exception
     */
    final public function register(RegisterRequest $request)
    {
        $email = $request->email;
        $password = $request->password;
        $this->authService->registerUser($email, $password);

        return ['message' => 'OK'];
    }

    /**
     * メールで送信した認証コードを確認する
     * @param AuthCodeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    final public function authorizeCode(AuthCodeRequest $request)
    {
        $auth_id = $request->auth_id;
        $auth_code = $request->auth_code;

        $token = $this->authService->authorizeCode($auth_id, $auth_code);

        return $this->respondWithToken($token);
    }
}
