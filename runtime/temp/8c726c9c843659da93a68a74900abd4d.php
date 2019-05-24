<?php /*a:1:{s:35:"../Theme/adminsys/admins\index.html";i:1550187480;}*/ ?>
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
				<table id="sample-table-1" class="table table-striped table-bordered table-hover col-lg-12">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>登陆账号</th>
                                <th>会员角色</td>
                                <th>登陆ip</td>
                                <th>登陆时间</td>
                                <th>负责人姓名</td>
                                <th>操作</td>
                            </tr>
                            </thead>
                            <tbody>
                          <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                              <tr>
                                <td><?php echo htmlentities($v['admin_id']); ?></td>
                                <td><?php echo htmlentities($v['admin_name']); ?></td>
                                <td><?php echo htmlentities($v['title']); ?></td>
                                <td><?php echo htmlentities($v['admin_ip']); ?></td>
                                <td><?php echo htmlentities(date("Y-m-d",!is_numeric($v['admin_time'])? strtotime($v['admin_time']) : $v['admin_time'])); ?></td>
                                <td><?php echo htmlentities($v['admin_user']); ?></td>
                                <td>
                                  <button onclick="updeadmin(<?php echo htmlentities($v['admin_id']); ?>)" class="btn btn-primary btn-xs"><i class="icon-pencil"></i></button>
                                  <button onclick="admindele(<?php echo htmlentities($v['admin_id']); ?>)" class="btn btn-danger btn-xs"><i class="icon-trash "></i></button>
                                  <button onclick="adminpass(<?php echo htmlentities($v['admin_id']); ?>)" class="btn btn-primary btn-xs" ><i class="icon-dollar ">密码重置</i></button>
                                  <button onclick="alog(<?php echo htmlentities($v['admin_id']); ?>)" class="btn btn-info btn-xs">记录</button> 
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