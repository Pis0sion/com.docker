<?php /*a:1:{s:39:"../Theme/adminsys/agent\applyagent.html";i:1550187480;}*/ ?>
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
		<title>用户列表</title>
	</head>

	<body>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">

						<ul class="search_content clearfix">
							<li><label class="l_f">会员名称</label><input name="keywords" id="keywords" type="text" class="text_add" placeholder="输入会员名称、电话、邮箱" style=" width:400px" /></li>
							<li style="width:90px;"><button type="button" id="search" class="btn_search"><i class="icon-search"></i>查询</button></li>
							<li style="width:90px;"><button type="button" id="searchAout" class="btn_search"><i class="icon-search"></i>全部</button></li>
						</ul>
					</div>
					<!---->
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th width="25"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
									<th >ID</th>
									<th >会员账户</th>
									<th >代理商姓名</th>
									<th >代理商电话</th>
									<!-- <th >申请状态</th> -->
									<th >申请城市</th>
									<th >公司名称能</th>
									<th >申请时间</th>
									<th >操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
										<td><?php echo htmlentities($v['agent_recode_id']); ?></td>
										<td><?php echo htmlentities($v['agent_user_id']); ?></td>
										<td><?php echo htmlentities($v['agent_recode_name']); ?></td>
										<td><?php echo htmlentities($v['agent_recode_phone']); ?></td>
										<!-- <td>
											<?php if($v['agent_recode_state']=='0'): ?>申请中
											<?php elseif($v['agent_recode_state']=='1'): ?>已批准
											<?php else: ?>已拒绝
											<?php endif; ?>
										</td> -->
										<td><?php echo htmlentities($v['agent_recode_city']); ?></td>
										<td><?php echo htmlentities($v['agent_recode_company']); ?></td> 
										<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['agent_recode_time'])? strtotime($v['agent_recode_time']) : $v['agent_recode_time'])); ?></td> 
										<td class="td-manage">
											<?php if($v['agent_recode_state']=='0'): ?>
											<a title="批准" onclick="cageste(<?php echo htmlentities($v['agent_recode_id']); ?>,'1')" class="btn btn-xs btn-info">批准</a>
											<a title="拒绝" onclick="cageste(<?php echo htmlentities($v['agent_recode_id']); ?>,'2')" class="btn btn-xs btn-danger">拒绝</a>
											<?php elseif($v['agent_recode_state']=='1'): ?>已批准
											<?php else: ?>已拒绝
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
			window.location.href = "<?php echo Url('Admin/Agent/applyagent'); ?>?keywords="+text; 
		});
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url('Admin/Agent/applyagent'); ?>"; 
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
	function cageste(id, idtype){
		var str = '';
		if(idtype == 1){
			str = '您确定要批准此用户的代理商申请吗?';
		}else if(idtype == 2){
			str = '您确定要拒绝此用户的代理商申请吗?';
		}
		layer.confirm(str, {
            btn: ['是','否'] //按钮
        }, function(){
        	var urls = "<?php echo Url('Admin/Agent/cagsta'); ?>";
			$.post(urls, {id:id, type:idtype}, function(e){
				if(e.error == 1){
					layer.msg(e.msg);
				}else if(e.error == 0){
					layer.msg(e.msg);
					// window.location.reload();
					window.location.href="<?php echo Url('agent/applyagent'); ?>"
				}
			});
        }, function(){

        });

	}
	
</script>