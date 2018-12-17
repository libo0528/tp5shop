<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:73:"D:\wamp\www\tpshop\public/../application/admin\view\attribute\create.html";i:1541422001;s:53:"D:\wamp\www\tpshop\application\admin\view\layout.html";i:1541416893;}*/ ?>
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

    <!-- 右 -->
    <div class="content">
        <div class="header">
            <h1 class="page-title">商品属性新增</h1>
        </div>

        <div class="well">
            <!-- edit form -->
            <form id="tab" action="<?php echo url('save'); ?>" method="post">
                <label>属性名称：</label>
                <input type="text" name="attr_name" value="" class="input-xlarge">
                <label>商品类型：</label>
                <select name="type_id" class="dfinput">
                    <option value="">--请选择--</option>
                    <?php foreach($type as $v): ?>
                    <option value="<?php echo $v['id']; ?>"><?php echo $v['type_name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <label>属性类型：</label>
                <span>
                    <input type="radio" name="attr_type" value="0" checked="checked">唯一属性&emsp;
                </span>
                <span>
                    <input type="radio" name="attr_type" value="1">单选属性
                </span>
                <label>录入方式：</label>
                <span class="input_type">
                    <input type="radio" name="attr_input_type" value="0" checked="checked">输入框&emsp;
                </span>
                <span class="input_type">
                    <input type="radio" name="attr_input_type" value="1">下拉列表
                </span>
                <span class="input_type hide">
                    <input type="radio" name="attr_input_type" value="2">多选框
                </span>
                <label>可选值：</label>
                <textarea name="attr_values" placeholder="请输入可选值，多个值之间请使用英文“,”分隔开" class="textinput"></textarea>
                <label></label>
                <button class="btn btn-primary" type="submit">保存</button>
                <a class="btn" href="<?php echo url('index'); ?>">返回</a>

            </form>
        </div>
        <!-- footer -->
        <footer>
            <hr>
            <p>© 2017 <a href="javascript:void(0);" target="_blank">ADMIN</a></p>
        </footer>
    </div>
    <script type="text/javascript">
        $(function(){
            $('input[name=attr_type]').change(function(){
                $('.input_type').toggleClass('hide');
            })
        })
    </script>

</body>
</html>