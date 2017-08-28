<?php


namespace app\back\validate;


use think\Validate;

/**
 * Class AuthGroupRuleSetValidate
 * 设置验证器类
 * @package app\back\validate
 */
class AuthGroupRuleSetValidate extends Validate
{
    // 定义规则
    protected $rule = [
    ];

    // 定义字段翻译, 在不需要自定义错误信息时, 可以仅仅翻译字段, 其他的信息采用内置的默认信息
    protected $field = [
    ];

}