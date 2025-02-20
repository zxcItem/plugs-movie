<?php

declare (strict_types=1);

namespace plugin\movie\command;

use plugin\movie\model\PluginMovieTypeItem;
use plugin\movie\service\ResourceService;
use think\admin\Command;
use think\admin\Exception;
use think\console\Input;
use think\console\Output;

/**
 * 根据资源vod_ids采集资源详情
 * @class Batch
 * @package plugin\movie\command
 */
class Batch extends Command
{
    /**
     * ID采集资源
     * @return void
     */
    protected function configure()
    {
        $this->setName('plugin:movie:batch');
        $this->setDescription('批量资源ID采集数据任务操作');
    }

    /**
     * 执行ID采集资源
     * @param Input $input
     * @param Output $output
     * @throws Exception
     */
    protected function execute(Input $input, Output $output)
    {
        $resource_id = $this->queue->data['resource_id'];
        $map = ['ids'=>$this->queue->data['vod_ids'] ?? '','ac'=>'detail','h'=> $this->queue->data['h'] ?? ''];
        $resource = ResourceService::getData(intval($resource_id),$map);
        $types = PluginMovieTypeItem::mk()->where('resource_id',$resource_id)->column('type_id,type_pid','resource_type_id');
        [$total, $count, $error] = [$resource['total'], 0, 0];
        foreach ($resource['list'] as &$model) try {
            $this->queue->message($total, ++$count, sprintf('开始采集【 %s 】资源详情', $model['vod_name']));
            if (!$type = $types[$model['type_id']]['type_id']) continue;
            $this->app->db->transaction(function () use ($model,$resource_id,$type) {
                $video_id = ResourceService::getVideoId(intval($resource_id),$model, $type);
                ResourceService::saveVideoPlay($video_id,$model['vod_play_from'],$model['vod_play_url']);
                ResourceService::saveVideoDown($video_id,$model['vod_down_from'],$model['vod_down_url']);
            });
        } catch (\Exception $exception) {
            $error++;
            $this->queue->message($total, $count, sprintf('采集【%s 】资源失败, %s', $model['vod_name'], $exception->getMessage()), 1);
        }
        $this->setQueueSuccess(sprintf('此次共采集 %d 条资源详情, 其中有 %d 条采集失败。', $total, $error));
    }
}