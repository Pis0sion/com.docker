<?php /*a:1:{s:36:"../Theme/adminsys/system/smslog.html";i:1540351774;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="renderer" content="webkit|ie-comp|ie-stand">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta http-equiv="Cache-Control" content="no-siteapp" />
		<link href="/static/admin/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="css/style.css" />
		<link href="/static/admin/assets/css/codemirror.css" rel="stylesheet">
		<link rel="stylesheet" href="/static/admin/assets/css/ace.min.css" />
		
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="/static/admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/static/admin/js/jquery-1.9.1.min.js"></script>
		<script src="/static/admin/assets/js/bootstrap.min.js"></script>
		<script src="/static/admin/assets/js/typeahead-bs2.min.js"></script>
		<script src="/static/admin/assets/js/jquery.dataTables.min.js"></script>
		<script src="/static/admin/assets/js/jquery.dataTables.bootstrap.js"></script>
		<title>管理权限</title>
	</head>

	<body>
		<div class="margin clearfix">
			<div class="border clearfix">
				
			</div>
			<div class="compete_list">
				<table id="sample-table-1" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="center">ID</th>
							<th>验证码</th>
							<th>发送时间</th>
							<th>接受设备</th>
							<th class="hidden-480">状态</th>
							<th class="hidden-480">类别</th>
						</tr>
					</thead>
					<tbody>
						<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<tr>
							<td class="center"><?php echo htmlentities($v['send_id']); ?></td>
							<td><?php echo htmlentities($v['send_code']); ?></td>
							<td><?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($v['send_time'])? strtotime($v['send_time']) : $v['send_time'])); ?></td>
							<td class="hidden-480"><?php echo htmlentities($v['send_target']); ?></td>
							<td><?php echo $v['send_state']==1 ? '已验证' : '未验证'; ?></td>
							<td>
								<?php switch($v['send_type']): case "1": ?>注册<?php break; case "2": ?>找回<?php break; case "3": ?>绑定<?php break; case "4": ?>通知<?php break; default: ?>其他
								<?php endswitch; ?>
							</td>
						</tr>
						<?php endforeach; endif; else: echo "" ;endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		
	</body>

</html>
<script type="text/javascript">
	
	//面包屑返回值
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.iframeAuto(index);
	$('.Order_form ,#Competence_add').on('click', function() {
		var cname = $(this).attr("title");
		var cnames = parent.$('.Current_page').html();
		var herf = parent.$("#iframe").attr("src");
		parent.$('#parentIframe span').html(cname);
		parent.$('#parentIframe').css("display", "inline-block");
		parent.$('.Current_page').attr("name", herf).css({
			"color": "#4c8fbd",
			"cursor": "pointer"
		});
		//parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+">" + cnames + "</a>");
		parent.layer.close(index);
	});
</script>