<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\Auth\PasswordResetMailRequest;
use App\Http\Requests\Api\Auth\PasswordResetRequest;

use Domain\ApplicationServices\Auth\PasswordResetService;

class PasswordResetController extends Controller
{
    private PasswordResetService $passwordResetService;

    /**
     * PasswordResetController constructor.
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * パスワード再設定用のURLをメールで送信する
     * @param PasswordResetMailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMailForPasswordReset(PasswordResetMailRequest $request)
    {
        $email = $request->email;

        $this->passwordResetService->sendMail($email);

        return response()->json(['message' => 'OK']);
    }

    /**
     * パスワードを再設定する
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(PasswordResetRequest $request)
    {
        $auth_code = $request->auth_code;
        $new_password = $request->new_password;

        $this->passwordResetService->resetPassword($auth_code, $new_password);

        return response()->json(['message' => 'OK']);
    }
}
