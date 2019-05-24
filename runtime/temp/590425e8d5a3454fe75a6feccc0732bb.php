<?php /*a:1:{s:38:"../Theme/adminsys/upgrade/upgrade.html";i:1540351774;}*/ ?>
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
						<div  class="tab-pane active">
							<form  action="<?php echo url('Admin/Upgrade/upgrade'); ?>?uid=<?php echo !empty($userData['user_id']) ? htmlentities($userData['user_id']) : '0'; ?>" id="home-form">
								<div class="search_style">
					      			<ul class="search_content clearfix">
								    	<li><label class="l_f">会员ID</label><input name="uid"  value="<?php echo !empty($userData['user_id']) ? htmlentities($userData['user_id']) : ''; ?>" id="uid" type="text" class="text_add" placeholder="本平台订单号" style=" width:150px"></li>
								    	<li style="width:90px;"><button type="button" onclick="gotoupg();" class="btn_search"><i class="fa fa-search"></i>查询</button></li>
							    	</ul>
							    </div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>姓名/手机号： </label>
									<div class="col-sm-9"><input type="text" id="SITE_NAME"  readonly="readonly" value="<?php echo !empty($userData['user_name']) ? htmlentities($userData['user_name']) : ''; ?>+<?php echo !empty($userData['user_phone']) ? htmlentities($userData['user_phone']) : ''; ?>" placeholder="控制在25个字、50个字节以内" class="col-xs-10 "></div>
								</div>
							
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>目前类型： </label>
									<div class="col-sm-9"><input type="text" id="SITE_DETECTION" placeholder="目前类型" readonly="readonly" value="<?php echo !empty($userData['user_type_id']) ? getUserType($userData['user_type_id']) : ''; ?>" class="col-xs-10"></div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>升级目标： </label>
									<div class="col-sm-9">
										<select name="type" id="type" style=" width:150px">
							 				<?php if(is_array($listType) || $listType instanceof \think\Collection || $listType instanceof \think\Paginator): $i = 0; $__LIST__ = $listType;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lt): $mod = ($i % 2 );++$i;?>
							 					<option value="<?php echo htmlentities($lt['type_id']); ?>"><?php echo htmlentities($lt['type_name']); ?></option>
							 				<?php endforeach; endif; else: echo "" ;endif; ?>
							 			</select>
				 		 			</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>升级目标： </label>
									<div class="col-sm-9">
										<select name="state" id="state" style=" width:150px">
							 					<option value="5">免费升级</option>
							 					<option value="6">关系升级</option>
							 				
							 			</select>
				 		 			</div>
								</div>
								<div class="form-group"><label class="col-sm-1 control-label no-padding-right" for="form-field-1"><i>*</i>升级理由： </label>
						          <div class="col-sm-9"><textarea class="textarea" name="liyou"  style="height: 85px;">默认升级</textarea></div>
						          </div>
								<div class="Button_operation">
									<button onclick="save_submit('<?php echo !empty($userData['user_id']) ? htmlentities($userData['user_id']) : '0'; ?>');" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
						
					</div>
				</div>
			</div>

		</div>
		
		<script>
			
			function  save_submit(idfend){
				var index = parent.layer.getFrameIndex(window.name);
				var url    = $("#home-form").attr('action');
				var data   = $("#home-form").serialize();
				
				ajaxPost(url,$("#home-form"),data,function (r) {
		            $("#home-form").removeAttr('disabled');
		            parent.layer.close(index);
		        })
			}
			function gotoupg(){
				var id = $('#uid').val(); 
				if(id==''){
					layer.msg('请输入会员ID');
					return false;
				}
				window.location.href='<?php echo Url('Admin/Upgrade/upgrade'); ?>?uid='+id;
			}
		</script>
	</body>
	
</html>