<?php

declare (strict_types=1);

namespace plugin\movie\model;


/**
 * 影视资源采集
 * Class PluginMovieResource
 */
class PluginMovieResource extends Abs
{

    /**
     * 获取资源采集设置信息
     * @param $key
     * @param string $field
     * @return array
     */
    public static function items($key, string $field = '*'): array
    {
        return self::mk()->column($field,$key);
    }
}