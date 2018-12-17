<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use app\home\model\User as UserModel;
class Login extends Controller
{
    /**
     * 显示登录页
     *
     * @return \think\Response
     */
    public function login()
    {
        //临时关闭模板布局
        $this->view->engine->layout(false);
        //渲染模板
        return view();

    }
    //检验登录
    public function dologin()
    {
        $data=request()->param();
        //dump($data);die;
        $rule=[
            'username'=>'require',
            'password'=>'require|length:6.16'
        ];
        $msg=[
            'username.require'=>'账户名不能为空',
            'password.require'=>'密码不能为空',
            'password.length'=>'密码格式不正确',

        ];
        $validate=new \think\Validate($rule,$msg);
        if($validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        $data['password']=encrypt_password($data['password']);
        //由于注册的有手机号 和 邮箱 两种注册方式 验证时需要都要查询
        $res=UserModel::where(function($query)use($data){
            $query->where('phone',$data['username'])->whereOr('email',$data['username']);
        })->where('password',$data['password'])->where('is_check',1)->find();
        if($res){
            //登录成功。设置登录标示
            session('user_info',$res->toArray());
            //迁移cookie中的购物车数据到服务器
            \app\home\model\Cart::cookieTodb();
            //先从session获得跳转地址
            $back_url=session('back_url')?session('back_url'):'home/index/index';
            $this->success('登录成功',$back_url);
        }else{
            $this->error('账户名或者密码错误');
        }
    }
    //前台用户退出登录
    public function logout(){
        //清空登录标示
        session(null);
        $this->redirect('home/index/index');
    }
    //QQ登录
    public function qqcallback()
    {
        require_once './plugins/qq/API/qqConnectAPI.php';
        $qc=new \QC();
        $access_tolen=$qc->qq_callback();
        $openid=$qc->get_openid();
        //重新实例化QC类 获取用户信息
        $qc=new \QC($access_tolen,$openid);
        $info=$qc->get_user_info();
        //自动注册登录
        //先查询用户以前是否登陆过
        $user=UserModel::where('openid',$openid)->find();
        if($user){
            //登陆过 更新信息（昵称等）
            $user->username=$info['nickname'];
            $user->save();
        }else{
            //没登录过 新用户 添加到数据表
            UserModel::create(['openid'=>$openid,'username'=>$info['nickname']]);
        }
        //自动登录 设置登录标识
        //这里因为上一步 有更新信息 或者添加 需要重新查询$user信息
        $user=UserModel::where('openid',$openid)->find();
        session('user_info',$user->toArray());
        //迁移cookie中的购物车数据到服务器
        \app\home\model\Cart::cookieTodb();
        //先从session获得跳转地址
        $back_url=session('back_url')?session('back_url'):'home/index/index';
        $this->redirect($back_url);

    }
    //微博登录
    public function weibocallback(){
        //session_start();
        //die;
        include_once( './plugins/weibo/config.php' );
        include_once( './plugins/weibo/saetv2.ex.class.php' );

        $o = new \SaeTOAuthV2( WB_AKEY , WB_SKEY );
        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = WB_CALLBACK_URL;
            try {
                $token = $o->getAccessToken( 'code', $keys ) ;
            } catch (OAuthException $e) {
            }
        }
        //获取微博用户信息
        if (isset($token)) {
            $c = new \SaeTClientV2( WB_AKEY , WB_SKEY , $token['access_token'] );
            $uid_get = $c->get_uid();
            $uid = $uid_get['uid'];
            $user_message = $c->show_user_by_id($uid);//根据ID获取用户等基本信息
            //$info=$o->get_token_info();
            //dump($user_message);die;
        }else{
            $this->redirect('home/login/login');
        }
        //自动注册登录
        //先查询用户以前是否登陆过
        $user=UserModel::where('openid',$uid)->find();
        if($user){
            //登陆过 更新信息（昵称等）
            $user->username=$user_message['name'];
            $user->save();
        }else{
            //没登录过 新用户 添加到数据表
            UserModel::create(['openid'=>$uid,'username'=>$user_message['name']]);
        }
        //自动登录 设置登录标识
        //这里因为上一步 有更新信息 或者添加 需要重新查询$user信息
        $user=UserModel::where('openid',$uid)->find();
        session('user_info',$user->toArray());
        //迁移cookie中的购物车数据到服务器
        \app\home\model\Cart::cookieTodb();
        //先从session获得跳转地址
        $back_url=session('back_url')?session('back_url'):'home/index/index';
        $this->redirect($back_url);
    }
    /**
     * 显示手机注册页.
     *
     * @return \think\Response
     */
    public function register()
    {
        //临时关闭模板布局
        $this->view->engine->layout(false);
        //渲染模板
        return view();
    }
    //发送验证码
    public function sendmsg()
    {
        //接收参数
        $phone=request()->param('phone');
        if(!preg_match('/1[3-9]\d{9}/',$phone)){
            //验证手机号
            $res=[
                'code'=>10001,
                'msg'=>'手机号格式错误'
            ];
            return json($res);
        }
        //发送频率检测
        $register_time=cache('register_time_'.$phone)?:0;
        if(time()-$register_time<60){
            $res=[
              'code'=>10003,
                'msg'=>'发送太频繁，请稍后再试',
            ];
            return json($res);
        }
        //处理数据 （发送短信）
        //生成一个验证码
        $code=mt_rand(1000,9999);
        $msg="【创信】你的验证码是：{$code}，3分钟内有效！";

        //调用封装的发送短信的函数
        //$result=sendmsg($phone,$msg);
        //echo $result;
        $result=true;       //开发阶段，不需要真正使用
        //判断返回的结果
        if($result===true){
            //验证码发送成功
            //将验证码存到缓存 用于后面你的表单提交验证  设置有限期
            cache('register_code_'.$phone,$code,300);
            //记录发送时间
            cache('register_time_' . $phone, time());
            $res=[
                'code'=>10000,
                'msg'=>'短信发送成功',
                'data'=>$code,          //开发测试阶段 使用 上线前必须拿掉
            ];
            return json($res);
        }else{
            $res=[
                'code'=>10002,
                'msg'=>'短信发送失败'
            ];
            return json($res);
        }
    }
    //手机号注册
    public function phone(){
        $data=request()->param();
            //参数检测 表单验证
        $rule=[
            'phone'=>'require|regex:^1[3-9]\d{9}$|unique:user',
            'code'=>'require|regex:^\d{4}$',
            'password'=>'require|length:6,16|confirm:repassword'
        ];
        $msg=[
            'phone.require'=>'手机号不能为空',
            'phone.regex'=>'手机号格式不正确',
            'phone.unique'=>'手机号已被注册',
            'code.require'=>'验证码不能为空',
            'code.regex'=>'验证码格式不正确',
            'password.require'=>'密码不能为空',
            'password.length'=>'密码长度必须是6~16位',
            'password.confirm'=>'两次密码必须输入一致',
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        //检测验证码
        $code=cache('register_code_'.$data['phone']);
        //dump($code);die;
        if($code!=$data['code']){
            $this->error('验证码错误');
        }
        cache('register_code_'.$data['phone'],null);
        $data['password']=encrypt_password($data['password']);
        $data['username']=$data['phone'];
        $data['is_check']=1;
        UserModel::create($data,true);
        $this->success('恭喜你注册成功！请登录！','login');
    }
    //邮箱注册页面显示
    public function register_email()
    {
        //临时关闭模板布局
        $this->view->engine->layout(false);
        //渲染模板
        return view();
    }
    //邮件验证
    public function email()
    {
        //接收参数并验证
        $data=request()->param();
        $rule=[
            'email'=>'require|email|unique:user',
            'password'=>'require|length:6,16|confirm:repassword'
        ];
        $msg=[
            'email.require'=>'邮箱地址不能为空',
            'email.email'=>'邮箱格式不正确',
            'password.require'=>'密码不能为空',
            'password.length'=>'密码长度必须是6~16位',
            'password.confirm'=>'两次密码必须输入一致',
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        //向用户表添加数据
        $data['password']=encrypt_password($data['password']);          //  密码加密
        $data['username']=$data['email'];
        //生成邮箱验证码
        $data['email_code']=mt_rand(1000,9999);
        $user=UserModel::create($data,true);
        //发送激活邮件
        $subject='黑马品优购商城账号激活邮件';
        $url=url('home/login/jihuo',['id'=>$user->id,'code'=>$data['email_code']],true,true);
        $body="欢迎注册黑马品优购商城！请点击以下链接：<br><a href='{$url}'>点此激活</a></br>激活账号继续使用";
        $res=sendmail($data['email'],$subject,$body);
        if($res){
            $this->success('注册成功！请到邮箱激活后登录本网站','login');
        }else{
            $this->error('激活邮件发送失败！请联系客服或重新注册');
        }
    }
    //邮件激活账号
    public function jihuo()
    {
        //接收参数并检测
        $data=request()->param();
        if(!preg_match('/^\d+$/',$data['id']) || !preg_match('/^\d+$/',$data['code'])){
            $this->error('参数错误');
            return;
        }
        $user=UserModel::where(['id'=>$data['id'],'email_code'=>$data['code']])->find();
        if($user){
            $user->is_check=1;
            $user->save();
            $this->success('账号激活成功','login');
        }else{
            $this->error('账户激活是失败！','home\index\index');
        }
    }










    //模拟接口编写，提供接口给别人调用
    public function testapi(){
        $data=request()->param();
        //参数验证
        //参数处理
        //返回数据
        $res=[
            'code'=>10000,
            'msg'=>'success',
            'data'=>$data
        ];
        return json($res);
    }
    //模拟接口调用， 假设这个方法和testapi是在两个不同项目中
    public function testrequest(){
        //发送请求调用接口
        $url="http://www.tpshop.com/home/login/testapi";
        $params=[
            'id'=>100,
            'username'=>'admin'
        ];
        //调用curl_request()发送请求
        $res=curl_request($url,true,$params);
        dump($res);die;
    }
    public function testemail(){
        $email='2630880613@qq.com';
        $subject='测试邮件';
        $body='这是测试的，收到就行了';
        $res=sendmail($email,$subject,$body);
        dump($res);die;
    }
}
