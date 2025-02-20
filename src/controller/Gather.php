<?php

namespace plugin\movie\controller;

use plugin\movie\model\PluginMovieType;
use plugin\movie\model\PluginMovieTypeItem;
use plugin\movie\service\ResourceService;
use think\admin\Controller;

/**
 * 采集数据
 */
class Gather extends Controller
{
    /**
     * 采集数据管理
     * @auth true
     * @menu true
     */
    public  function index()
    {
        $this->title = '资源采集管理';
        $this->resource_id = input('resource_id');
        $data = ResourceService::getData($this->resource_id,[
            'pg' => input('page','1'),
            't'  => intval(input('t','')),
            'wd' => intval(input('wd','')),
        ]);
        if(!empty($data['class'])) $this->types = ResourceService::typeHandle($this->resource_id,$data['class']);
        if (input('output')) return json(['code'=>0,'count'=>$data['total'],'data'=>$data['list']]);
        $this->fetch();
    }

    /**
     * 批量采集
     * @auth true
     */
    public function batch()
    {
        $map = $this->_vali(['resource_id.require'=>'资源ID不可为空','vod_ids.require'=>'请勾选需要采集的数据']);
        $this->_queue('批量资源ID采集数据任务操作', 'plugin:movie:batch',0,$map);
    }

    /**
     * 采集今日
     * @auth true
     */
    public function today()
    {
        $map = $this->_vali(['resource_id.require'=>'资源ID不可为空','h.value'=>24]);
        $this->_queue('批量资源ID采集数据任务操作', 'plugin:movie:batch',0,$map);
    }

    /**
     * 采集所有
     * @auth true
     */
    public function all()
    {
        $map = $this->_vali(['resource_id.require'=>'资源ID不可为空']);
        $this->_queue("批量资源ID:{$map['resource_id']}的采集数据任务操作", 'plugin:movie:allbatch',0,$map,0,10);
    }

    /**
     * 批量绑定
     * @auth true
     */
    public function batchBind()
    {
        $map = $this->_vali(['resource_id.require'=>'资源ID不可为空']);
        $data = ResourceService::getData($map['resource_id']);
        ResourceService::bindType($map['resource_id'],$data['class']);
        $this->success('绑定成功');
    }

    /**
     * 绑定采集分类
     * @auth true
     * @return void
     */
    public function bind()
    {
        if ($this->request->isGet()){
            $this->resource_id = $this->request->param('resource_id');
            $this->resource_type_id = $this->request->param('resource_type_id');
            $this->resource_type_name = $this->request->param('resource_type_name');
            $this->types = PluginMovieType::items(true);
            $this->fetch();
        }else{
            PluginMovieTypeItem::mk()->save($this->request->post());
            $this->success('绑定成功');
        }
    }

    /**
     * 解绑采集分类
     * @auth true
     * @return void
     */
    public function unbind()
    {
        PluginMovieTypeItem::mk()->where([
            'resource_id'=>input('resource_id'),
            'resource_type_id'=>input('resource_type_id')
        ])->delete();
        $this->success('解绑成功');
    }
}