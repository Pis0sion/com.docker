<?php /*a:1:{s:35:"../Theme/adminsys/article/edit.html";i:1540351774;}*/ ?>
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
		<title>编辑文章</title>

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
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>标题： </label>
								<div class="col-sm-8"><input type="text" value="<?php echo htmlentities($info['article_title']); ?>" name="title" class="col-xs-10 "></div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>分类： </label>
								<div class="col-sm-8">
									<select name='' class="col-xs-10 " style="margin-left:10px" disabled>
										<option value="0">请选择分类</option>
										<?php if(is_array($article_type) || $article_type instanceof \think\Collection || $article_type instanceof \think\Paginator): $i = 0; $__LIST__ = $article_type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
											<option value="<?php echo htmlentities($v['type_id']); ?>" <?php if($info['article_type']==$v['type_id']): ?>selected<?php endif; ?>><?php echo htmlentities($v['type_name']); ?></option>
										<?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>内容： </label>
								<div class="col-sm-8">
									<textarea class="col-xs-10" id="editor" style="height: 400px" name="body"><?php echo htmlentities($info['article_body']); ?></textarea>
									<script type="text/javascript">
										var ue = UE.getEditor('editor');
									</script>
								</div>
							</div>

							<?php if($info['article_type']==2): ?>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>图1： </label>
									<div class="col-sm-8">
										<label>
					                        <img src="<?php echo !empty($listimg['0']['img']) ? htmlentities($listimg['0']['img']) : ''; ?>" width="50" height="50" id="img_thumb1" onerror="this.src='/static/admin/images/image.png'">
					                        <input name="listimg[]" type="hidden" id="listimg1" value="<?php echo !empty($listimg['0']['img']) ? htmlentities($listimg['0']['img']) : ''; ?>">
					                    </label>
					                    <label title="上传图片" for="img_photo1" class="btn btn-primary">
					                        <input type="file" name="img_photo1" id="img_photo1" class="hide" accept="image/gif,image/jpeg,image/x-png"> 上传图片
					                    </label>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>图2： </label>
									<div class="col-sm-8">
										<label>
					                        <img src="<?php echo !empty($listimg['1']['img']) ? htmlentities($listimg['1']['img']) : ''; ?>" width="50" height="50" id="img_thumb2" onerror="this.src='/static/admin/images/image.png'">
					                        <input name="listimg[]" type="hidden" id="listimg2" value="<?php echo !empty($listimg['1']['img']) ? htmlentities($listimg['1']['img']) : ''; ?>">
					                    </label>
					                    <label title="上传图片" for="img_photo2" class="btn btn-primary">
					                        <input type="file" name="img_photo2" id="img_photo2" class="hide" accept="image/gif,image/jpeg,image/x-png"> 上传图片
					                    </label>
									</div>
								</div>
							<?php endif; ?>

							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>缩略图： </label>
								<div class="col-sm-8">
									<label>
				                        <img src="<?php echo htmlentities($info['article_img']); ?>" width="50" height="50" id="img_thumb" onerror="this.src='/static/admin/images/image.png'">
				                        <input name="img" type="hidden" id="img" value="<?php echo htmlentities($info['article_img']); ?>">
				                    </label>
				                    <label title="上传图片" for="img_photo" class="btn btn-primary">
				                        <input type="file" name="img_photo" id="img_photo" class="hide" accept="image/gif,image/jpeg,image/x-png"> 上传图片
				                    </label>
								</div>
							</div>
							<div class="Button_operation">
				                <input name="id" type="hidden" value="<?php echo htmlentities($info['article_id']); ?>">
				                <input name="type" type="hidden" value="<?php echo htmlentities($info['article_type']); ?>">
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
	    	$('#img_photo1').change(function(event) {
		        var formData = new FormData();
		        formData.append("file", $(this).get(0).files[0]);
		        $.ajax({
		            url:'/admin/upload/upload_photo',
		            type:'POST',
		            data:formData,
		            cache: false,
		            contentType: false,    //不可缺
		            processData: false,    //不可缺
		            success:function(data){
		                console.log(data)
		                if(data.code=='0'){
		                    $('#img_thumb1').attr('src',data.path);
		                    $('#listimg1').val(data.path);
		                    
		                }else{
		                    layer.open({
		                        content:data.msg
		                        ,skin: 'msg'
		                    });
		                }
		            }
		        });
		    });
	    	$('#img_photo2').change(function(event) {
		        var formData = new FormData();
		        formData.append("file", $(this).get(0).files[0]);
		        $.ajax({
		            url:'/admin/upload/upload_photo',
		            type:'POST',
		            data:formData,
		            cache: false,
		            contentType: false,    //不可缺
		            processData: false,    //不可缺
		            success:function(data){
		                console.log(data)
		                if(data.code=='0'){
		                    $('#img_thumb2').attr('src',data.path);
		                    $('#listimg2').val(data.path);
		                    
		                }else{
		                    layer.open({
		                        content:data.msg
		                        ,skin: 'msg'
		                    });
		                }
		            }
		        });
		    });
	    	$('#img_photo').change(function(event) {
		        var formData = new FormData();
		        formData.append("file", $(this).get(0).files[0]);
		        $.ajax({
		            url:'/admin/upload/upload_photo',
		            type:'POST',
		            data:formData,
		            cache: false,
		            contentType: false,    //不可缺
		            processData: false,    //不可缺
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