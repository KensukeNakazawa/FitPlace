<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Lib\Cache\RedisInterface;

class LogoutController extends Controller
{
    private RedisInterface $redis;

    /**
     * LogoutController constructor.
     * @param RedisInterface $redis
     */
    public function __construct(RedisInterface $redis)
    {
        $this->redis = $redis;
    }

    /**
     * ログアウト処理
     * キャッシュからキーを消すだけ
     * @param Request $request
     */
    final public function logout(Request $request)
    {
        $auth_id = $request->header('X-RequestAuthId');
        $key = config('enum.CACHE_KEY_PREFIX.AUTHENTICATED_ID') . $auth_id;

        $this->redis->deleteValue($key);

        response()->json(['message' => 'OK']);
    }

}
