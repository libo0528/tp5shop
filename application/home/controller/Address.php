<?php

namespace app\home\controller;

use think\Controller;
use think\Request;
use app\home\model\Address as AddressModel;
class Address extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {

    }

    /**
     * 新增收货地址.
     *
     * @return \think\Response
     */
    public function create()
    {

        $data=request()->param();
        $user_id=session('user_info.id');
        //dump($data);die;
        $rule=[
            'consignee'=>'require|length:2,50',
            'address'=>'require|length:2,200',
            'phone'=>'require|regex:^1[3-9]\d{9}$'
        ];
        $msg=[
            'consignee.require'=>'收货人姓名不能为空',
            'consignee.length'=>'收货人姓名不能超过50个字符',
            'address.require'=>'收货地址不能为空',
            'address.length'=>'收货地址不能超过200个字符',
            'phone.require'=>'手机号不能为空',
            'phone>regex'=>'手机号格式不正确'
        ];
        $data['user_id']=$user_id;
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            //$this->error($error);
            $res=[
                'code'=>10001,
                'msg'=>$error
            ];
            return json($res);
        }
        $adr=AddressModel::create($data,true);
//        $this->success('');
        if($adr){
            $res=[
                'code'=>10000,
                'msg'=>'success'
            ];
            return json($res);
        }else{
            $res=[
                'code'=>100021,
                'msg'=>'添加失败'
            ];
            return json($res);
        }
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
     * 删除地址
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if(!preg_match('/\d+/',$id)){
            $this->error('参数错误');
        }
        $del=AddressModel::destroy($id);
        if($del){
            $res=[
                'code'=>10000,
                'msg'=>'success'
            ];
            return json($res);
        }else{
            $res=[
                'code'=>10001,
                'msg'=>'删除失败'
            ];
            return json($res);
        }
    }
}
