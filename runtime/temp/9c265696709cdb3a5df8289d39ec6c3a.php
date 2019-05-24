<?php /*a:1:{s:39:"../Theme/adminsys/agent\profitlist.html";i:1550187480;}*/ ?>
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
		<script src="/static/admin/assets/laydate/laydate.js" type="text/javascript"></script>
	    <script  src="/plugins/common.js"></script>
		<title>用户列表</title>
	</head>

	<body>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">
						<form action="<?php echo Url('Admin/Agent/profitlist'); ?>" method="get">
						<ul class="search_content clearfix">
                          	<li>
                              <label class="l_f">代理商：</label>
                              <input name="agent" id="agent" type="text" class="text_add" style="width:100px;"  value="<?php if(isset($getdata['agent'])): ?><?php echo htmlentities($getdata['agent']); endif; ?>" placeholder="输入代理商ID" />  
                            </li>
							<li>
                              <label class="l_f">申请状态：</label>
                              <select name="benefit_type" id='benefit_type'>
                                <option value="">请选择</option>
                                <option value="1" <?php if(isset($getdata['benefit_type'])): ?><?php echo $getdata['benefit_type']=='1' ? 'selected' : ''; endif; ?>>申请中</option>
                                <option value="2" <?php if(isset($getdata['benefit_type'])): ?><?php echo $getdata['benefit_type']=='2' ? 'selected' : ''; endif; ?>>已提现</option>
                                <option value="3" <?php if(isset($getdata['benefit_type'])): ?><?php echo $getdata['benefit_type']=='3' ? 'selected' : ''; endif; ?>>已拒绝</option>
                              </select>     
                            </li>
                          	<li>
                             <label class="l_f">申请时间：</label>
                                <input type="text" name="starttime" value="<?php if(isset($getdata['starttime'])): ?><?php echo htmlentities($getdata['starttime']); endif; ?>" class="layui-input" id="starttime" placeholder="开始日期" lay-key="92">
                              	-
                                <input type="text" name="endtime" value="<?php if(isset($getdata['endtime'])): ?><?php echo htmlentities($getdata['endtime']); endif; ?>" style='margin-left:0px' class="layui-input" id="endtime" placeholder="结束日期" lay-key="93">
                            </li>
                          
							<li style="width:90px;"><input type="submit" id="" class="btn_search" value="查询"></li>
							<li style="width:90px;"><button type="button" id="searchAout" class="btn_search"><i class="icon-search"></i>全部</button></li>
						</ul>
					</div>
					<!---->
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th>ID</th>
                                    <th>提现卡</th>
                                  	<th>申请人</th>
                                    <th>总笔数</th>
                                    <th>申请金额</th>
                                    <th>申请时间</th>
                                    <th>打款时间</th>
                                    <th>分润开始时间</th>
                                    <th>分润结束时间</th>
                                  	<th>操作</th>
								</tr>
							</thead>
							<tbody>
							<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                              <tr>
                                <td><?php echo htmlentities($vo['benefit_id']); ?></td>
                                <td><b><a href="#" onclick="agcrd(<?php echo htmlentities($vo['benefit_cid']); ?>)"><?php echo htmlentities(getAgentCard($vo['benefit_cid'],"card_no")); ?></a></b></td>
                                <td><?php echo htmlentities(getAgent($vo['benefit_agent_id'],'agent_account')); ?></td>
                                <td><?php echo htmlentities($vo['benefit_count']); ?></td>
                                <td><?php echo htmlentities($vo['benefit_money']); ?></td>
                                <td><?php echo htmlentities(date("Y-m-d H:i:s",!is_numeric($vo['benefit_time'])? strtotime($vo['benefit_time']) : $vo['benefit_time'])); ?></td>
                                <td>
                                  <?php if($vo['benefit_pay_time']): ?>
                                    <?php echo htmlentities(date("Y-m-d H:i:s",!is_numeric($vo['benefit_pay_time'])? strtotime($vo['benefit_pay_time']) : $vo['benefit_pay_time'])); endif; ?>
                                </td>
                                <td><?php echo htmlentities(date("Y-m-d H:i:s",!is_numeric($vo['benefit_starttime'])? strtotime($vo['benefit_starttime']) : $vo['benefit_starttime'])); ?></td>
                                <td><?php echo htmlentities(date("Y-m-d H:i:s",!is_numeric($vo['benefit_endtime'])? strtotime($vo['benefit_endtime']) : $vo['benefit_endtime'])); ?></td>
                                <td>
                                  <?php if($vo['benefit_type']==0): ?>
                                  	  <a href="javascript:;" title="同意" data-url="<?php echo Url('profitsta'); ?>?id=<?php echo htmlentities($vo['benefit_id']); ?>&type=1&" data-confirm="您确认要通过该笔提现吗？"  class="link-confirm btn btn-xs btn-success">通过</a>
                                  	  <a href="javascript:;" title="拒绝" data-url="<?php echo Url('profitsta'); ?>?id=<?php echo htmlentities($vo['benefit_id']); ?>&type=2&" data-confirm="您确认要拒绝该笔提现吗？"  class="link-confirm btn btn-xs btn-danger">拒绝</a>
                                  <?php elseif($vo['benefit_type']==1): ?>
                                   已出款
                                  <?php elseif($vo['benefit_type']==2): ?>
                                   <span style='color:red'>已拒绝</span>
                                  <?php endif; ?>
                                  <div><a title="详情" href="javascript:;" onclick="details(<?php echo htmlentities($vo['benefit_id']); ?>)" class="btn btn-xs btn-success">详情</a></div>
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
	   
	</body>

</html>
<script>
		laydate({
			elem: '#starttime',
			event: 'focus'
		});
		laydate({
			elem: '#endtime',
			event: 'focus'
		});

		// 查看详情
        function details(id){
           window.location.href = "<?php echo Url('admin/agent/withdetail'); ?>?id="+id;
        }

	jQuery(function($) {
		
		$('#searchAout').on('click', function() {
			window.location.href = "<?php echo Url('Admin/Agent/profitlist'); ?>"; 
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
  
  	// 查看提现银行卡详情
        function agcrd(id){
            layer.open({
              type: 2 //Page层类型
              ,area: ['450px', '480px']
              ,title: '提现银行卡'
              ,shade: 0.5 //遮罩透明度
              ,maxmin: false //允许全屏最小化
              ,anim: 1 //0-6的动画形式，-1不开启
              ,content: "<?php echo Url('admin/agent/crdsdetail'); ?>?id="+id
            });
        }
	
		
</script>