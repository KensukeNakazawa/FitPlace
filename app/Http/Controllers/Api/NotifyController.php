<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\AuthService;
use Domain\ApplicationServices\Notify\NotifyService;
use Domain\ApplicationServices\Notify\LineNotifyService;


class NotifyController extends Controller
{

    private AuthService $authService;
    private NotifyService $notifyService;
    private LineNotifyService $lineNotifyService;

    /**
     * NotifyController constructor.
     * @param AuthService $authService
     * @param NotifyService $notifyService
     * @param LineNotifyService $lineNotifyService
     */
    public function __construct(
        AuthService $authService,
        NotifyService $notifyService,
        LineNotifyService $lineNotifyService
    ) {
        $this->authService = $authService;
        $this->notifyService = $notifyService;
        $this->lineNotifyService = $lineNotifyService;
    }

    /**
     * 全てのnotify_timeと自分が現在設定しているnotify_settingを取得する
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMeNotifyTime(Request $request)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $result = $this->notifyService->getMeNotifySetting($user);

        return response()->json(
            [
                'notify_settings' => $result['notify_settings'],
                'notify_times' => $result['notify_times']
            ]
        );
    }

    /**
     * 通知希望時間を設定する
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotifyTime(Request $request)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $notify_settings_ids = $request->notify_setting_ids;

        $this->notifyService->updateOrCreateNotifySetting($user, $notify_settings_ids);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }

    /**
     * ログインユーザーのline notifyアクセストークンを取得、LineNotifyの連携が行われているか
     * どうかを確認するために用いる
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLineNotify(Request $request)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);

        $line_notify = $this->lineNotifyService->getLineNotify($user);

        return response()->json(
            [
                'message' => 'OK',
                'line_notify' => $line_notify
            ]
        );
    }

    public function checkLineNotify(Request $request)
    {
        $auth = $this->getAuth($request);
        $user = $this->authService->getAuthUser($auth);
        $this->lineNotifyService->checkLineNotify($user);

        return response()->json(
            [
                'message' => 'OK'
            ]
        );
    }
}