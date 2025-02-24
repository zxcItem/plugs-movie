<?php

declare (strict_types=1);

namespace plugin\movie\model;

use think\model\relation\HasOne;

/**
 * 影视资源数据
 * Class PluginMovieItem
 */
class PluginMovieItem extends Abs
{

    /**
     * 关联视频分类
     * @return HasOne
     */
    public function type(): HasOne
    {
        return $this->hasOne(PluginMovieType::class, 'id', 'type_id');
    }
}