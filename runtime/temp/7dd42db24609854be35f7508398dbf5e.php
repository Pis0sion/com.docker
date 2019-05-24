<?php /*a:1:{s:31:"../Theme/adminsys/rate/add.html";i:1540351774;}*/ ?>
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
		<title>添加费率</title>
	</head>
	<body>
		
		<div class="add_menber" id="add_menber_style" >
			<form id="form" name="user-form" style="padding-top:2% ">
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>等级名称： </label>
					<div class="col-sm-8">
						<input name="type_id" value="<?php echo htmlentities($user_type['type_id']); ?>" type="hidden" />
						<input value="<?php echo htmlentities($user_type['type_name']); ?>" id="type_name" name="" type="text" class="form-control"/>

					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>费率： </label>
					<div class="col-sm-8">
						<input value="" id="rate" name="rate" type="text" class="form-control" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>结算费用： </label>
					<div class="col-sm-8">
						<input value="" id="close_rate" name="close_rate" type="text" class="form-control" required/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>类型： </label>
					<div class="col-sm-8">
						<select name="type" class="form-control" style="margin-left: 10px;">
							<option value="0">请选择类型</option>
							<option value="1">还款</option>
							<option value="2">收款</option>
						</select>
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
		            window.location.href="<?php echo Url('index'); ?>?type_id=<?php echo htmlentities($user_type['type_id']); ?>"
		        })
			}
		</script>
	</body>
</html>
