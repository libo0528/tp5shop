<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Role as RoleModel;
use app\admin\model\Auth as AuthModel;
class Role extends Base
{
    /**
     * 显示角色列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //获取数据显示
        $list= RoleModel::select();
        //渲染模板
        return view('index',['list'=>$list]);
    }

    /**
     * 新增角色.
     *
     * @return \think\Response
     */
    public function create()
    {
        //渲染模板
        return view();
    }
    /**
     * 分派权限.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function setauth($id)
    {
        $role=RoleModel::find($id);
        $top_auth=AuthModel::where('pid',0)->select();
        $second_auth=AuthModel::where('pid','gt',0)->select();
        //渲染模板
        return view('setauth',[
            'role'=>$role,
            'top_auth'=>$top_auth,
            'second_auth'=>$second_auth,
        ]);
    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收数据
        $data=$request->param();
        $rule=[
            'role_name'=>'require',
//            'role_auth_ids'=>'require'
        ];
        $msg=[
            'role_name.require'=>'姓名不能为空',
//            'role_auth_ids.require'=>'权限ids不能为空',
        ];
        $validate = new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        RoleModel::create($data,true);
        $this->success('添加成功','index');
    }

    /**
     * 保存分配的权限
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function saveauth()
    {
        //接收数据 这里接收到的id是权限id   一个数组   需要在变量名字后面加/a 变量修饰符 强制转化为数组类型
        $role_id=request()->param('role_id');
        $id=request()->param('id/a');
        //检查数据
        if(!preg_match('/^\d+$/',$role_id)){
            $this->error('参数错误，请重新提交');
        }
        //将权限id 转化为字符串 , 号分开
        $role_auth_ids=implode(',',$id);
        RoleModel::update(['role_auth_ids'=>$role_auth_ids],['id'=>$role_id]);
        $this->success('操作成功','index');
    }
    //显示修改角色页面
    public function edit($id){
        //检查数据
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误，请重新提交');
        }
        $list=RoleModel::find($id);
//        dump($list);die;
        return view('edit',['list'=>$list]);
    }
    //保存修改角色的数据
    public function update(Request $request)
    {
        $id=$request->param('id');
        $data=$request->param();
//        dump($id);die;
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误，请重新提交');
        }
        $rule=[
            'role_name'=>'require|max:30'
        ];
        $msg=[
            'role_name.require'=>'名字不能为空',
            'role_name.length'=>'名字不能超过30个字符',

        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        RoleModel::update($data,['id'=>$id],true);
        $this->success('操作成功','index');
    }
    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        RoleModel::destroy($id);
        $this->success('删除成功','index');
    }
}
