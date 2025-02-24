<?php

declare (strict_types=1);

namespace plugin\movie\model;

use think\model\relation\HasOne;

/**
 * 影视资源分类关联
 * Class PluginMovieTypeItem
 */
class PluginMovieTypeItem extends Abs
{

    /**
     * 关联分类
     * @return HasOne
     */
    public function type(): HasOne
    {
        return $this->hasOne(PluginMovieType::class, 'id', 'type_id')->bind(['type_name'=>'name']);
    }

    public static function typeList($where)
    {
        return self::mk()->with(['type'])->where($where)->select()->toarray();
    }
}