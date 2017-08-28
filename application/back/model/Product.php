<?php

namespace app\back\model;

use think\Model;

class Product extends Model
{
    /**
     * @var array 自动填充
     */
    protected $auto =[
        'upc', 'date_available'
    ];

    /**
     *  属性设置器
     */
    public function setUpcAttr($value)
    {
        return '' !== $value ? $value : date('YmdHis') . mt_rand(100,999) . mt_rand(100,999);
    }
    public function setDateAvailableAttr($value)
    {
        return '' !== $value ? $value:date('Y-m-d H:i:s');
    }
}