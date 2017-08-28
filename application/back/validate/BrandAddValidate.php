<?php

 namespace app\back\validate;
 use think\Validate;

 /**
 * @Author:
 * @Date:   2017/7/19 19:08
 * @Last Modified by:   Acer
 * @Email: y1wanghui@163.com
 */
class BrandAddValidate extends Validate
{
    /*
     *验证品牌类
     *
     */

    //定义的字段类 进行 对添加数据的验证
    protected $rule = [
        'title' => ['require','unique:brand '],
        'site'  => ['url'],
        'sort'  => ['require','number'],
    ];
    //定义的字段翻译 其他字段采用默认的字段
    protected $field = [
        'title' => '品牌',
        'site'  => '官网',
        'sort'  => '排序',
    ];
}