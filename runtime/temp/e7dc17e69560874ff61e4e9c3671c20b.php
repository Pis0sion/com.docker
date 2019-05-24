<?php /*a:1:{s:41:"../Theme/adminsys/payment/addpayment.html";i:1547802338;}*/ ?>
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
		<title>新增通道</title>
		
	    <script  src="/plugins/layer/layer.js"></script>
	    <script  src="/plugins/common.js"></script>
	</head>

	<body>
		<div class="margin clearfix">
			<div class="stystems_style">
				<div class="tabbable">
					<div class="tab-content">
						<div id="home" class="tab-pane active">
							<form  action="<?php echo url('Admin/Payment/addpayment'); ?>?id=<?php echo htmlentities($channel['channel_id']); ?>" id="pmt-form">
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>渠道名称： </label>
									<div class="col-sm-8"><input type="text" id="channel_name" name="channel_name" value="<?php echo htmlentities($channel['channel_name']); ?>"  readonly="readonly" class="form-control"></div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>支付名称： </label>
									<div class="col-sm-8"><input type="text" id="name" name="name" value="" placeholder="控制在25个字、50个字节以内"  class="form-control"></div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>费率： </label>
									<div class="col-sm-8">
										<input type="number" id="rate" name="rate" value="0.003"  class="form-control" step="0.0001">
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>结算费用： </label>
									<div class="col-sm-8">
										<input type="number" id="close_fee" name="close_fee"  value="0.5"  class="form-control" step="0.5">
									</div>
								</div>
								
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>通道类型： </label>
									<div class="col-sm-8">
										<select id="type" name="type" class="select form-control" style="margin-left: 10px;">
											<option value="1">收款</option>
											<option value="2">还款</option>
											<option value="3">代付</option>
											<option value="4">会员升级</option>
											<!-- <option value="5">混合</option> -->
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>通道状态： </label>
									<div class="col-sm-8">
										<select id="use" name="use" class="select form-control" style="margin-left: 10px;">
											<option value="0">未启用</option>
											<option value="1" selected>启用</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right"><i>*</i>每天最大笔数： </label>
									<div class="col-sm-8">
										<input type="number" class="form-control"  name="day_num" value="5">
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right"><i>*</i>最大笔数： </label>
									<div class="col-sm-8">
										<input type="number" class="form-control" name="payment_num" class="col-xs-10" value="30">
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1">时间限制： </label>
									<div class="col-sm-8"><input type="text" name="entime" id="entime"  class="form-control" value="0"></div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>控制器： </label>
									<div class="col-sm-8"><input type="text" name="controller" id="controller"  class="form-control"></div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>是否绑卡： </label>
									<div class="col-sm-8">
										<select id="bind" name="bind" class="select form-control" style="margin-left: 10px;">
											<option value="0">否</option>
											<option value="1">是</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>是否绑储蓄卡： </label>
									<div class="col-sm-8">
										<select id="bind" name="bind_d" class="select form-control" style="margin-left: 10px;">
											<option value="0">否</option>
											<option value="1">是</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind">绑卡方式： </label>
									<div class="col-sm-8">
										<select name="bind_way" class="form-control" style="margin-left: 10px;">
											<option value="api" <?php if($info['payment_bind_way']=='api'): ?>selected<?php endif; ?>>api</option>
											<option value="web" <?php if($info['payment_bind_way']=='web'): ?>selected<?php endif; ?>>web</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right"><i></i>单笔最小金额： </label>
									<div class="col-sm-8">
										<input type="number" class="form-control"  name="min_money"  step="100">
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right"><i></i>单笔最大金额： </label>
									<div class="col-sm-8">
										<input type="number" class="form-control"  name="max_money"  step="100">
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>总款限制： </label>
									<div class="col-sm-8"><input type="text" name="money" id="money"  class="form-control" value="999999"></div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1">风控值(开始)： </label>
									<div class="col-sm-8"><input type="text" name="risk_start" id="risk_start"  class="form-control" value="1"></div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1">风控值(结束)： </label>
									<div class="col-sm-8"><input type="text" name="risk_end" id="risk_end"  class="form-control" value="50"></div>
								</div>
								
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind"><i>*</i>扣款模式： </label>
									<div class="col-sm-8">
										<select name="money_mode" id="money_mode" class="form-control" style="margin-left: 10px;">
											<option value="0" <?php if($info['payment_money_mode']==0): ?> selected="selected" <?php endif; ?>>扣款有小数</option>
											<option value="1" <?php if($info['payment_money_mode']==1): ?> selected="selected" <?php endif; ?>>扣款无小数</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind"><i>*</i>还款模式： </label>
									<div class="col-sm-8">
										<select name="mode" id="mode" class="form-control" style="margin-left: 10px;">
												<option value="0">请选择还款模式</option>
												<option value="1">多刷一还</option>
												<option value="2">一刷多还</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind"><i>*</i>计划模式： </label>
									<div class="col-sm-8">
										<select name="pattern" id="pattern" class="form-control" style="margin-left: 10px;">
												<option value="0">请选择计划模式</option>
												<option value="1" >一刷一还</option>
												<option value="2" >二刷一还或一刷二还</option>
												<option value="3" >三刷一还或一刷三还</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind"><i>*</i>是否立即支付： </label>
									<div class="col-sm-8">
										<select name="paynow" id="paynow" class="form-control" style="margin-left: 10px;">
											<option value="0">否</option>
											<option value="1">是</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind"><i>*</i>是否支持地区： </label>
									<div class="col-sm-8">
										<select name="region" id="region" class="form-control" style="margin-left: 10px;">
												<option value="0">否</option>
												<option value="1">是</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind"><i>*</i>是否支持通道自动获取行业： </label>
									<div class="col-sm-8">
										<select name="mcc" id="mcc" class="form-control" style="margin-left: 10px;">
												<option value="0">否</option>
												<option value="1">是</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind"><i>*</i>是否有余额： </label>
									<div class="col-sm-8">
										<select name="pay_mode" id="pay_mode" class="form-control" style="margin-left: 10px;">
												<option value="0">否</option>
												<option value="1">是</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="bind"><i>*</i>订单关联： </label>
									<div class="col-sm-8">
										<select name="orders" id="orders" class="form-control" style="margin-left: 10px;">
												<option value="0">否</option>
												<option value="1">是</option>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right"><i></i>查询列表时间(分钟)： </label>
									<div class="col-sm-8">
										<input type="number" class="form-control"  name="que" value="10">
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right"><i></i>最小计划间隔时间(分钟)： </label>
									<div class="col-sm-8">
										<input type="number" class="form-control"  name="interval_time" value="60">
									</div>
								</div>
								<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>配置信息： </label>
						          <div class="col-sm-8"><textarea id="config" name="config" class="form-control" style="height: 75px;" placeholder="key:value|key:value|key:value|....."></textarea></div>
						        </div>
								<div class="Button_operation">
									<button onclick="pmt_submit();" id="btn-pmt" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

		</div>
		
		<script>
			
			function  pmt_submit(idfend){
				
				var url    		= $("#pmt-form").attr('action');
				var name   		= $('#name').val();
				var rate   	    = $('#rate').val();
				var close_fee   = $('#close_fee').val();
				var controller  = $('#controller').val();
				var day_num     = $('#day_num').val();
				var min_money   = $('#min_money').val();
				var max_money   = $('#max_money').val();
				var config      = $('#config').val();
				var money   	= $('#money').val();
				var payment_num = $('#payment_num').val();

				if(name==''){
					layer.msg('请输入支付名称');return false;
				}
				if(rate==''){
					layer.msg('请输入费率信息');return false;
				}
				if(close_fee==''){
					layer.msg('请输入结算费用');return false;
				}
				if(controller==''){
					layer.msg('请输入控制器文件');return false;
				}
				if(day_num=''){
					layer.msg('请输入每天最大笔数');return false;
				}
				if(payment_num=''){
					layer.msg('请输入最大笔数');return false;
				}
				if(min_money==''){
					layer.msg('请输入单笔最小金额');return false;
				}
				if(max_money==''){
					layer.msg('请输入单笔最大金额');return false;
				}
				if(money==''){
					layer.msg('请填写总款限制金额');return false;
				}
				var data   = $("#pmt-form").serialize();
				ajaxPost(url,$("#btn-pmt"),data,function (r) {
		            $("#btn-pmt").removeAttr('disabled');
		           window.location.href="<?php echo Url('index'); ?>?pid=<?php echo htmlentities($channel['channel_id']); ?>"
		        })
			}
			
		</script>
	</body>
	
</html>