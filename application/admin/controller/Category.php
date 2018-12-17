<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Category as CategoryModel;
class Category extends Base
{
    /**
     * 获取子分类
     *根据分类id 查询子分类
     * @return \think\Response
     */
    public function getSubCate($id)
    {
        //接收参数 判断
        if(empty($id)){
            $res=[
                'code'=>10001,
                'msg'=>'参数错误'
            ];
            return json($res);
        }
        $data=CategoryModel::where('pid',$id)->select();
        $res=[
            'code'=>10000,
            'msg'=>'success',
            'data'=>$data
        ];
        //使用框架封装的json()返回 防止报错 也可以使用 return json_encode($res) 有些情况会报错
        return json($res);
    }
//    显示商品分类列表
    public function index()
    {
        $list=CategoryModel::select();
        $list_tree=getTree($list);
//        dump($list_tree);die();
        return view('index',['list_tree'=>$list_tree]);
    }
    //分类新增
    public function create(){
        $cate_one=CategoryModel::where('pid',0)->select();
        return view('create',['cate_one'=>$cate_one]);
    }
    //分类编辑
    public function edit($id){
        $cate_one=CategoryModel::where('pid',0)->select();
        //获取当前分类的信息
        $cate_info=CategoryModel::find($id);
        if($cate_info['pid']==0){
            $this->error('当前分类不是底层分类，无法修改类别');
        }
        return view('edit',[
            'cate_one'=>$cate_one,
            'cate_info'=>$cate_info,
        ]);
    }
    //保存编辑的分类
    public function save(){

    }
    //删除分类
    public function  delete()
    {

    }
}
