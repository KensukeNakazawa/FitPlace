<?php
namespace App\Lib;

use Illuminate\Support\Arr;

use Domain\Lib\ArrayInterface;

/**
 * Class ArrayImplement
 * @package App\Lib\Arr
 */
final class ArrayImplement implements ArrayInterface
{
    public function flatten(array $target_array)
    {
        return Arr::flatten($target_array);
    }

}