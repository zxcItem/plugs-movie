<?php

namespace plugin\movie\controller;

use plugin\movie\model\PluginMovieNavigation;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 影视资源导航
 */
class Navigation extends Controller
{

    /**
     * 影视资源导航
     * @auth true
     * @menu true
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public  function index()
    {
        PluginMovieNavigation::mQuery()->layTable(function () {
            $this->title = '影视资源导航';
        });
    }

    /**
     * 添加
     * @auth true
     */
    public function add()
    {
        PluginMovieNavigation::mForm('form');
    }

    /**
     * 编辑
     * @auth true
     */
    public function edit()
    {
        PluginMovieNavigation::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state()
    {
        PluginMovieNavigation::mSave($this->_vali([
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
        PluginMovieNavigation::mDelete();
    }
}