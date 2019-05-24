<?php /*a:1:{s:33:"../Theme/agent/index/console.html";i:1542959844;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="/static/agent/css/layui.css">
    <link rel="stylesheet" href="/static/agent/css/view.css"/>
    <title></title>
</head>
<body class="layui-view-body">
    <div class="layui-content">
        <div class="layui-row layui-col-space20">
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-body chart-card">
                        <div class="chart-header">
                            <div class="metawrap">
                                <div class="meta">
                                    <span>今日消费金额</span>
                                </div>
                                <div class="total"><?php echo htmlentities($todymoney); ?></div>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div class="contentwrap">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-body chart-card">
                        <div class="chart-header">
                            <div class="metawrap">
                                <div class="meta">
                                    <span>今日分润</span>
                                </div>
                                <div class="total"><?php echo htmlentities($todyprofit); ?></div>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div class="contentwrap">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-body chart-card">
                        <div class="chart-header">
                            <div class="metawrap">
                                <div class="meta">
                                    <span>总交易额</span>
                                </div>
                                <div class="total"><?php echo htmlentities($sunmoney); ?></div>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div class="contentwrap">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6 layui-col-md3">
                <div class="layui-card">
                    <div class="layui-card-body chart-card">
                        <div class="chart-header">
                            <div class="metawrap">
                                <div class="meta">
                                    <span>总分润</span>
                                </div>
                                <div class="total"><?php echo htmlentities($sunprofit); ?></div>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div class="contentwrap">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm12 layui-col-md12" style="height: 100%;width:100%;">
                <div class="layui-card">
                    <div class="layui-tab layui-tab-brief" style="height: 100%;width:100%;">
                        <ul class="layui-tab-title">
                        </ul>
                        
                    </div>
                </div>
            </div>
        </div>
        <div id="container" style="height: 500%;width:100%;"></div>
    </div>
    <script src="/static/agent/js/layui.all.js"></script>
    <script src="/static/agent/js/echarts.js"></script>
    <script>
     var element = layui.element;
    </script>
</body>
</html>
<script>
var dom = document.getElementById("container");
var myChart = echarts.init(dom);
var app = {};
option = null;
app.title = '折柱混合';

option = {
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'cross',
            crossStyle: {
                color: '#999'
            }
        }
    },
    toolbox: {
        feature: {
            dataView: {show: true, readOnly: false},
            magicType: {show: true, type: ['line', 'bar']},
            restore: {show: true},
            saveAsImage: {show: true}
        }
    },
    legend: {
        data:['今日分润','今日交易额','平均交易额']
    },
    xAxis: [
        {
            type: 'category',
            data: <?php echo json_encode($key)  ?>,
            axisPointer: {
                type: 'shadow'
            }
        }
    ],
    yAxis: [
        {
            type: 'value',
            name: '今日分润',
            min: 0,
            max: 250,
            interval: 50,
            axisLabel: {
                formatter: '{value} '
            }
        },
        {
            type: 'value',
            name: '今日交易额',
            min: 0,
            max: 25,
            interval: 5,
            axisLabel: {
                formatter: '{value} 元'
            }
        }
    ],
    series: [
        {
            name:'今日分润',
            type:'bar',
            data:<?php echo json_encode($suarrs)  ?>
        },
        {
            name:'今日交易额',
            type:'bar',
            data:<?php echo json_encode($arrst)  ?>
        }
    ]
};
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
</script>