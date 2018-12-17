<?php

namespace app\home\model;

use think\Model;
use traits\model\SoftDelete;

class GoodsAttr extends Model
{
    //使用软删除
    use SoftDelete;
    //设置软删除相关字段
    protected $deleteTime='delete_time';   //表中没有的话需要自己设定的情况
    //封装 显示商品属性名 和属性值  $goods_attr_ids 查询属性表 和 关联表
    public static function goodsAttrInfo($goods_attr_ids){
        //select t1.*,t2.attr_name from `tpshop_goodsattr` left join `tpshop_attribute` on t1.attr_id=t2.id
        //where t1.id in ()
        $goodsattr=self::alias('t1')
            ->field('t1.*,t2.attr_name')
            ->join('tpshop_attribute t2','t1.attr_id=t2.id','left')
            ->where('t1.id','in',$goods_attr_ids)
            ->select();
        return $goodsattr;
    }
}
