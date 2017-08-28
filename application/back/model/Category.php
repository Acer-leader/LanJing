<?php

namespace app\back\model;

use think\Cache;
use think\Model;

class Category extends Model
{
    /**
     * 获取树状分类
     * @return mixed
     */
    public function getTree()
    {
        # 初始化缓存服务器(在配置文件完成)已经在配置文件中完成  每次都需设置新的缓存时间
     /*   $option = [
            'type' => 'redis',
            'host' => '127.0.0.1',
            'port' => '6379',
            'prefix' => 'hui_',
        ];
        Cache::connect($option);*/
        # 获取缓存
        $key = 'category_tree';
        $tree = Cache::get($key);
        # 判断缓存是否存在
        if (! $tree) {
            // 缓存不可用, 查询, 处理
            # 获取全部分类
            $rows = $this->order('sort')->select();
            # 树状排序
            $tree = $this->tree($rows);

            # 生成缓存
            Cache::set($key, $tree, 30*24*3600);
        }

        return $tree;
    }

    private function tree($rows, $category_id=0, $level=0)
    {
        // 静态局部变量, 保证在递归调用时 不会数据初始化
        static $tree = [];
        // 遍历
        foreach($rows as $row) {
            // 找到其子分类
            if ($row['parent_id'] == $category_id) {
                // 记录层级
                $row['level'] = $level;
                // 放入tree
                $tree[] = $row;
                // 找递归
                $this->tree($rows, $row['id'], $level+1);
            }
        }
        return $tree;
    }


    public function getChilds($id)
    {
        if (!$id) {
            return [];
        }
        // 利用模型获取全部的分类(缓存, 不用担心效率)
        $cats = $this->getTree();
        // 找到其中的当前分类的后代分类和自己
        $ids = [];// 所有的后代分类id(包含自己的)
        $begin = false;// 设置开始标记
        foreach($cats as $cat) {
            if ($cat['id'] == $id) {
                $begin = true;
                $curr = $cat;// 记录自己
            }
            // 从自己开始
            if(! $begin)  continue;
            // 到 第一个level不大于自己(自己不应该终止)的终止
            if ($cat['id'] != $curr['id'] && $cat['level'] <= $curr['level']) break;

            // 记录下来全部的id(包含自己的及其后代的)
            $ids[] = $cat['id'];
        }
        return $ids;
    }
}