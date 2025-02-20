<?php

declare (strict_types=1);

namespace plugin\movie\model;

use think\admin\Model;
use think\model\relation\HasOne;

/**
 * 影视资源剧集数据
 * Class PluginMovieEpisode
 */
class PluginMovieEpisode extends Model
{

    /**
     * 获取关联视频的标题
     * @return HasOne
     */
    public function video(): HasOne
    {
        return  $this->hasOne(PluginMovieItem::class, 'id', 'video_id')->bind(['video_name'=>'title']);
    }
}