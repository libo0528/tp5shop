<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:69:"D:\wamp\www\tpshop\public/../application/admin\view\goods\create.html";i:1541501054;s:53:"D:\wamp\www\tpshop\application\admin\view\layout.html";i:1541416893;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>后台管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="/static/admin/css/main.css" rel="stylesheet" type="text/css"/>
    <link href="/static/admin/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/static/admin/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
    <script src="/static/admin/js/jquery-1.8.1.min.js"></script>
    <script src="/static/admin/js/bootstrap.min.js"></script>
</head>

<body>
<!-- 上 -->
<div class="navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <ul class="nav pull-right">
                <li id="fat-menu" class="dropdown">
                    <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-user icon-white"></i> <?php echo \think\Session::get('manager_info.username'); ?>
                        <i class="icon-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a tabindex="-1" href="<?php echo url('admin/manager/editpsw'); ?>">修改密码</a></li>
                        <li class="divider"></li>
                        <li><a tabindex="-1" href="<?php echo url('admin/login/logout'); ?>">安全退出</a></li>
                    </ul>
                </li>
            </ul>
            <a class="brand" href="index.html"><span class="first">后台管理系统</span></a>
            <ul class="nav">
                <li class="active"><a href="<?php echo url('admin/index/index'); ?>">首页</a></li>
                <li><a href="javascript:void(0);">系统管理</a></li>
                <li><a href="<?php echo url('admin/auth/index'); ?>">权限管理</a></li>
            </ul>
        </div>
    </div>
</div>
<!-- 左 -->
<div class="sidebar-nav">
    <?php foreach($top_nav as $k=>$top_v): ?>
    <a href="#error-menu<?php echo $k; ?>" class="nav-header collapsed" data-toggle="collapse"><i class="icon-exclamation-sign"></i><?php echo $top_v['auth_name']; ?></a>
    <ul id="error-menu<?php echo $k; ?>" class="nav nav-list collapse in">
        <?php foreach($second_nav as $second_v): if(($second_v['pid']==$top_v['id'])): ?>
        <li><a href="<?php echo url($second_v['auth_c'].'/'.$second_v['auth_a']); ?>"><?php echo $second_v['auth_name']; ?></a></li>
        <?php endif; endforeach; ?>
    </ul>
    <?php endforeach; ?>
    <a href="#dashboard-menu" class="nav-header" data-toggle="collapse"><i class="icon-exclamation-sign"></i>系统管理</a>
    <ul id="dashboard-menu" class="nav nav-list collapse">
        <li><a href="<?php echo url('admin/manager/editpsw'); ?>">密码修改</a></li>
    </ul>
