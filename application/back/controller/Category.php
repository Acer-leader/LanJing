<?php

namespace app\back\controller;

use Symfony\Component\Yaml\Tests\B;
use think\Cache;
use think\Controller;
use think\Request;
use app\back\model\Category as CategoryModel;
use think\Loader;
use think\Session;

/**
 * Class Category
 * 分类管理控制器
 * @package app\back\controller
 */
class Category extends Controller
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
            $model = new CategoryModel();
        } else {
            $model = CategoryModel::get($id);
        }

        // 如果当前为get请求
        if ($request->isGet()) {
            // 直接渲染模板
            // 如果是更新, 则需要分配当前所更新的数据到模板
            $data = Session::has('data') ? Session::get('data') : $model->getData();
            $cats = $model->getTree();
            return $this->fetch('set', [
                'message'=>Session::get('message'),
                'data' => $data,
                'cats'=>$cats,
                'childs' => $model->getChilds($id),
            ]);
        }

        // post请求方式
        elseif ($request->isPost()) {
            // 获取提交的数据
            $data = $request->post();
            // 数据校验是否通过
            $validate = Loader::validate('CategorySetValidate');// use think\Loader;
            // 批量验证传入的数据
            $validate->batch(true);
            // 如果是编辑, 则指定为edit场景
            if ($id) {
                $validate->scene('edit');
            }
            $result = $validate->check($data);
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
            // 删除缓存
            $this->rmCache();
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
        $model = new CategoryModel();
        $rows = $model->getTree();

        # 模板展示
        return $this->fetch('index', ['rows'=>$rows]);
    }

    /**
     * 批量处理
     */
    public function multi()
    {
        // 利用模型完成删除即可
        CategoryModel::destroy(input('selected/a', []));
        // 删除缓存
        $this->rmCache();
        // 重定向
        $this->redirect('index');
    }

    /**
     * 删除缓存
     */
    protected function rmCache()
    {
        $key = 'category_tree';
        Cache::rm($key);
    }
}