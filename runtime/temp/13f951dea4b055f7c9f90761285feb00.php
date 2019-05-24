<?php /*a:1:{s:37:"../Theme/adminsys/userbank/index.html";i:1545377387;}*/ ?>
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
									<th >用户</th>
									<th >类型</th>
									<th >银行</th>
									<th >卡号</th>
									<th >开户人</th>
									<th >预留手机号</th>
									<th >信用额度</th>
								
									<th>帐单日</th>
									<th>还款日</th>
									<th>省市</th>
									<th >状态</th>
									<th >还款状态</th>
									<th width="250">操作</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
										<td><?php echo htmlentities($v['card_id']); ?></td>
										<td><?php echo htmlentities($v['user_account']); ?></td>
										<td><?php echo $v['card_type']==1 ? '信用卡' : '储蓄卡'; ?></td>
										<td><?php echo htmlentities($v['list_name']); ?></td>
										<td><?php echo htmlentities(formatCardNo($v['card_no'])); ?></td>
										<td><?php echo htmlentities($v['card_name']); ?></td>
										<td><?php echo htmlentities($v['card_phone']); ?></td>
										<td><?php echo htmlentities($v['card_credit_limit']); ?></td> 
										<td><?php echo htmlentities($v['card_account_day']); ?></td>
										<td><?php echo htmlentities($v['card_repayment_day']); ?></td>
										<td><?php echo htmlentities($v['card_province']); ?>-<?php echo htmlentities($v['card_city']); ?></td>
										<td class="td-status"><span  data-url="<?php echo Url('Admin/Userbank/updestate'); ?>?id=<?php echo htmlentities($v['card_id']); ?>" data-confirm="您确认<?php echo $v['card_blocked']==1 ? '冻结解除冻结' : '冻结'; ?>吗？"  class=" link-confirm label label-<?php echo $v['card_blocked']==1 ? 'danger' : 'success'; ?>  radius"><?php echo $v['card_blocked']==1 ? '冻结' : '正常'; ?></span></td>
										<td><?php switch($v['card_state']): case "1": ?>还款中<?php break; case "2": ?>已还完<?php break; case "3": ?><font color="red">还款失败</font><?php break; default: ?>未启动
											<?php endswitch; ?>
										</td>
										<td class="td-manage">
											<a href="javascript:;" title="停用" data-url="<?php echo Url('Admin/User/resets'); ?>?id=<?php echo htmlentities($v['user_id']); ?>" data-confirm="您确认要重置登陆密码吗？"  class="link-confirm btn btn-xs btn-success"><i class="icon-lock bigger-120"></i></a>
											<a title="编辑" onclick="bankedit('<?php echo htmlentities($v['card_id']); ?>')" href="javascript:;" class="btn btn-xs btn-info"><i class="icon-edit bigger-120"></i></a>
											<a title="删除" href="javascript:;" onclick="member_del(this,'<?php echo htmlentities($v['card_uid']); ?>','<?php echo htmlentities($v['card_id']); ?>')"  class="btn btn-xs btn-warning"><i class="icon-trash  bigger-120"></i></a>
											<?php if($v['card_state']=='0'): ?>
												<a title="新增计划" href="javascript:;" onclick="makeplan('<?php echo htmlentities($v['card_uid']); ?>','<?php echo htmlentities($v['card_id']); ?>')" class="btn btn-xs btn-success">
													新增计划
												</a>
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
			window.location.href = "<?php echo Url('Admin/Userbank/index'); ?>?keywords="+text; 
		});
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url('Admin/Userbank/index'); ?>"; 
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
	function bankedit(id){
			
			layer.open({
			  type: 2,
			  title: false,
			  closeBtn: 1,
			  area: ['900px', '500px'],
			  shadeClose: true,
			  skin: 'yourclass',
			  content: '<?php echo Url('Admin/Userbank/upbank'); ?>?id='+id
			});
		}
	function makeplan(uid,cid){
			
			parent.layer.open({
			  type: 2,
			  title: false,
			  closeBtn: 1,
			  area: ['90%', '90%'],
			  shadeClose: true,
			  skin: 'yourclass',
			  content: '<?php echo Url('Repayment/add'); ?>?uid='+uid+'&cid='+cid
			});
		}
	function member_del(obj,uid,cid){
		if(confirm('确定要删除吗？')){
			var url = "<?php echo Url('Userbank/DeleBank'); ?>";
	        $.post(url, {uid:uid, cid:cid}, function(e){
	        	if(e.error==0){
				    layer.open({
	                    content:e.msg
	                    ,skin: 'msg'
	                });
				obj.parentNode.parentNode.remove();
	        	}else{
	                layer.open({
	                    content:e.msg
	                    ,skin: 'msg'
	                });
	        	}
	        });
    	}
	}
</script>