<?php /*a:1:{s:39:"../Theme/adminsys/payrecords\index.html";i:1550187480;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
		<script src="/static/admin/assets/js/jquery.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<![endif]-->

		<!--[if !IE]> -->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='/static/admin/assets/js/jquery-2.0.3.min.js'>" + "<" + "/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script type="text/javascript">
		 window.jQuery || document.write("<script src='/static/admin/assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
		</script>
		<![endif]-->
		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='/static/admin/assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
		</script>
		<script src="/static/admin/assets/js/bootstrap.min.js"></script>
		<script src="/static/admin/assets/js/typeahead-bs2.min.js"></script>
		<!-- page specific plugin scripts -->
		<script type="text/javascript" src="/static/admin/js/H-ui.js"></script>
		<script type="text/javascript" src="/static/admin/js/H-ui.admin.js"></script>
		
		<script  src="/plugins/layer/layer.js"></script>
	    <script  src="/plugins/common.js"></script>
		<title>收款订单</title>
	</head>

	<body>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">
					  <form action="" method='get' id="forms">
						<ul class="search_content clearfix">
							<li><input name="keywords" id="keywords" type="text" class="text_add" value="<?php echo !empty($getdata['keywords']) ? htmlentities($getdata['keywords']) : ''; ?>" placeholder="输入会员名称、电话、订单单号" style=" width:400px" /></li>
                          	<li><label class="l_f">状态</label>
					 			<select name="state" id="state" style=" width:100px">
					 				<option value="">--全部--</option>
					 				<option value="1" <?php if(isset($getdata['state']) && $getdata['state']==1): ?>selected<?php endif; ?>>--交易成功--</option>
					 				<option value="2" <?php if(isset($getdata['state']) && $getdata['state']==2): ?>selected<?php endif; ?>>--支付失败--</option>
					 				<option value="3" <?php if(isset($getdata['state']) && $getdata['state']==3): ?>selected<?php endif; ?>>--支付中--</option>
                                  	<option value="4" <?php if(isset($getdata['state']) && $getdata['state']==4): ?>selected<?php endif; ?>>--代付中--</option>
                                  	<option value="5" <?php if(isset($getdata['state']) && $getdata['state']==5): ?>selected<?php endif; ?>>--代付失败--</option>
					 			</select>
					 		</li>
							<li style="width:90px;"><button type="button" id="search" class="btn_search"><i class="icon-search"></i>查询</button></li>
							<li style="width:90px;"><button type="button" id="searchAout" class="btn_search"><i class="icon-search"></i>全部</button></li>
						</ul>
                      </form>
					</div>
					<div class="amounts_style">
						<div class="transaction_Money clearfix">
							<div class="Money"><span>成交总额：<?php echo htmlentities($count['count']); ?>元</span>
								<p>统计每访问期1个小时更新一次</p>
							</div>
							<div class="Money"><span>总手续费：<?php echo htmlentities($count['count_fee']); ?>元</span>
								<p>统计每访问期1个小时更新一次</p>
							</div>
							<div class="Money"><span><em>￥</em><?php echo htmlentities($count['today']); ?>元</span>
								<p>当天成交额</p>
							</div>
							<div class="Money"><span><?php echo htmlentities($count['today_fee']); ?>元</span>
								<p>当天成交手续费</p>
							</div>
						</div>
					</div>
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th width="25"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
									<th width="80">ID</th>
									<th>通道</th>
									<th>姓名</th>
									<th>手机</th>
									<th>银行</th>
									<th>订单号</th>
									<th>订单金额</th>
									<th>实到金额</th>
                                  	<th>入金账户</th>
                                    <th>出金账户</th>
									<th>费率</th>
									<th>状态</th>
									<th>上游提示</th>
									<th>时间</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
										<td><?php echo htmlentities($v['records_id']); ?></td>
										<td title="<?php echo htmlentities($v['payment_name']); ?>[<?php echo htmlentities($v['payment_id']); ?>]"><?php echo htmlentities($v['channel_name']); ?></td>
										<td title="<?php echo htmlentities($v['user_id']); ?>"><?php echo htmlentities($v['user_name']); ?></td>
										<td><?php echo htmlentities($v['user_phone']); ?></td>
										<td title="[<?php echo htmlentities($v['back_id']); ?> - <?php echo htmlentities($v['card_id']); ?>]"><?php echo htmlentities($v['back_name']); ?></td>
										<td title="<?php echo htmlentities($v['records_form_number']); ?>"><?php echo htmlentities($v['records_form_no']); ?></td>
										<td><?php echo htmlentities($v['records_money']); ?></td>
										<td><?php echo htmlentities($v['records_amount']); ?></td>
                                      	<td><?php echo htmlentities(getCard($v['records_cid'],'card_no')); ?></td>
										<td><?php echo htmlentities(getCard($v['records_pay_cid'],'card_no')); ?></td>
										<td><?php echo htmlentities($v['records_rate'] * 100); ?>% +<?php echo htmlentities($v['records_close_rate']); ?>元</td>
										<td>
											<?php switch($v['records_state']): case "0": ?>未确认<?php break; case "1": ?>已完成<?php break; case "2": ?>支付失败<?php break; case "3": ?>支付中<?php break; case "4": ?>待付中<?php break; case "5": ?>代付失败<?php break; default: ?>其他异常
											<?php endswitch; ?>
										</td>
										<td><?php if($v['records_state'] != 1): ?><?php echo htmlentities($v['records_msg']); endif; ?></td>
										<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['records_time'])? strtotime($v['records_time']) : $v['records_time'])); ?></td>
										<td>
											<a title="查询状态" href="javascript:;" onclick="state('<?php echo htmlentities($v['records_id']); ?>')" class="btn btn-xs btn-success" id="state-btn">
												查询状态
											</a>
											<?php if($v['records_state'] == 5): ?>
												<a href="javascript:;" title="补单" data-url="<?php echo Url('bd'); ?>?records_id=<?php echo htmlentities($v['records_id']); ?>&form_no=<?php echo htmlentities($v['records_form_no']); ?>&type=0" data-confirm="您确认要补单吗？"  class="link-confirm btn btn-xs btn-warning">补单</a>
												<a href="javascript:;" title="查询余额" data-url="<?php echo Url('balance_query'); ?>?form_no=<?php echo htmlentities($v['records_form_no']); ?>&type=0" data-confirm="您确认要查询余额吗？"  class="link-confirm btn btn-xs btn-success">查询余额</a>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; endif; else: echo "" ;endif; ?>
							</tbody>
						</table>
					</div>
					<div>
		                <?php echo $list->render(); ?>
		            </div>
				</div>
			</div>
		</div>
	</body>

</html>
<script>
	jQuery(function($) {
		
		$('#search').on('click', function() {
			var form = document.getElementById('forms');

            form.submit();			
		});
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url(''); ?>";
		});
		
		$('table th input:checkbox').on('click', function() {
			var that = this;
			$(this).closest('table').find('tr > td:first-child input:checkbox')
				.each(function() {
					this.checked = that.checked;
					$(this).closest('tr').toggleClass('selected');
				});

		});

		$('[data-rel="tooltip"]').tooltip({
			placement: tooltip_placement
		});

		function tooltip_placement(context, source) {
			var $source = $(source);
			var $parent = $source.closest('table')
			var off1 = $parent.offset();
			var w1 = $parent.width();

			var off2 = $source.offset();
			var w2 = $source.width();

			if(parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
			return 'left';
		}
		
		
	})

	//查询状态
	function  state(id) {
		var url    = "<?php echo Url('state'); ?>";
		var data   = {id:id};
		ajaxPost(url,$("#state-btn"),data,function (r) {
			$("#state-btn").removeAttr('disabled');
			//location.reload();
			var index = parent.layer.getFrameIndex(window.name);
			parent.layer.close(index);
		})
	}
</script>