<?php /*a:1:{s:34:"../Theme/adminsys/auths/index.html";i:1540351774;}*/ ?>
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
		<title>文章列表</title>
	</head>

	<body>

<section id="container" class="">
    <!--header start-->
    <!--sidebar end-->
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
        	
			<div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <div class="panel-body">
                            
                            <div class="btn-group btn-group-devided" data-toggle="buttons">
			                    	<button type="button" class="btn btn-success link-popup" data-url="<?php echo Url('/Admin/Auths/auths_add'); ?>" data-title="新增权限" data-width="730" data-height="500">
			                    		<i class="fa fa-plus-circle"></i> 新增
			                    	</button>
			                    	<button type="button" class="btn btn-danger link-popup" data-url="<?php echo Url('/Admin/Auths/auths_class'); ?>" data-title="权限分类">
			                    		<i class="fa fa-sitemap"></i> 分类
			                    	</button>
			                    	<button type="button" class="btn btn-group link-popup" data-url="<?php echo Url('/Admin/Auths/reg_auth'); ?>" data-title="注册功能权限">
			                    		<i class="fa fa-wrench"></i> 权限
			                    	</button>
			                    </div>
                        </div>
                    </section>
                </div>
            </div>	        		
			<div class="row">
			    <div class="col-md-12">
			        <div class="portlet light bordered">
			             
			            <div class="portlet-body">
			                <div class="dataTables_wrapper no-footer">
			                	<div class="table-scrollable">
			                		<table class="table table-bordered table-hover table-striped">
			                            <thead>
			                                <tr>
			                                    <th class="col-lg-1">编号</th>
			                                    <th class="col-lg-2">主体</th>
			                                    <th class="col-lg-2">名字</th>
			                                    <th class="col-lg-1">类型</th>
			                                    <th class="col-lg-1">状态</th>
			                                    <th class="col-lg-3">条件</th>
			                                    <th class="col-lg-2">操作</th>
			                                </tr>
			                            </thead>
			                            <tbody>
			                            	<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
			                                <tr>
			                                    <td><?php echo htmlentities($v['id']); ?></td>
			                                    <td><?php echo htmlentities($v['name']); ?></td>
			                                    <td><?php echo htmlentities($v['title']); ?></td>
			                                    <td><?php echo $v['type']==1 ? "时时验证" : "登录验证"; ?></td>
			                                    <td><?php echo $v['status']==1 ? "<font color='green'>正常</font>" : "<font color='red'>禁用</font>"; ?></td>
			                                    <td><?php echo !empty($v['condition']) ? htmlentities($v['condition']) : '默认规则'; ?></td>
			                                    <td>
			                                    	<?php if($v['status'] == 1): ?>
			                                    	<a href="javascript:void(0);" class="link-confirm" data-url="<?php echo Url('/Admin/Auths/disable'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-confirm="您确定要禁用吗？">禁用</a>
			                                    	<?php else: ?>
			                                    	<a href="javascript:void(0);" class="link-confirm" data-url="<?php echo Url('/Admin/Auths/disable'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-confirm="您确定要启用吗？">启用</a>
			                                    	<?php endif; ?>
			                                    	<a href="javascript:void(0);" class="link-popup" data-url="<?php echo Url('/Admin/Auths/auths_edit'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-title="<?php echo htmlentities($v['title']); ?> - 修改权限" data-width="730" data-height="500">修改</a>
			                                    	<a href="javascript:void(0);" class="link-confirm" data-url="<?php echo Url('/Admin/Auths/delete'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-confirm="您确定要删除吗？">删除</a>
			                                    </td>
			                                </tr>
			                                <?php endforeach; endif; else: echo "" ;endif; ?>
			                        	</tbody>
			                        </table>
			                	</div>
			                	<div class="row">
			                		<div class="col-md-5 col-sm-5"></div>
			                		<div class="col-md-7 col-sm-7">
			                			<div class="dataTables_paginate paging_bootstrap_full_number" id="sample_1_paginate">
			                				<?php echo $list; ?>
			                			</div>
			                		</div>
			                	</div>
			                </div>
			            </div>
			        </div>
			    </div>
			</div>
    	</section>
    </section>
    <!--main content end-->
</section>
</body>
</html>
