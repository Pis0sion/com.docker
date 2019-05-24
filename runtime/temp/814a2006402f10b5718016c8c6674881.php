<?php /*a:1:{s:31:"../Theme/adminsys/user/add.html";i:1540351774;}*/ ?>
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
		<title>添加</title>

		<script src="/plugins/layer/layer.js"></script>
		<script src="/plugins/common.js"></script>

	    <script type="text/javascript" charset="utf-8" src="/plugins/ueditor1_4_3_3-utf8-php/ueditor.config.js"></script>
	    <script type="text/javascript" charset="utf-8" src="/plugins/ueditor1_4_3_3-utf8-php/ueditor.all.min.js"> </script>
	    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
	    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
	    <script type="text/javascript" charset="utf-8" src="/plugins/ueditor1_4_3_3-utf8-php/lang/zh-cn/zh-cn.js"></script>
	</head>

	<body>
		<div class="margin clearfix">
			<div class="stystems_style">
				<div class="tabbable">

					<div class="tab-content">
						<form name="bank-form" id="bank-form" action="">
						<div id="home" class="tab-pane active">
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>推广码： </label>
								<div class="col-sm-8">
									<input type="text" value="" name="code" class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>手机： </label>
								<div class="col-sm-8">
									<input type="text" value="" name="user_phone" class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>账户： </label>
								<div class="col-sm-8">
									<input type="text" value="" name="user_account" class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>密码： </label>
								<div class="col-sm-8">
									<input type="text" value="" name="user_password" class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>昵称： </label>
								<div class="col-sm-8">
									<input type="text" value="" name="user_nickname" class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>会员等级： </label>
								<div class="col-sm-8">
									<select name='user_type_id' class="col-xs-10 " style="margin-left:10px ">
										<option value="0">请选择</option>
										<?php if(is_array($user_type) || $user_type instanceof \think\Collection || $user_type instanceof \think\Paginator): $i = 0; $__LIST__ = $user_type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
											<option value="<?php echo htmlentities($v['type_id']); ?>" ><?php echo htmlentities($v['type_name']); ?></option>
										<?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</div>
							</div>

							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>是否实名： </label>
								<div class="col-sm-8">
									<select name='user_real' class="col-xs-10 " style="margin-left:10px ">
										<option value="0">否</option>
										<option value="1">是</option>
										
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>姓名： </label>
								<div class="col-sm-8">
									<input type="text" value="" name="user_name" class="col-xs-10 ">
								</div>
							</div>
							

							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>身份证： </label>
								<div class="col-sm-8">
									<input type="text" value="" name="user_idcard" class="col-xs-10 ">
								</div>
							</div>
							
							
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>用户头像： </label>
								<div class="col-sm-8">
									<label>
				                        <img src="" width="50" height="50" id="img_thumb" onerror="this.src='/static/admin/images/image.png'">
				                        <input name="user_img" type="hidden" id="img" value="">
				                    </label>
				                    <label title="上传图片" for="img_photo" class="btn btn-primary">
				                        <input type="file" name="img_photo" id="img_photo" class="hide" accept="image/gif,image/jpeg,image/x-png"> 上传图片
				                    </label>
								</div>
							</div>
							<div class="Button_operation">
								<input name="type" type="hidden" id="type" value="1">
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

	    <script>
	    	$('#img_photo').change(function(event) {
		        var formData = new FormData();
		        formData.append("file", $(this).get(0).files[0]);
		        $.ajax({
		            url:'/admin/upload/upload_photo',
		            type:'POST',
		            data:formData,
		            cache: false,
		            contentType: false,
		            processData: false,
		            success:function(data){
		                console.log(data)
		                if(data.code=='0'){
		                    $('#img_thumb').attr('src',data.path);
		                    $('#img').val(data.path);
		                    
		                }else{
		                    layer.open({
		                        content:data.msg
		                        ,skin: 'msg'
		                    });
		                }
		            }
		        });
		    });
	       

	    </script>

	</body>

</html>