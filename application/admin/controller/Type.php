<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Type as TypeModel;
class Type extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //渲染模板
        $list=TypeModel::select();
        return view('index',['list'=>$list]);
    }

    /**
     * 显示创建资源表单页.
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
        //接收数据
        $data=$request->param();
        if(!isset($data['type_name']) || empty($data['type_name'])){
            $this->error('商品类型格式不正确');
        }
        TypeModel::create($data,true);
        $this->success('操作成功','index');
    }
    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //检验数据
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        $info=TypeModel::find($id);
        return view('edit',['info'=>$info]);
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
        $data=$request->param();
        //表单数据验证
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        $rule=[
            'type_name'=>'require|max:100',
        ];
        $msg=[
            'type_name.require'=>'商品名称不能为空',
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        TypeModel::update($data,['id'=>$id],true);
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
        //表单数据验证
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        TypeModel::destroy($id);
        $this->success('删除成功','index');
    }
}
