<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class pay extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //渲染模板
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 支付成功
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function paysuccess($id)
    {
        //渲染模板
        return view();
    }
    /**
     * 支付失败
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function payfail($id)
    {
        //渲染模板
        return view();
    }
    /**
     * 微信支付
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function weixinpay($id)
    {
        //渲染模板
        return view();
    }
}
