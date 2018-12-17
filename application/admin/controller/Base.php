<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Auth as AuthModel;
use app\admin\model\Role as RoleModel;
use app\admin\model\Manager as ManagerModel;
class Base extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        //登录检测
        if(!session('?manager_info')){
            $this->redirect('admin/login/login');
        }
//        $cache=think\Cache::set('cc',$goods_logo,);
        //调用函数获取菜单权限展示
        $this->getnav();
        //调用检测权限函数
        $this->checkauth();
    }
    //获取左侧菜单权限
    public function getnav()
    {
        //从session中获取当前登录账号的角色id
        $role_id=session('manager_info.role_id');
        //判断是否超级管理员
        if(1==$role_id){
            // 反过来写  1== 可以防止少输入=号  输错编辑器会报错
            //查询顶级菜单
            $top_nav=AuthModel::where([
                'pid'=>0,
                'is_nav'=>1
            ])->select();
            //获取二级菜单
            $second_nav=AuthModel::where([
                'pid'=>['>',0],
                'is_nav'=>1
            ])->select();
        }else{
            //不是超级管理员  需先获取它的权限
            $role=RoleModel::find($role_id);
            $role_auth_ids=$role['role_auth_ids'];
            //查询顶级菜单
            $top_nav=AuthModel::where([
                'pid'=>0,
                'is_nav'=>1,
                'id'=>['in',$role_auth_ids]
            ])->select();
            //获取二级菜单
            $second_nav=AuthModel::where([
                'pid'=>['>',0],
                'is_nav'=>1,
                'id'=>['in',$role_auth_ids]
            ])->select();
        }
//        dump($second_nav);die;
        //直接给模板变量赋值
        $this->assign('top_nav',$top_nav);
        $this->assign('second_nav',$second_nav);
    }
    //检测访问权限
    public function checkauth()
    {
        //从session中获取   角色id
        $role_id=session('manager_info.role_id');
        //超级管理员不用检测
        if(1==$role_id){
             return;
        }
        //特殊情况  首页不用检测 都能访问
        //获取当前访问的控制器和方法
        $controller=request()->controller();
        $action=request()->action();
        if($controller=='Index' && $action=='index'){return;}
        //检测
        $role=RoleModel::find($role_id);
        $role_auth_ids=$role['role_auth_ids'];
        //根据控制器名字和方法名字获取当前访问的 权限id
        $auth=AuthModel::where(['auth_c'=>$controller,'auth_a'=>$action])->find();
        $auth_id=$auth['id'];
        if(!in_array($auth_id,explode(',',$role_auth_ids))){
            $this->error('没有访问权限','admin/index/index');
        }
        return;
    }
}
