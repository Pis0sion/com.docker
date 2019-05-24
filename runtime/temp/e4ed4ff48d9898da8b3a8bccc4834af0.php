<?php /*a:1:{s:38:"../Theme/adminsys/paymentbank/add.html";i:1544697152;}*/ ?>
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

							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>分类： </label>
								<div class="col-sm-8">
									<select name='bank_bid' id="bank_bid" class="col-xs-10 " style="margin-left:10px ">
										<option value="0">请选择银行</option>
										<?php if(is_array($bank_list) || $bank_list instanceof \think\Collection || $bank_list instanceof \think\Paginator): $i = 0; $__LIST__ = $bank_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
											<option value="<?php echo htmlentities($v['list_id']); ?>"><?php echo htmlentities($v['list_name']); ?></option>
										<?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</div>
							</div>

							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>银行名称： </label>
								<div class="col-sm-8"><input type="text" value="" name="name" class="col-xs-10 "></div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>银行编码： </label>
								<div class="col-sm-8"><input type="text" value="" name="code" class="col-xs-10 "></div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>银联号： </label>
								<div class="col-sm-8"><input type="text" value="" name="unionpay_no" class="col-xs-10 "></div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="">银行行号： </label>
								<div class="col-sm-8"><input type="text" value="" name="number_id" class="col-xs-10 "></div>
							</div>

							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="">备用1： </label>
								<div class="col-sm-8"><input type="text" value="" name="bank_num1" class="col-xs-10 "></div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="">备用2： </label>
								<div class="col-sm-8"><input type="text" value="" name="bank_num2" class="col-xs-10 "></div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>通道最小支付金额： </label>
								<div class="col-sm-8"><input type="text" value="" name="min_money" class="col-xs-10 "></div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for=""><i>*</i>通道最大支付金额： </label>
								<div class="col-sm-8"><input type="text" value="" name="max_money" class="col-xs-10 "></div>
							</div>

							<div class="Button_operation">
				                <input name="pay_id" type="hidden" value="<?php echo htmlentities($pay_id); ?>">
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
	    	$('#bank_bid').change(function() {
	    		var id=$("#bank_bid").val();
	    		if(id>0)
	    		{
	    			var url    = "<?php echo Url('change'); ?>";
					var data   = {id:id};
					$.post(url, data, function(_) { 
						if (_.error) {
							ajaxError(_.msg);
						} else {
							document.getElementsByName("name")[0].value = _.bank.list_name; 
							document.getElementsByName("code")[0].value = _.bank.list_code;
							document.getElementsByName("unionpay_no")[0].value = _.bank.list_unionpay_no;
						}
					},'json');
	    		}
	    		
		    });
	    </script>
	</body>

</html>