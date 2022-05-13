<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Request;
use Closure;

use App\Lib\Cache\RedisInterface;

use Illuminate\Support\Facades\Crypt;


class Authorization
{

    private $redis;

    public function __construct(RedisInterface $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request_authorization = explode(':', $request->header('Authorization'));
        $request_token = trim($request_authorization[1]);

        $login_id = $this->redis->getValue($request_token);

        if (!$login_id) {
            abort(403, 'ログインしてください。');
        }

        return $next($request);
    }
}
