<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"D:\wamp\www\tpshop\public/../application/home\view\login\register.html";i:1541723697;}*/ ?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<title>个人注册</title>

    <link rel="stylesheet" type="text/css" href="/static/home/css/all.css" />
    <link rel="stylesheet" type="text/css" href="/static/home/css/pages-register.css" />
    
	<script type="text/javascript" src="/static/home/js/all.js"></script>
	<script type="text/javascript" src="/static/home/js/pages/register.js"></script>
</head>

<body>
	<div class="register py-container ">
		<!--head-->
		<div class="logoArea">
			<a href="" class="logo"></a>
		</div>
		<!--register-->
		<div class="registerArea">
			<h3>注册新用户<span class="go">我有账号，去<a href="login.html" target="_blank">登陆</a></span>
				<span class="go"><a href="<?php echo url('register_email'); ?>">邮箱注册</a>&emsp;</span></h3>
			<div class="info">
				<form action="<?php echo url('phone'); ?>" method="post" id="reg_form" class="sui-form form-horizontal">
					<div class="control-group">
						<label class="control-label">手机号：</label>
						<div class="controls">
							<input type="text" id="phone" name="phone" placeholder="请输入你的手机号" class="input-xfat input-xlarge">
							<span class="error"></span>
						</div>
					</div>
					<div class="control-group">
						<label for="inputPassword" class="control-label">验证码：</label>
						<div class="controls">
							<input type="text" id="code" name="code" placeholder="验证码" class="input-xfat input-xlarge" style="width:120px">
							<button type="button" class="btn-xlarge" id="dyMobileButton">发送验证码</button>
							<span class="error"></span>
						</div>
					</div>
					<div class="control-group">
						<label for="inputPassword" class="control-label">登录密码：</label>
						<div class="controls">
							<input type="password" id="password" name="password" placeholder="设置登录密码" class="input-xfat input-xlarge">
							<span class="error"></span>
						</div>
					</div>
					<div class="control-group">
						<label for="inputPassword" class="control-label">确认密码：</label>
						<div class="controls">
							<input type="password" id="repassword" name="repassword" placeholder="再次确认密码" class="input-xfat input-xlarge">
							<span class="error"></span>
						</div>
					</div>
					<div class="control-group">
						<label for="inputPassword" class="control-label">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
						<div class="controls">
							<input name="m1" type="checkbox" value="2" checked=""><span>同意协议并注册《品优购用户协议》</span>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"></label>
						<div class="controls btn-reg">
							<a id="reg_btn" class="sui-btn btn-block btn-xlarge btn-danger reg-btn" href="javascript:;">完成注册</a>
						</div>
					</div>
					<div class="controls">


					</div>

				</form>
				<div class="clearfix"></div>
			</div>
		</div>
		<!--foot-->
		<div class="py-container copyright">
			<ul>
				<li>关于我们</li>
				<li>联系我们</li>
				<li>联系客服</li>
				<li>商家入驻</li>
				<li>营销中心</li>
				<li>手机品优购</li>
				<li>销售联盟</li>
				<li>品优购社区</li>
			</ul>
			<div class="address">地址：北京市昌平区建材城西路金燕龙办公楼一层 邮编：100096 电话：400-618-4000 传真：010-82935100</div>
			<div class="beian">京ICP备08001421号京公网安备110108007702
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	$(function(){
		$('#reg_btn').click(function(){
			var flag=0;
			//获取手机号input的值
			var phone=$('#phone').val();
			var phone_pattern=/^1[3-9]\d{9}$/;
			// alert('123');
			if(phone==''){
				$('#phone').next().html('手机号码不能为空');
				flag++;
			}else if(!phone_pattern.test(phone)){
				//手机号格式不正确
				$('#phone').next().html('手机号格式不正确');
				flag++;
			}else{
				$('#phone').next().html('');
			}
			// 验证码
			var code=$('#code').val();
			if(code==''){
				$('#code').parent().find('.error').html('验证码不能为空');
				flag++;
			}else{
				$('#code').parent().find('.error').html('');
			}
			//密码
			var password=$('#password').val();
			if(password==''){
				$('#password').parent().find('.error').html('密码不能为空');
				flag++;
			}else{
				$('#password').parent().find('.error').html('');
			}
			//验证密码
			var repassword=$('#repassword').val();
			if(repassword==''){
				$('#repassword').parent().find('.error').html('确认密码不能为空');
				flag++;
			}else if(repassword!=password){
				$('#repassword').parent().find('.error').html('两次密码不同');
				flag++;
			}else{
				$('#repassword').parent().find('.error').html('');
			}
			//提交表单
			// console.log($(':checkbox').prop('checked'));
			if(!flag && $(':checkbox').prop('checked')==true){
				$('#reg_form').submit();
			}
		});
		//验证码倒计时效果
		$('#dyMobileButton').click(function(){
		    var phone=$('#phone').val();
		    if(phone==''){
		        return;
			}
			//console.log(phone);
			var data={'phone':phone};
			//发送ajax请求 请求发送验证码
			$.ajax({
				'url':"<?php echo url('home/login/sendmsg'); ?>",
				'type':'post',
				'data':data,
				'dataType':'json',
				'success':function(res){
				    alert(res.msg);return;
				}
			});
			var time=6;
			//设置定时器
			var interval=setInterval(function(){
				if(time>0){
					time--;
					//改变button的内容
					$('#dyMobileButton').html(time+'s后重新发送验证码');
					// 将button的点击效果设为不可用
					$('#dyMobileButton').attr('disabled',true);
				}else{
					$('#dyMobileButton').html('发送验证码');
					$('#dyMobileButton').attr('disabled',false);
					clearInterval(interval);
				}
			},1000);
		});
	});

</script>
</html>