<?php


namespace app\index\controller;


use plugin\movie\model\PluginMovieNavigation;
use think\admin\Controller;
use think\App;

class Base extends Controller
{

    public function __construct(App $app)
    {
        $this->navigation = PluginMovieNavigation::mk()->where('status',1)->column('url','title');
        parent::__construct($app);
    }
}