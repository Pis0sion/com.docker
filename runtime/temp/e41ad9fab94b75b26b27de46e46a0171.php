<?php /*a:1:{s:42:"../Theme/adminsys/paymentchannel/edit.html";i:1540351774;}*/ ?>
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
		<script src="/plugins/jquery-1.9.1.min.js"></script>
		<script src="/static/admin/assets/js/bootstrap.min.js"></script>
		<script src="/static/admin/assets/js/typeahead-bs2.min.js"></script>
		<script src="/static/admin/assets/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="/static/admin/assets/js/jquery.ui.touch-punch.min.js"></script>
		<script src="/static/admin/assets/js/ace-elements.min.js"></script>
		<script src="/static/admin/assets/js/ace.min.js"></script>
		<title>编辑</title>

		<script src="/plugins/layer/layer.js"></script>
		<script src="/plugins/common.js"></script>

		<script type="text/javascript" charset="utf-8" src="/plugins/ueditor1_4_3_3-utf8-php/ueditor.config.js"></script>
		<script type="text/javascript" charset="utf-8" src="/plugins/ueditor1_4_3_3-utf8-php/ueditor.all.min.js"> </script>
		<script type="text/javascript" charset="utf-8" src="/plugins/ueditor1_4_3_3-utf8-php/lang/zh-cn/zh-cn.js"></script>


	    <!-- <link href="/plugins/umeditor1_2_3-utf8-php/themes/default/css/umeditor.css" type="text/css" rel="stylesheet">
	    <script type="text/javascript" src="/plugins/umeditor1_2_3-utf8-php/third-party/jquery.min.js"></script>
	    <script type="text/javascript" charset="utf-8" src="/plugins/umeditor1_2_3-utf8-php/umeditor.config.js"></script>
	    <script type="text/javascript" charset="utf-8" src="/plugins/umeditor1_2_3-utf8-php/umeditor.min.js"></script>
	    <script type="text/javascript" src="/plugins/umeditor1_2_3-utf8-php/lang/zh-cn/zh-cn.js"></script> -->
	</head>
	<body>
		<div class="margin clearfix">
			<div class="stystems_style">
				<div class="tabbable">
					<div class="tab-content">
						<form name="bank-form" id="bank-form" action="">
						<div id="home" class="tab-pane active">
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right"><i>*</i>名称： </label>
								<div class="col-sm-8"><input  class="form-control" value="<?php echo htmlentities($info['channel_name']); ?>" name="title" class="col-xs-10 "></div>
							</div>
							<!-- <div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind">需要绑卡： </label>
								<div class="col-sm-8">
									<select name="bind" class="form-control">
									  <option value="0">否</option>
									  <option value="1" <?php if($info['channel_bind']==1): ?>selected<?php endif; ?>>是</option>
									</select>
								</div>
							</div> -->
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="use">状态： </label>
								<div class="col-sm-8">
									<select name="use" class="form-control">
									  <option value="0">未启用</option>
									  <option value="1" <?php if($info['channel_use']==1): ?>selected<?php endif; ?>>启用</option>
									</select>
								</div>
							</div>
							
							<div class="Button_operation">
				                <input name="id" type="hidden" value="<?php echo htmlentities($info['channel_id']); ?>">
								<button onclick="saveSubmit();" id="bank-btn" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			function  saveSubmit () {
				var url    = $("#bank-form").attr('action');
				var data   = $("#bank-form").serialize();
				ajaxPost(url,$("#bank-btn"),data,function (r) {
		            $("#bank-btn").removeAttr('disabled');
		            //location.reload();
                    window.parent.location.reload();
		            var index = parent.layer.getFrameIndex(window.name);
					parent.layer.close(index);
		        })
			}
		</script>
	</body>

</html>