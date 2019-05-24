<?php /*a:1:{s:44:"../Theme/adminsys/paymentchannel/addpmt.html";i:1540351774;}*/ ?>
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
		<title>支付渠道</title>
		
	    <script  src="/plugins/layer/layer.js"></script>
	    <script  src="/plugins/common.js"></script>
	</head>

	<body>
		<div class="margin clearfix">
			<div class="stystems_style">
				<div class="tabbable">
					<div class="tab-content">
						<div id=home class="tab-pane active">
							<form  action="<?php echo url('addpmt'); ?>" id="home-form">
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>渠道名称： </label>
									<div class="col-sm-9"><input type="text" id="name" name="name" value="" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>是否绑卡： </label>
									<div class="col-sm-9">
										<select id="bind" name="bind" class="select col-xs-10 ">
											<option value="0">否</option>
											<option value="1">是</option>
										</select>
									</div>
								</div>
								<div class="Button_operation">
									<button onclick="save_submit();" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			
			function  save_submit(){
				var url    = $("#home-form").attr('action');
				var data   = $("#home-form").serialize();
				ajaxPost(url,$("#home-form"),data,function (r) {
		            $("#home-form").removeAttr('disabled');
		            //location.reload();
		            var index = parent.layer.getFrameIndex(window.name);
					parent.layer.close(index);
		        })
			}
			
		</script>
	</body>
	
</html>