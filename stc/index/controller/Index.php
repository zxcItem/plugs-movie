<?php

namespace app\index\controller;

use plugin\movie\model\PluginMovieItem;
use plugin\movie\model\PluginMovieType;

class Index extends Base
{
    /**
     * 首页
     * @return void
     */
    public function index()
    {
        $this->types = PluginMovieType::mk()->where('pid',0)->field('id,name')->select()->toArray();
        foreach ($this->types as &$type)
        {
            $type['list'] = PluginMovieItem::mk()->where(['status'=>1])->order('update_at desc')->limit(12)->field('id,title,cover,update_at')->select()->toArray();
        }
        $this->updates = PluginMovieItem::mk()->where(['status'=>1])->order('update_at desc')->limit(6)->field('id,title,cover,update_at')->select()->toArray();
       $this->fetch();
    }

    /**
     * 列表
     * @return void
     */
    public function list()
    {
        $this->fetch();
    }


}
