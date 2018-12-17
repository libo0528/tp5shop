<?php

namespace app\home\model;

use think\Model;
use traits\model\SoftDelete;
class Order extends Model
{
    //使用软删除
    use SoftDelete;
    //设置软删除相关字段
    protected $deleteTime='delete_time';   //表中没有的话需要自己设定的情况
}
