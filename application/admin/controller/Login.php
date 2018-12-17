<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Manager as ManagerModel;
class Login extends Controller
{
    /**
     * 显示后台登录页
     *
     * @return \think\Response
     */
    public function login()
    {
        if(request()->isGet()){
            //临时关闭模板布局
            $this->view->engine->layout(false);
            //渲染模板
            return view();
        }
        $data=request()->param();
        //定义验证规则
        $rule=[
            'username'=>'require',
            'password'=>'require|length:6,16'
        ];
        $msg=[
            'username.require'=>'用户名不能为空',
            'password.require'=>'密码不能为空',
            'password.length'=>'密码长度必须在6~16个字符之间'
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        //验证码  先校验验证码 可以提前避免数据库压力
        if(!captcha_check($data['code'])){
            //验证码错误 跳转
            $this->error('验证码错误');
        }
        $password=encrypt_password($data['password']);          //密码加密
        //查询数据库
        $res=ManagerModel::where([
            'username'=>$data['username'],
            'password'=>$password
        ])->find();
//        dump($res->toArray());die;
        if($res){
            session('manager_info',$res->toArray());
//            dump(session('manager_info')['username']);die;
            $this->success(session('manager_info')['username'].'登录成功',
                'admin/index/index');
        }else{
            $this->error('用户名或者密码错误');
        }
    }

    /**
     * 退出登录功能.
     *
     * @return \think\Response
     */
    public function logout()
    {
        //清除session
        session(null);
        $this->redirect('admin/login/login');
    }
    //修改密码
    public function editpsw(){
        return view();
    }
    public function update(){
        $info=request()->param();
//        dump($info);die;
        $res1=ManagerModel::where(['password'=>encrypt_password($info['password']),'id'=>$info['id']])
            ->find();
        $res2=ManagerModel::where(['password'=>encrypt_password($info['repassword']),'id'=>$info['id']])
            ->find();
        if(!$res1){
            $this->error('原始密码输入错误');
        }elseif($res2){
            $this->error('新密码和原始密码不能一样');
        }
        //定义验证规则
        $rule=[
            'password'=>'require',
            'repassword'=>'require|length:6,16',
            'newpassword'=>'require|length:6,16'
        ];
        $msg=[
            'password.require'=>'原始密码不能为空',
            'repassword.require'=>'新密码不能为空',
            'newpassword.require'=>'新密码不能为空',
            'repassword.length'=>'密码长度必须在6~16个字符之间'
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($info)){
            $error=$validate->getError();
            $this->error($error);
        }
        $id=$info['id'];
//        dump($id);die;
        ManagerModel::update(['password'=>encrypt_password($info['repassword'])],
            ['id'=>$id],true);
        //清除session
        session(null);
        $this->success('密码修改成功！即将重新登录！','admin/login/login');
    }
}
