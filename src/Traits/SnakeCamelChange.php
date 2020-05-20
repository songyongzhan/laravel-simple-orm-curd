<?php

namespace Songyz\Simple\Orm\Traits;

/**
 * 驼峰 下划线 相互转换
 * Trait SnakeCamelChange
 * @package App\Traits
 */
trait SnakeCamelChange
{
    //下划线转驼峰转
    public function camel(array $data)
    {
        if (!$data) {
            return [];
        }
        $newAttributes = [];
        foreach ($data as $k => $v) {
            $newAttributes[\Illuminate\Support\Str::camel($k)] = is_null($v) ? '' : $v;
        }

        return $newAttributes;
    }

    //驼峰转下划线或其他字符
    public function snake(array $data, $delimiter = '_')
    {
        if (!$data) {
            return [];
        }
        
        $newAttributes = [];
        foreach ($data as $k => $v) {
            $newAttributes[\Illuminate\Support\Str::snake($k, $delimiter)] = $v;
        }

        return $newAttributes;
    }

    //针对一维数组进行驼峰到下划线的转换
    public function valueSnake(array $data, $delimiter = '_')
    {
        $newAttributes = [];
        foreach ($data as $k => $v) {
            $newAttributes[$k] = \Illuminate\Support\Str::snake($v, $delimiter);
        }

        return $newAttributes;
    }

}
