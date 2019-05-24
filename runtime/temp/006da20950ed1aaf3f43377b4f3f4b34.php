<?php /*a:1:{s:34:"../Theme/adminsys/index\index.html";i:1550187480;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title><?php echo htmlentities($config['SITE_NAME']); ?>-管理系统</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link href="/static/admin/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="/static/admin/assets/css/font-awesome.min.css" />
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/static/admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
		<link rel="stylesheet" href="/static/admin/assets/css/ace.min.css" />
		<link rel="stylesheet" href="/static/admin/assets/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="/static/admin/assets/css/ace-skins.min.css" />
		<link rel="stylesheet" href="/static/admin/css/style.css" />
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="/static/admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/static/admin/assets/js/ace-extra.min.js"></script>
		<!--[if lt IE 9]>
		<script src="/static/admin/assets/js/html5shiv.js"></script>
		<script src="/static/admin/assets/js/respond.min.js"></script>
		<![endif]-->
		<!--[if !IE]> -->
		<script src="/static/admin/js/jquery-1.9.1.min.js"></script>
		<!-- <![endif]-->
		<!--[if IE]>
         <script type="text/javascript">window.jQuery || document.write("<script src='/static/admin/assets/js/jquery-1.10.2.min.js'>"+"<"+"script>");</script>
        <![endif]-->
		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='/static/admin/assets/js/jquery.mobile.custom.min.js'>" + "<" + "script>");
		</script>
		<script src="/static/admin/assets/js/bootstrap.min.js"></script>
		<script src="/static/admin/assets/js/typeahead-bs2.min.js"></script>
		<!--[if lte IE 8]>
		  <script src="/static/admin/assets/js/excanvas.min.js"></script>
		<![endif]-->
		<script src="/static/admin/assets/js/ace-elements.min.js"></script>
		<script src="/static/admin/assets/js/ace.min.js"></script>
		<script src="/static/admin/assets/layer/layer.js" type="text/javascript"></script>
		<script src="/static/admin/assets/laydate/laydate.js" type="text/javascript"></script>
		<script src="/static/admin/js/jquery.nicescroll.js" type="text/javascript"></script>

		<script type="text/javascript">
			$(function() {
				var cid = $('#nav_list> li>.submenu');
				cid.each(function(i) {
					$(this).attr('id', "Sort_link_" + i);

				})
			})
			jQuery(document).ready(function() {
				$.each($(".submenu"), function() {
					var $aobjs = $(this).children("li");
					var rowCount = $aobjs.size();
					var divHeigth = $(this).height();
					$aobjs.height(divHeigth / rowCount);
				});
				//初始化宽度、高度

				$("#main-container").height($(window).height() - 76);
				$("#iframe").height($(window).height() - 140);

				$(".sidebar").height($(window).height() - 99);
				var thisHeight = $("#nav_list").height($(window).outerHeight() - 173);
				$(".submenu").height();
				$("#nav_list").children(".submenu").css("height", thisHeight);

				//当文档窗口发生改变时 触发  
				$(window).resize(function() {
					$("#main-container").height($(window).height() - 76);
					$("#iframe").height($(window).height() - 140);
					$(".sidebar").height($(window).height() - 99);

					var thisHeight = $("#nav_list").height($(window).outerHeight() - 173);
					$(".submenu").height();
					$("#nav_list").children(".submenu").css("height", thisHeight);
				});
				$(document).on('click', '.iframeurl', function() {
					var cid = $(this).attr("name");
					var cname = $(this).attr("title");
					$("#iframe").attr("src", cid).ready();
					$("#Bcrumbs").attr("href", cid).ready();
					$(".Current_page a").attr('href', cid).ready();
					$(".Current_page").attr('name', cid);
					$(".Current_page").html(cname).css({
						"color": "#333333",
						"cursor": "default"
					}).ready();
					$("#parentIframe").html('<span class="parentIframe iframeurl"> </span>').css("display", "none").ready();
					$("#parentIfour").html('').css("display", "none").ready();
				});

			});
			/******/
			$(document).on('click', '.link_cz > li', function() {
				$('.link_cz > li').removeClass('active');
				$(this).addClass('active');
			});
			/*******************/
			//jQuery( document).ready(function(){
			//	  $("#submit").click(function(){
			//	// var num=0;
			//     var str="";
			//     $("input[type$='password']").each(function(n){
			//          if($(this).val()=="")
			//          {
			//              // num++;
			//			   layer.alert(str+=""+$(this).attr("name")+"不能为空！\r\n",{
			//                title: '提示框',				
			//				icon:0,				
			//          }); 
			//             // layer.msg(str+=""+$(this).attr("name")+"不能为空！\r\n");
			//             layer.close(index);
			//          }		  
			//     });    
			//})		
			//	});

			/*********************点击事件*********************/
			$(document).ready(function() {
				$('#nav_list,.link_cz').find('li.home').on('click', function() {
					$('#nav_list,.link_cz').find('li.home').removeClass('active');
					$(this).addClass('active');
				});
				//时间设置
				function currentTime() {
					var d = new Date(),
						str = '';
					str += d.getFullYear() + '年';
					str += d.getMonth() + 1 + '月';
					str += d.getDate() + '日';
					str += d.getHours() + '时';
					str += d.getMinutes() + '分';
					str += d.getSeconds() + '秒';
					return str;
				}

				setInterval(function() {
					$('#time').html(currentTime)
				}, 1000);
				//修改密码
				$('.change_Password').on('click', function() {
					layer.open({
						type: 1,
						title: '修改密码',
						area: ['300px', '300px'],
						shadeClose: true,
						content: $('#change_Pass'),
						btn: ['确认修改'],
						yes: function(index, layero) {
							if($("#password").val() == "") {
								layer.alert('原密码不能为空!', {
									title: '提示框',
									icon: 0,

								});
								return false;
							}
							if($("#Nes_pas").val() == "") {
								layer.alert('新密码不能为空!', {
									title: '提示框',
									icon: 0,

								});
								return false;
							}

							if($("#c_mew_pas").val() == "") {
								layer.alert('确认新密码不能为空!', {
									title: '提示框',
									icon: 0,

								});
								return false;
							}
							if(!$("#c_mew_pas").val || $("#c_mew_pas").val() != $("#Nes_pas").val()) {
								layer.alert('密码不一致!', {
									title: '提示框',
									icon: 0,

								});
								return false;
							} else {
								layer.alert('修改成功！', {
									title: '提示框',
									icon: 1,
								});
								layer.close(index);
							}
						}
					});
				});
				$('#Exit_system').on('click', function() {
					layer.confirm('是否确定退出系统？', {
							btn: ['是', '否'], //按钮
							icon: 2,
						},
						function() {
							location.href = "<?php echo Url('Admin/Main/logout'); ?>";
						});
				});
			});

			function link_operating(name, title) {
				var cid = $(this).name;
				var cname = $(this).title;
				$("#iframe").attr("src", cid).ready();
				$("#Bcrumbs").attr("href", cid).ready();
				$(".Current_page a").attr('href', cid).ready();
				$(".Current_page").attr('name', cid);
				$(".Current_page").html(cname).css({
					"color": "#333333",
					"cursor": "default"
				}).ready();
				$("#parentIframe").html('<span class="parentIframe iframeurl"> </span>').css("display", "none").ready();
				$("#parentIfour").html('').css("display", "none").ready();
			}
		</script>
	</head>

	<body>
		<div class="navbar navbar-default" id="navbar">
			<script type="text/javascript">
				try {
					ace.settings.check('navbar', 'fixed')
				} catch(e) {}
			</script>
			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">
					<a href="#" class="navbar-brand">
						<small>					
						<img src="/static/admin/images/logo.png" width="470px">
						</small>
					</a>
					<!-- /.brand -->
				</div>
				<!-- /.navbar-header -->
				<div class="navbar-header operating pull-left">

				</div>
				<div class="navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<span class="time"><em id="time"></em></span><span class="user-info"><small>欢迎光临,</small>超级管理员</span>
								<i class="icon-caret-down"></i>
							</a>
							<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="javascript:void(0" name="<?php echo Url('Admin/System/index'); ?>" title="系统设置" class="iframeurl"><i class="icon-cog"></i>网站设置</a>
								</li>
								<li>
									<a href="javascript:void(0)" name="<?php echo Url('Admin/Admins/Index'); ?>" title="修改密码" class="iframeurl"><i class="icon-user"></i>修改密码</a>
								</li>
								<li class="divider"></li>
								<li>
									<a href="javascript:ovid(0)" id="Exit_system"><i class="icon-off"></i>退出</a>
								</li>
							</ul>
						</li>
						<li class="purple">
							<a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon-bell-alt"></i><span class="badge badge-important"><?php echo htmlentities($tongzhi_count); ?></span></a>
							<?php if($tongzhi_count>0): ?>
							<ul class="pull-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
								<?php if($tongzhi['mission'] !=''): ?>
								<li>
									<a href="javascript:void(0)" name="/admin/repayment/index.html?state=1&type=1" title="权限配置" class="iframeurl">
										<div class="clearfix">
											<span class="pull-left">
												<i class="btn btn-xs no-hover btn-pink icon-credit-card"></i>
												查看异常订单
											</span>
											<span class="pull-right badge badge-info">+<?php echo htmlentities($tongzhi['mission']); ?></span>
										</div>
									</a>
								</li>
								<?php endif; ?>
							</ul>
							<?php endif; ?>
						</li>

					</ul>
					<!-- <div class="right_info">
                 
                   <div class="get_time" ><span id="time" class="time"></span>欢迎光临,管理员</span></div>
					<ul class="nav ace-nav">	
						<li><a href="javascript:ovid(0)" class="change_Password">修改密码</a></li>
                        <li><a href="javascript:ovid(0)" id="Exit_system">退出系统</a></li>
					</ul>
				</div>-->
				</div>
			</div>
		</div>
		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try {
					ace.settings.check('main-container', 'fixed')
				} catch(e) {}
			</script>
			<div class="main-container-inner">
				<a class="menu-toggler" id="menu-toggler" href="#">
					<span class="menu-text"></span>
				</a>
				<div class="sidebar" id="sidebar">
					<script type="text/javascript">
						try {
							ace.settings.check('sidebar', 'fixed')
						} catch(e) {}
					</script>
					<div class="sidebar-shortcuts" id="sidebar-shortcuts">
						<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
							<a class="btn btn-success">
								<i class="icon-signal"></i>
							</a>

							<a class="btn btn-info">
								<i class="icon-pencil"></i>
							</a>

							<a class="btn btn-warning">
								<i class="icon-group"></i>
							</a>

							<a class="btn btn-danger">
								<i class="icon-cogs"></i>
							</a>
						</div>

						<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
							<span class="btn btn-success"></span>

							<span class="btn btn-info"></span>

							<span class="btn btn-warning"></span>

							<span class="btn btn-danger"></span>
						</div>
					</div>
					<!-- #sidebar-shortcuts -->
					<div id="menu_style" class="menu_style">
						<ul class="nav nav-list" id="nav_list">
							<li class="home">
								<a href="javascript:void(0)" name="home.html" class="iframeurl" title=""><i class="icon-home"></i><span class="menu-text"> 系统首页 </span></a>
							</li>
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-cogs"></i><span class="menu-text"> 系统管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)"  name="<?php echo Url('Admin/System/index'); ?>" title="系统设置" class="iframeurl"><i class="icon-double-angle-right"></i>系统设置</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/System/smslog'); ?>" title="短信日志" class="iframeurl"><i class="icon-double-angle-right"></i>短信日志</a>
									</li>
                                 	<li class="home">
                                        <a href="javascript:void(0)" name="<?php echo Url('Admin/Versions/index'); ?>" title="版本控制" class="iframeurl"><i class="icon-double-angle-right"></i>版本控制</a>
                                    </li>
								</ul>
							</li>

							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-group"></i><span class="menu-text"> 管理员管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Admins/addmin'); ?>" title="添加管理员" class="iframeurl"><i class="icon-double-angle-right"></i>添加管理员</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Admins/mypass'); ?>" title="修改密码" class="iframeurl"><i class="icon-double-angle-right"></i>修改密码</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Admins/index'); ?>" title="管理员管理" class="iframeurl"><i class="icon-double-angle-right"></i>管理员管理</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Admins/infolog'); ?>" title="操作记录" class="iframeurl"><i class="icon-double-angle-right"></i>操作记录</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Auths/index'); ?>" title="权限管理" class="iframeurl"><i class="icon-double-angle-right"></i>权限管理</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Roles/roles_list'); ?>" title="权限配置" class="iframeurl"><i class="icon-double-angle-right"></i>权限配置</a>
									</li>

								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-user"></i><span class="menu-text"> 代理商管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Agent/addent'); ?>" title="添加代理商" class="iframeurl"><i class="icon-double-angle-right"></i>添加代理商</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Agent/index'); ?>" title="管理用户" class="iframeurl"><i class="icon-double-angle-right"></i>代理商管理</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Agent/applyagent'); ?>" title="代理商申请" class="iframeurl"><i class="icon-double-angle-right"></i>代理商申请</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Grade/index'); ?>" title="代理商等级" class="iframeurl"><i class="icon-double-angle-right"></i>代理商等级</a>
									</li>
                                   <li class="home">
                                        <a href="javascript:void(0)" name="<?php echo Url('Admin/Agent/profitlist'); ?>" title="代理商等级" class="iframeurl"><i class="icon-double-angle-right"></i>代理商分润提现管理</a>
                                    </li>
                                   <li class="home">
                                        <a href="javascript:void(0)" name="<?php echo Url('Admin/Agent/underlevelrun'); ?>" title="代理商分润报表" class="iframeurl"><i class="icon-double-angle-right"></i>代理商分润报表</a>
                                   </li>
								</ul>
							</li>

							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-user"></i><span class="menu-text"> 用户管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/User/index'); ?>" title="管理用户" class="iframeurl"><i class="icon-double-angle-right"></i>管理用户</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/User/disauser'); ?>" title="禁用用户" class="iframeurl"><i class="icon-double-angle-right"></i>禁用用户</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Userbank/index'); ?>" title="信用卡管理" class="iframeurl"><i class="icon-double-angle-right"></i>银行卡管理</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Usertype/index'); ?>" title="用户等级" class="iframeurl"><i class="icon-double-angle-right"></i>用户等级</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/User/pftwarll'); ?>" title="分润提现管理" class="iframeurl"><i class="icon-double-angle-right"></i>分润提现管理</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-picture "></i><span class="menu-text"> 订单管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Repayment/index'); ?>" title="分类管理" class="iframeurl"><i class="icon-double-angle-right"></i>还款订单</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Payrecords/index'); ?>" title="收款订单" class="iframeurl"><i class="icon-double-angle-right"></i>收款订单</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Upgrade/index'); ?>" title="升级订单" class="iframeurl"><i class="icon-double-angle-right"></i>升级订单</a>
									</li>
									<!--<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Order/index'); ?>" title="商城订单" class="iframeurl"><i class="icon-double-angle-right"></i>商城订单</a>
									</li>
