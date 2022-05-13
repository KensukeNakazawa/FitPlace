<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Request;

use App\Lib\Cache\RedisInterface;

class Authenticate
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
        $auth_id = $request->headers['X-RequestAuthId'];
        $request_token = $request->bearerToken();

        $key = config('enum.CACHE_KEY_PREFIX.AUTHENTICATED_ID')  . $auth_id;
        $auth_token = $this->redis->getValue($key);

        if ($request_token !== $auth_token) {
            abort(403, 'ログインしてください。');
        }

        return $next($request);

    }
}
