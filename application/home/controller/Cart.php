<?php

namespace app\home\controller;

use think\Collection;
use think\Controller;
use think\Request;
use app\home\model\Cart as CartModel;
use app\home\model\GoodsAttr as GoodsAttrModel;
use app\home\model\Goods as GoodsModel;
class Cart extends Base
{
    /**
     * 显示加入购物车页面
     *
     * @return \think\Response
     */
    public function addcart()
    {
        //该页面只能是post提交显示 不能通过刷新浏览器地址栏显示
        if(request()->isGet()){
            $this->redirect('home/index/index');
        }
        $data=request()->param();
        //dump($data);die;
        $rule=[
            'number'=>'require|integer|gt:0',
            'goods_attr_ids'=>'require',
            'goods_id'=>'require|integer|gt:0'
        ];
        $msg=[
            'number.require'=>'购买数量不能为0',
            'number.integer'=>'购买数量参数错误',
            'number.gt'=>'购买数量参数错误',
            'goods_id.require'=>'请选择商品',
            'goods_id.integer'=>'商品参数错误',
            'goods_id.gt'=>'商品参数错误',
            'goods_attr_ids.require'=>'请选择商品属性'
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        CartModel::addCart($data['goods_id'],$data['number'],$data['goods_attr_ids']);
        //查询商品基本信息
        $goods=\app\admin\model\Goods::find($data['goods_id']);
        //查询商品属性名和属性值
        $goodsattr=GoodsAttrModel::goodsAttrInfo($data['goods_attr_ids']);
        //dump($goodsattr);die;
        //渲染模板
        return view('addcart',[
            'goods'=>$goods,
            'number'=>$data['number'],
            'goodsattr'=>$goodsattr
        ]);
    }

    /**
     * 显示购物车单页.（旧方法 不适用）
     *
     * @return \think\Response
     */
  /*  public function index()
    {
        $list=CartModel::getAllCart();
        foreach($list as &$v){
            //添加商品基本属性的键值对
            $v['goods']=GoodsModel::find($v['goods_id'])->toArray();
            //添加商品属性名称 属性值的键值对
            $v['goodsattr']=GoodsAttrModel::goodsAttrInfo($v['goods_attr_ids']);
            //不建议在遍历中查询数据库 会增加数据库的压力 执行缓慢  需要优化
        }
        dump($list);die;
        unset($v);
        //渲染模板
        return view('index',['list'=>$list]);
    }*/
    public function index()
    {
        $list=CartModel::getAllCart();
        $goods_ids=[];
        $goods_attr_ids=[];
        foreach($list as $v){
            //查询所有购物车商品的id  放入数组 批量查询基本属性
           $goods_ids[]=$v['goods_id'];
           //查购物车内所有的商品属性值的id  放入数组  批量查询具体的名字
           $goods_attr_ids=array_merge($goods_attr_ids,explode(',',$v['goods_attr_ids']));
        }
        //去重
        $goods_ids=array_unique($goods_ids);
        //dump($goods_ids);die;
        $goods_attr_ids=array_unique($goods_attr_ids);
        //dump($goods_attr_ids);die;
        //批量查询购物车的商品的基本属性 二维数组
        $goods_data=GoodsModel::where('id', 'in', $goods_ids)->select();
        $goods_data=(new \think\Collection($goods_data))->toArray();
        //批量查询购物车内所有的商品属性值 和属性名  二维数组
        $goodsattr_data=GoodsAttrModel::goodsAttrInfo($goods_attr_ids);
        $goodsattr_data=(new \think\Collection($goodsattr_data))->toArray();
        //dump($goodsattr_data);die;
        //整合数组list 先转化商品基本信息数组结构，将商品id作为下标
        $new_goods_data=[];
        foreach($goods_data as $goods){
            $new_goods_data[$goods['id']]=$goods;
        }
        //dump($new_goods_data);die;
        foreach($list as &$v){
            $v['goods']=$new_goods_data[$v['goods_id']];
        }
        unset($v);
        //整合数组list  先转化商品属性值信息数组结构，以属性值id作为下标   再对购物车的数组，关联对应的商品属性
        $new_goodsattr_data=[];
        foreach($goodsattr_data as $goodsattr){
            //$goodsattr是一条具体的商品属性值和名称
            $new_goodsattr_data[$goodsattr['id']]=$goodsattr;
        }
        //dump($new_goodsattr_data);die;
        //遍历$list 添加goodsattr 的键值对数组 关联属性值信息和购物记录
        foreach($list as &$v){
            $v['goodsattr']=[];
            $temp_ids=explode(',',$v['goods_attr_ids']);
            foreach($temp_ids as $goods_attr_id){
                $v['goodsattr'][]=$new_goodsattr_data[$goods_attr_id];
            }
        }
        unset($v);
        //dump($list);
        //渲染模板
        return view('index',['list'=>$list]);
    }
    //改变购物车商品数量
    public function changenum(){
        $data=request()->param();
        //数据检测
        $rule=[
            'goods_id'=>'require|integer|gt:0',
            'goods_attr_ids'=>'require',
            'number'=>'require|integer|gt:0',
        ];
        $msg=[
            'goods_id.require'=>'请选择商品',
            'goods_id.integer'=>'商品参数不正确',
            'goods_id.gt'=>'商品参数不正确',
            'number.require'=>'商品数量不能为空',
            'number.integer'=>'商品数量不正确',
            'number.gt'=>'商品数量必须大于0',
            'goods_attr_ids'=>'商品属性不能为空'
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        CartModel::changeNum($data['goods_id'],$data['number'],$data['goods_attr_ids']);
        $res=[
            'code'=>10000,
            'msg'=>'success'
        ];
        return json($res);
    }
    //删除购物车记录
    public function delcart(){
        $data=request()->param();
        //数据检测
        $rule=[
            'goods_id'=>'require|integer|gt:0',
            'goods_attr_ids'=>'require',
        ];
        $msg=[
            'goods_id.require'=>'请选择商品',
            'goods_id.integer'=>'商品参数不正确',
            'goods_id.gt'=>'商品参数不正确',
            'goods_attr_ids'=>'商品属性不能为空'
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        CartModel::delCart($data['goods_id'],$data['goods_attr_ids']);
        $res=[
            'code'=>10000,
            'meg'=>'success'
        ];
        return json($res);
    }
    //右上角 购物车 商品个数
    public function shopnum(){
        //判断登录
        if(session('?user_info')){
            $data=CartModel::count('id');
            if(!$data){
                $res=[
                    'code'=>10000,
                    'msg'=>'success',
                    'data'=>0
                ];
                return json($res);
            }
            $res=[
                'code'=>10000,
                'msg'=>'success',
                'data'=>$data
            ];
            return json($res);
        }else{
            $data=cookie('cart')?unserialize(cookie('cart')):[];
            $data=count($data);
            $res=[
                'code'=>10000,
                'msg'=>'success',
                'data'=>$data
            ];
            return json($res);
        }

    }
    //显示cookie 测试用
    public function cookie()
    {
        dump(unserialize(cookie('cart')));die;
    }
}
