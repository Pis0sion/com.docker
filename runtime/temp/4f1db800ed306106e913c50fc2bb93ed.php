<?php /*a:1:{s:42:"../Theme/adminsys/pmtapplycon/paclist.html";i:1543544514;}*/ ?>
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
		<title>申请通道列表</title>
	</head>

	<body>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">

						<ul class="search_content clearfix">
							<li><label class="l_f">通道名称</label><input name="keywords" id="keywords" type="text" class="text_add" placeholder="通道名称" style=" width:400px" /></li>
							<li style="width:90px;"><button type="button" id="search" class="btn_search"><i class="icon-search"></i>查询</button></li>
							<li style="width:90px;"><button type="button" id="searchAout" class="btn_search"><i class="icon-search"></i>全部</button></li>
						</ul>
						<div><a title="申请通道" href="javascript:;" onclick="add(<?php echo htmlentities($type); ?>)" class="btn btn-xs btn-success">申请通道</a></div>
					</div>
					<!---->
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th>ID</th>
									<th>通道名称</th>
									<th>渠道支付名称</th>
									<th>费率（扣款）</th>
									<th>申请类别</th>
									<th>申请时间</th>
									<th>控制器</th>
									<th>状态</th>
									<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><?php echo htmlentities($v['apply_id']); ?></td>
										<td><?php echo htmlentities($v['apply_name']); ?></td>
										<td><?php echo htmlentities($v['apply_qdname']); ?></td>
										<td><?php echo htmlentities($v['apply_rate']); ?></td>
										<td>
											<?php switch($v['apply_type']): case "1": ?>信用卡申请<?php break; case "2": ?>网贷申请<?php break; case "3": ?>积分兑换<?php break; endswitch; ?>
										</td>
										<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['apply_time'])? strtotime($v['apply_time']) : $v['apply_time'])); ?></td>
										<td><?php echo htmlentities($v['payment_controller']); ?></td>
										<td>
											<?php if($v['apply_use']==1): ?>
												<a href="javascript:;" title="启用" data-url="<?php echo Url('pacsta'); ?>?id=<?php echo htmlentities($v['apply_id']); ?>&type=0" data-confirm="您确认要设置为禁用状态吗？"  class="link-confirm btn btn-xs btn-success">启用</a>
											<?php else: ?>
												<a href="javascript:;" title="禁用" data-url="<?php echo Url('pacsta'); ?>?id=<?php echo htmlentities($v['apply_id']); ?>&type=1" data-confirm="您确认要设置为启用状态吗？"  class="link-confirm btn btn-xs btn-danger">禁用</a>
											<?php endif; ?>
										</td>
										<td class="td-manage">
											<div><a title="编辑" href="javascript:;" onclick="edit('<?php echo htmlentities($v['apply_id']); ?>')" class="btn btn-xs btn-success">编辑</a></div>
											<a href="javascript:;" title="删除" data-url="<?php echo Url('pacdel'); ?>?id=<?php echo htmlentities($v['apply_id']); ?>" data-confirm="您确认要删除该跳数据吗？"  class="link-confirm btn btn-xs btn-danger">删除</a>
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
			var text = $('#keywords').val();
			if(text==''){
				layer.msg('请输入搜索关键词');
				return false;
			}
			window.location.href = "<?php echo Url('Admin/Pmtapplycon/paclist'); ?>?keywords="+text; 
		});
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url('Admin/Pmtapplycon/paclist'); ?>"; 
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
	
	function add(type) {
		layer.open({
		  type: 2,
		  title: false,
		  closeBtn: 1,
		  area: ['68%', '70%'],
shadeClose: true,
		  skin: 'yourclass',
		  content: "<?php echo Url('pacadd'); ?>?type="+type
		});
	}
	function edit(id) {
		layer.open({
		  type: 2,
		  title: false,
		  closeBtn: 1,
		  area: ['68%', '70%'],
shadeClose: true,
		  skin: 'yourclass',
		  content: "<?php echo Url('pacedit'); ?>?id="+id
		});
	}
	
</script>