-->
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-credit-card"></i><span class="menu-text"> 支付管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/paymentchannel/index'); ?>" title="支付通道" class="iframeurl"><i class="icon-double-angle-right"></i>支付通道</a>
									</li>
								</ul>
							</li>
<!-- 							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-laptop"></i><span class="menu-text"> 积分商城 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/goods/index'); ?>" title="商品列表" class="iframeurl"><i class="icon-double-angle-right"></i>商品列表</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/goodstype/index'); ?>" title="商品分类" class="iframeurl"><i class="icon-double-angle-right"></i>商品分类</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Order/index'); ?>" title="商品列表" class="iframeurl"><i class="icon-double-angle-right"></i>订单管理</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Order/index'); ?>" title="商品列表" class="iframeurl"><i class="icon-double-angle-right"></i>代发货订单</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Order/index'); ?>" title="商品列表" class="iframeurl"><i class="icon-double-angle-right"></i>已完成订单</a>
									</li>
								</ul>
							</li> 
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-laptop"></i><span class="menu-text"> 优惠券管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Coupon/coulist'); ?>" title="优惠券列表" class="iframeurl"><i class="icon-double-angle-right"></i>优惠券列表</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Coupon/usedcoulist'); ?>" title="已使用券列表" class="iframeurl"><i class="icon-double-angle-right"></i>已使用券列表</a>
									</li>
								</ul>
							</li>-->
							 <li>
								<a href="#" class="dropdown-toggle"><i class="icon-comments-alt"></i><span class="menu-text"> 消息管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/feedback/index'); ?>" title="意见反馈" class="iframeurl"><i class="icon-double-angle-right"></i>意见反馈</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Message/addmsg'); ?>" title="消息发送" class="iframeurl"><i class="icon-double-angle-right"></i>消息发送</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/Message/msglog'); ?>" title="发送记录" class="iframeurl"><i class="icon-double-angle-right"></i>发送记录</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-bookmark"></i><span class="menu-text"> 文章管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/article/index',array('type'=>1)); ?>" title="文章列表" class="iframeurl"><i class="icon-double-angle-right"></i>文章列表</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/article/index',array('type'=>2)); ?>" title="日报列表" class="iframeurl"><i class="icon-double-angle-right"></i>日报列表</a>
									</li>

									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/article/index',array('type'=>3)); ?>" title="还款指南" class="iframeurl"><i class="icon-double-angle-right"></i>还款指南</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/article/index',array('type'=>4)); ?>" title="收款指南" class="iframeurl"><i class="icon-double-angle-right"></i>收款指南</a>
									</li>
									<!-- <li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/article/index',array('type'=>5)); ?>" title="常见问题" class="iframeurl"><i class="icon-double-angle-right"></i>常见问题</a>
									</li> -->

									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/articletype/index'); ?>" title="分类管理" class="iframeurl"><i class="icon-double-angle-right"></i>分类管理</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-picture"></i><span class="menu-text"> 图片管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/img/index',array('type'=>1)); ?>" title="推广二维码背景" class="iframeurl"><i class="icon-double-angle-right"></i>推广二维码背景</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/img/index',array('type'=>2)); ?>" title="首页滚图" class="iframeurl"><i class="icon-double-angle-right"></i>首页滚图</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/img/index',array('type'=>3)); ?>" title="启动图" class="iframeurl"><i class="icon-double-angle-right"></i>启动图</a>
									</li>
								</ul>
							</li>
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-credit-card"></i><span class="menu-text"> 网申管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/lender/index',array('type'=>1)); ?>" title="信用卡申请" class="iframeurl"><i class="icon-double-angle-right"></i>信用卡申请</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/lender/index',array('type'=>2)); ?>" title="贷款申请" class="iframeurl"><i class="icon-double-angle-right"></i>贷款申请</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/lendertype/index'); ?>" title="平台" class="iframeurl"><i class="icon-double-angle-right"></i>平台</a>
									</li>
								</ul>
							</li>
							
							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-credit-card"></i><span class="menu-text"> 常见问题 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/issue/index'); ?>" title="常见问题" class="iframeurl"><i class="icon-double-angle-right"></i>常见问题</a>
									</li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/issuetype/index'); ?>" title="问题分类" class="iframeurl"><i class="icon-double-angle-right"></i>问题分类</a>
									</li>
								</ul>
							</li>


							<li>
								<a href="#" class="dropdown-toggle"><i class="icon-credit-card"></i><span class="menu-text"> 银行管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/bank/index'); ?>" title="银行列表" class="iframeurl"><i class="icon-double-angle-right"></i>银行列表</a>
									</li>
								</ul>
							</li>
                           <li>
								<a href="#" class="dropdown-toggle"><i class="icon-credit-card"></i><span class="menu-text"> 申请通道管理 </span><b class="arrow icon-angle-down"></b></a>
								<ul class="submenu">
									<li class="home">
                                     	<a href="javascript:void(0)" name="<?php echo Url('Admin/pmtapplycon/pacrcord'); ?>" title="申请记录" class="iframeurl"><i class="icon-double-angle-right"></i>申请记录</a>
                                    </li>
									<li class="home">
										<a href="javascript:void(0)" name="<?php echo Url('Admin/pmtapplycon/paclist'); ?>" title="申请通道列表" class="iframeurl"><i class="icon-double-angle-right"></i>申请通道列表</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
					<script type="text/javascript">
						$("#menu_style").niceScroll({
							cursorcolor: "#888888",
							cursoropacitymax: 1,
							touchbehavior: false,
							cursorwidth: "5px",
							cursorborder: "0",
							cursorborderradius: "5px"
						});
					</script>
					<div class="sidebar-collapse" id="sidebar-collapse">
						<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
					</div>
					<script type="text/javascript">
						try {
							ace.settings.check('sidebar', 'collapsed')
						} catch(e) {}
					</script>
				</div>

				<div class="main-content">
					<script type="text/javascript">
						try {
							ace.settings.check('breadcrumbs', 'fixed')
						} catch(e) {}
					</script>
					<div class="breadcrumbs" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="icon-home home-icon"></i>
								<a href="index.html">首页</a>
							</li>
							<li class="active"><span class="Current_page iframeurl"></span></li>
							<li class="active" id="parentIframe"><span class="parentIframe iframeurl"></span></li>
							<li class="active" id="parentIfour"><span class="parentIfour iframeurl"></span></li>
						</ul>
					</div>

					<iframe id="iframe" style="border:0; width:100%; background-color:#FFF;" name="iframe" frameborder="0" src="<?php echo url('Admin/index/home'); ?>">  </iframe>

					<!-- /.page-content -->
				</div>
				<!-- /.main-content -->

				<div class="ace-settings-container" id="ace-settings-container">
					<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
						<i class="icon-cog bigger-150"></i>
					</div>

					<div class="ace-settings-box" id="ace-settings-box">
						<div>
							<div class="pull-left">
								<select id="skin-colorpicker" class="hide">
									<option data-skin="default" value="#438EB9">#438EB9</option>
									<option data-skin="skin-1" value="#222A2D">#222A2D</option>
									<option data-skin="skin-2" value="#C6487E">#C6487E</option>
									<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
								</select>
							</div>
							<span>&nbsp; 选择皮肤</span>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
							<label class="lbl" for="ace-settings-sidebar"> 固定滑动条</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />
							<label class="lbl" for="ace-settings-rtl">切换到左边</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />
							<label class="lbl" for="ace-settings-add-container">
                                  切换窄屏
                                  <b></b>
                              </label>
						</div>
					</div>
				</div>
				<!-- /#ace-settings-container -->
			</div>
			<!-- /.main-container-inner -->

		</div>
		<!--底部样式-->
<!-- 
		<div class="footer_style" id="footerstyle">
			<script type="text/javascript">
				try {
					ace.settings.check('footerstyle', 'fixed')
				} catch(e) {}
			</script>
			<p class="l_f">版权所有：<?php echo htmlentities($config['SITE_COMPANY']); ?> 苏ICP备11011739号</p>
			<p class="r_f">地址：xxxxxxxxxxxxxxxxxxxx更多模板：
				<a href="http://www.mycodes.net/" target="_blank">源码之家</a>
			</p>
		</div> -->
		<!--修改密码样式-->
		<div class="change_Pass_style" id="change_Pass">
			<ul class="xg_style">
				<li><label class="label_name">原&nbsp;&nbsp;密&nbsp;码</label><input name="原密码" type="password" class="" id="password"></li>
				<li><label class="label_name">新&nbsp;&nbsp;密&nbsp;码</label><input name="新密码" type="password" class="" id="Nes_pas"></li>
				<li><label class="label_name">确认密码</label><input name="再次确认密码" type="password" class="" id="c_mew_pas"></li>
			</ul>
		</div>
		<!-- /.main-container -->
		<!-- basic scripts -->

	</body>

</html>