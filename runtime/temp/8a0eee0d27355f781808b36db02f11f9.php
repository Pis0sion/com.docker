<?php /*a:1:{s:36:"../Theme/adminsys/upgrade/index.html";i:1545356831;}*/ ?>
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
		<link rel="stylesheet" href="/static/admin/font/css/font-awesome.min.css" />
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="/static/admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/static/admin/js/jquery-1.9.1.min.js"></script>
		<script src="/static/admin/assets/js/bootstrap.min.js"></script>

		<script src="/static/admin/assets/js/typeahead-bs2.min.js"></script>
		<script src="/static/admin/assets/layer/layer.js" type="text/javascript"></script>
		<script src="/static/admin/assets/laydate/laydate.js" type="text/javascript"></script>
		
		<title>交易金额</title>
		<script  src="/plugins/layer/layer.js"></script>
	    <script  src="/plugins/common.js"></script>
	</head>

	<body>
		<div class="margin clearfix">
			<div class="search_style">
				<form action="" method="get">
	      			<ul class="search_content clearfix">
                      	<li><label class="l_f">用户手机号 </label><input name="phone" id="phone" type="text" class="text_add" value="<?php if(isset($getdata['phone'])): ?><?php echo htmlentities($getdata['phone']); endif; ?>" placeholder="用户手机号" style=" width:150px"></li>
				 		<li><label class="l_f">订单状态类型</label>
				 			<select name="state" id="state" style=" width:150px">
				 				<option value="">--全部类别--</option>
				 				<option value="0" <?php if(isset($getdata['state']) && $getdata['state']=='0'): ?>selected<?php endif; ?>>--0未支付--</option>
				 				<option value="1" <?php if(isset($getdata['state']) && $getdata['state']=='1'): ?>selected<?php endif; ?>>--支付成功--</option>
				 				<option value="2" <?php if(isset($getdata['state']) && $getdata['state']=='2'): ?>selected<?php endif; ?>>--支付失败--</option>
				 				<option value="3" <?php if(isset($getdata['state']) && $getdata['state']=='3'): ?>selected<?php endif; ?>>--处理中--</option>
				 				<option value="4" <?php if(isset($getdata['state']) && $getdata['state']=='4'): ?>selected<?php endif; ?>>--已退款--</option>
				 				<option value="5" <?php if(isset($getdata['state']) && $getdata['state']=='5'): ?>selected<?php endif; ?>>--免费升级--</option>
				 				<option value="6" <?php if(isset($getdata['state']) && $getdata['state']=='6'): ?>selected<?php endif; ?>>--关系升级--</option>
				 			</select>
				 		</li>
				    	<li><label class="l_f">订单号</label><input name="form_no" value="<?php if(isset($getdata['form_no'])): ?><?php echo htmlentities($getdata['form_no']); endif; ?>" id="form_no" type="text" class="text_add" placeholder="本平台订单号" style=" width:150px"></li>
				    	<li><label class="l_f">上游订单号</label><input name="sn"  value="<?php if(isset($getdata['sn'])): ?><?php echo htmlentities($getdata['sn']); endif; ?>" id="sn" type="text" class="text_add" placeholder="上游订单号" style=" width:150px"></li>
				    	<li><label class="l_f">升级类别</label>
				    		<select name="type_id" id="type_id" style=" width:150px">
				 				<option value="">--全部类别--</option>
				 				<?php if(is_array($listType) || $listType instanceof \think\Collection || $listType instanceof \think\Paginator): $i = 0; $__LIST__ = $listType;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lt): $mod = ($i % 2 );++$i;?>
				 					<option value="<?php echo htmlentities($lt['type_id']); ?>" <?php if(isset($getdata['type_id']) && $getdata['type_id']==$lt['type_id']): ?>selected<?php endif; ?>><?php echo htmlentities($lt['type_name']); ?></option>
				 				<?php endforeach; endif; else: echo "" ;endif; ?>
				 			</select>
				    	</li>
				    	<li><label class="l_f">订单时间</label><input name="time" value="<?php if(isset($getdata['time'])): ?><?php echo htmlentities($getdata['time']); endif; ?>" class="inline laydate-icon" id="start" style=" margin-left:10px;"></li>
				    	<li style="width:90px;"><button type="submit" class="btn_search"><i class="fa fa-search"></i>查询</button></li>
			    	</ul>
		    	</form>
		    </div>

			<div class="amounts_style">
				
				<div class="transaction_Money clearfix">
					<div class="Money"><span>成交总额：<?php echo htmlentities($count['sum']); ?>元</span>
						<p>统计每访问期1个小时更新一次</p>
					</div>
					<div class="Money"><span><em>￥</em><?php echo htmlentities($count['today']); ?>元</span>
						<p>当天成交额</p>
					</div>
				</div>
				
				<div class="border clearfix">
					<span class="l_f">
			    		<a href="<?php echo Url('Admin/Upgrade/index'); ?>" class="btn btn-info">全部订单</a>
			    		<a href="javascript:;" onclick="upgrade();" class="btn btn-danger">当天订单</a>
			       </span>
					<span class="r_f">共：<b><?php echo htmlentities($count['count']); ?></b>笔</span>
				</div>
				<div class="Record_list">
					<table class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th >用户表ID</th>
								<th>支付渠道</th>
								<th>订单号</th>
								<th>上游订单号</th>
								<th>金额</th>
								<th>升级类别</th>
								<th>支付状态</th>
								<th>发起时间</th>
								<th>完成时间</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
							<tr>
								<td><?php echo htmlentities(getUser($v['upgrade_uid'],'user_account')); ?></td>
								<td><?php echo htmlentities(getPayment($v['upgrade_pay_id'],'payment_name')); ?></td>
								<td><?php echo htmlentities($v['upgrade_form_no']); ?></td>
								<td><?php echo htmlentities($v['upgrade_sn']); ?></td>
								<td><?php echo htmlentities($v['upgrade_money']); ?></td>
								<td><?php echo htmlentities(getUserType($v['upgrade_type_id'])); ?></td>
								<td>
								<?php switch($v['upgrade_state']): case "1": ?>支付成功<?php break; case "2": ?>支付失败<?php break; case "3": ?>处理中<?php break; case "4": ?>已退款<?php break; case "5": ?>免费升级<?php break; case "6": ?>关系升级<?php break; default: ?>未支付
								<?php endswitch; ?>
								</td>
								<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['upgrade_time'])? strtotime($v['upgrade_time']) : $v['upgrade_time'])); ?></td>
								<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['upgrade_oktime'])? strtotime($v['upgrade_oktime']) : $v['upgrade_oktime'])); ?></td>
								<td>
									<?php if($v['upgrade_state']==0): ?>
										<a title="补单" href="javascript:;"  data-url="<?php echo Url('Admin/Upgrade/supply'); ?>?id=<?php echo htmlentities($v['upgrade_id']); ?>" data-confirm="您确定要把此订单修改为成功<br/>并修改状态吗？"  class="link-confirm btn btn-xs btn-info Refund_detailed">补单</a>
									<?php endif; ?>
									<a title="删除" href="javascript:;"  data-url="<?php echo Url('Admin/Upgrade/delete'); ?>?id=<?php echo htmlentities($v['upgrade_id']); ?>" data-confirm="确定删除吗？数据不可逆的"  class="link-confirm btn btn-xs btn-danger">删除</a>
								</td>
							</tr>
							<?php endforeach; endif; else: echo "" ;endif; ?>
						</tbody>
					</table>
					<div>
		                <?php echo $list->render(); ?>
		            </div>
				</div>
			</div>
		</div>
		
	</body>

</html>
<script>
	$(function() {
		
		$('table th input:checkbox').on('click', function() {
			var that = this;
			$(this).closest('table').find('tr > td:first-child input:checkbox')
				.each(function() {
					this.checked = that.checked;
					$(this).closest('tr').toggleClass('selected');
				});

		});
		
		laydate({
		    elem: '#start',
		    event: 'focus' 
		});
		
		
	})
	
	function upgrade () {
		parent.layer.open({
			type: 2,
			title: false,
			closeBtn: 1,
			area: ['700px', '600px'],
			shadeClose: true,
			skin: 'yourclass',
			content: '<?php echo Url('Admin/Upgrade/upgrade'); ?>',
			
		});
	}
</script>