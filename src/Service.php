<?php

declare (strict_types=1);

namespace plugin\cinema;

use plugin\movie\command\AllBatch;
use plugin\movie\command\Batch;
use think\admin\Plugin;

/**
 * 组件注册服务
 * @class Service
 * @package plugin\movie
 */
class Service extends Plugin
{
    /**
     * 定义插件名称
     * @var string
     */
    protected $appName = '影视系统';

    /**
     * 定义安装包名
     * @var string
     */
    protected $package = 'xiaochao/plugs-movie';

    /**
     * 插件服务注册
     * @return void
     */
    public function register(): void
    {
        $this->commands([Batch::class,AllBatch::class]);
    }

    /**
     * 影院系统菜单配置
     * @return array[]
     */
    public static function menu(): array
    {
        // 设置插件菜单
        $code = app(static::class)->appCode;
        // 设置插件菜单
        return [
            [
                'name' => '影院管理',
                'subs' => [
                    ['name' => '视频分类管理', 'icon' => 'layui-icon layui-icon-chart', 'node' => "{$code}/type/index"],
                    ['name' => '资源采集管理', 'icon' => 'layui-icon layui-icon-chart', 'node' => "{$code}/resource/index"],
                    ['name' => '视频资源管理', 'icon' => 'layui-icon layui-icon-chart', 'node' => "{$code}/movie/index"]
                ],
            ],
        ];
    }
}