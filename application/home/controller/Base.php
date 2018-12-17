<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Base extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        //查询商品分类信息
        $category=\app\home\model\Category::select();
        $category=(new \think\Collection($category))->toArray();
        //实现分类树结构
        $category=get_cate_tree($category);
        $this->assign('category',$category);
//        dump($category);die;
    }
}
