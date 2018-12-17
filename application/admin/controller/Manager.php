<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Manager as ManagerModel;
use app\admin\model\Role as RoleModel;
class Manager extends Base
{
    /**
     * 显示管理员列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询管理员信息 展示到页面
//        $list= ManagerModel::select();
        $list=ManagerModel::alias('m')
            ->join('tpshop_role r','m.role_id=r.id','left')
            ->field('m.*,r.role_name')
            ->paginate(3);
        //分页显示列表
//        $list=ManagerModel::paginate(3);
        //渲染模板
        return view('index',['list'=>$list]);
    }

    /**
     * 显示管理员新增单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //渲染模板
        $role=RoleModel::select();
        return view('create',['role'=>$role]);
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
        $data['password']=encrypt_password(123456);
//        dump($data);die;
        //1、定义验证规则
        $rule=[
            'username'=>'require',
            'nickname'=>'require',
            'email'=>'require|email'
        ];
        //2、定义提示信息
        $msg=[
            'username.require'=>'用户名不能为空',
            'nickname.require'=>'昵称不能为空',
            'email.require'=>'邮箱不能为空',
            'email'=>'邮箱格式不正确'
        ];
        //3、实例化验证类Validate
        $validate=new \think\Validate($rule,$msg);
        //4、执行验证 效用check方法
        if(!$validate->check($data)){
            //验证失败，调用getError方法获取具体的错误信息
            $error=$validate->getError();
            $this->error($error);
        }
//        dump($data);die();
        $res1=ManagerModel::where('username','=',$data['username'])->select();
        $res2=ManagerModel::where('nickname','=',$data['nickname'])->select();
        if($res1 || $res2){
            $this->error('该管理员名称或昵称已存在，请重新添加');
        }
        //给数据库添加数据
        \app\admin\model\Manager::create($data,true);
//        跳转页面
        $this->success('添加成功！','index');
    }

    /**
     * 重置密码
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function reset($id)
    {
        //验证接收到的id值
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        ManagerModel::update(['password'=>encrypt_password(123456)],['id'=>$id],true);
        $this->success('重置密码成功','index');
    }

    /**
     * 编辑管理员信息.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //验证接收到的id值
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        $info= ManagerModel::find($id);
        $role=RoleModel::select();
        //渲染模板
        return view('edit',[
            'info'=>$info,
            'role'=>$role
        ]);
    }

    /**
     * 管理员编辑页面
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //验证接收到的id值
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        $data=$request->param();
//        dump($data);die;
        $rule=[
            'nickname'=>'require|max:100',
            'email'=>'require|email'
        ];
        $msg=[
            'nickname.require'=>'昵称不能为空',
            'nickname.max'=>'昵称不能超过100个字符',
            'email.require'=>'邮箱不能为空',
            'email'=>'邮箱格式不正确'
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        ManagerModel::update($data,['id'=>$id],true);
        $this->success('操作成功','index');

    }
    /**
     * 删除指定资源
     *软删除 destory() 传第二个参数true 代表真删除
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //验证接收到的id值
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        ManagerModel::destroy($id);
        $this->success('删除成功','index');
    }
    //显示搜索信息
    public function search(Request $request)
    {
        $keyword=input('get.')['keyword'];
//        dump($keyword);die;
        $list=ManagerModel::where('username','like',"%{$keyword}%")->
        paginate(3,false,['query'=>['keyword'=>$keyword]]);
        return view('index',['list'=>$list]);
    }
    //修改密码
     public function editpsw(){
         return view('editpsw');
     }
     public function updatepsw(){
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
