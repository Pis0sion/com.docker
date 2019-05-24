<?php /*a:1:{s:39:"../Theme/adminsys/roles\roles_list.html";i:1550187480;}*/ ?>
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
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
        	
        	<div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <div class="panel-body">

                            <form class="form-inline" role="form" action="" method="get">
                                <div class="form-group">
                                    <header class="panel-heading">
		                                <div class="btn-group btn-group-devided" data-toggle="buttons">
					                    	<button type="button" class="btn btn-primary right link-popup" data-url="<?php echo Url('/Admin/Roles/roles_add'); ?>" data-title="新增角色" data-width="430" data-height="300">
					                    		<i class="fa fa-plus-circle"></i> 新增
					                    	</button>
					                    </div>
                                    </header>
                                  </div>
                            </form>

                        </div>
                    </section>
                </div>
            </div>
            
            
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                          	 权限设置
                        </header>
                        <table class="table table-striped table-advance table-hover  ">
                            <thead>
	                            <tr>
	                                <th class="col-lg-1">编号</th>
	                                <th class="col-lg-2">名字</th>
	                                <th class="col-lg-1">状态</th>
	                                <th class="col-lg-3">操作</th>
	                            </tr>
                            </thead>
                            <tbody>
                          	<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): if( count($list)==0 ) : echo "" ;else: foreach($list as $key=>$v): ?>
	                            <tr>
	                                <td><?php echo htmlentities($v['id']); ?></td>
	                                <td><?php echo htmlentities($v['title']); ?></td>
	                                <td><?php echo $v['status']==1 ? "<font color='green'>正常</font>" : "<font color='red'>禁用</font>"; ?></td>
	                                <td>
	                                	<?php if($v['status'] == 1): ?>
	                                	<a href="javascript:void(0);" class="link-confirm" data-url="<?php echo Url('/Admin/Roles/disable'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-confirm="您确定要禁用吗？">禁用</a>
	                                	<?php else: ?>
	                                	<a href="javascript:void(0);" class="link-confirm" data-url="<?php echo Url('/Admin/Roles/disable'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-confirm="您确定要启用吗？">启用</a>
	                                	<?php endif; ?>
	                                	<a href="javascript:void(0);" class="link-popup" data-url="<?php echo Url('/Admin/Roles/roles_edit'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-title="<?php echo htmlentities($v['title']); ?> - 修改角色" data-width="430" data-height="300">修改</a>
	                                	<a href="javascript:void(0);" class="link-popup" data-url="<?php echo Url('/Admin/Roles/set_auths'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-title="<?php echo htmlentities($v['title']); ?> - 设置权限">设置权限</a>
	                                	<a href="javascript:void(0);" class="link-confirm" data-url="<?php echo Url('/Admin/Roles/delete'); ?>?id=<?php echo htmlentities($v['id']); ?>" data-confirm="您确定要删除吗？">删除</a>
	                                </td>
	                            </tr>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                               </tbody>
                           </table>
                       </section>
                   </div>
               </div>
 </section>
    </section>
    <!--main content end-->
</section>

<!-- js placed at the end of the document so the pages load faster -->
<script src="/static/admin/js/jquery.js"></script>
<script src="/static/admin/js/bootstrap.min.js"></script>
<script src="/static/admin/js/jquery.scrollTo.min.js"></script>
<script src="/static/admin/js/jquery.nicescroll.js" type="text/javascript"></script>

<!--common script for all pages-->
<script src="/static/admin/js/common-scripts.js"></script>
<script  src="/plugins/jquery-1.9.1.min.js"></script>
<script  src="/plugins/layer/layer.js"></script>
<script  src="/plugins/common.js"></script>
<script src="/static/admin/js/roles.js" type="text/javascript"></script>
</body>
</html>
