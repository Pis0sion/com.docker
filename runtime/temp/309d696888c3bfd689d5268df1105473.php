<?php /*a:1:{s:31:"../Theme/agent/index/index.html";i:1543305942;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/agent/css/layui.css">
    <link rel="stylesheet" href="/static/agent/css/admin.css">
    <link rel="icon" href="/favicon.ico">
    <title>代理后台</title>
</head>
<body class="layui-layout-body">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header custom-header">
            
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item slide-sidebar" lay-unselect>
                    <a href="javascript:;" class="icon-font"><i class="ai ai-menufold"></i></a>
                </li>
            </ul>

            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item">
                    <a href="javascript:;"><?php echo !empty($agent['agent_name']) ? htmlentities($agent['agent_name']) : htmlentities($agent['agent_account']); ?></a>
                    <dl class="layui-nav-child">
                      	<dd><a id="extencode" href="javascript:;">推广码</a></dd>
                        <dd><a id="upspass" href="javascript:;">修改密码</a></dd>
                        <dd><a href="<?php echo Url('Agent/Login/logout'); ?>">退出</a></dd>
                    </dl>
                </li>
            </ul>
        </div>

        <div class="layui-side custom-admin">
            <div class="layui-side-scroll">

                <div class="custom-logo">
                    <img src="/static/agent/image/logo.png" alt=""/>
                    <h1>代理商后台</h1>
                </div>
                <ul id="Nav" class="layui-nav layui-nav-tree">
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <i class="layui-icon layui-icon-home"></i>
                            <em>首页</em>
                        </a>
                        <dl class="layui-nav-child">
                            <dd><a href="<?php echo Url('agent/index/console'); ?>">首页</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <i class="layui-icon">&#xe612;</i>
                            <em>个人资料</em>
                        </a>
                        <dl class="layui-nav-child">
                            <dd><a href="<?php echo Url('agent/user/info'); ?>">个人基本信息</a></dd>
                        </dl>
                        <dl class="layui-nav-child">
                            <dd><a href="<?php echo Url('agent/user/getagreta'); ?>">我的成本</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <i class="layui-icon">&#xe612;</i>
                            <em>用户管理</em>
                        </a>
                        <dl class="layui-nav-child">
                            <dd><a href="<?php echo Url('agent/user/userlist'); ?>">用户列表</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <dd><a href="<?php echo Url('Agent/User/agentlist'); ?>">
                            <i class="layui-icon">&#xe612;</i>
                            <em>代理商管理</em>
                        </a></dd>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <i class="layui-icon">&#xe629;</i>
                            <em>分润管理</em>
                        </a>
                        <dl class="layui-nav-child">
                            <dd><a href="<?php echo Url('Agent/Profit/perrunfine'); ?>">我的分润明细</a></dd>
                            <dd><a href="<?php echo Url('Agent/Profit/underlevelrun'); ?>">下级分润报表</a></dd>
                            <dd><a href="<?php echo Url('Agent/Profit/Putforward'); ?>">分润提现</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <i class="layui-icon">&#xe65e;</i>
                            <em>提现管理</em>
                        </a>
                        <dl class="layui-nav-child">
                            <dd><a href="<?php echo Url('Agent/Withdrwal/index'); ?>">提现记录</a></dd>
                            <dd><a href="<?php echo Url('Agent/Withdrwal/cardmag'); ?>">提现卡管理</a></dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item">
                        <a href="javascript:;">
                            <i class="layui-icon">&#xe65e;</i>
                            <em>订单管理</em>
                        </a>
                        <dl class="layui-nav-child">
                            <dd>
                                <a href="<?php echo Url('Agent/Order/missionlist'); ?>">还款订单</a>
                            </dd>
                            <dd>
                                <a href="<?php echo Url('Agent/Order/recordslist'); ?>">收款订单</a>
                            </dd>
                            <dd>
                                <a href="<?php echo Url('Agent/Order/upgradelist'); ?>">升级订单</a>
                            </dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>

        <div class="layui-body">
             <div class="layui-tab app-container" lay-allowClose="true" lay-filter="tabs">
                <ul id="appTabs" class="layui-tab-title custom-tab"></ul>
                <div id="appTabPage" class="layui-tab-content"></div>
            </div>
        </div>
        <div class="mobile-mask"></div>
    </div>
    <script src="/static/agent/js/layui.js"></script>
    <script src="/static/agent/js/index.js" data-main="home"></script>
    <script src="/static/agent/js/jquery.min.js"></script>
    <script type="text/javascript">
        $('#upspass').click(function(){

            layer.open({
                type: 2 //Page层类型
                ,area: ['400px', '500px']
                ,title: '更改账户密码'
                ,shade: 0.5 //遮罩透明度
                ,maxmin: false //允许全屏最小化
                ,anim: 1 //0-6的动画形式，-1不开启
                ,content: "<?php echo Url('Agent/User/updapass'); ?>"
            });
        });   
      
      	 $('#extencode').click(function(){

            layer.open({
                type: 2 //Page层类型
                ,area: ['300px', '350px']
                ,title: '推广'
                ,shade: 0.5 //遮罩透明度
                ,maxmin: false //允许全屏最小化
                ,anim: 1 //0-6的动画形式，-1不开启
                ,content: "<?php echo Url('Agent/User/excode'); ?>"
            });
        }); 
    </script>
</body>
</html>