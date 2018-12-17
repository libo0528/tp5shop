<?php

namespace app\home\controller;

use app\admin\model\GoodsAttr;
use think\Controller;
use think\Request;
use app\home\model\Goods as GoodsModel;
use app\home\model\Attribute as AttributeModel;
use app\home\model\Type as TypeModel;
use app\home\model\GoodsAttr as GoodsAttrModel;
use app\home\model\Goodspics as GoodspicsModel;
use app\home\model\Category as CategoryModel;
class Goods extends Base
{
    /**
     * 显示商品列表
     *
     * @return \think\Response
     */
    public function index($id)
    {
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        //查询当前分类
        $cate=CategoryModel::find($id);
        //查询当前分类的商品列表
        $list=GoodsModel::where([
            'cate_id'=>$id,
            ])->order('id desc')->paginate(5);
        $total=GoodsModel::count('id');
        $totalpage=ceil($total/5);
        //渲染模板
        return view('index',[
            'cate'=>$cate,
            'list'=>$list,
            'totalpage'=>$totalpage,
        ]);
    }

    /**
     * 显示商品详情
     *
     * @return \think\Response
     */
    public function detail($id)
    {
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
//        dump($id);die;
        //商品基本信息
        $goods=GoodsModel::find($id);
//        dump($goods);die;
        //商品相册
        $goodspics=GoodspicsModel::where('goods_id',$id)->select();
//        dump($goodspics);die;
        //商品属性名称展示
        $attribute=AttributeModel::where('type_id',$goods->type_id)->select();
//        $aa=new \think\Collection($attribute);
//        dump($aa->toArray()['id']);die;
//        dump($attribute['id']);die;
        //查询商品所有的属性值
        $goodsattr=GoodsAttrModel::where('goods_id',$id)->select();
        //dump($goodsattr);die();
        //转化属性值数组结构 方便前台遍历 [  属性id=>[属性值1，[属性值2]] ]
        $new_goodsattr=[];
        foreach($goodsattr as $v){
//            if (in_array($attribute['attr_id'],$goodsattr['attr_id']->toArray())){
                $new_goodsattr[$v['attr_id']][]=($v->toArray());
        }
        //dump($new_goodsattr);die();
        //渲染模板
        return view('detail',[
            'goods'=>$goods,
            'goodspics'=>$goodspics,
            'attribute'=>$attribute,
            'new_goodsattr'=>$new_goodsattr,
        ]);
    }
    public function read($id)
    {
        //
    }

}
