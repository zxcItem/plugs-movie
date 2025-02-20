<?php

namespace plugin\movie\controller;

use plugin\movie\model\PluginMovieItem;
use plugin\movie\model\PluginMovieType;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 影视资源数据
 */
class Movie extends Controller
{
    /**
     * 影视资源数据
     * @auth true
     * @menu true
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public  function index()
    {
        PluginMovieItem::mQuery()->withoutField('remark,actors')->layTable(function () {
            $this->title = '影视资源数据';
            $this->types = PluginMovieType::items();
        }, static function (QueryHelper $query) {
            $query->with(['region','type'=>function($type){
                $type->with(['type']);
            }])->like('title')->equal('type_id')->dateBetween('create_at');
        });
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_page_filter(array &$data)
    {

    }

    /**
     * 添加
     * @auth true
     */
    public function add()
    {
        PluginMovieItem::mForm('form');
    }

    /**
     * 编辑
     * @auth true
     */
    public function edit()
    {
        PluginMovieItem::mForm('form');
    }

    /**
     * 表单数据处理
     * @param array $data
     */
    protected function _form_filter(array &$data)
    {
        if ($this->request->isGet()) {
            $this->cates = PluginMovieType::items(true);
            $data['cates'] = $data['cates'] ?? [];
        }
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state()
    {
        PluginMovieItem::mSave($this->_vali([
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
        PluginMovieItem::mDelete();
    }
}