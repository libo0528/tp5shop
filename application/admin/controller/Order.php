<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Order as OrderModel;
class Order extends Base
{
    /**
     * 显示订单管理页面
     *
     * @return \think\Response
     */
    public function index()
    {
        //渲染模板
        return view();
    }

    /**
     * 显示订单新增单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //渲染模板
        return view();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //渲染模板
        return view();
    }

    /**
     * 显示订单详情
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function detail()
    {
        //渲染模板
        return view();
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
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
