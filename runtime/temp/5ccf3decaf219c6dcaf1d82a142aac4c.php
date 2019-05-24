<?php /*a:1:{s:36:"../Theme/adminsys/usertype/edit.html";i:1543901202;}*/ ?>
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
		<title>等级修改</title>
	</head>
	<body>
		
		<div class="add_menber" id="add_menber_style" >
			<form id="user-form" name="user-form" style="padding-top:2% ">
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>等级名称： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($list['type_name']); ?>" id="type_name" name="type_name" type="text"  class="form-control"/>
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>升级费用： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($list['type_fee']); ?>" id="type_fee" name="type_fee" type="text" class="form-control"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>推广用户数量： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($list['type_free_count']); ?>" id="type_free_count" name="type_free_count" type="text"  class="form-control"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>最低还款金额： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($list['type_free_amount']); ?>" id="type_free_amount" name="type_free_amount" type="text"  class="form-control"/>

					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>排序： </label>
					<div class="col-sm-8">
						<input value="<?php echo htmlentities($list['type_sort']); ?>" id="type_sort" name="type_sort" type="text"  class="form-control"/>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="bind">等级分润： </label>
					<div class="col-sm-8">
						<select name="type_profit" class="form-control" style="margin-left: 10px;">
							<option value="0">不参与分润</option>
							<option value="1" <?php if($list['type_profit']==1): ?> selected="selected" <?php endif; ?>>普通分润</option>
							<option value="2" <?php if($list['type_profit']==2): ?> selected="selected" <?php endif; ?>>VIP分润</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right" for="form-field-1"></label>
					<div class="col-sm-8">
						<button id="sub_btn" onclick="saveSubmit(<?php echo htmlentities($list['type_id']); ?>);" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
					</div>
				</div>
			</form>
		</div>
		<script>
			function saveSubmit (id) {
				var index = parent.layer.getFrameIndex(window.name);
				var log_url       = '<?php echo Url(''); ?>?id='+id;
		        var user_nickname = $("#user_nickname").val();
		        var user_name     = $("#user_name").val();
		        var user_phone    = $("#user_phone").val();
				var user_idcard   = $("#user_idcard").val();
		        if(user_nickname==''){
		            errorTips("sub_btn",$("#user_nickname"),"请输入用户昵称");
		            return;
		        }
		        if(user_name==''){
		            errorTips("sub_btn",$("#user_name"),"请输入用户姓名");
		            return;
		        }
				if(user_phone==''){
					errorTips("sub_btn",$("#user_phone"),"请输入移动电话");
		            return;
				}
				if(user_idcard==''){
					errorTips("sub_btn",$("#user_idcard"),"请输入身份证号");
		            return;
				}
				var data   = $("#user-form").serialize();
		        ajaxPost(log_url,$("#sub_btn"),data,function (r) {
		            $("#sub_btn").removeAttr('disabled');
		           parent.layer.close(index);//window.location.href = r.url;
		        })
			}
		</script>
	</body>
</html>
