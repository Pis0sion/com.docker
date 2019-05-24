<?php /*a:1:{s:37:"../Theme/adminsys/repayment/edit.html";i:1547189018;}*/ ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

		<!-- <![endif]-->

		<!--[if IE]>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<![endif]-->

		<!--[if !IE]> -->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='/static/admin/assets/js/jquery-2.0.3.min.js'>" + "<" + "/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script type="text/javascript">
		 window.jQuery || document.write("<script src='/static/admin/assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
		</script>
		<![endif]-->
		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='/static/admin/assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
		</script>
		<script src="/static/admin/assets/js/bootstrap.min.js"></script>
		<script src="/static/admin/assets/js/typeahead-bs2.min.js"></script>
		<!-- page specific plugin scripts -->
		<script type="text/javascript" src="/static/admin/js/H-ui.js"></script>
		<script type="text/javascript" src="/static/admin/js/H-ui.admin.js"></script>
		
		<script  src="/plugins/layer/layer.js"></script>
	    <script  src="/plugins/common.js"></script>
		<title>修改</title>
	</head>
	<body>
		
		<div class="add_menber" id="add_menber_style" >
			<form id="form" name="user-form" style="padding-top:2% ">
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>订单号： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($plan_form_no); ?>" id="form_no" name="form_no" type="text" class="form-control" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>金额： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($plan_money); ?>" id="money" name="money" type="text" class="form-control" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>手续费： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($plan_fee); ?>" id="fee" name="fee" type="text" class="form-control" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>返回信息： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($plan_msg); ?>" id="msg" name="msg" type="text" class="form-control" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>状态： </label>
					<div class="col-sm-8">
						<select name="state" class="form-control" style="margin-left: 10px;">
							<option value="0">未执行</option>
							<option value="1" <?php if($plan_state==1): ?>selected<?php endif; ?>>已支付</option>
							<option value="2" <?php if($plan_state==2): ?>selected<?php endif; ?>>失败</option>
							<option value="3" <?php if($plan_state==3): ?>selected<?php endif; ?>>支付中</option>
							<option value="4" <?php if($plan_state==4): ?>selected<?php endif; ?>>已退款</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>时间： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($plan_pay_time); ?>" id="pay_time" name="pay_time" type="text" class="form-control" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""></label>
					<div class="col-sm-8">
						<button onclick="saveSubmit();" id="bank-btn" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
					</div>
				</div>
			</form>
		</div>
		<script>
			function  saveSubmit() {
				var url    = $("#form").attr('action');
				var data   = $("#form").serialize();
				ajaxPost(url,$("#bank-btn"),data,function (r) {
		            $("#bank-btn").removeAttr('disabled');
		            var index = parent.layer.getFrameIndex(window.name);
					parent.layer.close(index);
		        })
			}
		</script>
	</body>
</html>
