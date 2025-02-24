<?php

use think\facade\Route;

Route::rule('/','index/index/index');

Route::get('dianying','index/index/list')->append(['id'=>1]);
Route::get('dianshiju','index/index/list')->append(['id'=>2]);
Route::get('zongyi','index/index/list')->append(['id'=>3]);
Route::get('dongman','index/index/list')->append(['id'=>4]);

Route::get('/:id','index/video/detail');
Route::rule('message','index/message/index');