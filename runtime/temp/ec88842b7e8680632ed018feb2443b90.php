<?php /*a:1:{s:37:"../Theme/agent/order/missionlist.html";i:1543317768;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/agent/css/layui.css">
    <link rel="stylesheet" href="/static/agent/css/view.css"/>
    <link rel="icon" href="/favicon.ico">
    <title>代理后台--提现记录</title>
    <style type="text/css">
        .pagination{display:inline-block;padding-left:0;border-radius:4px;float:right;margin-right:1rem;}
        .pagination li{display:inline;}
        .pagination li a,.pagination li span{position:relative;float:left;padding:4px 10px;line-height:1.5;color:#393D49;background:#fff;margin:0 0 0 5px;border:1px solid #eee}
        .pagination li a:hover{color:#fff;background:#1E9FFF}
        .pagination .active span{background:#1E9FFF;color:#fff;border-radius:30%;}
        .pagination .disabled{display:none}
    </style>
</head>
<body class="layui-view-body">
    <div class="layui-content">
        <div class="layui-page-header">
            <div class="pagewrap">
                <span class="layui-breadcrumb">
                  <a href="">首页</a>
                  <a href="">订单管理</a>
                  <a><cite>还款订单</cite></a>
                </span>
            </div>
        </div>
       
        <div class="layui-row">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="form-box">
                        <div class="layui-form layui-form-item">
                            <div class="layui-inline">
                                <form>
                                    <div class="layui-input-inline" style="width: 13rem;">
                                        <input type="text" placeholder="用户名|手机号|姓名|执行单号" name="txts" value="<?php echo htmlentities($getdata['txts']); ?>" class="layui-input" id="" lay-key="92">
                                    </div>
                                    
                                    <!-- <div class="layui-inline">
                                      <label class="layui-form-label">时间</label>
                                      <div class="layui-input-inline" style="width: 100px;">
                                        <input type="text" name="starttime" value="<?php if(isset($getdata['starttime'])): ?><?php echo htmlentities($getdata['starttime']); endif; ?>" class="layui-input" id="starttime" placeholder="开始日期" lay-key="92">
                                      </div>
                                      <div class="layui-form-mid">
                                        -
                                      </div>
                                      <div class="layui-input-inline" style="width: 100px;">
                                        <input type="text" name="endtime" value="<?php if(isset($getdata['endtime'])): ?><?php echo htmlentities($getdata['endtime']); endif; ?>" class="layui-input" id="endtime" placeholder="结束日期" lay-key="93">
                                      </div>
                                      <div class="layui-inline"> -->

                                      <div class="layui-form-mid">计划状态：</div>
                                      <div class="layui-input-inline" style="width: 100px;">
                                        <select name="type">
                                            <option value="">全部类别</option>
                                            <option value="l" <?php if($getdata['type']=='l'): ?>selected<?php endif; ?>>未启用</option>
                                            <option value="1" <?php if(isset($getdata['type']) && $getdata['type']==1): ?>selected<?php endif; ?>>进行中</option>
                                            <option value="2" {if isset($getdata['type']) && $getdata['type']==2}selected{/if>任务成功</option>
                                            <option value="3" <?php if(isset($getdata['type']) && $getdata['type']==3): ?>selected<?php endif; ?>>失败</option>
                                            <option value="4" <?php if(isset($getdata['type']) && $getdata['type']==4): ?>selected<?php endif; ?>>用户终止计划</option>
                                        </select>     
                                    </div>

                                    </div>
                                    
                                    <button class="layui-btn layui-btn-blue">查询</button>
                                </form>
                            </div>
                        </div>
                        <table id="demo">
                          <table class="layui-table">
                            <thead>
                                <tr>
                                    <th>编号</th>
                                    <th>计划单号</th>
                                    <th>用户</th>
                                    <th>手机</th>
                                    <th>还款金额</th>
                                    <th>总手续费</th>
                                    <th>当前手续费</th>
                                    <th>开始时间</th>
                                    <th>结束时间</th>
                                    <!-- <th width="150">计划创建时间</th> -->
                                    <th>执行时间</th>
                                    <th>还款笔数</th>
                                    <th>消费笔数</th>
                                    <th>费率</th>
                                    <th>银行名称</th>
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                            <tbody>
                              <tr>
                                <td><?php echo htmlentities($v['mission_id']); ?></td>
                                <td><?php echo htmlentities($v['mission_form_no']); ?></td>
                                <td><?php echo htmlentities($v['user_name']); ?>[<?php echo htmlentities($v['mission_uid']); ?>]</td>
                                <td><?php echo htmlentities($v['user_phone']); ?></td>
                                <td><?php echo htmlentities($v['mission_money']); ?></td>
                                <td><?php echo htmlentities($v['mission_fee']); ?></td>
                                <td><?php echo htmlentities($v['mission_at_fee']); ?></td>
                                <td>
                                    <?php echo htmlentities($v['mission_start_time']); ?>
                                </td>
                                <td>
                                    <?php echo htmlentities($v['mission_end_time']); ?>
                                </td>
                                <td>
                                    <?php echo htmlentities($v['mission_pay_time']); ?>
                                </td>
                                <td>
                                    <?php echo htmlentities($v['mission_repayment_number']); ?>/<?php echo htmlentities($v['mission_repayment']); ?>
                                </td>
                                <td>
                                    <?php echo htmlentities($v['mission_consume_number']); ?>/<?php echo htmlentities($v['mission_consume']); ?>
                                </td>
                                <td><?php echo htmlentities($v['mission_rate']*100); ?>%+<?php echo htmlentities($v['mission_close_rate']); ?></td>
                                <td><?php echo htmlentities($v['list_name']); ?>[<?php echo htmlentities($v['list_id']); ?>]</td>
                                <td>
                                    <?php switch($v['mission_state']): case "1": if($v['mission_type']==1 || $v['mission_type']==2): ?>
                                                <font color="red">需补单</font>
                                            <?php elseif($v['mission_type']==3): ?>
                                                处理中
                                            <?php else: ?>
                                                还款中
                                            <?php endif; break; case "2": ?>已完成<?php break; case "3": ?><font color="red">失败</font><?php break; case "4": ?><font color="red">用户终止计划</font><?php break; default: ?>未启动
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <a title="" class="layui-btn layui-btn-xs" onclick="fine(<?php echo htmlentities($v['mission_id']); ?>)" href="javascript:;">
                                        计划明细
                                    </a>
                                </td>
                              </tr>
                            </tbody>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                          </table>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <?php echo $list->render(); ?>
    <script src="/static/agent/js/layui.all.js"></script>

    <script>
        layui.use('laydate', function(){

          var laydate = layui.laydate;
          laydate.render({
            elem: '#starttime' //指定元素
          });

          laydate.render({
            elem: '#endtime' //指定元素
          });

        });

        // 还款订单计划明细
        function fine(id){
          window.location.href = "<?php echo Url('agent/Order/missdetails'); ?>?id="+id;
        }

    </script>
</body>
</html>