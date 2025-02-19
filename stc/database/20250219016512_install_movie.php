<?php

use think\admin\extend\PhinxExtend;
use think\migration\Migrator;

class InstallMovie extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->_create_plugin_movie_item();
        $this->_create_plugin_movie_type();
        $this->_create_plugin_movie_type_item();
        $this->_create_plugin_movie_resource();
        $this->_create_plugin_movie_episode();
    }

    /**
     * 影视资源采集
     * @class PluginMovieResource
     * @table plugin_movie_resource
     * @return void
     */
    private function _create_plugin_movie_resource()
    {
        // 创建数据表对象
        $table = $this->table('plugin_movie_resource', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '影视资源采集',
        ]);
        PhinxExtend::upgrade($table, [
            ['name', 'string', ['limit' => 255,'default' => '', 'null' => true, 'comment' => '资源名称']],
            ['address', 'string', ['limit' => 255,'default' => '', 'null' => true, 'comment' => '资源地址']],
            ['remark', 'string', ['limit' => 255,'default' => '', 'null' => true, 'comment' => '资源备注']],
            ['type', 'string', ['limit' => 32,'default' => '', 'null' => true, 'comment' => '资源类型 1-json,2-xml']],
            ['parse_mod', 'integer', ['limit' => 1,'default' => 1, 'null' => true, 'comment' => '1-播放器模式,2-高级模式,3-json解析模式']],
            ['parse_address', 'string', ['limit' => 255,'default' => '', 'null' => true, 'comment' => '解析地址，视频播放地址']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '激活状态(0无效,1有效)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'status','create_at',
        ], true);
    }

    /**
     * 影视资源分类
     * @class PluginMovieType
     * @table plugin_movie_type
     * @return void
     */
    private function _create_plugin_movie_type()
    {
        // 创建数据表对象
        $table = $this->table('plugin_movie_type', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '影视资源分类',
        ]);
        PhinxExtend::upgrade($table, [
            ['pid', 'integer', ['limit' => 10,'default' => 0, 'null' => true, 'comment' => '上级分类']],
            ['name', 'string', ['limit' => 32,'default' => '', 'null' => true, 'comment' => '分类名称']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '激活状态(0无效,1有效)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'pid','status','create_at',
        ], true);
    }

    /**
     * 影视资源分类关联
     * @class PluginMovieTypeItem
     * @table plugin_movie_type_item
     * @return void
     */
    private function _create_plugin_movie_type_item()
    {
        // 创建数据表对象
        $table = $this->table('plugin_movie_type_item', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '影视资源分类关联',
        ]);
        PhinxExtend::upgrade($table, [
            ['resource_id', 'integer', ['limit' => 10,'default' => 0, 'null' => true, 'comment' => '资源ID']],
            ['resource_type_id', 'integer', ['limit' => 10,'default' => 0, 'null' => true, 'comment' => '资源分类ID']],
            ['type_id', 'integer', ['limit' => 10,'default' => 0, 'null' => true, 'comment' => '分类ID']],
            ['type_pid', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '上级分类ID']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'resource_type_id','type_id','create_at',
        ], true);
    }

    /**
     * 影视资源数据
     * @class PluginMovieItem
     * @table plugin_movie_item
     * @return void
     */
    private function _create_plugin_movie_item()
    {
        // 创建数据表对象
        $table = $this->table('plugin_movie_item', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '影视资源数据',
        ]);
        PhinxExtend::upgrade($table, [
            ['resource_id', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '资源ID']],
            ['vod_id', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '资源采集ID']],
            ['title', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '视频标题']],
            ['cover', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '视频封面']],
            ['type_id', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '分类二级']],
            ['type_pid', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '分类一级']],
            ['theme', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '视频题材']],
            ['directors', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '视频导演']],
            ['actors', 'string', ['limit' => 500, 'default' => '', 'null' => true, 'comment' => '视频演员']],
            ['remark', 'string', ['limit' => 999, 'default' => '', 'null' => true, 'comment' => '视频简介']],
            ['letter', 'string', ['limit' => 10, 'default' => '', 'null' => true, 'comment' => '首字母']],
            ['view', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '浏览量']],
            ['note', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => '连载状态']],
            ['year', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => '视频年份']],
            ['region', 'string', ['limit' => 32, 'default' => 0, 'null' => true, 'comment' => '地区']],
            ['total', 'integer', ['limit' => 10, 'default' => 0, 'null' => true, 'comment' => '总集数']],
            ['recommend', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '是否推荐 0否 1是']],
            ['release_time', 'string', ['limit' => 32, 'default' => NULL, 'null' => true, 'comment' => '上映时间']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '激活状态(0无效,1有效)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
            ['update_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '更新时间']],
        ], [
            'type_pid','sort','status','create_at','update_at'
        ], true);
    }

    /**
     * 影视资源剧集数据
     * @class PluginMovieEpisode
     * @table plugin_movie_episode
     * @return void
     */
    private function _create_plugin_movie_episode()
    {
        // 创建数据表对象
        $table = $this->table('plugin_movie_episode', [
            'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => '影视资源剧集数据',
        ]);
        PhinxExtend::upgrade($table, [
            ['movie_item_id', 'biginteger', ['limit' => 20, 'default' => 0, 'null' => true, 'comment' => '视频ID']],
            ['line_name', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => '线路标题']],
            ['name', 'string', ['limit' => 32, 'default' => '', 'null' => true, 'comment' => '名称']],
            ['type', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '类型0播放1下载']],
            ['url', 'string', ['limit' => 255, 'default' => '', 'null' => true, 'comment' => '播放地址']],
            ['sort', 'biginteger', ['default' => 0, 'null' => true, 'comment' => '排序权重']],
            ['status', 'integer', ['limit' => 1, 'default' => 0, 'null' => true, 'comment' => '激活状态(0无效,1有效)']],
            ['create_at', 'datetime', ['default' => NULL, 'null' => true, 'comment' => '创建时间']],
        ], [
            'movie_item_id','type','sort','status','create_at',
        ], true);
    }
}
