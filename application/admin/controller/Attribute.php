<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\Attribute as AttributeModel;
use app\admin\model\Type as TypeModel;
class Attribute extends Base
{
    /**
     * 显示商品属性
     *
     * @return \think\Response
     */
    public function index()
    {
//        $list=AttributeModel::select();
        //联表查询 select t1.*,t2.type_name from attribute lefe join tpshop_type t2 on t1.type_id=t2.id
        $list=AttributeModel::alias('t1')
            ->field('t1.*,t2.type_name')
            ->join('tpshop_type t2','t1.type_id=t2.id','left')
            ->select();
        //渲染模板
        return view('index',[
            'list'=>$list
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //查询商品类型 展示到页面下拉框
        $type=TypeModel::select();
        //渲染模板
        return view('create',[
            'type'=>$type
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
        //接收数据 并验证
        $data=$request->param();
        $rule=[
            'attr_name'=>'require|max:100',
            'type_id'=>'require|gt:0',
            'attr_type'=>'require|egt:0',
            'attr_input_type'=>'require|egt:0',
        ];
        $msg=[
            'attr_name.require'=>'属性名称不能为空',
            'attr_name.max'=>'属性名称不能超过100个字符',
            'type_id.require'=>'商品类型不能为空',
            'type_id.gt'=>'商品类型格式不正确',
            'attr_input_type.require'=>'录入方式不能为空',
            'attr_input_type.egt'=>'录入方式格式不正确',
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        AttributeModel::create($data,true);
        $this->success('添加成功','index');
    }
    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //查询商品类型 展示到页面下拉框
        $type=TypeModel::select();
        //表单数据验证
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        $attr=AttributeModel::find($id);
        $attr=$attr->getData();
//        $attr['attr_type']=$attr['attr_type']->getData();
//        $attr['attr_input_type']=$attr['attr_input_type']->getData();

//        dump($attr);die;
        return view('edit',[
            'type'=>$type,
            'attr'=>$attr,
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
        //表单数据验证
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        //接收数据 并验证
        $data=$request->param();
        $rule=[
            'attr_name'=>'require|max:100',
            'type_id'=>'require|gt:0',
            'attr_type'=>'require|egt:0',
            'attr_input_type'=>'require|egt:0',
        ];
        $msg=[
            'attr_name.require'=>'属性名称不能为空',
            'attr_name.max'=>'属性名称不能超过100个字符',
            'type_id.require'=>'商品类型不能为空',
            'type_id.gt'=>'商品类型格式不正确',
            'attr_input_type.require'=>'录入方式不能为空',
            'attr_input_type.egt'=>'录入方式格式不正确',
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        AttributeModel::update($data,['id'=>$id],true);
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
        AttributeModel::destroy($id);
        $this->success('删除成功','index');
    }
    //商品属性页
    public function getattr($type_id){
        if(!preg_match('/^\d+$/',$type_id)){
            $res=[
                'code'=>10001,
                'msg'=>'参数错误'
            ];
            return json($res);
        }
        $data=AttributeModel::where('type_id',$type_id)->select();
        //对数据进行转化 取得原始数据  这数据使用传址  更新$data里的数据
        foreach($data as &$v){
            $v=$v->getData();
            //对取出的字符串的attr_values 转化成数组 方便在html中遍历
            $v['attr_values']=explode(',',$v['attr_values']);
        }
        //销毁传址的$v 防止对后面代码有影响 节约内存
        unset($v);
        //返回数据
        $res=[
            'code'=>10000,
            'msg'=>'success',
            'data'=>$data
        ];
        return json($res);
    }
}
