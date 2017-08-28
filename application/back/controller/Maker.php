<?php

namespace app\back\controller;


use think\Config;
use think\Controller;
use think\Db;
use app\back\model\AuthRule as AuthRuleModel;

class Maker extends Controller
{

    /**
     * 填写表信息
     */
    public function table()
    {
        return $this->fetch();
    }


    /**
     * 表信息
     */
    public function info()
    {
        // 表名
        $table = input('table');
        # 获取表注释.
        // 拼凑获取表信息的SQL
        $schema = Config::get('database.database');
        $name = Config::get('database.prefix') . $table;
        $sql = "SELECT TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=? AND TABLE_NAME=?";
        // 执行, query永远返回二维数组
        $rows = Db::query($sql, [$schema, $name]);
        $comment = $rows[0]['TABLE_COMMENT'];

        # 字段信息, 字段名和字段描述
        $sql = "SELECT COLUMN_NAME, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=?";
        // 执行, query永远返回二维数组
        $fields = Db::query($sql, [$schema, $name]);
        // 下标转小写
        $fields = array_map('array_change_key_case', $fields);

        // 依据格式, 响应数据
        return [
            'comment' => $comment,
            'fields' => $fields,
        ];
    }

    /**
     * 生成CRUD代码
     */
    public function generate()
    {
        # 接收参数
        $table = input('table');
        $comment = input('comment');
        $fields = input('fields/a');

        # 增加相应的授权规则
        $this->addRule($table, $comment);
        # 生成控制器
        $this->makeController($table, $comment);
        # 生成验证器
        $this->makeValidate($table);
        # 生成模型
        $this->makeModel($table);
        # 生成index视图
        $this->makeViewIndex($table, $comment, $fields);
        # 生成set视图
        $this->makeViewSet($table, $comment, $fields);
    }

    private function addRule($table, $comment)
    {
        $rows = [
            ['name'=>'back/'.$table.'/index', 'title'=>$comment.'列表'],
            ['name'=>'back/'.$table.'/set', 'title'=>$comment.'设置'],
            ['name'=>'back/'.$table.'/multi', 'title'=>$comment.'批量操作'],
        ];
        $model = new AuthRuleModel();
        $model->saveAll($rows);
    }

    private function makeController($table, $comment)
    {
        # 读取模板
        $template = file_get_contents(APP_PATH . 'back/codeTemplate/controller.tpl');

        # 处理替换数据
        $controller = $model = implode(array_map('ucfirst', explode('_', $table)));// 分割, 首字母大写, 连接
        $title = $comment;

        # 替换模板
        $search = ['%controller%', '%title%', '%model%'];// 占位符们
        $replace = [$controller, $title, $model];
        $content = str_replace($search, $replace, $template);

        # 生成文件
        $file = APP_PATH . 'back/controller/' . $controller . '.php';
        file_put_contents($file, $content);

        echo '控制器:', $file, '生成成功', '<br>';
    }

    private function makeValidate($table)
    {
        # 读取模板
        $template = file_get_contents(APP_PATH . 'back/codeTemplate/validate.tpl');

        # 处理替换数据
        $model = implode(array_map('ucfirst', explode('_', $table)));// 分割, 首字母大写, 连接

        # 替换模板
        $search = ['%model%'];// 占位符们
        $replace = [$model];
        $content = str_replace($search, $replace, $template);

        # 生成文件
        $file = APP_PATH . 'back/validate/' . $model . 'SetValidate.php';
        file_put_contents($file, $content);

        echo '验证器:', $file, '生成成功', '<br>';
    }

    private function makeModel($table)
    {
        # 读取模板
        $template = file_get_contents(APP_PATH . 'back/codeTemplate/model.tpl');

        # 处理替换数据
        $model = implode(array_map('ucfirst', explode('_', $table)));// 分割, 首字母大写, 连接

        # 替换模板
        $search = ['%model%'];// 占位符们
        $replace = [$model];
        $content = str_replace($search, $replace, $template);

        # 生成文件
        $file = APP_PATH . 'back/model/' . $model . '.php';
        file_put_contents($file, $content);

        echo '模型:', $file, '生成成功', '<br>';
    }

    private function makeViewIndex($table, $comment, $fields)
    {
        # 拼凑字段部分
        // 遍历全部字段, 需要列表展示的, 拼凑到一起
        $list_head_order_template = file_get_contents(APP_PATH . 'back/codeTemplate/list_head_order.tpl');
        $list_head_template = file_get_contents(APP_PATH . 'back/codeTemplate/list_head.tpl');
        $list_body_template = file_get_contents(APP_PATH . 'back/codeTemplate/list_body.tpl');
        $list_head = $list_body = '';
        foreach($fields as $field) {
            // 不需要列表, 处理下一个字段
            if(! isset($field['is_list'])) continue;
            # 标题部分
            // 根据字段是否需要排序, 使用不同的模板
            $template = isset($field['is_order']) ? $list_head_order_template : $list_head_template;
            // 替换模板
            $search = ['%field_name%', '%field_comment%'];// 占位符们
            $replace = [$field['name'], $field['comment']];
            $list_head .= str_replace($search, $replace, $template);

            #内容部分
            $list_body .= str_replace($search, $replace, $list_body_template);
        }

        # 生成整体模板
        // 读取模板
        $template = file_get_contents(APP_PATH . 'back/codeTemplate/list.tpl');
        // 替换模板
        $search = ['%title%', '%list_head%', '%list_body%'];// 占位符们
        $replace = [$comment, $list_head, $list_body];
        $content = str_replace($search, $replace, $template);
        // 生成文件
        // 需要考虑视图目录不存在的情况
        if (! is_dir(APP_PATH . 'back/view/' .$table)) {
            mkdir (APP_PATH . 'back/view/' .$table);
        }
        $file = APP_PATH . 'back/view/' .$table . '/index.html';
        file_put_contents($file, $content);

        echo '列表视图:', $file, '生成成功', '<br>';

    }

    private function makeViewSet($table, $comment, $fields)
    {
        # 字段模板
        $field_list = '';
        foreach($fields as $field) {
            // 是否需要出现在表单中
            if (!isset($field['is_set'])) continue;
            $template = file_get_contents(APP_PATH . 'back/codeTemplate/set_field.tpl');
            // 替换模板
            $search = ['%field_name%', '%field_comment%'];// 占位符们
            $replace = [$field['name'], $field['comment']];
            $field_list .= str_replace($search, $replace, $template);
        }
        // 读取模板
        $template = file_get_contents(APP_PATH . 'back/codeTemplate/set.tpl');
        // 替换模板
        $search = ['%title%', '%field_list%'];// 占位符们
        $replace = [$comment, $field_list];
        $content = str_replace($search, $replace, $template);
        // 生成文件
        // 需要考虑视图目录不存在的情况
        if (! is_dir(APP_PATH . 'back/view/' .$table)) {
            mkdir (APP_PATH . 'back/view/' .$table);
        }
        $file = APP_PATH . 'back/view/' .$table . '/set.html';
        file_put_contents($file, $content);

        echo '设置视图:', $file, '生成成功', '<br>';
    }
}