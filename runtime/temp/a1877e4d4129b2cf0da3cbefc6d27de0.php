<?php /*a:1:{s:33:"../Theme/adminsys/index\home.html";i:1550187480;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="/static/admin/assets/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="/static/admin/css/style.css"/>
        <link rel="stylesheet" href="/static/admin/assets/css/ace.min.css" />
        <link rel="stylesheet" href="/static/admin/assets/css/font-awesome.min.css" />
        <link href="/static/admin/assets/css/codemirror.css" rel="stylesheet">
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/static/admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->
        <!--[if lte IE 8]>
		  <link rel="stylesheet" href="/static/admin/assets/css/ace-ie.min.css" />
		<![endif]-->
		<script src="/static/admin/assets/js/ace-extra.min.js"></script>
		<!--[if lt IE 9]>
		<script src="/static/admin/assets/js/html5shiv.js"></script>
		<script src="/static/admin/assets/js/respond.min.js"></script>
		<![endif]-->
        		<!--[if !IE]> -->
		<script src="/static/admin/assets/js/jquery.min.js"></script>        
		<!-- <![endif]-->
           	<script src="/static/admin/assets/dist/echarts.js"></script>
        <script src="/static/admin/assets/js/bootstrap.min.js"></script>            
       <title></title>
       </head>		
<body>
<div class="page-content clearfix">
 <div class="alert alert-block alert-success">
  <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
  <i class="icon-ok green"></i>欢迎使用<strong class="green"><?php echo htmlentities($config['SITE_NAME']); ?>后台管理系统<small>(v<?php echo htmlentities($config['SITE_VERSION']); ?>)</small></strong>,你本次登录时间为<?php echo htmlentities(date('Y-m-d H:i:s',!is_numeric($admin['admin_time'])? strtotime($admin['admin_time']) : $admin['admin_time'])); ?>，登录IP:<?php echo htmlentities($admin['admin_ip']); ?>.	
 </div>
 <div class="state-overview clearfix">
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                      <a href="#" title="商城会员">
                          <div class="symbol terques">
                             <i class="icon-user"></i>
                          </div>
                          <div class="value">
                              <h1><?php echo htmlentities($count['user']); ?></h1>
                              <p>平台用户</p>
                          </div>
                          </a>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol red">
                              <i class="icon-tags"></i>
                          </div>
                          <div class="value">
                              <h1><?php echo htmlentities($count['agent']); ?></h1>
                              <p>代理商</p>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol yellow">
                              <i class="icon-shopping-cart"></i>
                          </div>
                          <div class="value">
                              <h1><?php echo htmlentities($count['benefit']); ?></h1>
                              <p>提现申请</p>
                          </div>
                      </section>
                  </div>
                  <div class="col-lg-3 col-sm-6">
                      <section class="panel">
                          <div class="symbol blue">
                              <i class="icon-bar-chart"></i>
                          </div>
                          <div class="value">
                              <h1><?php echo htmlentities($count['upgrade']); ?></h1>
                              <p>升级订单</p>
                          </div>
                      </section>
                  </div>
              </div>
             <!--实时交易记录-->
             <div class="clearfix">
              <div class="Order_Statistics ">
          <div class="title_name">订单统计信息</div>
           <table class="table table-bordered">
           <tbody>
           <tr><td class="name">未处理订单：</td><td class="munber"><a href="#">0</a>&nbsp;个</td></tr>
           <tr><td class="name">待发货订单：</td><td class="munber"><a href="#">10</a>&nbsp;个</td></tr>
           <tr><td class="name">待结算订单：</td><td class="munber"><a href="#">13</a>&nbsp;个</td></tr>
           <tr><td class="name">已成交订单数：</td><td class="munber"><a href="#">26</a>&nbsp;个</td></tr>
           <tr><td class="name">交易失败：</td><td class="munber"><a href="#">26</a>&nbsp;个</td></tr>
           </tbody>
          </table>
         </div> 
         <div class="Order_Statistics">
          <div class="title_name">商品统计信息</div>
           <table class="table table-bordered">
           <tbody>
           <tr><td class="name">商品总数：</td><td class="munber"><a href="#">340</a>&nbsp;个</td></tr>
           <tr><td class="name">回收站商品：</td><td class="munber"><a href="#">10</a>&nbsp;个</td></tr>
           <tr><td class="name">上架商品：</td><td class="munber"><a href="#">13</a>&nbsp;个</td></tr>
           <tr><td class="name">下架商品：</td><td class="munber"><a href="#">26</a>&nbsp;个</td></tr>
           <tr><td class="name">商品评论：</td><td class="munber"><a href="#">21s6</a>&nbsp;条</td></tr>

           </tbody>
          </table>
         </div> 
         <div class="Order_Statistics">
          <div class="title_name">会员统计信息</div>
           <table class="table table-bordered">
           <tbody>
           	<?php if(is_array($usertype) || $usertype instanceof \think\Collection || $usertype instanceof \think\Paginator): $i = 0; $__LIST__ = $usertype;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$uv): $mod = ($i % 2 );++$i;?>
           		<tr><td class="name"><?php echo htmlentities($uv['name']); ?>：</td><td class="munber"><a href="#"><?php echo htmlentities($uv['count']); ?></a>&nbsp;位</td></tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
           </tbody>
          </table>
         </div> 
             <!--<div class="t_Record">
               <div id="main" style="height:300px; overflow:hidden; width:100%; overflow:auto" ></div>     
              </div> -->
         <div class="news_style">
          <div class="title_name">最新消息</div>
          <ul class="list">
          	<?php if(is_array($feedback) || $feedback instanceof \think\Collection || $feedback instanceof \think\Paginator): $i = 0; $__LIST__ = $feedback;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fv): $mod = ($i % 2 );++$i;?>
           		<li><i class="icon-bell red"></i><a href="<?php echo Url('Admin/Feedback/index'); ?>"><?php echo htmlentities($fv['feedback_title']); ?></a></li>
         	<?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
         </div> 
         </div>
 <!--记录-->
 <div class="clearfix">
  <div class="home_btn">
     <div>
     <a href="<?php echo Url('Admin/Payment/index'); ?>"  title="添加商品" class="btn  btn-info btn-sm no-radius">
     <i class="bigger-200"><img src="/static/admin/images/icon-addp.png" /></i>
     <h5 class="margin-top">通道管理</h5>
     </a>
     <a href="Category_Manage.html"  title="产品分类" class="btn  btn-primary btn-sm no-radius">
     <i class="bigger-200"><img src="/static/admin/images/icon-cpgl.png" /></i>
     <h5 class="margin-top">产品分类</h5>
     </a>
     <a href="<?php echo Url('Admin/Admins/mypass'); ?>"  title="个人信息" class="btn  btn-success btn-sm no-radius">
     <i class="bigger-200"><img src="/static/admin/images/icon-grxx.png" /></i>
     <h5 class="margin-top">个人信息</h5>
     </a>
     <a href="<?php echo Url('Admin/System/add'); ?>"  title="系统设置" class="btn  btn-info btn-sm no-radius">
     <i class="bigger-200"><img src="/static/admin/images/xtsz.png" /></i>
     <h5 class="margin-top">系统设置</h5>
     </a>
     <a href="Order_handling.html"  title="商品订单" class="btn  btn-purple btn-sm no-radius">
     <i class="bigger-200"><img src="/static/admin/images/icon-gwcc.png" /></i>
     <h5 class="margin-top">商品订单</h5>
     </a>
     <a href="<?php echo Url('Admin/Img/index'); ?>?type=1"  title="添加广告" class="btn  btn-pink btn-sm no-radius">
     <i class="bigger-200"><img src="/static/admin/images/icon-ad.png" /></i>
     <h5 class="margin-top">图片管理</h5>
     </a>
      <a href="<?php echo Url('Admin/Article/add'); ?>?type=1"  title="添加文章" class="btn  btn-info btn-sm no-radius">
     <i class="bigger-200"><img src="/static/admin/images/icon-addwz.png" /></i>
     <h5 class="margin-top">添加文章</h5>
     </a>
     </div>
  </div>
 
 </div>
   
     </div>
</body>
</html>
<script type="text/javascript">
//面包屑返回值
var index = parent.layer.getFrameIndex(window.name);
parent.layer.iframeAuto(index);
$('.no-radius').on('click', function(){
	var cname = $(this).attr("title");
	var chref = $(this).attr("href");
	var cnames = parent.$('.Current_page').html();
	var herf = parent.$("#iframe").attr("src");
    parent.$('#parentIframe').html(cname);
    parent.$('#iframe').attr("src",chref).ready();;
	parent.$('#parentIframe').css("display","inline-block");
	parent.$('.Current_page').attr({"name":herf,"href":"javascript:void(0)"}).css({"color":"#4c8fbd","cursor":"pointer"});
	//parent.$('.Current_page').html("<a href='javascript:void(0)' name="+herf+" class='iframeurl'>" + cnames + "</a>");
    parent.layer.close(index);
	
});
     $(document).ready(function(){
		 
		  $(".t_Record").width($(window).width()-640);
		  //当文档窗口发生改变时 触发  
    $(window).resize(function(){
		 $(".t_Record").width($(window).width()-640);
		});
 });
	 
	 
 </script>   