<?php

namespace App\Lib\Cache;

use Illuminate\Support\Facades\Redis;

class RedisImplement implements RedisInterface
{
    public function setKeyValue(string $key, string $value, int $ttl = null)
    {
        Redis::set($key, $value);

        if ($ttl) {
            Redis::expire($key, $ttl);
        }
    }

    public function getValue(string $key)
    {
       return Redis::get($key);
    }

    public function deleteValue(string $key)
    {
        return Redis::del($key);
    }

    public function countUp(string $key, int $ttl)
    {
        $value = Redis::get($key);

        if (empty($value)) {
            Redis::set($key, 1);
            $value = 1;
        } else {
            $value += 1;
            Redis::set($key, $value);
        }
        Redis::expire($key, $ttl);

        return $value;
    }

}