<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Test extends Controller
{
    public function test()
    {
        $goods= \think\Db::table('tpshop_goods')->where('id','<','40')->select();
//        dump($goods);
//        $data=['goods_name'=>'桃','goods_price'=>4.5];
//        $res=\think\Db::table('tpshop_goods')->insert($data);
//        $res=\think\Db::table('tpshop_goods')->update(['goods_name'=>'梨','id'=>40]);
        $res=\think\Db::table('tpshop_goods')->delete([41,42,43]);
        dump($res);
    }

}
