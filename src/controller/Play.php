<?php

namespace plugin\movie\controller;

use plugin\movie\model\PluginMovieEpisode;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 影视资源剧集数据
 */
class Play extends Controller
{
    /**
     * 影视资源剧集数据
     * @auth true
     * @menu true
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public  function index()
    {
        PluginMovieEpisode::mQuery()->layTable(function () {
            $this->title = '影视资源剧集数据';
        }, static function (QueryHelper $query) {
            $query->with(['video'])->equal('video_id,status,line_id')->like('name')->dateBetween('create_at');
        });
    }

    /**
     * 添加
     * @auth true
     */
    public function add()
    {
        PluginMovieEpisode::mForm('form');
    }

    /**
     * 编辑
     * @auth true
     */
    public function edit()
    {
        PluginMovieEpisode::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state()
    {
        PluginMovieEpisode::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除
     * @auth true
     */
    public function remove()
    {
        PluginMovieEpisode::mDelete();
    }
}