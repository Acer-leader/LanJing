<?php
namespace app\behavior;
use think\Request;
use think\session;
/**
 * @Author: Acer
 * @Email: y1wanghui@163.com
 * @注语:
 * @Date: 2017年7月25日下午8:06:47
 */
class CheckAuth
{
    /**
     * 固定的方法 在tags.php 中配置
     */
    public function run()
    {
        $request = Request::instance();
        //如果不是后台back  自动跳过
        if ('back' != $request->module()){
            return ;
        }
        //获取当前的请求标志
        $action = $request->module().'/'.$request->controller().'/'.
            $request->action();
        //判断当前的请求是否需要校验(后台登录操作不需要登录才能执行)
        //特列列表
        $except = [
          'back/Admin/login'  
        ];
        //检验是否出现在特例列表中
        if (in_array($action, $except)) return;
        //认证登录校验
       if (!session::has('admin')){
            //没有登录定向到登录页面
            redirect('back/admin/login');
        }
        //校验权限
        $auth = new \auth\Auth();
        if (! $auth->check([$action], Session::get('admin.id'))){
            //没有权限
            redirectU('back/admin/login',[],302,['message'=>'没权限，请使用其他登录']);
        }
    }
}