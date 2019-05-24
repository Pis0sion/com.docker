<?php /*a:1:{s:36:"../Theme/adminsys/admins\mypass.html";i:1550187480;}*/ ?>
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
							<form class="form-horizontal" role="form" name="save-form" id="add-member-form" action="">
								
                                <div class="form-group">
                                    <label  class="col-lg-2 control-label">旧密码</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="pass" name="pass"  value="" placeholder=" ">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label  class="col-lg-2 control-label">请输入新密码</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" value="" name="passn" id="passn"  placeholder=" ">
                                    </div>
                                </div>
                               <div class="form-group">
                                    <label  class="col-lg-2 control-label">请重复输入密码</label>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" value="" name="passr" id="passr"  placeholder=" ">
                                    </div>
                                </div>
                              
                                <div class="form-group">
                                    <div class="col-lg-offset-2 col-lg-10">
                                        <button type="button" id="save-member" class="btn btn-success">修改</button>
                                    </div>
                                </div>
                            </form>
						</div>
						
						
					</div>
				</div>
			</div>

		</div>
		
		<script>
		    $("#save-member").click(function(){
		        var add_url = "<?php echo url("","",true,false);?>";
		        var pass = $("#pass").val();
		        var passr   = $("#passr").val();
		      
		        if(pass == ''){
		            errorTips("add-member",$("#pass"),"请输入旧密码！");
		            return;
		        }
		        if(passr==''){
		            errorTips("add-member",$("#passr"),"请输入新密码！");
		            return;
		        }
		        
		        var data = $("form[name=save-form]").serialize();
		        ajaxPost(add_url,$("#save-member"),data,function (r) {
		            $("#login_btn").removeAttr('disabled');
		            window.location.href = r.url;
		           // location.reload();
		        })
		    });
		</script>
	</body>
	
</html>