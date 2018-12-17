<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Auth as AuthModel;
use app\admin\model\Role as RoleModel;
class Auth extends Base
{
    /**
     * 显示权限列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $list= AuthModel::select();
//        树形结构显示
        $list_tree=getTree($list);
        //渲染模板
        return view('index',['list_tree'=>$list_tree]);
    }

    /**
     * 修改权限.
     *
     * @return \think\Response
     */
    public function create()
    {
        $auth_list=AuthModel::select();
        $auth_list=getTree($auth_list);
        //渲染模板
        return view('create',['auth_list'=>$auth_list]);
    }

    /**
     * 管理分派权限
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收数据并检验
        $data=$request->param();
        $rule=[
            'auth_name'=>'require|max:50',
            'pid'=>'require|integer',
            'is_nav'=>'require|integer',
        ];
        $msg=[
            'auth_name.require'=>'权限名字不能为空',
            'auth_name.max'=>'权限名字不能超过50个字符',
//            'pid.require'=>'上级权限不能为空',
//            'pid.gt'=>'上级权限参数错误',
            'is_nav.integer'=>'是否菜单项 参数错误'
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        AuthModel::create($data,true);
        $this->success('添加权限成功','index');
    }
    /**
     * 编辑权限.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit(Request $request,$id)
    {
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        $info= AuthModel::find($id);
        $auth_list=AuthModel::where('pid',0)->select();
        //渲染模板
        return view('edit',[
            'info'=>$info,
            'auth_list'=>$auth_list
        ]);
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
        //接收数据并检验
        $data=$request->param();
//        dump($data['auth_name']);die;
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        $rule=[
          "auth_name"=>'require|max:50',
          "pid"=>'require|integer',
          "is_nav"=>'require|integer',
//          "auth_c"=>'require|max:50',
//          "auth_a"=>'require|max:50',
        ];
        $msg=[
            'auth_name.require'=>'权限名字不能为空',
            'auth_name.max'=>'权限名字不能超过50个字符',
            'pid.require'=>'请选择上级权限',
            'pid.integer'=>'上级权限选择不正确',
            'is_nav.require'=>'请选择菜单控制',
            'is_nav.require'=>'菜单控制参数格式不正确',
    /*        'auth_c.require'=>'控制器名字不能为空',
            'auth_c.max'=>'控制器名字不能超过50个字符',
            'auth_a.require'=>'方法名字不能为空',
            'auth_a.max'=>'方法名字不能超过50个字符',*/
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        AuthModel::update($data,['id'=>$id],true);
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
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        //软删除
        AuthModel::destroy($id);
        $this->success('删除成功','index');
    }
}
