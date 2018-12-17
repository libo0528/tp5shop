<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;
class Attribute extends Model
{
    //使用软删除
    use SoftDelete;
    //设置软删除相关字段
    protected $deleteTime='delete_time';   //表中没有的话需要自己设定的情况
    public function getAttrTypeAttr($value)
    {
        //0 唯一属性 1 单选属性
        return $value ? '单选属性' : '唯一属性';
    }
    public function getAttrInputTypeAttr($value)
    {
        // 0  input输入框 1 下拉列表  2多选框
        $attr_input_type=['input输入框','下拉列表','多选框'];
        return $attr_input_type[$value];
    }
}
