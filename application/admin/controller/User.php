<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\User as UserModel;
class User extends Base
{
    /**
     * 显示会员列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //渲染模板
        $list=UserModel::paginate(3);
        return view('index',['list'=>$list]);
    }

    /**
     * 会员新增.
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
