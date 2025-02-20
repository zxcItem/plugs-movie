<?php

namespace plugin\movie\controller;

use plugin\movie\model\PluginMovieType;
use think\admin\Controller;
use think\admin\extend\DataExtend;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 影视资源分类
 */
class Type extends Controller
{
    protected $maxLevel = 2;

    /**
     * 影视资源分类
     * @auth true
     * @menu true
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public  function index()
    {
        PluginMovieType::mQuery()->layTable(function () {
            $this->title = '影视资源分类';
        }, static function (QueryHelper $query) {
            $query->like('name')->dateBetween('create_at');
        });
    }

    /**
     * 列表数据处理
     * @param array $data
     */
    protected function _index_page_filter(array &$data)
    {
        foreach ($data as &$datum) $datum['spc'] = implode(',',DataExtend::getArrSubIds($data,$datum['id']));
        $data = DataExtend::arr2tree($data,'id','pid','children');
        PluginMovieType::addIsParent($data);
    }


    /**
     * 添加
     * @auth true
     */
    public function add()
    {
        PluginMovieType::mForm('form');
    }

    /**
     * 编辑
     * @auth true
     */
    public function edit()
    {
        PluginMovieType::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state()
    {
        PluginMovieType::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 表单数据处理
     * @param array $data
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    protected function _form_filter(array &$data)
    {
        if ($this->request->isGet()) {
            $data['pid'] = intval($data['pid'] ?? input('pid', '0'));
            $this->cates = PluginMovieType::getParentData($this->maxLevel, $data, [
                'id' => '0', 'pid' => '-1', 'name' => '顶部分类',
            ]);
        }
    }

    /**
     * 删除分类
     * @auth true
     */
    public function remove()
    {
        PluginMovieType::mDelete();
    }

    public function addIsParent(&$categories) {
        foreach ($categories as &$category) {
            // 如果子分类列表不为空，则设置 isParent 为 true，否则为 false
            $category['isParent'] = !empty($category['children']);
            if (empty($category['children'])) $category['children'] = [];
            // 递归调用，处理子分类
            if (!empty($category['children'])) {
                self::addIsParent($category['children']);
            }
        }
    }
}