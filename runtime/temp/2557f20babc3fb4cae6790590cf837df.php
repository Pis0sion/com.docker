<?php /*a:1:{s:34:"../Theme/adminsys/agent\index.html";i:1550187480;}*/ ?>
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
							<li ><button type="button"  onClick="location.href='<?php echo Url('Admin/Agent/addent'); ?>'"  id="searchAout" class="btn_search"style="width:100px;" ><i class="icon-user"></i>添加代理</button></li>
						</ul>
					</div>
					<!---->
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th width="25"><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
									<th >ID</th>
									<th >推广码</th>
									<th >推荐上级</th>
									<th >用户名</th>
									<th >姓名</th>
									<th >电话</th>
									<th >身份证</th>
									<th >客户承载量</th>
									<th >可用数量</th>
									<th >代理类型</th>
									<th >注册时间</th>
									<th >状态</th>
									<th >操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
										<td><?php echo htmlentities($v['agent_id']); ?></td>
										<td><?php echo htmlentities($v['agent_code']); ?></td>
										<td><?php echo htmlentities(getAgent($v['agent_pid'],'agent_account')); ?></td>
										<td><?php echo htmlentities($v['agent_account']); ?></td>
										<td><?php echo htmlentities($v['agent_name']); ?></td>
										<td><?php echo htmlentities($v['agent_phone']); ?></td>
										<td><?php echo htmlentities($v['agent_idcard']); ?></td> 
										<td><?php echo htmlentities($v['agent_capacity']); ?></td>
										<td><?php echo htmlentities($v['agent_can_allot']); ?></td> 
										<td><?php echo htmlentities(getAentGrade($v['agent_grade'])); ?></td> 
										<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['agent_time'])? strtotime($v['agent_time']) : $v['agent_time'])); ?></td> 
										<td class="td-status"><span  data-url="<?php echo Url('Admin/Agent/updestate'); ?>?id=<?php echo htmlentities($v['agent_id']); ?>" data-confirm="您确认<?php echo $v['agent_state']==1 ? '冻结解除冻结' : '冻结'; ?>吗？"  class=" link-confirm label label-<?php echo $v['agent_state']==1 ? 'danger' : 'success'; ?>  radius"><?php echo $v['agent_state']==1 ? '冻结' : '正常'; ?></span></td>
										<td class="td-manage">
											<a href="javascript:;" title="查看费率" onclick="lookcRate(<?php echo htmlentities($v['agent_id']); ?>)"  class=" btn btn-xs"><i class="icon-cogs bigger-120"></i></a>
											
											<a href="javascript:;" title="停用" data-url="<?php echo Url('Admin/Agent/resets'); ?>?id=<?php echo htmlentities($v['agent_id']); ?>" data-confirm="您确认要重置登陆密码吗？"  class="link-confirm btn btn-xs btn-success"><i class="icon-lock bigger-120"></i></a>
											<a title="发展会员"   href="<?php echo Url('Admin/Agent/listus'); ?>?aid=<?php echo htmlentities($v['agent_id']); ?>" class="btn btn-xs btn-info">发展会员</a>
											<a title="下级代理"   href="<?php echo Url('Admin/Agent/listag'); ?>?aid=<?php echo htmlentities($v['agent_id']); ?>" class="btn btn-xs btn-danger">下级代理</a>
											<a title="编辑" href="<?php echo Url('Admin/Agent/aedit'); ?>?id=<?php echo htmlentities($v['agent_id']); ?>" class="btn btn-xs btn-default"><i class="icon-edit bigger-120"></i></a>
											<a title="登录该代理后台"   onclick="agentlogin('<?php echo htmlentities($v['agent_id']); ?>')" href="javascript:;" class="btn btn-xs btn-info"><i class="icon-user bigger-120"></i></a>
										</td>
									</tr>
								<?php endforeach; endif; else: echo "" ;endif; ?>
							</tbody>
						</table>
						<!-- {/volist} -->
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
			window.location.href = "<?php echo Url('Admin/Agent/index'); ?>?keywords="+text; 
		});
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url('Admin/Agent/index'); ?>"; 
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
	
	/*用户-查看*/
	function agentlogin(id) {
		layer.confirm('您确定要登录此代理商后台么?', {
            btn: ['是','否'] //按钮
        }, function(){
        	var url = "<?php echo Url('Admin/Agent/gologin'); ?>?id="+id;
            window.open(url);
        }, function(){

        });
	}
	/*
	 * 查看费率
	 * 2018年10月11日14:37:18
	 * 刘媛媛
	 */
	function lookcRate(id){
		layer.open({
		  type: 2,
		  title: '代理费率',
		  shadeClose: true,
		  shade: 0.8,
		  area: ['380px', '290'],
		  content: '<?php echo Url('Admin/Agent/getrate'); ?>?id='+id //iframe的url
		}); 
	}
</script>