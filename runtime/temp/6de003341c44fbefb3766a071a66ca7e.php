<?php /*a:1:{s:42:"../Theme/adminsys/agent\underlevelrun.html";i:1550187480;}*/ ?>
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
      	<div class="state-overview clearfix">
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol terques">
                       <i class="icon-bar-chart"></i>
                    </div>
                    <div class="value">
                        <h1><?php echo htmlentities($todymoney); ?></h1>
                        <p>今日总金额</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol red">
                        <i class="icon-bar-chart"></i>
                    </div>
                    <div class="value">
                        <h1><?php echo htmlentities($todyprofit); ?></h1>
                        <p>今日分润金额</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol yellow">
                        <i class="icon-bar-chart"></i>
                    </div>
                    <div class="value">
                        <h1><?php echo htmlentities($sunmoney); ?></h1>
                        <p>总交易额</p>
                    </div>
                </section>
            </div>
            <div class="col-lg-3 col-sm-6">
                <section class="panel">
                    <div class="symbol blue">
                        <i class="icon-bar-chart"></i>
                    </div>
                    <div class="value">
                        <h1><?php echo htmlentities($sunprofit); ?></h1>
                        <p>总分润</p>
                    </div>
                </section>
            </div>
        </div>
		<div class="page-content clearfix">
			<div id="Member_Ratings">
				<div class="d_Confirm_Order_style">
					<div class="search_style">
						<form action="<?php echo Url('Admin/Agent/underlevelrun'); ?>" method="get">
						<ul class="search_content clearfix">
                          	<li>
                              <label class="l_f">代理 : </label>
                              <input type="text" placeholder="用户名|手机号" name="account" value="<?php echo htmlentities($getdata['account']); ?>" class="text_add" style="width:200px;" />  
                            </li>
                          
							<li style="width:90px;"><input type="submit" id="" class="btn_search" value="查询"></li>
						</ul>
					</div>
					<!---->
					<div class="table_menu_list">
						<table class="table table-striped table-bordered table-hover" >
							<thead>
								<tr>
									<th>编号</th>
                                    <th>代理[姓名]</th>
                                    <th>上级</th>
                                    <th>总笔数</th>
                                    <th>总金额</th>
                                    <th>收款分润</th>
                                    <th>还款分润</th>
                                    <th>升级分润</th>
                                    <th>总分润</th>
                                    <th>操作</th>
								</tr>
							</thead>
							<tbody>
							<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                              <tr>
                                <td><?php echo htmlentities($vo['id']); ?></td>
                                <td><?php echo htmlentities($vo['aginfo']); ?></td>
                                <td><?php echo htmlentities($vo['agsuper']); ?></td>
                                <td><?php echo htmlentities($vo['couopens']); ?></td>
                                <td><?php echo htmlentities($vo['couamount']); ?></td>
                                <td><?php echo htmlentities($vo['skcoufenrun']); ?></td>
                                <td><?php echo htmlentities($vo['hkcoufenrun']); ?></td>
                                <td><?php echo htmlentities($vo['sjcoufenrun']); ?></td>
                                <td><?php echo htmlentities($vo['coufenrun']); ?></td>
                                <td>
                                  <a title="" class="layui-btn layui-btn-xs" onclick="fine(<?php echo htmlentities($vo['id']); ?>)" href="javascript:;">
                                    按照明细汇总
                                  </a>
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
	// 代理分润明细汇总
  function fine(id){
    window.location.href = "<?php echo Url('admin/agent/fenrunfine'); ?>?pid="+id;
  }
		
</script>