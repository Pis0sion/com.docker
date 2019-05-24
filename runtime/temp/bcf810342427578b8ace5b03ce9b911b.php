<?php /*a:1:{s:43:"../Theme/adminsys/pmtapplycon/pacrcord.html";i:1543544514;}*/ ?>
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

		<script src="/static/admin/assets/js/jquery.easy-pie-chart.min.js"></script>

		<script src="/static/admin/js/lrtk.js" type="text/javascript"></script>

		
		<script  src="/plugins/layer/layer.js"></script>
	    <script  src="/plugins/common.js"></script>
	    <script>
		layer.config({
			extend: 'extend/layer.ext.js'
		});
		</script>
		<title>申请通道列表</title>
	</head>

	<body>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">

						<ul class="search_content clearfix">
							<li><label class="l_f">申请时间：</label>
								<input class="inline laydate-icon" value="<?php echo htmlentities($startTime); ?>" id="start" name="startTime" style=" margin-left:10px;"> —
								<input class="inline laydate-icon" value="<?php echo htmlentities($endTime); ?>" id="end" name="endTime" >
							</li>
							<li style="width:90px;"><button type="button" id="search" class="btn_search"><i class="icon-search"></i>查询</button></li>
							<li style="width:90px;"><button type="button" id="searchAout" class="btn_search"><i class="icon-search"></i>全部</button></li>
						</ul>
					</div>
					<!---->
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th>ID</th>
									<th>申请类型</th>
									<th>申请用户</th>
									<th>申请通道</th>
									<th>申请银行卡</th>
									<th>申请订单号</th>
									<th>申请时间</th>
									<th>申请姓名</th>
									<th>身份证号码</th>
									<th>申请手机号</th>
									<th>申请状态</th>
									<th>申请状态扩展信息</th>
									<th>上游单号</th>
									<th>分配状态</th>
									<th>分配金额</th>
                                  	<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><?php echo htmlentities($v['log_id']); ?></td>
										<td>
											<?php switch($v['log_type']): case "1": ?>信用卡类型<?php break; case "2": ?>网贷申请<?php break; case "3": ?>积分兑换<?php break; endswitch; ?>
										</td>
										<td><?php echo htmlentities(getUser($v['log_user'],'user_account')); ?></td>
										<td><?php echo htmlentities(getbankApply($v['log_bank'],'apply_name')); ?></td>
										<td><?php echo htmlentities($v['log_bank_name']); ?></td>
										<td><?php echo htmlentities($v['log_sn']); ?></td>
										<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['log_time'])? strtotime($v['log_time']) : $v['log_time'])); ?></td>
										<td><?php echo htmlentities($v['log_bank_user']); ?></td>
										<td><?php echo htmlentities($v['log_bank_idcard']); ?></td>
										<td><?php echo htmlentities($v['log_bank_phone']); ?></td>
										<td>
											<?php switch($v['log_state']): case "0": ?>提交请求<?php break; case "1": ?>接受请求<?php break; case "3": ?>拒绝申请<?php break; case "4": ?>申请中<?php break; case "5": ?>申请通过<?php break; case "6": ?>申请失败<?php break; endswitch; ?>
										</td>
										<td><?php echo htmlentities($v['log_expand']); ?></td>
										<td><?php echo htmlentities($v['log_tradeNo']); ?></td>
										<td>
											<?php switch($v['log_fre_type']): case "0": ?>未完成<?php break; case "1": ?>待分配<?php break; case "2": ?>已分配<?php break; endswitch; ?>
										</td>
										<td><?php echo htmlentities($v['log_fre']); ?></td>
                                      	<td>
                                      		<?php if($v['log_fre_type']==1): ?>
                                         	    <a title="分配" href="javascript:;" onclick="dist('<?php echo htmlentities($v['log_id']); ?>')" class="btn btn-xs btn-success">分配</a>
                                          	<?php elseif($v['log_fre_type']==2): ?>
                                          		已分配
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
	//时间选择
	laydate({
		elem: '#start',
		event: 'focus'
	});
	laydate({
		elem: '#end',
		event: 'focus'
	});

	jQuery(function($) {
		
		$('#search').on('click', function() {
			var start = $('#start').val();
			var end   = $('#end').val();
			
			window.location.href = "<?php echo Url('Admin/Pmtapplycon/pacrcord'); ?>?startTime="+start+"&endTime="+end; 
		});
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url('Admin/Pmtapplycon/pacrcord'); ?>"; 
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

	function dist(id){
    	layer.prompt({title: '请输入分配金额', formType: 3}, function(pass, index){
            layer.close(pass);

            if(!/^\d+$/.test(pass)){
               layer.msg('输入格式不正确');
              	return false;
            }
          	ajaxPost("<?php echo Url('Pmtapplycon/dismon'); ?>",$("#bank-btn") , {id:id, mon:pass}, function (e) {
				window.location.reload();
            })
      	});
    }
	
</script>