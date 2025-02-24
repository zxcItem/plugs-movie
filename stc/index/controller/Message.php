<?php

namespace app\index\controller;

use think\admin\Controller;
use think\admin\extend\CodeExtend;
use think\admin\service\CaptchaService;

class Message extends Controller
{

    public function index()
    {
        $this->captchaType = 'LoginCaptcha';
        $this->captchaToken = CodeExtend::uniqidDate(18);
        $this->app->session->set($this->captchaType, $this->captchaToken);
        $this->fetch();
    }

    /**
     * 生成验证码
     * @return void
     */
    public function captcha()
    {
        $input = $this->_vali([
            'type.require'  => '类型不能为空!',
            'token.require' => '标识不能为空!',
        ]);
        $image = CaptchaService::instance()->initialize();
        $captcha = ['image' => $image->getData(), 'uniqid' => $image->getUniqid()];
        if ($this->app->session->get($input['type']) === $input['token']) {
            $this->app->session->delete($input['type']);
        }
        $this->success('生成验证码成功', $captcha);
    }

    /**
     * 提交留言
     * @return void
     */
    public function submit()
    {
        $data = $this->_vali([
            'content.require' => '留言内容不能为空!',
            'verify.require'  => '图形验证码不能为空!',
            'uniqid.require'  => '图形验证标识不能为空!',
        ]);
        if (!CaptchaService::instance()->check($data['verify'], $data['uniqid'])) {
            $this->error('图形验证码验证失败，请重新输入!');
        }
        $this->success('留言成功');
    }
}