</div>
    <script type="text/javascript" charset="utf-8" src="/plugins/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/plugins/ueditor/ueditor.all.min.js"> </script>
    <script type="text/javascript" charset="utf-8" src="/plugins/ueditor/lang/zh-cn/zh-cn.js"></script>
    <!-- 右 -->
    <div class="content">
        <div class="header">
            <h1 class="page-title">商品新增</h1>
        </div>

        <!-- add form -->
        <form action="<?php echo url('save'); ?>" method="post" id="tab" enctype="multipart/form-data">
            <ul class="nav nav-tabs">
              <li role="presentation" class="active"><a href="#basic" data-toggle="tab">基本信息</a></li>
              <li role="presentation"><a href="#desc" data-toggle="tab">商品描述</a></li>
              <li role="presentation"><a href="#attr" data-toggle="tab">商品属性</a></li>
              <li role="presentation"><a href="#pics" data-toggle="tab">商品相册</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="basic">
                    <div class="well">
                        <label>商品名称：</label>
                        <input type="text" name="goods_name" value="" class="input-xlarge">
                        <label>商品价格：</label>
                        <input type="text" name="goods_price" value="" class="input-xlarge">
                        <label>商品数量：</label>
                        <input type="text" name="goods_number" value="" class="input-xlarge">
                        <label>商品logo：</label>
                        <input type="file" name="goods_logo" value="" class="input-xlarge">
                        <label>商品分类：</label>
                        <select name="" id="cate_one" class="input-xlarge">
                            <option value="">请选择一级分类</option>
                            <?php foreach($cate_one as $v): ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo $v['cate_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="" id="cate_two" class="input-xlarge">
                            <option value="">请选择二级分类</option>
                        </select>
                        <select name="cate_id" id="cate_three" class="input-xlarge">
                            <option value="">请选择三级分类</option>
                        </select>
                    </div>
                </div>
                <div class="tab-pane fade in" id="desc">
                    <div class="well">
                        <label>商品简介：</label>
                        <textarea  id="editor" name="goods_introduce" class="input-xlarge"
                                   style="width: 800px;height: 500px;"></textarea>
                    </div>
                </div>
                <div class="tab-pane fade in" id="attr">
                    <div class="well">
                        <label><b>商品类型：</b></label>
                        <select name="type_id" class="input-xlarge">
                            <option value="">请选择</option>
                            <?php foreach($type as $v): ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo $v['type_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="attrs">
                            <!--<label>商品品牌：</label>-->
                            <!--<input type="text" name="" value="" class="input-xlarge">-->
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade in" id="pics">
                    <div class="well">
                        <div>[<a href="javascript:void(0);" class="add">+</a>]商品图片：<input type="file" name="goods_pics[]" value="" class="input-xlarge"></div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">保存</button>
                <a class="btn" href="<?php echo url('index'); ?>">返回</a>
            </div>
        </form>
        <!-- footer -->
        <footer>
            <hr>
            <p>© 2018 <a href="javascript:void(0);" target="_blank">
                <?php echo \think\Session::get('manager_info.username'); ?></a></p>
        </footer>
    </div>
    <script type="text/javascript">
        $(function(){
            UE.getEditor('editor');
            $('.add').click(function(){
                var add_div = '<div>[<a href="javascript:void(0);" class="sub">-</a>]商品图片：' +
                    '<input type="file" name="goods_pics[]" value="" class="input-xlarge"></div>';
                $(this).parent().after(add_div);
            });
            $('.sub').live('click',function(){
                $(this).parent().remove();
            });
            //绑定一级分类的改变事件 根据一级分类获取二级分类
            $('#cate_one').change(function(){
                //先还原二三级目录
                $('#cate_two').html(' <option value="">请选择二级分类</option>');
                $('#cate_three').html('<option value="">请选择三级分类</option>');
                //获取一级目录的id
                var id=$(this).val();
                //判断id 没选择的时候为空
                if(id==''){return};
                var data={
                    'id':id
                }
                $.ajax({
                    'type':'get',
                    'url':"<?php echo url('admin/category/getsubcate'); ?>",
                    'data':data,
                    'dataType':'json',
                    'success':function(res){
//                        接受数据成功后处理
                        if(res.code!=10000){
                            alert(res.msg);return;
                        }
                        var str='<option value="">请选择二级分类</option>';
                        $.each(res.data ,function(i,v){
                            //v是json对象 是具体的二级数据
                            str+='<option value="'+v.id+'">'+v.cate_name+'</option>';
                        });
                        $('#cate_two').html(str);
                    }
                });
            })
            //绑定二级分类的改变事件 根据二级分类获取三级分类
            $('#cate_two').change(function(){
                //先还原二三级目录
                $('#cate_three').html('<option value="">请选择三级分类</option>');
                //获取一级目录的id
                var id=$(this).val();
                //判断id 没选择的时候为空
                if(id==''){return};
                var data={
                    'id':id
                }
                $.ajax({
                    'type':'get',
                    'url':"<?php echo url('admin/category/getsubcate'); ?>",
                    'data':data,
                    'dataType':'json',
                    'success':function(res){
//                        接受数据成功后处理
                        if(res.code!=10000){
                            alert(res.msg);return;
                        }
                        var str='<option value="">请选择三级分类</option>';
                        $.each(res.data ,function(i,v){
                            //v是json对象 是具体的二级数据
                            str+='<option value="'+v.id+'">'+v.cate_name+'</option>';
                        });
                        $('#cate_three').html(str);
                    }
                });
            })
            $("select[name='type_id']").change(function(){
                $('#attrs').html('');
                //获取选中商品类型的id
                var type_id=$(this).val();
//                console.log(type_id);
                if(type_id==''){return}
                //发送ajax请求 显示下方属性内容
                var data={'type_id':type_id}
                $.ajax({
                    'url':"<?php echo url('admin/attribute/getattr'); ?>",
                    'type':'post',
                    'data':data,
                    'dataType':'json',
                    'success':function(res){
                        if(res.code!=10000){
                            alert(res.msg);return;
                        }
                        //遍历返回的结果集 显示内容
                        var str='';
//                        console.log(res);
                        $.each(res.data,function(i,v){
                            //v 是一条属性对象
                            str+='<label><b>'+v.attr_name+'：</b></label>';
                            //判断attr_inout_type的类型
                            if(v.attr_input_type=='0'){
                                //input输入框
                                str+='<input type="text" name="attr_value['+v.id+'][]" value="" class="input-xlarge">'
                            }else if(v.attr_input_type=='1'){
                                //下拉列表
                                str+='<select name="attr_value['+v.id+'][]">';
                                $.each(v.attr_values,function(key,value){
                                    str+='<option value="'+ value +'">'+value+'</option>'
                                });
                                str+='</select>';
                            }else{
                                //多选框
                                $.each(v.attr_values,function(key,value){
                                    str+='<input type="checkbox" name="attr_value['+v.id+'][]" value="'+value+'">'+value+'';
                                });
                            }
                        });
                        $('#attrs').html(str);


                    }
                });
            });
        });
    </script>

</body>
</html>