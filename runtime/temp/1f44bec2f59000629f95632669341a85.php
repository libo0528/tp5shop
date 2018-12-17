<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:67:"D:\wamp\www\tpshop\public/../application/admin\view\auth\index.html";i:1541302796;s:53:"D:\wamp\www\tpshop\application\admin\view\layout.html";i:1541416893;}*/ ?>
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
<style type="text/css">
    .pagination li{list-style:none;float:left;margin-left:10px;
        padding:0 10px;
        background-color:#5a98de;
        border:1px solid #ccc;
        border-radius: 5px;
        height:26px;
        line-height:26px;
        cursor:pointer;color:#fff;
    }
    .pagination li a{color:white;padding: 0;line-height: inherit;border: none;}
    .pagination li a:hover{background-color: #5a98de;}
    .pagination li.active{background-color:white;color:gray;}
    .pagination li.disabled{background-color:white;color:gray;}
</style>
    <!-- 右 -->
    <div class="content">
        <div class="header">
            <h1 class="page-title">权限列表</h1>
        </div>

        <div class="well">
            <a class="btn btn-primary" href="<?php echo url('admin/auth/create'); ?>">新增</a>
            <!-- table -->
            <table class="table table-bordered table-hover table-condensed">
                <thead>
                    <tr>
                        <th>编号</th>
                        <th>权限名称</th>
                        <th>上级id</th>
                        <th>控制器</th>
                        <th>方法</th>
                        <th>是否菜单项</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($list_tree as $v): ?>
                    <tr class="success">
                        <td><?php echo $v['id']; ?></td>
                        <td><?php echo str_repeat('&nbsp;',$v['level']*8); ?><?php echo $v['auth_name']; ?></td>
                        <td><?php echo $v['pid']; ?></td>
                        <td><?php echo $v['auth_c']; ?></td>
                        <td><?php echo $v['auth_a']; ?></td>
                        <td>
                            <?php if(($v['is_nav']==1)): ?>是<?php else: ?>否<?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo url('admin/auth/edit',['id'=>$v['id']]); ?>"> 编辑 </a>
                            <a href="javascript:void(0);" onclick="if(confirm('确认删除？'))
                                location.href='<?php echo url('admin/auth/delete',['id'=>$v['id']]); ?>'"> 删除 </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- footer -->
        <footer>
            <hr>
            <p>© 2017 <a href="javascript:void(0);" target="_blank">ADMIN</a></p>
        </footer>
    </div>

</body>
</html>