<?php /*a:1:{s:38:"../Theme/adminsys/issuetype/index.html";i:1540351774;}*/ ?>
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
		<title>平台管理</title>
	</head>

	<body>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">

						<ul class="search_content clearfix">
							<li><label class="l_f">名称</label><input name="keywords" id="keywords" type="text" class="text_add" placeholder="输入名称" style=" width:400px" /></li>
							<li style="width:90px;"><button type="button" id="search" class="btn_search"><i class="icon-search"></i>查询</button></li>
							<li style="width:90px;"><button type="button" id="searchAout" class="btn_search"><i class="icon-search"></i>全部</button></li>
						</ul>
						<div><a title="添加分类" href="javascript:;" onclick="add()" class="btn btn-xs btn-success">添加</a></div>
					</div>
					<!---->
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th width="25"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
									<th>ID</th>
									<th>图标</th>
									<th>平台</th>
									<th>时间</th>
									<th>操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
										<td><?php echo htmlentities($v['type_id']); ?></td>
										<td><img src="<?php echo htmlentities($v['type_icon']); ?>" width="50" height="50" onerror="this.src='/static/admin/images/image.png'"></td>
										<td><?php echo htmlentities($v['type_title']); ?></td>
										<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['type_time'])? strtotime($v['type_time']) : $v['type_time'])); ?></td>
										<td class="td-manage">
											<a title="编辑" href="javascript:;" onclick="edit('<?php echo htmlentities($v['type_id']); ?>')" class="btn btn-xs btn-success">编辑</a>
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
		<!--添加用户图层-->
		
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
			window.location.href = "<?php echo Url('Admin/user/index'); ?>?keywords="+text; 
		});
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url('Admin/user/index'); ?>"; 
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
	
	function add() {
		layer.open({
		  type: 2,
		  title: false,
		  closeBtn: 1,
		  area: ['400px', '300px'],
		  shadeClose: true,
		  skin: 'yourclass',
		  content: "<?php echo Url('add'); ?>"
		});
	}
	function edit(id) {
		layer.open({
		  type: 2,
		  title: false,
		  closeBtn: 1,
		  area: ['400px', '300px'],
		  shadeClose: true,
		  skin: 'yourclass',
		  content: "<?php echo Url('edit'); ?>?id="+id
		});
	}
</script>