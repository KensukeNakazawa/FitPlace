<?php
namespace Domain\Lib;

interface ArrayInterface
{
    /**
     * 多次元配列を1次元にする
     * @param array $target_array
     * @return array
     */
    public function flatten(array $target_array);
}