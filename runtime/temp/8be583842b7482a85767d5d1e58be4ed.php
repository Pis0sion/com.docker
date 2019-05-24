<?php /*a:1:{s:35:"../Theme/adminsys/system/index.html";i:1546068505;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="renderer" content="webkit|ie-comp|ie-stand">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
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
		<script  src="/plugins/jquery-1.9.1.min.js"></script>
		<script src="/static/admin/assets/js/bootstrap.min.js"></script>
		<script src="/static/admin/assets/js/typeahead-bs2.min.js"></script>
		<script src="/static/admin/assets/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="/static/admin/assets/js/jquery.ui.touch-punch.min.js"></script>
		<script src="/static/admin/assets/js/ace-elements.min.js"></script>
		<script src="/static/admin/assets/js/ace.min.js"></script>
		<title>系统设置</title>
		
	    <script  src="/plugins/layer/layer.js"></script>
	    <script  src="/plugins/common.js"></script>
	</head>

	<body>
		<div class="margin clearfix">
			<div class="stystems_style">
				<div class="tabbable">
					<ul class="nav nav-tabs" id="myTab">
						<li class="active">
							<a data-toggle="tab" href="#home"><i class="green fa fa-home bigger-110"></i>&nbsp;基本设置</a>
						</li>
						
						<li class="">
							<a data-toggle="tab" data-toggle="dropdown" class="dropdown-toggle" href="#dropdown">短信设置</a>
						</li>
						<li class="">
							<a data-toggle="tab" data-toggle="dropdown" class="dropdown-toggle" href="#fenxiao">分销设置</a>
						</li>
                       <li class="">
							<a data-toggle="tab" data-toggle="dropdown" class="dropdown-toggle" href="#wanghsen">网申分销设置</a>
						</li>
						<li class="">
							<a data-toggle="tab" data-toggle="dropdown" class="dropdown-toggle" href="#czfenxiao">升级分销设置</a>
						</li>
						<li class="">
							<a data-toggle="tab" data-toggle="dropdown" class="dropdown-toggle" href="#yjmember">押金会员</a>
						</li>
						<li class="">
							<a data-toggle="tab" data-toggle="dropdown" class="dropdown-toggle" href="#agent">代理设置</a>
						</li>
						<li class="">
							<a data-toggle="tab" data-toggle="dropdown" class="dropdown-toggle" href="#other">其他设置</a>
						</li>
					</ul>
					<div class="tab-content">
						<div id="home" class="tab-pane active">
							<form  action="<?php echo url('Admin/System/index'); ?>" id="home-form">
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>网站名称： </label>
									<div class="col-sm-9"><input type="text" id="SITE_NAME" name="SITE_NAME" value="<?php echo !empty($config['SITE_NAME']) ? htmlentities($config['SITE_NAME']) : ''; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>关键词： </label>
									<div class="col-sm-9"><input type="text" id="SITE_KEYWORD" name="SITE_KEYWORD" value="<?php echo !empty($config['SITE_KEYWORD']) ? htmlentities($config['SITE_KEYWORD']) : ''; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>描述： </label>
									<div class="col-sm-9"><input type="text" id="SITE_DETECTION" name="SITE_DETECTION" placeholder="空制在80个汉字，160个字符以内" value="<?php echo !empty($config['SITE_DETECTION']) ? htmlentities($config['SITE_DETECTION']) : ''; ?>" class="col-xs-10"></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>公司主体： </label>
									<div class="col-sm-9"><input type="text" id="SITE_COMPANY" name="SITE_COMPANY" placeholder="默认为空，为相对路径" value="<?php echo !empty($config['SITE_COMPANY']) ? htmlentities($config['SITE_COMPANY']) : ''; ?>" class="col-xs-10"></div>
								</div>
								
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>版本信息： </label>
									<div class="col-sm-9"><input type="text" name="SITE_VERSION" id="SITE_VERSION" placeholder="" value="<?php echo !empty($config['SITE_VERSION']) ? htmlentities($config['SITE_VERSION']) : ''; ?>" class="col-xs-10 "></div>
								</div>
                              <div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>绑卡奖励： </label>
									<div class="col-sm-9"><input type="text" name="POINTS_CARD" id="POINTS_CARD" placeholder="" value="<?php echo !empty($config['POINTS_CARD']) ? htmlentities($config['POINTS_CARD']) : ''; ?>" class="col-xs-10 "></div>
								</div>
                              <div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>还款奖励： </label>
									<div class="col-sm-9"><input type="text" name="POINTS_REPAYMENT" id="POINTS_REPAYMENT" placeholder="" value="<?php echo !empty($config['POINTS_REPAYMENT']) ? htmlentities($config['POINTS_REPAYMENT']) : ''; ?>" class="col-xs-10 "></div>
								</div>
                              <div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>收款奖励： </label>
									<div class="col-sm-9"><input type="text" name="POINTS_RECEIVABLES" id="POINTS_RECEIVABLES" placeholder="" value="<?php echo !empty($config['POINTS_RECEIVABLES']) ? htmlentities($config['POINTS_RECEIVABLES']) : ''; ?>" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>首页LOGO </label>
									<div class="col-sm-9"><input type="text" name="SITE_LOGO" id="SITE_LOGO" placeholder="" value="<?php echo !empty($config['SITE_LOGO']) ? htmlentities($config['SITE_LOGO']) : ''; ?>" class="col-xs-10 ">
										<a href='https://www.superbed.cn/' target="_blank" >上传图片</a>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>需要推荐码 </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['USER_CODE']==1 ? 'checked' : ''; ?> value="1" name="USER_CODE" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['USER_CODE']==1 ? '' : 'checked'; ?> value="0" name="USER_CODE" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>
								
								 
								<div class="Button_operation">
									<button onclick="save_submit('home');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
						
						<div id="dropdown" class="tab-pane">
							<form  action="<?php echo url('Admin/System/index'); ?>" id="dropdown-form">
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>平台账号： </label>
									<div class="col-sm-9"><input type="text" id="MSM_ACCOUNT" name="MSM_ACCOUNT" value="<?php echo !empty($config['MSM_ACCOUNT']) ? htmlentities($config['MSM_ACCOUNT']) : ''; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>平台密码： </label>
									<div class="col-sm-9"><input type="text" id="MSM_PASS" name="MSM_PASS" value="<?php echo !empty($config['MSM_PASS']) ? htmlentities($config['MSM_PASS']) : ''; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>短信签名： </label>
									<div class="col-sm-9"><input type="text" id="MSM_SIGN" name="MSM_SIGN" placeholder="空制在80个汉字，160个字符以内" value="<?php echo !empty($config['MSM_SIGN']) ? htmlentities($config['MSM_SIGN']) : ''; ?>" class="col-xs-10"></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>APPKEY： </label>
									<div class="col-sm-9"><input type="text" id="MSM_APPKEY" name="MSM_APPKEY" placeholder="默认为空，为相对路径" value="<?php echo !empty($config['MSM_APPKEY']) ? htmlentities($config['MSM_APPKEY']) : ''; ?>" class="col-xs-10"></div>
								</div>
								
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>SECRETKEY： </label>
									<div class="col-sm-9"><input type="text" name="MSM_SECRETKEY" id="MSM_SECRETKEY" placeholder="" value="<?php echo !empty($config['MSM_SECRETKEY']) ? htmlentities($config['MSM_SECRETKEY']) : ''; ?>" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>异常通知手机号： </label>
									<div class="col-sm-9"><input type="text" id="MSM_PHONE" name="MSM_PHONE" value="<?php echo !empty($config['MSM_PHONE']) ? htmlentities($config['MSM_PHONE']) : ''; ?>" placeholder="异常订单通知的手机号码" class="col-xs-10 "></div>
								</div>
								<div class="Button_operation">
									<button onclick="save_submit('dropdown');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
						<div id="fenxiao" class="tab-pane ">
							<form  action="<?php echo url('Admin/System/index'); ?>" id="fenxiao-form">
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>是否开启激活： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['USER_ACTIVATION']==1 ? 'checked' : ''; ?> value="1" name="USER_ACTIVATION" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['USER_ACTIVATION']==1 ? '' : 'checked'; ?> value="0" name="USER_ACTIVATION" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>激活金额： </label>
									<div class="col-sm-9"><input type="number" id="USER_ACTIVATION_TYPE" name="USER_ACTIVATION_TYPE" value="<?php echo !empty($config['USER_ACTIVATION_TYPE']) ? htmlentities($config['USER_ACTIVATION_TYPE']) : '0'; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>会员分销： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['USER_DISTRI']==1 ? 'checked' : ''; ?> value="1" name="USER_DISTRI" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['USER_DISTRI']==1 ? '' : 'checked'; ?> value="0" name="USER_DISTRI" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>分销值： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input  <?php echo $config['USER_DISTRI_TYPE']==1 ? 'checked' : ''; ?> value="1" name="USER_DISTRI_TYPE" type="radio" class="ace" ><span class="lbl">百分比(单位%)</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input  <?php echo $config['USER_DISTRI_TYPE']==1 ? '' : 'checked'; ?> value="0" name="USER_DISTRI_TYPE" type="radio" class="ace" ><span class="lbl">固定金额(单位元)</span></label>
										   </span>     
									</div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>分销模式： </label>
									<div class="col-sm-9">
											<span class="">
												<label><input  <?php echo $config['USER_DISTRI_MODE']==0 ? 'checked' : ''; ?> value="0" name="USER_DISTRI_MODE" type="radio" class="ace" ><span class="lbl">模式一</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
												<label><input  <?php echo $config['USER_DISTRI_MODE']==1 ? 'checked' : ''; ?> value="1" name="USER_DISTRI_MODE" type="radio" class="ace" ><span class="lbl">模式二(取用户代理高签分润)</span></label>
											</span>     
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1">模式一： </label>
									<label class="col-sm-2 control-label no-padding-left" for="form-field-1"><i>(单位 % 或 元 请注意填写)</i></label>
								</div>
								<div></div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>一级： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>还款 </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_TYPE_HK_1" name="USER_DISTRI_TYPE_HK_1" value="<?php echo !empty($config['USER_DISTRI_TYPE_HK_1']) ? htmlentities($config['USER_DISTRI_TYPE_HK_1']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>收款： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_TYPE_SK_1" name="USER_DISTRI_TYPE_SK_1" value="<?php echo !empty($config['USER_DISTRI_TYPE_SK_1']) ? htmlentities($config['USER_DISTRI_TYPE_SK_1']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
								</div>
								
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>还款 </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_TYPE_HK_2" name="USER_DISTRI_TYPE_HK_2" value="<?php echo !empty($config['USER_DISTRI_TYPE_HK_2']) ? htmlentities($config['USER_DISTRI_TYPE_HK_2']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>收款： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_TYPE_SK_2" name="USER_DISTRI_TYPE_SK_2" value="<?php echo !empty($config['USER_DISTRI_TYPE_SK_2']) ? htmlentities($config['USER_DISTRI_TYPE_SK_2']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>三级： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>还款 </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_TYPE_HK_3" name="USER_DISTRI_TYPE_HK_3" value="<?php echo !empty($config['USER_DISTRI_TYPE_HK_3']) ? htmlentities($config['USER_DISTRI_TYPE_HK_3']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>收款： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_TYPE_SK_3" name="USER_DISTRI_TYPE_SK_3" value="<?php echo !empty($config['USER_DISTRI_TYPE_SK_3']) ? htmlentities($config['USER_DISTRI_TYPE_SK_3']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
								</div>


								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1">模式二： </label>
									<label class="col-sm-2 control-label no-padding-left" for="form-field-1"><i>(单位 % 或 元 请注意填写)</i></label>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>一级(还款)： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>普通用户： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_HK_1" name="USER_DISTRI_MODE_HK_1" value="<?php echo !empty($config['USER_DISTRI_MODE_HK_1']) ? htmlentities($config['USER_DISTRI_MODE_HK_1']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12 "></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>VIP用户：</label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_HK_V_1" name="USER_DISTRI_MODE_HK_V_1" value="<?php echo !empty($config['USER_DISTRI_MODE_HK_V_1']) ? htmlentities($config['USER_DISTRI_MODE_HK_V_1']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写"  class="col-xs-12 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>一级(收款)： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>普通用户： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_SK_1" name="USER_DISTRI_MODE_SK_1" value="<?php echo !empty($config['USER_DISTRI_MODE_SK_1']) ? htmlentities($config['USER_DISTRI_MODE_SK_1']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写"  class="col-xs-12"></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>VIP用户：</label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_SK_V_1" name="USER_DISTRI_MODE_SK_V_1" value="<?php echo !empty($config['USER_DISTRI_MODE_SK_V_1']) ? htmlentities($config['USER_DISTRI_MODE_SK_V_1']) : '0'; ?>"  placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
								</div>

								
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级(还款)： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>普通用户： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_HK_2" name="USER_DISTRI_MODE_HK_2" value="<?php echo !empty($config['USER_DISTRI_MODE_HK_2']) ? htmlentities($config['USER_DISTRI_MODE_HK_2']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12 "></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>VIP用户：</label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_HK_V_2" name="USER_DISTRI_MODE_HK_V_2" value="<?php echo !empty($config['USER_DISTRI_MODE_HK_V_2']) ? htmlentities($config['USER_DISTRI_MODE_HK_V_2']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写"  class="col-xs-12 "></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级(收款)： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>普通用户： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_SK_2" name="USER_DISTRI_MODE_SK_2" value="<?php echo !empty($config['USER_DISTRI_MODE_SK_2']) ? htmlentities($config['USER_DISTRI_MODE_SK_2']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写"  class="col-xs-12"></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>VIP用户： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_SK_V_2" name="USER_DISTRI_MODE_SK_V_2" value="<?php echo !empty($config['USER_DISTRI_MODE_SK_V_2']) ? htmlentities($config['USER_DISTRI_MODE_SK_V_2']) : '0'; ?>"  placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
								</div>
								
								<div class="form-group">
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>三级(还款)： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>普通用户： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_HK_3" name="USER_DISTRI_MODE_HK_3" value="<?php echo !empty($config['USER_DISTRI_MODE_HK_3']) ? htmlentities($config['USER_DISTRI_MODE_HK_3']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>VIP用户：</label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_HK_V_3" name="USER_DISTRI_MODE_HK_V_3" value="<?php echo !empty($config['USER_DISTRI_MODE_HK_V_3']) ? htmlentities($config['USER_DISTRI_MODE_HK_V_3']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写"  class="col-xs-12"></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>三级(收款)： </label>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>普通用户： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_SK_3" name="USER_DISTRI_MODE_SK_3" value="<?php echo !empty($config['USER_DISTRI_MODE_SK_3']) ? htmlentities($config['USER_DISTRI_MODE_SK_3']) : '0'; ?>" placeholder="单位% 或 单位元 请注意填写"  class="col-xs-12"></div>
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>VIP用户： </label>
									<div class="col-sm-3"><input type="number" id="USER_DISTRI_MODE_SK_V_3" name="USER_DISTRI_MODE_SK_V_3" value="<?php echo !empty($config['USER_DISTRI_MODE_SK_V_3']) ? htmlentities($config['USER_DISTRI_MODE_SK_V_3']) : '0'; ?>"  placeholder="单位% 或 单位元 请注意填写" class="col-xs-12"></div>
								</div>

								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>首次添加信用卡(元)： </label>
									<div class="col-sm-9"><input type="text" id="POINTS_CARD" name="POINTS_CARD" placeholder="默认为空，为相对路径" value="<?php echo !empty($config['POINTS_CARD']) ? htmlentities($config['POINTS_CARD']) : '0'; ?>" class="col-xs-10"></div>
								</div>
								
									
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>首次完成收款(元)： </label>
									<div class="col-sm-9"><input type="number" name="POINTS_RECEIVABLES" id="POINTS_RECEIVABLES" placeholder="" value="<?php echo !empty($config['POINTS_RECEIVABLES']) ? htmlentities($config['POINTS_RECEIVABLES']) : '0'; ?>" class="col-xs-10"></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>首次完成还款(元)： </label>
									<div class="col-sm-9"><input type="number" id="POINTS_REPAYMENT" name="POINTS_REPAYMENT" placeholder="" value="<?php echo !empty($config['POINTS_REPAYMENT']) ? htmlentities($config['POINTS_REPAYMENT']) : '0'; ?>" class="col-xs-10 "></div>
								</div>
<!-- 								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>首次分享： </label>
									<div class="col-sm-9"><input type="number" id="POINTS_SHARE" name="POINTS_SHARE" placeholder="" value="<?php echo !empty($config['POINTS_SHARE']) ? htmlentities($config['POINTS_SHARE']) : '0'; ?>" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>文章分享： </label>
									<div class="col-sm-9"><input type="number" id="POINTS_ARTICLE" name="POINTS_ARTICLE" placeholder="" value="<?php echo !empty($config['POINTS_ARTICLE']) ? htmlentities($config['POINTS_ARTICLE']) : '0'; ?>" class="col-xs-10 "/></div>
								</div> -->
								<div class="Button_operation">
									<button onclick="save_submit('fenxiao');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
						
                       <div id="wanghsen" class="tab-pane ">
							<form  action="<?php echo url('Admin/System/index'); ?>" id="wanghsen-form">
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>信用卡分销： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['USER_CRARD_CZ']==1 ? 'checked' : ''; ?> value="1" name="USER_CRARD_CZ" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['USER_CRARD_CZ']==1 ? '' : 'checked'; ?> value="0" name="USER_CRARD_CZ" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>
								
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>一级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_CRARD_TYPE_CZ_1" name="USER_CRARD_TYPE_CZ_1" value="<?php echo !empty($config['USER_CRARD_TYPE_CZ_1']) ? htmlentities($config['USER_CRARD_TYPE_CZ_1']) : '0'; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_CRARD_TYPE_CZ_2" name="USER_CRARD_TYPE_CZ_2" value="<?php echo !empty($config['USER_CRARD_TYPE_CZ_2']) ? htmlentities($config['USER_CRARD_TYPE_CZ_2']) : '0'; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
                              <div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_CRARD_TYPE_CZ_3" name="USER_CRARD_TYPE_CZ_3" value="<?php echo !empty($config['USER_CRARD_TYPE_CZ_3']) ? htmlentities($config['USER_CRARD_TYPE_CZ_3']) : '0'; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>网贷分销： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['USER_LOAN_CZ']==1 ? 'checked' : ''; ?> value="1" name="USER_LOAN_CZ" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['USER_LOAN_CZ']==1 ? '' : 'checked'; ?> value="0" name="USER_LOAN_CZ" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>
								
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>一级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_LOAN_TYPE_CZ_1" name="USER_LOAN_TYPE_CZ_1" value="<?php echo !empty($config['USER_LOAN_TYPE_CZ_1']) ? htmlentities($config['USER_LOAN_TYPE_CZ_1']) : '0'; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_LOAN_TYPE_CZ_2" name="USER_LOAN_TYPE_CZ_2" value="<?php echo !empty($config['USER_LOAN_TYPE_CZ_2']) ? htmlentities($config['USER_LOAN_TYPE_CZ_2']) : '0'; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>三级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_LOAN_TYPE_CZ_3" name="USER_LOAN_TYPE_CZ_3" value="<?php echo !empty($config['USER_LOAN_TYPE_CZ_3']) ? htmlentities($config['USER_LOAN_TYPE_CZ_3']) : '0'; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>积分兑换分销： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['USER_INTEGRAL_CZ']==1 ? 'checked' : ''; ?> value="1" name="USER_INTEGRAL_CZ" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['USER_INTEGRAL_CZ']==1 ? '' : 'checked'; ?> value="0" name="USER_INTEGRAL_CZ" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>
								
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>一级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_INTEGRAL_TYPE_CZ_1" name="USER_INTEGRAL_TYPE_CZ_1" value="<?php echo !empty($config['USER_INTEGRAL_TYPE_CZ_1']) ? htmlentities($config['USER_INTEGRAL_TYPE_CZ_1']) : '0'; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_INTEGRAL_TYPE_CZ_2" name="USER_INTEGRAL_TYPE_CZ_2" value="<?php echo !empty($config['USER_INTEGRAL_TYPE_CZ_2']) ? htmlentities($config['USER_INTEGRAL_TYPE_CZ_2']) : '0'; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
                              <div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>三级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_INTEGRAL_TYPE_CZ_3" name="USER_INTEGRAL_TYPE_CZ_3" value="<?php echo !empty($config['USER_INTEGRAL_TYPE_CZ_3']) ? htmlentities($config['USER_INTEGRAL_TYPE_CZ_3']) : '0'; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
								<div class="Button_operation">
									<button onclick="save_submit('wanghsen');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
                      
						<div id="czfenxiao" class="tab-pane ">
							<form  action="<?php echo url('Admin/System/index'); ?>" id="czfenxiao-form">
								
<!-- 								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>代理分销： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['AGENT_ACTIVATION_CZ']==1 ? 'checked' : ''; ?> value="1" name="AGENT_ACTIVATION_CZ" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['AGENT_ACTIVATION_CZ']==1 ? '' : 'checked'; ?> value="0" name="AGENT_ACTIVATION_CZ" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div> -->
<!-- 								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>代理分销等级： </label>
									<div class="col-sm-9"><input type="number" id="AGENT_DISTRI_TYPE_CZ_1" name="AGENT_DISTRI_TYPE_CZ_1" value="<?php echo !empty($config['AGENT_DISTRI_TYPE_CZ_1']) ? htmlentities($config['AGENT_DISTRI_TYPE_CZ_1']) : '0'; ?>" placeholder="0为不限制等级" class="col-xs-10 ">

									</div>
								</div> -->
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>会员分销： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['USER_ACTIVATION_CZ']==1 ? 'checked' : ''; ?> value="1" name="USER_ACTIVATION_CZ" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['USER_ACTIVATION_CZ']==1 ? '' : 'checked'; ?> value="0" name="USER_ACTIVATION_CZ" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>分销值： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input  <?php echo $config['USER_DISTRI_TYPE_CZ']==1 ? 'checked' : ''; ?> value="1"	name="USER_DISTRI_TYPE_CZ" type="radio" class="ace" ><span class="lbl">百分百</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input  <?php echo $config['USER_DISTRI_TYPE_CZ']==1 ? '' : 'checked'; ?> value="0"	name="USER_DISTRI_TYPE_CZ" type="radio" class="ace" ><span class="lbl">固定金额</span></label>
										   </span>     
									</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>一级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_DISTRI_TYPE_CZ_1" name="USER_DISTRI_TYPE_CZ_1" value="<?php echo !empty($config['USER_DISTRI_TYPE_CZ_1']) ? htmlentities($config['USER_DISTRI_TYPE_CZ_1']) : '0'; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_DISTRI_TYPE_CZ_2" name="USER_DISTRI_TYPE_CZ_2" value="<?php echo !empty($config['USER_DISTRI_TYPE_CZ_2']) ? htmlentities($config['USER_DISTRI_TYPE_CZ_2']) : '0'; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>三级值： </label>
									<div class="col-sm-9"><input type="number" id="USER_DISTRI_TYPE_CZ_3" name="USER_DISTRI_TYPE_CZ_3" placeholder="空制在80个汉字，160个字符以内" value="<?php echo !empty($config['USER_DISTRI_TYPE_CZ_3']) ? htmlentities($config['USER_DISTRI_TYPE_CZ_3']) : '0'; ?>" class="col-xs-10"></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>代理分销： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['AGENT_ACTIVATION_CZ']==1 ? 'checked' : ''; ?> value="1" name="AGENT_ACTIVATION_CZ" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['AGENT_ACTIVATION_CZ']==1 ? '' : 'checked'; ?> value="0" name="AGENT_ACTIVATION_CZ" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>分销值： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input  <?php echo $config['AGENT_DISTRI_TYPE_CZ']==1 ? 'checked' : ''; ?> value="1"	name="AGENT_DISTRI_TYPE_CZ" type="radio" class="ace" ><span class="lbl">百分百</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input  <?php echo $config['AGENT_DISTRI_TYPE_CZ']==1 ? '' : 'checked'; ?> value="0"	name="AGENT_DISTRI_TYPE_CZ" type="radio" class="ace" ><span class="lbl">固定金额</span></label>
										   </span>     
									</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>一级值： </label>
									<div class="col-sm-9"><input type="number" id="AGENT_DISTRI_TYPE_CZ_1" name="AGENT_DISTRI_TYPE_CZ_1" value="<?php echo !empty($config['AGENT_DISTRI_TYPE_CZ_1']) ? htmlentities($config['AGENT_DISTRI_TYPE_CZ_1']) : '0'; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>二级值： </label>
									<div class="col-sm-9"><input type="number" id="AGENT_DISTRI_TYPE_CZ_2" name="AGENT_DISTRI_TYPE_CZ_2" value="<?php echo !empty($config['AGENT_DISTRI_TYPE_CZ_2']) ? htmlentities($config['AGENT_DISTRI_TYPE_CZ_2']) : '0'; ?>" placeholder="控制在25个字、50个字节以内"  class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>三级值： </label>
									<div class="col-sm-9"><input type="number" id="AGENT_DISTRI_TYPE_CZ_3" name="AGENT_DISTRI_TYPE_CZ_3" placeholder="空制在80个汉字，160个字符以内" value="<?php echo !empty($config['AGENT_DISTRI_TYPE_CZ_3']) ? htmlentities($config['AGENT_DISTRI_TYPE_CZ_3']) : '0'; ?>" class="col-xs-10"></div>
								</div>
								<div class="Button_operation">
									<button onclick="save_submit('czfenxiao');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
						<div id="yjmember" class="tab-pane ">
							<form  action="<?php echo url('Admin/System/index'); ?>" id="yjmember-form">
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>押金会员： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['DEPOSIT_MEMBER']==1 ? 'checked' : ''; ?> value="1" name="DEPOSIT_MEMBER" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['DEPOSIT_MEMBER']==1 ? '' : 'checked'; ?> value="0" name="DEPOSIT_MEMBER" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>退保人数： </label>
									<div class="col-lg-6"><input type="number" id="DEPOSIT_MEMBER_SUM" name="DEPOSIT_MEMBER_SUM" value="<?php echo !empty($config['DEPOSIT_MEMBER_SUM']) ? htmlentities($config['DEPOSIT_MEMBER_SUM']) : '0'; ?>" placeholder="控制在25个字、50个字节以内" min="0" class="col-xs-10 " style="margin-left: 0px;line-height: 1.428571429" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')"></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>选择等级： </label>
                                    <div class="col-lg-6">
                                        <select class="form-control" name="DEPOSIT_MEMBER_ID" id="DEPOSIT_MEMBER_ID">
                                            <option value='0' >请选择</option>
                                            <?php if(is_array($usertype) || $usertype instanceof \think\Collection || $usertype instanceof \think\Paginator): $i = 0; $__LIST__ = $usertype;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                            <option value='<?php echo htmlentities($v['type_id']); ?>' <?php echo $config['DEPOSIT_MEMBER_ID']!=$v['type_id'] ? '' : 'selected'; ?> ><?php echo htmlentities($v['type_name']); ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
								</div>
								<div class="Button_operation">
									<button onclick="save_submit('yjmember');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div> 
							</form>
						</div>
						<div id="agent" class="tab-pane ">
							<form  action="<?php echo url('Admin/System/index'); ?>" id="agent-form">
<!-- 								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>GPS归属： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['AGENT_AREA_IS']==1 ? 'checked' : ''; ?> value="1" name="AGENT_AREA_IS" type="radio" class="ace" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['AGENT_AREA_IS']==1 ? '' : 'checked'; ?> value="0" name="AGENT_AREA_IS" type="radio" class="ace" ><span class="lbl">关闭</span></label>
										   </span>     
									</div>
								</div> -->
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>代理分类： </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input <?php echo $config['AGENT_GRADE']==1 ? 'checked' : ''; ?> value="1" name="AGENT_GRADE" type="radio" class="ace" ><span class="lbl">选择等级</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input <?php echo $config['AGENT_GRADE']==1 ? '' : 'checked'; ?> value="0" name="AGENT_GRADE" type="radio" class="ace" ><span class="lbl">手输费率</span></label>
										   </span>     
									</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>分润提现手续费： </label>
									<div class="col-sm-9"><input type="text" id="SITE_CASHFREE" name="SITE_CASHFREE" placeholder="" value="<?php echo !empty($config['SITE_CASHFREE']) ? htmlentities($config['SITE_CASHFREE']) : ''; ?>" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>会员中继： </label>
									<div class="col-sm-9">
									   <span class="">  
								       <label><input 	name="USER_INHERIT" id="USER_INHERIT" type="radio" class="ace"  <?php echo $config['USER_INHERIT']==1 ? 'checked' : ''; ?> value="1" ><span class="lbl">启用</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
								       <label><input 	name="USER_INHERIT" id="USER_INHERIT" type="radio" class="ace"  <?php echo $config['USER_INHERIT']==1 ? '' : 'checked'; ?> value="0"  ><span class="lbl">关闭</span></label></span>     
									</div>
								</div>
                              <div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>特推代理： </label>
                                    <div class="col-lg-6">
                                        <select class="form-control" name="DEPOSIT_AGENT_ID" id="DEPOSIT_AGENT_ID">
                                            <option value='0' >请选择</option>
                                            <?php if(is_array($agentlist) || $agentlist instanceof \think\Collection || $agentlist instanceof \think\Paginator): $i = 0; $__LIST__ = $agentlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                                            <option value='<?php echo htmlentities($v['agent_id']); ?>' <?php echo $config['DEPOSIT_AGENT_ID']!=$v['agent_id'] ? '' : 'selected'; ?> ><?php echo htmlentities($v['agent_name']); ?></option>
                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
								</div>
								<div class="Button_operation">
									<button onclick="save_submit('agent');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div> 
							</form>
						</div>
						
						<div id="other" class="tab-pane">
							<form  action="<?php echo Url('Admin/System/other'); ?>" id="other-form">
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>屏蔽词： </label>
									<div class="col-sm-9"><textarea id="keywords" name="keywords"  class="textarea"><?php echo htmlentities($other); ?></textarea></div>
								</div>
								<div class="Button_operation">
									<button onclick="save_submit('other');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

		</div>
		
		<script>
			
			function  save_submit(idfend){
				
				var url    = $("#"+idfend+"-form").attr('action');
				var data   = $("#"+idfend+"-form").serialize();
				
				ajaxPost(url,$("#"+idfend+"-form"),data,function (r) {
		            $("#"+idfend+"-form").removeAttr('disabled');
		            location.reload();
		        })
			}
			
			
		</script>
	</body>
	
</html>