<?php

namespace plugin\movie\service;

use plugin\movie\model\PluginMovieResource;
use plugin\movie\model\PluginMovieType;
use plugin\movie\model\PluginMovieTypeItem;
use plugin\movie\model\PluginMovieItem;
use plugin\movie\model\PluginMovieEpisode;
use think\admin\Service;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 资源采集
 */
class ResourceService extends Service
{

    /**
     * 批量绑定采集分类
     * @param int $resource_id
     * @param array $resourceType
     * @return void
     */
    public static function bindType(int $resource_id,array $resourceType)
    {
        if(PluginMovieTypeItem::mk()->where('resource_id',$resource_id)->findOrEmpty()){
            $data = array_column($resourceType,'type_id','type_name');
            $types = PluginMovieType::mk()->column('id','name');
            foreach($data as $key => $value) {
                if (!empty($types[$key])){
                    PluginMovieTypeItem::mk()->save([
                        'resource_id'=>$resource_id,'resource_type_id'=>$value,'type_id'=>$types[$key]
                    ]);
                }
            }
        }
    }

    /**
     * 资源采集请求
     */
    public static function getData(int $resource_id, array $data = [])
    {
        $resource = PluginMovieResource::items('id','type,address');
        if ($resource[$resource_id]){
            if($resource[$resource_id]['type'] == 'json'){
                $result = json_decode(http_get($resource[$resource_id]['address'],$data),true);
            }
        }
        return $result ?? [];
    }

    /**
     * 获取分类关联信息
     * @param int $resource_id
     * @param array $resourceType
     * @return array
     */
    public static function typeHandle(int $resource_id,array $resourceType): array
    {
        $types = PluginMovieTypeItem::typeList(['resource_id'=>$resource_id]);
        $types = array_column($types,'type_name','resource_type_id');
        foreach($resourceType as &$vo) {
            $vo['resource_type_name'] = $types[$vo['type_id']] ?? '';
            $vo['resource_id']        = $resource_id;
        }
        return $resourceType;
    }

    /**
     * 获取新增数据ID
     * @param int $resource_id
     * @param array $model
     * @param int $type
     * @return int|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function getVideoId(int $resource_id,array $model,int $type)
    {
        $video = PluginMovieItem::mk()->where(['type_id'=>$type,'title'=>$model['vod_name']])->find();
        if($video){
            PluginMovieItem::mk()->where(['type_id'=>$type,'title'=>$model['vod_name']])
                ->update(['note'=>$model['vod_remarks'],'update_at'=>$model['vod_time']]);
            return $video['id'];
        }
        return PluginMovieItem::mk()->insertGetId([
            'resource_id'  => $resource_id,
            'vod_id'       => $model['vod_id'],
            'title'        => $model['vod_name'],
            'cover'        => $model['vod_pic'],
            'type_id'      => $type,
            'directors'    => $model['vod_director'],
            'actors'       => $model['vod_actor'],
            'note'         => $model['vod_remarks'],
            'remark'       => $model['vod_blurb'],
            'letter'       => $model['vod_letter'],
            'year'         => $model['vod_year'],
            'region'       => $model['vod_area'],
            'total'        => $model['vod_total'],
            'release_time' => $model['vod_pubdate'],
            'theme'        => $model['vod_class'],
            'create_at'    => date('Y-m-d H:i:s'),
            'update_at'    => $model['vod_time']
        ]);
    }

    /**
     * 采集播放路线及剧集保存
     * @param int $video_id
     * @param string $vod_play_from
     * @param string $vod_play_url
     * @return void
     */
    public static function saveVideoPlay(int $video_id,string $vod_play_from,string $vod_play_url)
    {
        if($vod_play_from && $vod_play_url){
            $play_from = explode("$$$", $vod_play_from);
            $play_url = explode("$$$", $vod_play_url);
            for ($i = 0; $i < count($play_from); $i++) {
                $result[] = ['name' => $play_from[$i], 'url' => $play_url[$i]];
            }
            foreach($result as &$item){
                $item['url'] = explode("#", $item['url']);
                foreach($item['url'] as &$vo){
                    if($vo){
                        $video = explode("$", $vo);
                        PluginMovieEpisode::mk()->save([
                            'movie_item_id' => $video_id,
                            'name'          => $video[0],
                            'url'           => $video[1],
                            'line_name'     => $item['name'],
                            'create_at'     => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 采集下载路线及剧集保存
     * @param int $video_id
     * @param string $vod_down_from
     * @param string $vod_down_url
     * @return void
     */
    public static function saveVideoDown(int $video_id,string $vod_down_from,string $vod_down_url)
    {
        if($vod_down_from && $vod_down_url){
            $play_from = explode("$$$", $vod_down_from);
            $play_url = explode("$$$", $vod_down_url);
            for ($i = 0; $i < count($play_from); $i++) {
                $result[] = ['name' => $play_from[$i], 'url' => $play_url[$i]];
            }
            foreach($result as &$item){
                $item['url'] = explode("#", $item['url']);
                foreach($item['url'] as &$vo){
                    if($vo){
                        $video = explode("$", $vo);
                        PluginMovieEpisode::mk()->save([
                            'movie_item_id' => $video_id,
                            'type'          => 1,
                            'name'          => $video[0],
                            'url'           => $video[1],
                            'line_name'     => $item['name'],
                            'create_at'     => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
        }
    }
}