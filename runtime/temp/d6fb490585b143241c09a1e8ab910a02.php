<?php /*a:1:{s:36:"../Theme/adminsys/user\pftwarll.html";i:1550187480;}*/ ?>
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
									<th><label><input type="checkbox" class="ace"><span class="lbl"></span></label></th>
									<th >ID</th>
									<th >用户</th>
									<th >银行卡卡号</th>
                                    <th >开户人</th>
                                    <th >预留手机号</th>
                                  	<th>开户行</th>
                                     <th >银行类型</th>
									<th>订单编号</th>
									<th>分润提现金额</th>
									<th >实际到账金额</th>
									<th>提现时间</th>
									<th >打款时间</th>
									<th >打款人</th>
									<th>操作人</th>
								</tr>
							</thead>
							<tbody>
								<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
									<tr>
										<td><label><input type="checkbox" class="ace"><span class="lbl"></span></label></td>
										<td><?php echo htmlentities($v['profit_id']); ?></td>
										<td><?php echo htmlentities(getUser($v['profit_uid'],'user_account')); ?></td>
                                        <td><?php echo htmlentities(getCard($v['profit_card_id'],'card_no')); ?></td>
                                        <td ><?php echo htmlentities(getCard($v['profit_card_id'],'card_name')); ?></td>
                                    	<td ><?php echo htmlentities(getCard($v['profit_card_id'],'card_phone')); ?></td>
                                      	<td><?php echo htmlentities($v['user_branch']); ?></td>
                                     	<td ><?php echo htmlentities(getBankList(getCard($v['profit_card_id'],'card_bank_id'),'list_name')); ?></td>
										<td><?php echo htmlentities($v['profit_form_no']); ?></td>
										<td><?php echo htmlentities($v['profit_money']); ?></td>
										<td><?php echo htmlentities($v['profit_true_money']); ?></td>
										<td><?php echo htmlentities(date("Y-m-d H:i:s",!is_numeric($v['profit_time'])? strtotime($v['profit_time']) : $v['profit_time'])); ?></td> 
										<td><?php echo htmlentities(date("Y-m-d H:i:s",!is_numeric($v['profit_paytime'])? strtotime($v['profit_paytime']) : $v['profit_paytime'])); ?></td> 
										<td><?php echo htmlentities($v['profit_admin_id']); ?></td> 
										<td>
											<?php if($v['profit_type']==1): ?>
												<a href="javascript:;" data-url="<?php echo Url('Admin/User/wallsta'); ?>?id=<?php echo htmlentities($v['profit_id']); ?>&type=1&tt=0" data-confirm="是否确认通过该笔提现" title="同意" class="link-confirm btn btn-xs btn-success">同意</a>                            
												<a href="javascript:;" data-url="<?php echo Url('Admin/User/wallsta'); ?>?id=<?php echo htmlentities($v['profit_id']); ?>&type=2&tt=0" data-confirm="是否拒绝该笔提现" title="拒绝" class="link-confirm btn btn-xs btn-warning">拒绝</a>
											<?php elseif($v['profit_type']==2): ?>
			                                  已打款
			                                <?php elseif($v['profit_type']==3): ?>
			                                  打款失败                               
			                                <?php endif; ?>
										</td> 
										
										
									</tr>
								<?php endforeach; endif; else: echo "" ;endif; ?>
							</tbody>
						</table>
					</div>
					<div>
		                <?php echo $page; ?>
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

	})

	function stas(ptfid, type){
		var msg = '';
		if(type == '1'){
			msg = '是否确认通过该笔提现';
		}else if(type == '2'){
			msg = '是否确认拒绝该笔提现';
		}

		layer.confirm('您确定要登录此代理商后台么?', {
            btn: ['是','否'] //按钮
        }, function(){
        	var url = "<?php echo Url('Admin/User/wallsta'); ?>";
            $.post(url, {id:ptfid, type:type}, function(e){
            	alert(e);
            });
        }, function(){});
	}

</script>