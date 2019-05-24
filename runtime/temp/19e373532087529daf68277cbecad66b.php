<?php /*a:1:{s:32:"../Theme/adminsys/user/info.html";i:1541568140;}*/ ?>
<!DOCTYPE HTML>
<html>

	<head>
		<meta charset="utf-8">
		<meta name="renderer" content="webkit|ie-comp|ie-stand">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
		<meta http-equiv="Cache-Control" content="no-siteapp" />
		<link href="/static/admin/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="/static/admin/css/style.css" />
		<link href="/static/admin/assets/css/codemirror.css" rel="stylesheet">
		<link rel="stylesheet" href="/static/admin/assets/css/ace.min.css" />
		<link rel="stylesheet" href="/static/admin/assets/css/font-awesome.min.css" />
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/static/admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="/static/admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/static/admin/assets/js/jquery.min.js"></script>
		<title>用户查看</title>
	</head>

	<body>
		<div class="member_show">
			<div class="member_jbxx clearfix">
				<img class="img" src="/static/admin/images/user.png">
				<dl class="right_xxln">
					<dt><span class=""><?php echo htmlentities($user['user_name']); ?></span> <span class="">余额：<?php echo htmlentities($user['user_moeny']); ?></span></dt>
					<dd class="" style="margin-left:0">这家伙很懒，什么也没有留下</dd>
				</dl>
			</div>
			<div class="member_content">
				<ul>
					<!--<li><label class="label_name">性别：</label><span class="name">男</span></li>-->
					<li><label class="label_name">手机：</label><span class="name"><?php echo htmlentities($user['user_phone']); ?></span></li>
					<li><label class="label_name">推荐码：</label><span class="name"><?php echo htmlentities($user['user_code']); ?></span></li>
					<li><label class="label_name">注册ip：</label><span class="name"><?php echo htmlentities($user['user_ip']); ?></span></li>
					<li><label class="label_name">地址：</label><span class="name"><?php echo htmlentities($user['user_address']); ?></span></li>
					<li><label class="label_name">注册时间：</label><span class="name"><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($user['user_time'])? strtotime($user['user_time']) : $user['user_time'])); ?></span></li>
					<li><label class="label_name">积分：</label><span class="name"><?php echo htmlentities($user['user_integral']); ?></span></li>
					<li><label class="label_name">等级：</label><span class="name"><?php echo htmlentities(getUserType($user['user_type_id'])); ?></span></li>
				</ul>
			</div>
		</div>
	</body>
</html>