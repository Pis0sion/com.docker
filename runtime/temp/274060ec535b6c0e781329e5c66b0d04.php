<?php /*a:1:{s:38:"../Theme/adminsys/versions/addver.html";i:1542075408;}*/ ?>
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
								<label class="col-sm-2 control-label no-padding-right" for="title"><i>*</i>版本号： </label>
								<div class="col-sm-8"><input type="text" value="" name="app_verber" id="app_verber" class="col-xs-10 "></div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="url"><i>*</i>Android热下载地址： </label>
								<div class="col-sm-6"><input type="text" value="" id="apk_url" name="apk_url" class="col-xs-10 "></div>
								<div class="col-sm-2">
								<label title="上传图片" for="img_photo" class="btn btn-primary">
				                    <input type="file" name="img_photo" id="img_photo" class="hide" accept="*"> 上传文件
				                </label>
				               </div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="url"><i>*</i>Android下载地址： </label>
								<div class="col-sm-6"><input type="text" value="" id="app_androlink" name="app_androlink" class="col-xs-10 "></div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="url"><i>*</i>IOS热下载地址： </label>
								<div class="col-sm-6"><input type="text" id="ipa_url" value="" name="ipa_url" class="col-xs-10 "></div>
								<div class="col-sm-2">
									<label title="上传图片" for="img_photoa" class="btn btn-primary">
					                    <input type="file" name="img_photoa" id="img_photoa" class="hide" accept="*"> 上传文件
					                </label>
				                </div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="url"><i>*</i>IOS分发下载地址： </label>
								<div class="col-sm-6"><input type="text" value="" id="app_ioslink" name="app_ioslink" class="col-xs-10 "></div>
							</div>
							<div class="Button_operation">
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
				var url   		  = $("#bank-form").attr('action');
				var data  		  = $("#bank-form").serialize();
				var app_verber    = $('app_verber').val();
				var apk_url       = $('apk_url').val();
				var ipa_url 	  = $('ipa_url').val();
				var app_ioslink   = $('app_ioslink').val();
				var app_androlink = $('app_androlink').val();
				
				
				
				if(app_verber==''){
					layer.msg('请输入版本号');
				}
				if(app_verber==''){
					layer.msg('请输入安卓下载地址');
				}
				if(ipa_url==''){
					layer.msg('请输入苹果下载地址');
				}
				if(app_ioslink==''){
					layer.msg('请输入安卓分发下载地址');
				}
				if(app_androlink==''){
					layer.msg('请输入苹果分发下载地址');
				}
				ajaxPost(url,$("#bank-btn"),data,function (r) {
		            $("#bank-btn").removeAttr('disabled');
					window.location.reload();
		        })
			}
		</script>

	    <script>
	    	$('#img_photo').change(function(event) {
		        var formData = new FormData();
		        formData.append("file", $(this).get(0).files[0]);
		        $.ajax({
		            url:'/admin/Versions/upload_apk',
		            type:'POST',
		            data:formData,
		            cache: false,
		            contentType: false,    //不可缺
		            processData: false,    //不可缺
		            success:function(data){
		                console.log(data)
		                if(data.code=='0'){
		                    $('#apk_url').val(data.path);
		                    
		                }else{
		                    layer.open({
		                        content:data.msg
		                        ,skin: 'msg'
		                    });
		                }
		            }
		        });
		    });
	        //aip:deb、ipa 和 pxl
	        $('#img_photoa').change(function(event) {
		        var formData = new FormData();
		        formData.append("file", $(this).get(0).files[0]);
		        $.ajax({
		            url:'/admin/Versions/upload_ipa',
		            type:'POST',
		            data:formData,
		            cache: false,
		            contentType: false,    //不可缺
		            processData: false,    //不可缺
		            success:function(data){
		                console.log(data)
		                if(data.code=='0'){
		                    $('#ipa_url').val(data.path);
		                    
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