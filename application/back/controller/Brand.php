<?php


namespace app\back\controller;

use think\Controller;
use think\Request;
use think\Loader;
use app\back\model\Brand as BrandModel;
use think\Session;
use think\Validate;

/**
 * Class Brand
 * 后台品牌管理控制器
 * @package app\back\controller
 */
class Brand extends Controller
{
    public function index()
    {
        
        # 设置过滤条件
        $where = $filter = [];
        // 判断是否传递了过滤条件
        // 标题
        $filter['filter_title'] = input('filter_title', '');
        if ($filter['filter_title'] !== '') {
            $where['title'] = ['like', $filter['filter_title']. '%'];
        }
        // 站点
        $filter['filter_site'] = input('filter_site', '');
        if ($filter['filter_site'] !== '') {
            // 自动增加http://
            if (substr($filter['filter_site'], 0, 7) != 'http://' && substr($filter['filter_site'], 0, 8) != 'https://') {
                $httpSite = 'http://' . $filter['filter_site'];
                $httpsSite = 'https://' . $filter['filter_site'];
                $where['site'] = ['like', [$httpSite . '%', $httpsSite . '%'], 'Or'];
            } else {
                $where['site'] = ['like', $filter['filter_site'] . '%'];
            }
        }
        // 设置
        $query = BrandModel::where($where);
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
        $rows = $query->paginate($limit);//, false, [
        //            'query' => \array_merge($filter, $order)
        //        ]);
        //        $rows = BrandModel::all();
        
        # 模板展示
        //        $this->assign('rows', $rows);
        //        dump($rows);
        return $this->fetch('index', ['rows'=>$rows, 'filter'=>$filter, 'order'=>$order]);
    }
    
    /**
     * @return mixed
     * 设置 = 添加和更新
     * @param $id int 正在编辑的品牌ID, 如果为null, 添加
     */
    public function set($id=null)
    {
        $request = Request::instance();// request()
        
        // 获取模型, 如果是添加获取新模型, 如果是更新获取id对应的模型
        if (is_null($id)) {
            $model = new BrandModel();
        } else {
            $model = BrandModel::get($id);
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
            $validate = Loader::validate('BrandAddValidate');// use think\Loader;
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
            // 插入是否成功
            // 重定向到
            $this->redirect('index');
        }
    }
    
    /**
     * 校验标题唯一
     * @return string
     */
    public function titleUniqueCheck()
    {
        // 获取当前填写的titlt
        $title = input('title');
        // 利用验证器完成
        $id = input('id', '');
        $except = $id==='' ? '' : ",$id";
        $result = Validate::unique(null, 'brand,title'.$except, ['title'=>$title], 'title');// use think\Validate
        if ($result) {
            return true;
        } else {
            return false;
        }
        // 注意:期望字符串而不是布尔值,  如果响应的布尔型, 本框架中是可以的
        // 本框架, 会自动将ajax请求的数据 json化处理, 参考配置: 'default_ajax_return'    => 'json',
        // 布尔型数据就会: true布尔值 => true字符串
    }
    
    public function add()
    {
        $request = Request::instance();// request()
        // 如果当前为get请求
        if ($request->isGet()) {
            // 直接渲染模板
            return $this->fetch('add', ['message'=>Session::get('message'), 'data' => Session::get('data')]);
        }
        
        // post请求方式
        elseif ($request->isPost()) {
            // 获取提交的数据
            $data = $request->post();
            // 数据校验是否通过
            $validate = Loader::validate('BrandAddValidate');// use think\Loader;
            // 批量验证传入的数据
            $result = $validate->batch(true)->check($data);
            if (! $result) {
                // 未通过验证
                // 重定向到add, 通常需要携带错误消息和错误数据
                $this->redirect('add', [], 302, [
                    'message'=>$validate->getError(),
                    'data' => $data,
                ]);
            }
            
            
            // 得到Brand模型处理
            $model = new BrandModel();
            // 模型填充数据
            $model->data($data);
            // 存储
            $model->save();
            // 插入是否成功
            // 重定向到
            $this->redirect('index');
        }
    }
    
    
    /**
     * 索引列表
     * @return mixed
     */
   
    public function multi()
    {
        // 利用模型完成删除即可
        BrandModel::destroy(input('selected/a', []));
        
        $this->redirect('index');
    }
}