<?php

namespace App\Http\Controllers\ExternalApi;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\AuthService;
use App\Services\User\UserService;

use Domain\ApplicationServices\Notify\LineNotifyService;
use Illuminate\Support\Facades\Log;


/**
 * Class LineNotifyAuth
 * @package App\Http\Controllers\ExternalApi
 * @see https://notify-bot.line.me/doc/ja/
 *
 * OAuth2準拠
 * 1 ユーザーをline notifyのページにリダイレクトさせる
 * 2 必要な情報入力後、指定したcallbackに戻ってくる、token付き
 * 3 tokenを持って指定のエンドポイント(/oauth/token)にリクエスト、access_tokenを取得する
 */
class LineNotifyAuthController extends Controller
{
    private AuthService $authService;
    private UserService $userService;
    private LineNotifyService $lineNotifyService;

    /**
     * LineNotifyAuthController constructor.
     * @param AuthService $authService
     * @param UserService $userService
     * @param LineNotifyService $lineNotifyService
     */
    public function __construct(
        AuthService $authService,
        UserService $userService,
        LineNotifyService $lineNotifyService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->lineNotifyService = $lineNotifyService;
    }


    /**
     * LineNotifyと連携するためのページにリダイレクトする
     * OAuth2 のauthorization endpoint URIにリダイレクト
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToLineNotify(Request $request)
    {
        $auth = $this->getAuthFromToken($request->token);
        $redirect_url = 'https://notify-bot.line.me/oauth/authorize';
        $redirect_url .= '?response_type=code';
        $redirect_url .= '&client_id=' . config("services.line_notify.client_id");
        $redirect_url .= '&redirect_uri=' . config("services.line_notify.redirect");
        $redirect_url .= '&scope=notify';
        $redirect_url .= '&state=' . $auth->id;

        return redirect()->away($redirect_url);
    }

    /**
     * LineNotifyで連携した後のコールバックを受けての処理を行う
     * Oauth2のtoken endpointの処理を行い、アクセストークンを設定する
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleCallback(Request $request)
    {
        $code = $request->code;
        $auth_id = $request->state;

        $error = $request->error ? $request->error : null;

        $flash_message = __('messages.external.line_notify.connected');
        if (empty($error)) {
            $this->lineNotifyService->setLineNotifyAccessToken($auth_id, $code);
        } else {
            switch ($error) {
                case 'access_denied':
                    $flash_message = __('messages.external.line_notify.canceled');
                    Log::info($flash_message);
                    break;
                default:
                    $flash_message = __('messages.external.line_notify.error');
                    Log::warning('LINE Notify Warn: ' . $error . $request->error_description);
                    break;
            }

        }

        return redirect('/my_page/notify_setting?line_notify_message=' . $flash_message);
    }

    /**
     * LineNotifyとの連携を解除する
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disconnectLineNotify(Request $request)
    {
        $auth = $this->getAuthFromToken($request->token);
        $user = $this->authService->getAuthUser($auth);

        $this->lineNotifyService->disconnectLineNotify($user);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }
}
