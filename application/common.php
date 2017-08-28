<?php
use think\response\Redirect;

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 自定义的重定向函数, 与控制器的$this->redirect()
 */
function redirectU($url, $params=[], $code=302, $with=[])
{
    $response = new \think\response\Redirect($url);
    $response->code($code)->params($params)->with($with);
    throw new \think\exception\HttpResponseException($response);
}

// 模板中权限校验
function authCheck($rules, $logic='OR')
{
    $auth = new \auth\Auth();
    return $auth->check($rules, \think\Session::get('admin.id'), $logic);
}
