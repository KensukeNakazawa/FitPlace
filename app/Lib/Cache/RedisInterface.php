<?php

namespace App\Lib\Cache;

interface RedisInterface
{
    /**
     * @param string $key キャッシュのキー
     * @param string $value キャッシュの値
     * @param int|null $ttl キャッシュする時TTL(Time To Live)
     * @return mixed
     */
    public function setKeyValue(string $key, string $value, int $ttl=null);

    /**
     * @param string $key キャッシュのキー
     * @return string|null キャッシュのキーに対応する値、無ければnull
     */
    public function getValue(string $key);

    /**
     * @param string $key
     * @return mixed
     */
    public function deleteValue(string $key);

    /**
     * @param string $key
     * @param int $ttl
     * @return mixed
     */
    public function countUp(string $key,  int $ttl);
}