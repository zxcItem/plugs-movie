<?php

declare (strict_types=1);

namespace plugin\movie\model;

use think\admin\extend\DataExtend;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\model\relation\HasOne;

/**
 * 影视资源分类
 * Class PluginMovieType
 */
class PluginMovieType extends Abs
{

    /**
     * 获取上级可用选项
     * @param int $max 最大级别
     * @param array $data 表单数据
     * @param array $parent 上级分类
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function getParentData(int $max, array &$data, array $parent = []): array
    {
        $items = static::mk()->order('sort desc,id asc')->select()->toArray();
        $cates = DataExtend::arr2table(empty($parent) ? $items : array_merge([$parent], $items));
        if (isset($data['id'])) foreach ($cates as $cate) if ($cate['id'] === $data['id']) $data = $cate;
        foreach ($cates as $key => $cate) {
            $isSelf = isset($data['spt']) && isset($data['spc']) && $data['spt'] <= $cate['spt'] && $data['spc'] > 0;
            if ($cate['spt'] >= $max || $isSelf) unset($cates[$key]);
        }
        return $cates;
    }

    /**
     * 处理折叠表格数据
     * @param $categories
     */
    public static function addIsParent(&$categories)
    {
        foreach ($categories as &$category) {
            // 如果子分类列表不为空，则设置 isParent 为 true，否则为 false
            $category['isParent'] = !empty($category['children']);
            if (empty($category['children'])) $category['children'] = [];
            // 递归调用，处理子分类
            if (!empty($category['children'])) {
                self::addIsParent($category['children']);
            }
        }
        return $categories;
    }

    /**
     * 关联视频分类
     * @return HasOne
     */
    public function type(): HasOne
    {
        return $this->hasOne(PluginMovieType::class, 'id', 'pid')->bind(['pid_name'=>'name']);
    }

    /**
     * 获取列表数据
     * @param bool $simple 仅子级别
     * @return array
     */
    public static function items(bool $simple = false): array
    {
        $query = static::mk()->where(['status' => 1])->order('sort desc,id asc');
        $cates = array_column(DataExtend::arr2table($query->column('id,pid,name', 'id')), null, 'id');
        foreach ($cates as $cate) isset($cates[$cate['pid']]) && $cates[$cate['id']]['parent'] =& $cates[$cate['pid']];
        foreach ($cates as $key => $cate) {
            $id = $cate['id'];
            $cates[$id]['ids'][] = $cate['id'];
            $cates[$id]['names'][] = $cate['name'];
            while (isset($cate['parent']) && ($cate = $cate['parent'])) {
                $cates[$id]['ids'][] = $cate['id'];
                $cates[$id]['names'][] = $cate['name'];
            }
            $cates[$id]['ids'] = array_reverse($cates[$id]['ids']);
            $cates[$id]['names'] = array_reverse($cates[$id]['names']);
            if (isset($pky) && $simple && in_array($cates[$pky]['name'], $cates[$id]['names'])) {
                unset($cates[$pky]);
            }
            $pky = $key;
        }
        foreach ($cates as &$cate) {
            unset($cate['sps'], $cate['parent']);
        }
        return array_values($cates);
    }
}