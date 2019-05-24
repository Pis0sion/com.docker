<?php /*a:1:{s:37:"../Theme/adminsys/message/addmsg.html";i:1542075852;}*/ ?>
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
					<div class="tab-content">
						<div id="home" class="tab-pane active">
							<form  action="<?php echo url('Admin/Message/dosmg'); ?>" id="home-form">
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>发送标题 </label>
									<div class="col-sm-9"><input type="text" id="sms_title" name="sms_title"  placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
								<div class="form-group">
									<label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>是否群发 </label>
									<div class="col-sm-9">
										   <span class="">
										       <label><input  value="1" name="sms_isqun" type="radio" checked  class="ace" ><span class="lbl">开启群发</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
										       <label><input  value="0" name="sms_isqun" type="radio" class="ace" ><span class="lbl">关闭群发</span></label>
										   </span>     
									</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>发送ID </label>
									<div class="col-sm-9"><input type="text" id="sms_uid" name="sms_uid" placeholder="可以输入1,2,3,4,5,6,7,8,9"  class="col-xs-10 "></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>通知内容： </label>
									<div class="col-sm-9"><textarea id="sms_text" name="sms_text"  class="textarea"></textarea></div>
								</div>
								<div class="Button_operation">
									<button onclick="save_submit('home');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;立即发送</button>
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
				var sms_title = $('#sms_title').val();
				var sms_text = $('#sms_text').val();
				if(sms_title==''){
					layer.msg('请输入标题');
					return;
				}
				
				if(sms_text==''){
					layer.msg('请输入内容');
					return;
				}
				ajaxPost(url,$("#"+idfend+"-form"),data,function (r) {
		            $("#"+idfend+"-form").removeAttr('disabled');
		            location.reload();
		        })
			}
			
			
		</script>
	</body>
	
</html>