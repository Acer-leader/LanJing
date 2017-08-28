<?php

namespace app\back\controller;

use think\Controller;
use think\Request;
use app\back\model\AuthGroupRule as AuthGroupRuleModel;
use think\Loader;
use think\Session;

/**
 * Class AuthGroupRule
 * 规则和组关系管理控制器
 * @package app\back\controller
 */
class AuthGroupRule extends Controller
{
    /**
     * @return mixed
     * 设置 = 添加和更新
     * @param $id int
     */
    public function set($id=null)
    {
        $request = Request::instance();// request()

        // 获取模型, 如果是添加获取新模型, 如果是更新获取id对应的模型
        if (is_null($id)) {
            $model = new AuthGroupRuleModel();
        } else {
            $model = AuthGroupRuleModel::get($id);
        }

        // 如果当前为get请求
        if ($request->isGet()) {
            // 直接渲染模板
            // 如果是更新, 则需要分配当前所更新的数据到模板
            $data = Session::has('data') ? Session::get('data') : $model->getData();
            return $this->fetch('set', ['message'=>Session::get('message'), 'data' => $data]);
        }

        // post请求方式
        elseif ($request->isPost()) {
            // 获取提交的数据
            $data = $request->post();
            // 数据校验是否通过
            $validate = Loader::validate('AuthGroupRuleSetValidate');// use think\Loader;
            // 批量验证传入的数据
            $result = $validate->batch(true)->check($data);
            if (! $result) {
                // 未通过验证
                // 重定向到add, 通常需要携带错误消息和错误数据
                $this->redirect('set', [], 302, [
                    'message'=>$validate->getError(),
                    'data' => $data,
                ]);
            }

            // 模型填充数据
            $model->data($data);
            // 存储, 可以同时完成insert 和 update
            $model->save();
            // 重定向到
            $this->redirect('index');
        }
    }

    /**
     * 索引列表
     * @return mixed
     */
    public function index()
    {
        # 设置过滤条件
        $where = $filter = [];
        // 判断是否传递了过滤条件
        // 设置
        $query = AuthGroupRuleModel::where($where);

        # 处理排序
        $order = [];
        $field = input('order_field', '');
        $type = input('order_type', 'asc');
        // 存在排序字段
        if ($field !== '') {
            $query->order([$field=>$type]);
            // 分配到模板, 用于确定URL
            $order = ['field'=>$field, 'type'=>$type];
        }

        # 获取数据
        $limit = 5;
        $rows = $query->paginate($limit);

        # 模板展示
        return $this->fetch('index', ['rows'=>$rows, 'filter'=>$filter, 'order'=>$order]);
    }
    /**
     * 批量处理
     */
    public function multi()
    {
        // 利用模型完成删除即可
        AuthGroupRuleModel::destroy(input('selected/a', []));
        // 重定向
        $this->redirect('index');
    }
}