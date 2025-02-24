<?php


namespace app\index\controller;



use plugin\movie\model\PluginMovieItem;
use plugin\movie\model\PluginMovieType;

class Video extends Base
{
    /**
     * 详情
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail()
    {
        $map = $this->_vali(['id.require'=>'id不可为空！']);
        $this->vo = PluginMovieItem::mk()->where($map)->find()->toArray();
        $this->vo['type'] = PluginMovieType::mk()->where('id',$this->vo['type_id'])->value('name');
        $this->fetch();
    }
}