<?php /*a:1:{s:39:"../Theme/adminsys/repayment/detail.html";i:1547191031;}*/ ?>
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
		<title>计划列表</title>
	</head>

	<body>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">

						<ul class="search_content clearfix">
							<li><input name="keywords" id="keywords" type="text" class="text_add" placeholder="输入会员名称、电话、计划单号" style=" width:400px" /></li>
							<li style="width:90px;"><button type="button" id="search" class="btn_search"><i class="icon-search"></i>查询</button></li>
							<li style="width:90px;"><button type="button" id="searchAout" class="btn_search"><i class="icon-search"></i>全部</button></li>
							<li style="width:90px;"><a class="btn btn-xs btn-danger" href="/admin/repayment/detail.html?id=<?php echo htmlentities($id); ?>&state=2">异常订单</a></li>
						</ul>
					</div>
					<!---->
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th width="25"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
									<th width="80">ID</th>
									<th>计划单号</th>
									<th>行业</th>
									<th>还款/扣款金额</th>
									<th>手续费</th>
									<th>计划还款/扣款时间</th>
									<th>执行还款/扣款时间</th>
									<th>类型</th>
									<th>状态</th>
									<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
										<td><?php echo htmlentities($v['plan_id']); ?></td>
										<td><?php echo htmlentities($v['plan_form_no']); ?></td>
										<td title="<?php echo htmlentities($v['plan_mcc']); ?>"><?php echo htmlentities($v['plan_mcc_name']); ?></td>
										<td><?php echo htmlentities($v['plan_money']); ?></td>
										<td><?php echo htmlentities($v['plan_fee']); ?></td>
										<td><?php echo htmlentities($v['plan_pay_time']); ?></td>
										<td><?php echo htmlentities($v['plan_time']); ?></td>
										<td><?php echo htmlentities($v['type_name']); ?></td>
										<td><?php echo htmlentities($v['status']); ?></td>
										<td>
											<a title="查询状态" href="javascript:;" onclick="state('<?php echo htmlentities($v['plan_form_no']); ?>')" class="btn btn-xs btn-success" id="state-btn">
												查询状态
											</a>

											<a title="修改" href="javascript:;" onclick="edit('<?php echo htmlentities($v['plan_id']); ?>')" class="btn btn-xs btn-warning">
												修改
											</a>
											<?php if($v['plan_state']==2): ?>
												<a href="javascript:;" title="补单" data-url="<?php echo Url('Pay/task/repayment_bd'); ?>?id=<?php echo htmlentities($v['plan_id']); ?>&form_no=<?php echo htmlentities($v['plan_form_no']); ?>&type=0" data-confirm="您确认要补单吗？"  class="link-confirm btn btn-xs btn-info">补单</a>
												<?php if($v['plan_type']==1): ?>
													<a href="javascript:;" title="查询余额" data-url="<?php echo Url('balance_query'); ?>?id=<?php echo htmlentities($v['plan_id']); ?>&type=0" data-confirm="您确认要查询余额吗？"  class="link-confirm btn btn-xs btn-success">查询余额</a>
												<?php endif; endif; if($v['plan_state']==3): ?>
												<a title="处理中" href="javascript:;" class="btn btn-xs btn-danger">
												处理中
												</a>
											<?php endif; ?>
											<!-- <?php if($v['plan_state']==1): ?>
												<a href="javascript:;" title="设为已退款" data-url="<?php echo Url('pay_state',array('id'=>$v['plan_id'],'type'=>4)); ?>" data-confirm="您确认要设为已退款吗？"  class="link-confirm btn btn-xs btn-danger">已退款</a>
											<?php endif; ?> -->
<!-- 											<?php if($v['plan_state']==0 ||  $v['plan_state']==2 || $v['plan_state']==3): ?>
												<a href="javascript:;" title="设为已支付" data-url="<?php echo Url('pay_state',array('id'=>$v['plan_id'],'type'=>1)); ?>" data-confirm="您确认要设为已支付吗？"  class="link-confirm btn btn-xs btn-danger">已支付</a>
											<?php endif; if($v['plan_state']==0 ||  $v['plan_state']==1 || $v['plan_state']==3): ?>
												<a href="javascript:;" title="设为支付失败" data-url="<?php echo Url('pay_state',array('id'=>$v['plan_id'],'type'=>2)); ?>" data-confirm="您确认要设为支付失败吗？"  class="link-confirm btn btn-xs btn-danger">支付失败</a>
											<?php endif; if($v['plan_state']==0 ||  $v['plan_state']==1 || $v['plan_state']==2): ?>
												<a href="javascript:;" title="设为支付中" data-url="<?php echo Url('pay_state',array('id'=>$v['plan_id'],'type'=>3)); ?>" data-confirm="您确认要设为支付中吗？"  class="link-confirm btn btn-xs btn-danger">支付中</a>
											<?php endif; ?> -->
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
			var text = $('#keywords').val();
			if(text==''){
				layer.msg('请输入搜索关键词');
				return false;
			}
			window.location.href = "<?php echo Url(); ?>?id=<?php echo htmlentities($id); ?>&keywords="+text; 
		});
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url(); ?>?id=<?php echo htmlentities($id); ?>"; 
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
	// 补单
	function pay_bd(id){
			
		layer.open({
		  type: 2,
		  title: false,
		  closeBtn: 1,
		  area: ['90%', '90%'],
		  shadeClose: true,
		  skin: 'yourclass',
		  content: "<?php echo Url('Pay/index/pay_bd'); ?>?id="+id
		});
	}
	// 处理支付
	function processed(id){
		layer.open({
		  type: 2,
		  title: false,
		  closeBtn: 1,
		  area: ['90%', '90%'],
		  shadeClose: true,
		  skin: 'yourclass',
		  content: "<?php echo Url('Pay/index/processed'); ?>?id="+id
		});
	}
	
	//查询状态
	function  state(id) {
		var url    = "<?php echo Url('state'); ?>";
		var data   = {id:id};
		ajaxPost(url,$("#state-btn"),data,function (r) {
			$("#state-btn").removeAttr('disabled');
			location.reload();
		})
	}
	function edit(id){
			
			layer.open({
			  type: 2,
			  title: false,
			  closeBtn: 1,
			  area: ['60%', '50%'],
			  shadeClose: true,
			  skin: 'yourclass',
			  content: '<?php echo Url('edit'); ?>?id='+id,
			  end: function () {
		        location.reload();
		      }
			});
		}
</script>