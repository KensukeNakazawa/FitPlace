<?php

namespace Domain\ApplicationServices\Notify;

use App\Models\User;
use App\Models\LineNotify;

use App\Services\User\UserService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use Domain\Repositories\Notifies\LineNotifyRepositoryInterface;

/**
 * Class LineNotifyService
 * @package Domain\ApplicationServices\Notify
 * @see https://notify-bot.line.me/doc/ja/
 *
 * OAuth2準拠
 * 1 ユーザーをline notifyのページにリダイレクトさせる
 * 2 必要な情報入力後、指定したcallbackに戻ってくる、token付き
 * 3 tokenを持って指定のエンドポイント(/oauth/token)にリクエスト、access_tokenを取得する
 */
class LineNotifyService
{

    protected LineNotify $lineNotifyModel;
    private UserService $userService;
    private LineNotifyRepositoryInterface $lineNotifyRepository;

    /**
     * LineNotifyService constructor.
     * @param LineNotify $lineNotifyModel
     * @param UserService $userService
     * @param LineNotifyRepositoryInterface $lineNotifyRepository
     */
    public function __construct(
        LineNotify $lineNotifyModel,
        UserService $userService,
        LineNotifyRepositoryInterface $lineNotifyRepository
    ) {
        $this->lineNotifyModel = $lineNotifyModel;
        $this->userService = $userService;
        $this->lineNotifyRepository = $lineNotifyRepository;
    }

    /**
     * LineNotifyからアクセストークンを取得し、ユーザーに設定する
     * @param string $auth_id
     * @param string $code
     */
    final public function setLineNotifyAccessToken(string $auth_id, string $code)
    {
        $access_token = $this->requestTokenEndpoint($code);
        $user = $this->userService->getUserByAuthId($auth_id);
        $this->setAccessToken($user, $access_token);
    }

    /**
     * LineNotifyの連携を解除する
     * @param User $user
     */
    final public function disconnectLineNotify(User $user): void
    {
        /**
         *  Method	POST
         *  Content-Type	application/x-www-form-urlencoded
         *  Authorization	Bearer <access_token>
         */
        $base_url = 'https://notify-api.line.me/api/revoke';
        $response = Http::withToken($user->getAccessToken())->withHeaders(
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->post($base_url);

        Log::error($response);
        $this->lineNotifyModel->newQuery()->updateOrInsert(
            ['user_id' => $user->id],
            ['access_token' => null]
        );
    }

    /**
     * @param string $code
     * @return string access_token
     */
    final public function requestTokenEndpoint(string $code)
    {
        $request_parameters = '?grant_type=authorization_code';
        $request_parameters .= '&code=' . $code;
        $request_parameters .= '&redirect_uri=' . config("services.line_notify.redirect");
        $request_parameters .= '&client_id=' . config("services.line_notify.client_id");
        $request_parameters .= '&client_secret=' . config("services.line_notify.client_secret");

        $request_url = 'https://notify-bot.line.me/oauth/token' . $request_parameters;

        $response = Http::withHeaders(
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->post($request_url);

        $response_body = $response->json();
        return $response_body['access_token'];
    }


    /**
     * 対象のユーザのLineNotifyのアクセストークンを設定する
     * @param User $user
     * @param string $access_token
     * @test
     */
    final public function setAccessToken(User $user, string $access_token): void
    {
        $this->lineNotifyModel->newQuery()->updateOrInsert(
            ['user_id' => $user->id],
            ['access_token' => $access_token]
        );
    }

    /**
     * @param string $access_token
     * @param string $send_content
     */
    final public function sendLineNotify(string $access_token, string $send_content): void
    {
        $request_url = 'https://notify-api.line.me/api/notify';
        $request_url .= '?message=' . $send_content;

        $response = Http::withToken($access_token)->withHeaders(
            ['Content-Type' => 'application/x-www-form-urlencoded']
        )->post($request_url);

        if ($response->failed()) {
            $response_body = $response->json();
            Log::error("Line Notify Send Error: " . $response_body['message']);
        }
    }

    /**
     * LineNotifyを取得する、無ければnull
     * @param User $user
     * @return mixed
     */
    public function getLineNotify(User $user)
    {
        return $this->lineNotifyRepository->getByUserId($user->id);
    }

    /**
     * LineNotifyのNoticeを確認する
     * @param User $user
     */
    public function checkLineNotify(User $user)
    {
        $this->lineNotifyRepository->checkNoteLineNotify($user->id);
    }
}