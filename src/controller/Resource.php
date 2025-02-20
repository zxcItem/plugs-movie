<?php

namespace plugin\movie\controller;

use plugin\movie\model\PluginMovieResource;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\HttpResponseException;

/**
 * 影视资源采集
 */
class Resource extends Controller
{
    /**
     * 影视资源采集
     * @auth true
     * @menu true
     * @return void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public  function index()
    {
        PluginMovieResource::mQuery()->layTable(function () {
            $this->title = '影视资源采集';
        }, static function (QueryHelper $query) {
            $query->like('name')->dateBetween('create_at');
        });
    }

    /**
     * 添加
     * @auth true
     */
    public function add()
    {
        PluginMovieResource::mForm('form');
    }

    /**
     * 编辑
     * @auth true
     */
    public function edit()
    {
        PluginMovieResource::mForm('form');
    }

    /**
     * 修改状态
     * @auth true
     */
    public function state()
    {
        PluginMovieResource::mSave($this->_vali([
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
        PluginMovieResource::mDelete();
    }

    /**
     * 检测资源路径
     * @return void
     */
    public function check()
    {
        try {
            $headers['headers'] = ["Content-Type: text/html;charset=utf-8"];
            $data = json_decode(http_get(input('address'),[],$headers),true);
            if ($data['code'] !== 1) $this->error('连接失败！','',2);
            $this->success('连接成功',$data,1);
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->error("连接失败：{$exception->getMessage()}",'',2);
        }
    }
}