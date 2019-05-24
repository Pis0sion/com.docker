<?php /*a:1:{s:38:"../Theme/adminsys/repayment\index.html";i:1550187480;}*/ ?>
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
		
		<script src="/static/admin/assets/laydate/laydate.js" type="text/javascript"></script>
		<script  src="/plugins/layer/layer.js"></script>
	    <script  src="/plugins/common.js"></script>
		<title>计划列表</title>
	</head>

	<body>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">

						<ul class="search_content clearfix">
					 		<li><label class="l_f">计划状态</label>
					 			<select name="state" id="state" style=" width:100px">
					 				<option value="">--全部类别--</option>
					 				<option value="1" <?php if(isset($getdata['state']) && $getdata['state']==1): ?>selected<?php endif; ?>>--还款中--</option>
					 				<option value="2" <?php if(isset($getdata['state']) && $getdata['state']==2): ?>selected<?php endif; ?>>--已完成--</option>
					 				<option value="3" <?php if(isset($getdata['state']) && $getdata['state']==3): ?>selected<?php endif; ?>>--还款失败--</option>
					 			</select>
					 		</li>
					 		<li><label class="l_f">支付状态</label>
					 			<select name="type" id="type" style=" width:100px">
					 				<option value="">--全部类别--</option>
					 				<option value="0" <?php if(isset($getdata['type']) && $getdata['type']==0): ?>selected<?php endif; ?>>--正常--</option>
					 				<option value="1" <?php if(isset($getdata['type']) && $getdata['type']==1): ?>selected<?php endif; ?>>--需补单--</option>
					 				<!-- <option value="2" <?php if(isset($getdata['type']) && $getdata['type']==2): ?>selected<?php endif; ?>>--失败--</option> -->
					 				<option value="3" <?php if(isset($getdata['type']) && $getdata['type']==3): ?>selected<?php endif; ?>>--支付中--</option>
					 			</select>
					 		</li>
					    	<li><label class="l_f">搜索</label><input name="keywords" value="<?php echo !empty($getdata['keywords']) ? htmlentities($getdata['keywords']) : ''; ?>" id="keywords" type="text" class="text_add" placeholder="输入会员名称、电话、计划单号" style=" width:210px"></li>
					    	<li><label class="l_f">执行订单号</label><input name="form_no" value="<?php echo !empty($getdata['form_no']) ? htmlentities($getdata['form_no']) : ''; ?>" id="form_no" type="text" class="text_add" placeholder="执行订单号" style=" width:210px"></li>
							<li style="width:90px;"><button type="button" id="search" class="btn_search"><i class="icon-search"></i>查询</button></li>
							<li style="width:90px;"><button type="button" id="searchAout" class="btn_search"><i class="icon-search"></i>全部</button></li>
							<li style="width:90px;"><a class="btn btn-xs btn-danger" href="/admin/repayment/index.html?state=1&type=1">异常订单</a></li>
						</ul>
					</div>
					<div class="amounts_style">
						<div class="transaction_Money clearfix">
							<div class="Money"><span>还款总额：<?php echo htmlentities($count['count1']); ?>元</span>
								<p>统计每访问期1个小时更新一次</p>
							</div>
							<div class="Money"><span><em>￥</em><?php echo htmlentities($count['today1']); ?>元</span>
								<p>当天成交额</p>
							</div>
							<div class="Money"><span>总手续费：<?php echo htmlentities($count['count_fee']); ?>元</span>
								<p>统计每访问期1个小时更新一次</p>
							</div>
						</div>
						<div class="transaction_Money clearfix">
							<div class="Money"><span>消费总额：<?php echo htmlentities($count['count2']); ?>元</span>
								<p>统计每访问期1个小时更新一次</p>
							</div>
							<div class="Money"><span><em>￥</em><?php echo htmlentities($count['today2']); ?>元</span>
								<p>当天成交额</p>
							</div>
							<div class="Money"><span><em>￥</em><?php echo htmlentities($count['today_fee']); ?>元</span>
								<p>当天手续费</p>
							</div>
						</div>
					</div>
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th width="25"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
									<th>ID</th>
									<th>通道</th>
									<th>计划单号</th>
									<th>用户</th>
									<th>手机</th>
									<th>还款金额</th>
									<th>总手续费</th>
									<th>当前手续费</th>
									<th>开始时间</th>
									<th>结束时间</th>
									<th>执行时间</th>
									<th>还款笔数</th>
									<th>消费笔数</th>
									<th>费率</th>
									<th>银行名称</th>
									<th>当前订单状态</th>
									<th>状态</th>
									<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
										<td><?php echo htmlentities($v['mission_id']); ?></td>
										<td title="<?php echo htmlentities($v['payment_name']); ?>[<?php echo htmlentities($v['payment_id']); ?>]"><?php echo htmlentities($v['channel_name']); ?></td>
										<td title="<?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['mission_time'])? strtotime($v['mission_time']) : $v['mission_time'])); ?>"><?php echo htmlentities($v['mission_form_no']); ?></td>
										<td title="<?php echo htmlentities($v['mission_uid']); ?>"><?php echo htmlentities($v['user_name']); ?></td>
										<td><?php echo htmlentities($v['user_phone']); ?></td>
										<td><?php echo htmlentities($v['mission_money']); ?></td>
										<td><?php echo htmlentities($v['mission_fee']); ?></td>
										<td><?php echo htmlentities($v['mission_at_fee']); ?></td>
										<td>
											<?php echo htmlentities($v['mission_start_time']); ?>
										</td>
										<td>
											<?php echo htmlentities($v['mission_end_time']); ?>
										</td>
										<!-- <td><?php echo htmlentities($v['mission_time']); ?></td> -->
										<td>
											<!-- <?php  $time = date('Y-m-d H:i:s'); if($v['mission_pay_time'] < $time && $v['mission_state']==1 && $v['mission_type']==0): ?>
											<a href="javascript:;" title="发起支付" data-url="<?php echo Url('pay/Task/index'); ?>?id=<?php echo htmlentities($v['mission_id']); ?>&type=1" data-confirm="您确认要发起支付吗？"  class="link-confirm btn btn-xs btn-warning"><?php echo htmlentities($v['mission_pay_time']); ?></a>
											<?php else: ?>
											<?php echo htmlentities($v['mission_pay_time']); endif; ?>
											 -->
											<?php echo htmlentities($v['mission_pay_time']); ?>
										</td>
										<td>
											<?php echo htmlentities($v['mission_repayment_number']); ?>/<?php echo htmlentities($v['mission_repayment']); ?>
										</td>
										<td>
											<?php echo htmlentities($v['mission_consume_number']); ?>/<?php echo htmlentities($v['mission_consume']); ?>
										</td>
										<td><?php echo htmlentities($v['mission_rate']*100); ?>%+<?php echo htmlentities($v['mission_close_rate']); ?></td>
										<td title="[<?php echo htmlentities($v['list_id']); ?>-<?php echo htmlentities($v['card_id']); ?>]"><?php echo htmlentities($v['list_name']); ?></td>
										<td>
											<?php if($v['mission_current_state']==1): ?>
												<a href="javascript:;" title="还款" data-url="<?php echo Url('Admin/Repayment/current_state'); ?>?id=<?php echo htmlentities($v['mission_id']); ?>&type=2" data-confirm="您确认要更改为消费状态吗？"  class="link-confirm btn btn-xs btn-info">还款</a>
											<?php elseif($v['mission_current_state']==2): ?>
												<a href="javascript:;" title="消费" data-url="<?php echo Url('Admin/Repayment/current_state'); ?>?id=<?php echo htmlentities($v['mission_id']); ?>&type=1" data-confirm="您确认要更改为还款状态吗？"  class="link-confirm btn btn-xs btn-warning">消费</a>
											<?php endif; ?>
										</td>
										<td>
											<?php switch($v['mission_state']): case "1": if($v['mission_type']==1 || $v['mission_type']==2): ?>
														<font color="red">需补单</font>
														<a href="javascript:;" title="修改为正常状态" data-url="<?php echo Url('Admin/Repayment/type'); ?>?id=<?php echo htmlentities($v['mission_id']); ?>&type=99" data-confirm="您确认要修改为正常状态吗？"  class="link-confirm btn btn-xs btn-danger">修改为正常状态</a>
													<?php elseif($v['mission_type']==3): ?>
														处理中
													<?php else: ?>
												    	还款中
													<?php endif; break; case "2": ?>已完成<?php break; case "3": ?><font color="red">失败</font><?php break; case "4": ?><font color="red">用户终止计划</font><?php break; case "5": ?><font color="red">未及时付款</font><?php break; case "6": ?><font color="red">后台关闭计划</font><?php break; default: ?>未启动
											<?php endswitch; ?>
										</td>
										<td>
											<a title="计划明细" href="javascript:;" onclick="detail('<?php echo htmlentities($v['mission_id']); ?>')" class="btn btn-xs btn-success">
												计划明细
											</a>
											<?php if($v['mission_state']==1): ?>
												<a href="javascript:;" title="关闭计划" data-url="<?php echo Url('Admin/Repayment/close'); ?>?id=<?php echo htmlentities($v['mission_id']); ?>&type=1" data-confirm="您确认要关闭计划吗？"  class="link-confirm btn btn-xs btn-danger">关闭计划</a>
											<?php elseif($v['mission_state']==0 || $v['mission_state']>2): ?>
												<a href="javascript:;" title="启用计划" data-url="<?php echo Url('Admin/Repayment/close'); ?>?id=<?php echo htmlentities($v['mission_id']); ?>&type=0" data-confirm="您确认要启用计划吗？"  class="link-confirm btn btn-xs btn-success">启用计划</a>
											<?php endif; ?>

											<a href="javascript:;" title="删除计划" data-url="<?php echo Url('Admin/Repayment/del'); ?>?id=<?php echo htmlentities($v['mission_id']); ?>&type=1" data-confirm="您确认要删除计划吗？"  class="link-confirm btn btn-xs btn-danger">删除计划</a>
										</td>
									</tr>
								<?php endforeach; endif; else: echo "" ;endif; ?>
							</tbody>
						</table>
                        <?php echo $list; ?>
					</div>
				</div>
			</div>
		</div>
	</body>

</html>
<script>
	jQuery(function($) {
		
		$('#search').on('click', function() {
			var state = $('#state').val();
			var type = $('#type').val();
			var keywords = $('#keywords').val();
			var form_no = $('#form_no').val();
			window.location.href = "<?php echo Url(''); ?>?state="+state+"&type="+type+"&keywords="+keywords+"&form_no="+form_no; 
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

		laydate({
		    elem: '#start',
		    event: 'focus' 
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

	function detail(id){
			
			layer.open({
			  type: 2,
			  title: false,
			  closeBtn: 1,
			  area: ['90%', '90%'],
			  shadeClose: true,
			  skin: 'yourclass',
			  content: '<?php echo Url('detail'); ?>?id='+id
			});
		}
</script>