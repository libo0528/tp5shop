<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;
class Goods extends Model
{
    //使用软删除
    use SoftDelete;
    //设置软删除相关字段
    protected $deleteTime='delete_time';   //表中没有的话需要自己设定的情况
    //当提供的数据表名不规格时，设置完整的数据表名称
//    protected $table='tpshop_goods';
}
