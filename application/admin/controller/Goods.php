<?php

namespace app\admin\controller;

use app\admin\model\Goodspics;
use think\Controller;
use think\Request;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Category as CategoryModel;
use app\admin\model\Goodspics as GoodspicsModel;
use app\admin\model\GoodsAttr as GoodsAttrModel;
use app\admin\model\Type as TypeModel;
use app\admin\model\Attribute as AttributeModel;
class Goods extends Base
{
    /**
     * 显示商品列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询列表 需要的数据
//        $list= \app\admin\model\Goods::order('id','desc')->limit('4')->select();
        //分页显示数据
        $list= \app\admin\model\Goods::order('id','desc')->paginate(5);
//        dump($list);die;
        //渲染模板  模板变量赋值
//        $this->assign('list',$list);
        return view('index',['list'=>$list]);
    }

    /**
     * 显示新增商品类型
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function create()
    {
        //查询商品一级分类
        $cate_one=CategoryModel::where('pid',0)->select();
        //查询商品类型 显示到下拉框
        $type=TypeModel::select();
        //渲染模板
        return view('create',[
            'cate_one'=>$cate_one,
            'type'=>$type,
            ]);
    }
    /**
     * 保存新增商品的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收数据
        $data=$request->param();
//        dump($data);die;
        $data['goods_introduce']=$request->param('goods_introduce','','remove_xxs');
        //参数检测 表单验证
        //1、定义验证规则
        $rule=[
            'goods_name'=>'require',
            'goods_price'=>'require|float|gt:0',
            'goods_number'=>'require|integer|gt:0',
            'cate_id'=>'require|integer|gt:0',
            'type_id'=>'require|integer|gt:0'
        ];
        //2、定义提示信息
        $msg=[
            'goods_name.require'=>'商品名称不能为空',
            'goods_price.require'=>'商品价格不能为空',
            'goods_price.float'=>'商品价格格式不正确',
            'goods_price.gt'=>'商品价格必须大于0',
            'goods_number.require'=>'商品数量不能为空',
            'goods_number.integer'=>'商品数量格式不正确',
            'goods_number.gt'=>'商品数量必须大于0',
            'cate_id.require'=>'必须选择商品分类',
            'type_id.gt'=>'商品类型参数不正确',
            'type_id.require'=>'必须选择商品类型',
        ];
        //3、实例化验证类Validate
        $validate=new \think\Validate($rule,$msg);
        //4、执行验证 效用check方法
        if(!$validate->check($data)){
            //验证失败，调用getError方法获取具体的错误信息
            $error=$validate->getError();
            $this->error($error);
        }
        //商品logo图片上传 调用upload_logo函数
        $data['goods_logo']=$this->upload_logo();
//        dump($data);die();
        //给数据库添加数据
        $goods=\app\admin\model\Goods::create($data,true);
        //上传相册
        $this->uploads_pics($goods->id);
        //处理接收到的商品属性
        $attr_values=$data['attr_value'];
        $goodsattr_data=[];
        foreach($attr_values as $k=>$v){
            //$k 是attr_id  $k 是包含了属性值的数组
            foreach($v as $value){
                //$value才是属性值  可以组装一个数组 添加到goods_attr商品属性关联表
                $row=[
                    'goods_id'=>$goods->id,
                    'attr_id'=>$k,
                    'attr_value'=>$value
                ];
                //将组合的新数据数组 添加到数据集  批量添加到商品属性关联表
                $goodsattr_data[]=$row;
            }
        }
        //批量添加
        $goodsattr=new GoodsAttrModel();
        $goodsattr->saveAll($goodsattr_data);
//        跳转页面
        $this->success('添加成功！','index');
    }


    /**
     * 显示商品修改单页.
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
        $goods= \app\admin\model\Goods::find($id);
        $pics=GoodspicsModel::where('goods_id',$id)->select();
//        dump($pics);die;
//        dump($goods->toArray());die;
        //编辑页面立即显示以及一级分类目录
        $cate_one_all=CategoryModel::where('pid',0)->select();
        //根据当前商品的cate_id 查询分类表 获取当前商品的三级分类信息 pid就是当前商品二级分类的id
        $cate_three=CategoryModel::find($goods['cate_id']);     //数组
        //根据当前商品的pid 获取当前商品所属二级分类的信息  pid就是当前商品一级分类的id
        $cate_two=CategoryModel::find($cate_three['pid']);
        //查询当前商品所属一级分类下的 所有二级分类信息 显示在页面二级分类下拉表
        $cate_two_all=CategoryModel::where('pid',$cate_two['pid'])->select();
        //查询当前商品所属三级分类的所有信息 显示在页面三级分类下拉列表
        $cate_three_all=CategoryModel::where('pid',$cate_three['pid'])->select();
        //查询商品类型
        $type=TypeModel::select();
        //查询商品所属商品类型下的信息 显示到页面
        $attribute=AttributeModel::where('type_id',$goods['type_id'])->select();
        //由于使用了获取器 需要获取原始数据 方面在前台判定
        foreach($attribute as &$v){
            $v=$v->getData();
            //将attr_value转化为数组
            $v['attr_values']=explode(',',$v['attr_values']);
        }
        unset($v);          //销货传址的$v 防止影响后面代码
//        dump($attribute);die;
        //查询该商品的具体属性值
        $goodsattr=GoodsAttrModel::where('goods_id',$id)->select();
//        dump($goodsattr);die;//    结果集对象
        //转化结果集 方便遍历显示
        $new_goodsattr=[];
        foreach($goodsattr as $v){
            //$v 是每一条具体的数据对象
            $new_goodsattr[$v['attr_id']][]=$v['attr_value'];
        }
        //遍历属性表 判断商品是否每个属性都有值  没有的默认添加空
        foreach($attribute as $v){
               if(!isset($new_goodsattr[$v['id']])){
                    $new_goodsattr[$v['id']]=[];
                }
        }
        //dump($new_goodsattr);die;
        //渲染模板
        return view('edit',[
            'goods'=>$goods,
            'cate_one_all'=>$cate_one_all,
            'cate_two_all'=>$cate_two_all,
            'cate_two'=>$cate_two,
            'cate_three'=>$cate_three,
            'cate_three_all'=>$cate_three_all,
            'pics'=>$pics,
            'type'=>$type,
            'attribute'=>$attribute,
            'new_goodsattr'=>$new_goodsattr
        ]);
    }
    /**
     * 修改商品信息
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
        //接收数据
        $data=$request->param();
//        dump($data);die;
        //处理提交的商品属性 方便添加的数据表
        $goodsattr_data=[];
        foreach($data['attr_value'] as $k=>$v){
            //$k 是属性id  $v 是数组格式的属性值
            foreach($v as $value){
                $row=[
                    'goods_id'=>$id,
                    'attr_id'=>$k,
                    'attr_value'=>$value
                ];
                //批量添加
                $goodsattr_data[]=$row;
            }
        }
        //dump($goodsattr_data);die;
        //表格里结果一个attr_id有多个 直接修改不行 先删除 在添加
        GoodsAttrModel::where('goods_id',$id)->delete();
        $goodsattr=new GoodsAttrModel();
        $goodsattr->saveAll($goodsattr_data);
        //防止富文本XXS攻击
        $data['goods_introduce']=$request->param('goods_introduce','','remove_xss');
//        dump($data['goods_introduce']);die;
        //验证规则
        $rule=[
            'goods_name'=>'require|max:100',
            'goods_price'=>'require|float|gt:0',
            'goods_number'=>'require|integer|gt:0',
            'cate_id'=>'require|integer|gt:0'
        ];
        $msg=[
            'goods_name.require'=>'商品名称不能为空',
            'goods_name.max'=>'商品名称不能超过100个字符',
            'goods_price.require'=>'商品价格不能为空',
            'goods_price.float'=>'商品价格格式不正确',
            'goods_price.gt'=>'商品价格必须大于0',
            'goods_number.require'=>'商品数量不能为空',
            'goods_number.float'=>'商品数量格式不正确',
            'goods_number.gt'=>'商品数量必须大于0',
            'cate_id.require'=>'必须选择商品分类',
        ];
        $validate=new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error=$validate->getError();
            $this->error($error);
        }
        //商品logo的修改   先判断有没有 再修改数据库
        $file=$request->file('goods_logo');
        if($file){
            $data['goods_logo']=$this->upload_logo();
        }
        $this->uploads_pics($id);
        GoodsModel::update($data,['id'=>$id],true);
        $this->success('操作成功','index');
    }
    //删除相册图片
    public function delpics($id){
        if(!preg_match('/^\d+$/',$id)){
            $res=[
                'code'=>10001,
                'msg'=>'参数错误'
            ];
            return json($res);
        }
        //删除当前id的图片信息
        $pics_sma=GoodspicsModel::where('id',$id)->value('pics_sma');
//        dump($pics_sma);
        $pics_big=GoodspicsModel::where('id',$id)->value('pics_big');
        unlink('.'.$pics_sma);
        unlink('.'.$pics_big);
        \app\admin\model\Goodspics::destroy($id,true);

        $res=[
            'code'=>10000,
            'msg'=>'success'
        ];
        return json($res);
    }
    /**
     * 删除商品
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //验证接收到的id值
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        GoodsModel::destroy($id);
        $this->success('删除成功','index');
    }
    /**
     * 后台商品详情
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function detail($id)
    {
        //验证接收到的id值
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        //渲染模板
        return view();
    }
    //显示回收站的商品列表
    public function recyle()
    {
        $list=GoodsModel::onlyTrashed()->select();
        return view('recyle',['list'=>$list]);
    }
    //查找回收站的商品
    public function searchrecyle(Request $request)
    {
        $keyword=$request->param('keyword');
        $list=GoodsModel::onlyTrashed()->where([
        'goods_name'=>['like',"%{$keyword}%"]
    ])->select();
        return view('recyle',['list'=>$list]);
    }
    //还原回收站的商品
    public function reset($id)
    {
        //验证接收到的id值
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        GoodsModel::update(['delete_time'=>null],['id'=>$id],true);
        $this->success('还原商品成功','recyle');
    } //还原回收站的所有商品
    public function resetall()
    {
        GoodsModel::update(['delete_time'=>null],['delete_time'=>['neq','']],true);
        $this->success('还原商品成功','recyle');
    }
    //物理删除回收站的商品
    public function clear($id)
    {
        //验证接收到的id值
        if(!preg_match('/^\d+$/',$id)){
            $this->error('参数错误');
        }
        GoodsModel::destroy($id,true);
        $this->success('数据库删除商品成功','recyle');
    }
    //清空回收站的商品
    public function clearall()
    {
        GoodsModel::destroy(['delete_time'=>['neq','']],true);
        $this->success('清空回收站成功','recyle');
    }
    //显示查找商品
    public function search(Request $request){
        $keyword=$request->param('keyword');
//        dump($keyword) ;die;
        $list=GoodsModel::where('goods_name','like',"%{$keyword}%")->
        paginate(2,false,['query'=>['keyword'=>$keyword]]);
        return view('index',['list'=>$list]);
    }
    //封装文件上传的函数
    private function upload_logo(){
        //获取到上传的文件  对象
        $file=request()->file('goods_logo');
        //判断是否长传
        if(empty($file))
        {
            $this->error('没有logo图片上传');
        }
        //移动图片到指定目录
        $info=$file->validate(['size'=>5*1024*1024,'ext'=>'jpg,jpeg,gif,png'])
            ->move(ROOT_PATH.'public'.DS.'uploads');
        if($info){
            //上传文件成功 并返回访问路径
            $goods_logo=DS.'uploads'.DS.$info->getSaveName();
//            生成缩略图
            //打开图片文件
            $image=\think\Image::open('.' . $goods_logo);
            //调用thumb方法生成缩略图 save方法保存到指定路径
            $image->thumb(200,240)->save('.' . $goods_logo);
            //添加文字水印
            $image=\think\Image::open('.' . $goods_logo);
            $image->text('黑马制造','.'.DS.'static'.DS.'simhei.ttf',8)
            ->save('.' . $goods_logo);
            return $goods_logo;
        }else{
            //文件上传失败 获取错误信息 跳转
            $error=$file->getError();
            $this->error($error);
        }
    }
    //封装相册图片上传函数
    private function uploads_pics($goods_id){
        //获取上传图片信息
        $files=request()->file('goods_pics');
//        dump($files);die;
        //遍历每个上传的图片
        $goodspics_data=[];
        foreach($files as $file){
            //移动文件到指定文件夹
            $info=$file->validate(['size'=>5*1024*1024,'ext'=>'jpg,jpeg,png,gif'])->
            move(ROOT_PATH.'public'.DS.'uploads');
            //定义图片的访问路径  20181103/daskfjs899s87f.jpg
//            dump($info);die;
            if($info){
                $pics=DS.'uploads'.DS.$info->getSaveName();
                //生成缩略图 在商品详情页显示 big是放大的图片800*800 sma是正常左边图片400*400 尺寸是前端给的
                //1、定义出两个缩略图的保存路径 20181103/thumb_800_daskfjs899s87f.jpg
                $temp=explode(DS,$info->getSaveName());   //$temp[0]是日期 20181103 $temp[1]是 daskfjs899s87f.jpg
                $pics_big=DS.'uploads'.DS.$temp[0].DS.'thumb_800_'.$temp[1];
                $pics_sma=DS.'uploads'.DS.$temp[0].DS.'thumb_400_'.$temp[1];
                //2、生成缩略图
                $image=\think\Image::open('.'.$pics);
                //打开图片后 先生成大缩略图 在生成小缩略图 否则只会出现小缩略图
                $image->thumb(800,800)->save('.'.$pics_big);
                $image->thumb(400,400)->save('.'.$pics_sma);
                // 生成完缩略图 删除原图
                unset($info);
                unlink('.'.$pics);
                //将上传的图片 路径 商品id 保存到数组
                $row=[
                    'goods_id'=>$goods_id,
                    'pics_big'=>$pics_big,
                    'pics_sma'=>$pics_sma
                ];
                //将遍历的得到的数组 放入结果集
                $goodspics_data[]=$row;
            }
        }
        //  批量存到数据表
        $goodspics=new GoodspicsModel();
        $goodspics->saveAll($goodspics_data);
    }

}
