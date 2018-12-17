<?php

namespace app\home\model;

use think\Model;

class Cart extends Model
{
    //封装加入购物车的函数 在其他页面也可以调用
    public static function addCart($goods_id,$number,$goods_attr_ids){
        //cookie('cart',null);die;
        //验证用户是否登录 登录 添加到数据表 未登录 添加到cookie
        if(session('?user_info')){
            //用户已登录
            $user_id=session('user_info.id');
            //查看数据库 用户是否存在相同的购买记录 同一个用户购买同一个商品，选中的属性值一样）
            $data=[
                'goods_id'=>$goods_id,
                'user_id'=>$user_id,
                'goods_attr_ids'=>$goods_attr_ids
            ];
            $cart=self::where($data)->find();
            if($cart){
                //有相同购买记录 直接累加数量
                $cart->number+=$number;
                $cart->save();
            }else{
                //不存在相同记录 创建新的记录
                $data['number']=$number;
                self::create($data);
            }
            return true;
        }else{
            //用户未登录 加入到cookie
            //获取客户cookie的购物车数据   cookie中只能存储字符串  框架可以自动把数组和cookie相互转化
            $data=cookie('cart')?unserialize(cookie('cart')):[];  //框架中可以不使用unserialize
            //拼接当前操作的数据的下标 goods_id - goods_attr_ids   =>$number
            $key=$goods_id.'-'.$goods_attr_ids;
            //$data[$key]=$number;
            //同上 先判断cookie中用户是否添加过相同的购买记录
            if(isset($data[$key])){
                //存在相同记录
                $data[$key]+=$number;
            }else{
                //不存在相同记录
                $data[$key]=$number;
            }
            //将数据重新存储到cookie
            cookie('cart',serialize($data),7*86400);
            return true;
        }
    }
    //查询所有购物车 数据
    public static function getAllCart(){
        if(session('?user_info')){
            //已登录的情况 直接从数据库获取购物车信息
            $user_id=session('user_info.id');
            $data=self::where('user_id',$user_id)->select();
            $data=(new \think\Collection($data))->toArray();
            return $data;
        }else{
            //未登录 查询cookie
            $cart_data=cookie('cart')?unserialize(cookie('cart')):[];
            //由于cookie取出的是 goods_id-goods_attr_ids=>number 的数组  得转化成标准的二维数组
            $data=[];
            foreach($cart_data as $k=>$v){
                $temp=explode('-',$k);
                $goods_id=$temp[0];
                $goods_attr_ids=$temp[1];
                $number=$v;
                $row=[
                    'id'=>'',   //额外添加id字段备用 值留空
                    'goods_id'=>$goods_id,
                    'goods_attr_ids'=>$goods_attr_ids,
                    'number'=>$number,
                ];
                $data[]=$row;
            }
            return $data;
        }
    }
    //迁移cookie数据到数据表 登录时调用
    public static function cookieTodb(){
        $data=cookie('cart')?unserialize(cookie('cart')):[];
        foreach($data as $k=>$v){
            $temp=explode('-',$k);
            $goods_id=$temp[0];
            $goods_attr_ids=$temp[1];
            $number=$v;
            self::addCart($goods_id,$number,$goods_attr_ids);
        }
        //清除cookie中的购物车数据
        cookie('cart',null);
    }
    public static function changeNum($goods_id,$number,$goods_attr_ids){
        //先判断是否登录
        if(session('?user_info')){
            //已登录 直接修改数据表
            $user_id=session('user_info.id');
            $where=[
                'goods_id'=>$goods_id,
                'user_id'=>$user_id,
                'goods_attr_ids'=>$goods_attr_ids
            ];
            self::update(['number'=>$number],$where);
        }else{
            //未登录 修改cookie
            $data=cookie('cart')?unserialize(cookie('cart')):[];
            //拼接当前记录的下标
            $key=$goods_id.'-'.$goods_attr_ids;
            $data[$key]=$number;
            //重新保存cookie
            cookie('cart',serialize($data),7*86400);
        }
    }
    public static  function delCart($goods_id,$goods_attr_ids){
        //判断是否登录
        if(session('?user_info')){
            //已登录
            $user_id=session('user_info.id');
            $where=[
                'goods_id'=>$goods_id,
                'user_id'=>$user_id,
                'goods_attr_ids'=>$goods_attr_ids
            ];
            self::where($where)->delete();
        }else{
            //未登录
            $data=cookie('cart')?unserialize(cookie('cart')):[];
            $key=$goods_id.'-'.$goods_attr_ids;
            //删除当前的记录
            unset($data[$key]);
            //重新保存cookie
            cookie('cart',serialize($data),7*86400);
        }
    }
}
