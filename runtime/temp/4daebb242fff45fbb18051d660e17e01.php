<?php /*a:1:{s:34:"../Theme/index/index/register.html";i:1549953925;}*/ ?>
<!DOCTYPE html>
<!-- saved from url=(0052)http://kayuhome.cn/mobile/index/app_register/95.html -->
<html class="pixel-ratio-1">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=SSFtLjfZA9eQTavnSK5fRot1k5rSDmaq"></script>
    <script type="text/javascript" src="/static/index/api"></script><script type="text/javascript" src="/static/index/getscript"></script>
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <title><?php echo getconfig('SITE_NAME'); ?></title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link rel="stylesheet" href="/static/index/css/sm.min.css">
    <!-- <link rel="stylesheet" href="http://kayuhome.cn/static/mobile/css/style.css">   -->
    <link rel="stylesheet" href="/static/index/css/weui.css">
    <link rel="stylesheet" href="/static/index/css/example.css">
	<style type="text/css">
		.page-current{ background-color: #000000; }
		.button { height: 2rem; line-height: 2rem !important; font-size: 0.8rem; }
		.imageinput {
			opacity:0;
			filter:alpha(opacity=0);
			height: 95px;
			width: 100px;
			position: absolute;
			top: 0;
			left: 0;
			z-index: 9;
		}
		div
		{
			position: relative;
		}
       .mui-popup.mui-popup-in {
    display: block;
    -webkit-transition-duration: 400ms;
    transition-duration: 400ms;
    -webkit-transform: translate3d(-50%,-50%,0) scale(1);
    transform: translate3d(-50%,-50%,0) scale(1);
    opacity: 1;
}
	.mui-popup {
    position: fixed;
    z-index: 10000;
    top: 50%;
    left: 50%;
    display: none;
    overflow: hidden;
    width: 270px;
    -webkit-transition-property: -webkit-transform,opacity;
    transition-property: transform,opacity;
    -webkit-transform: translate3d(-50%,-50%,0) scale(1.185);
    transform: translate3d(-50%,-50%,0) scale(1.185);
    text-align: center;
    opacity: 0;
    color: #000;
    border-radius: 13px;
}
      .mui-popup-inner {
    position: relative;
    padding: 15px;
    border-radius: 13px 13px 0 0;
    background: rgba(255,255,255,.95);
}
      .mui-popup-title {
    font-size: 18px;
    font-weight: 500;
    text-align: center;
}
      .mui-popup-title+.mui-popup-text {
    font-family: inherit;
    font-size: 14px;
    margin: 5px 0 0;
}
      .mui-popup-inner:after {
    position: absolute;
    z-index: 15;
    top: auto;
    right: auto;
    bottom: 0;
    left: 0;
    display: block;
    width: 100%;
    height: 1px;
    content: '';
    -webkit-transform: scaleY(.5);
    transform: scaleY(.5);
    -webkit-transform-origin: 50% 100%;
    transform-origin: 50% 100%;
    background-color: rgba(0,0,0,.2);
}
      .mui-popup-buttons {
    position: relative;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    height: 44px;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    justify-content: center;
}
      .mui-popup-button {
    font-size: 17px;
    line-height: 44px;
    position: relative;
    display: block;
    overflow: hidden;
    box-sizing: border-box;
    width: 100%;
    height: 44px;
    padding: 0 5px;
    cursor: pointer;
    text-align: center;
    white-space: nowrap;
    text-overflow: ellipsis;
    color: #007aff;
    background: rgba(255,255,255,.95);
    -webkit-box-flex: 1;
}
      
      .mui-popup-button.mui-popup-button-bold {
    font-weight: 600;
}
      .mui-popup-button:first-child:last-child {
    border-radius: 0 0 13px 13px;
}
      
      .mui-popup-backdrop {
    position: fixed;
    z-index: 998;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    -webkit-transition-duration: 400ms;
    transition-duration: 400ms;
    opacity: 0;
    background: rgba(0,0,0,.4);
}
      .mui-popup-backdrop.mui-active {
    opacity: 1;
}
      .mui-popup.mui-popup-out
{
    -webkit-transition-duration: 400ms;
            transition-duration: 400ms;
    -webkit-transform: translate3d(-50%, -50%, 0) scale(1);
            transform: translate3d(-50%, -50%, 0) scale(1);

    opacity: 0;
}
	</style>
	<link rel="stylesheet" type="text/css" href="/static/index/css/style.css">
</head>
<body ontouchstart="" >
	<div id="allmap" style="width: 0%;height: 0px;"></div>
	<div class="container js_container"></div>
  	<input name="Longitude" class="weui_input" id ="Longitude" type="hidden" value="" />
	<input name="latitude" class="weui_input" id ="latitude" type="hidden" value="" />
<script type="text/html" id="tpl_reg1">
		<div class="page" style="display:block;" >
			<div class="hd" style="padding:0.5em;">
				<h1 class="page_title" style="font-size:1.5em;padding: 20px 0 30px 0;"><img height="70px" src="<?php echo getconfig('SITE_LOGO'); ?>"/></h1>
			</div>
			<div class="bd" >
				<div class="weui_cells weui_cells_form">
					<div class="weui_cells" style="margin-top: 0px;">
						<div class="weui_cell" style="height:44px;padding:0 0 0 15px;">
							<div class="weui_cell_hd"><label class="weui_label" style="width: 4em;">账号</label></div>
							<div class="weui_cell_bd weui_cell_primary">
								<input class="weui_input" name='phone' id="phone" type="number" pattern="[0-9]*" placeholder="请输入手机号"/>
							</div>
						</div>

						<div class="weui_cell" style="height:44px;padding:0 0 0 15px;">
							<div class="weui_cell_hd"><label class="weui_label" style="width: 4em;">密码</label></div>
							<div class="weui_cell_bd weui_cell_primary">
								<input name="keywords" class="weui_input" id ="keywords" type="password" pattern="[6-60]*" placeholder="请填写登录密码"/>
							</div>
						</div>
						<input type="hidden" name="user_isapp" value="2" />
						<div class="weui_cell" style="height:44px;padding:0 0 0 15px;">
							<div class="weui_cell_hd"><label class="weui_label" style="width: 4em;">确认密码</label></div>
							<div class="weui_cell_bd weui_cell_primary">
								<input name="rekeywords" class="weui_input" id ="rekeywords" type="password" pattern="[6-60]*" placeholder="请再次填写登录密码"/>

							</div>
						</div>
	                   <div class="weui_cell" style="height:44px;padding:0 0 0 15px;">
							<div class="weui_cell_hd"><label class="weui_label" style="width: 4em;">验证码</label></div>
							<div class="weui_cell_bd weui_cell_primary">
	                            <input style="width:60%;" name="smscode" class="weui_input" id ="smscode" type="number" pattern="[6-60]*" placeholder="请输入短信验证码" value=""/>
	                            <button class="code" style="float:right;color: #007aff;background: #fff;border: 0;width: 40%;" name="code2" href="javascript:" id="code2" onclick=" code()" >获取验证码</button>               
							</div>
						</div>
					</div>
					<div class="weui_cell">邀请码：<?php echo htmlentities($id); ?></div>
					<input type="hidden" name="recommend" value="<?php echo htmlentities($id); ?>" />
				</div>
				<div style="height: 40px; line-height:40px; margin-left: 10px; font-size: 12px;">
					<label for="reg_protocol">
						<input type="checkbox" name="reg_protocol" id="reg_protocol" checked>
					 	注册即表示您已同意<a href="<?php echo Url('api/Main/protocol'); ?>" target="_blank">《用户注册协议》</a>
				 	</label>
					
				</div>
				<div class="weui_btn_area">
					<a class="weui_btn weui_btn_primary js_grid" style="background: #007aff;width: 90%;" href="javascript:" id="showTooltips">立即注册</a>
				</div>
				
				<div style="height: 20px;"></div>
				<div class="weui_btn_area">
					<a class="weui_btn weui_btn_primary " style="background: #fff;width: 90%;border:0.5px solid #007aff ;color: #007aff; "  href="<?php echo Url('Index/index/download'); ?>"  id="">下载地址</a>
				</div>
			</div>
		</div>
        </script>
		<script type="text/javascript" src="/static/index/js/zepto.min.js" charset="utf-8"></script>
		<script type="text/javascript" src="/static/index/js/sm.min.js" charset="utf-8"></script>
		<script src="/static/index/js/jweixin-1.0.0.js"></script>
		<script type="text/javascript" src="/static/index/js/jquery-1.8.3.min.js"></script>
  		<script type="text/javascript" src="/static/index/js/mui.min.js"></script>
  		<script type="text/javascript" src="http://api.map.baidu.com/api?ak=PlhFWpA02aoURjAOpnWcRGqw7AI8EEyO&v=2.0&services=false"></script> 

		<script type="text/javascript">
        // 百度地图API功能
		var map = new BMap.Map("allmap");
        var geoc = new BMap.Geocoder();
        var geolocation = new BMap.Geolocation();
        geolocation.getCurrentPosition(function(r){
          if(this.getStatus() == BMAP_STATUS_SUCCESS){
            var mk = new BMap.Marker(r.point);
            map.addOverlay(mk);
            map.panTo(r.point);
            $("#Longitude").val(r.point.lng);
            $("#latitude").val(r.point.lat);
           // alert($("#longitude").val());
           // alert($("#latitude").val());
            console.log("当前位置经度为:"+r.point.lng+"纬度为:"+r.point.lat);
           
          } else {
            console.log('无法定位到您的当前位置，导航失败，请手动输入您的当前位置！'+this.getStatus());
          }
        },{enableHighAccuracy: true});          
          
	    </script>

		<script type="text/javascript">

		    var flag = true;
		    
		    // 获取短信验证码
		    function code(){
		        var phoneNum = $("input[name='phone']").val();
		        var r = /^[1][3,4,5,6,7,8,9][0-9]{9}$/;
		        if (!r.test(phoneNum)) {
		                mui.alert('手机号码不正确，请输入正确的手机号码');
		                return false;
		        }
		       if(flag){
		            $.ajax({
		                url:"<?php echo Url('api/Main/getsms'); ?>",
		                type:"get",
		                dataType:"json",
		                data:{"phone":phoneNum, 'type':1},
		                timeout:5000,
		                success: function(data) {
		                    //$.hideIndicator();
		                    if(data.error == 1){
				                mui.alert(data.msg);
				                return false;
		                    }else{
			                    mui.alert(data.msg, '温馨提示', function() {
									var validCode=true;
							        var time=60;
							        time--;
							       	$('#code2').html(time+"S");
							        if (time==0) {
							        	$('#code2').removeAttr("disabled");
							            clearInterval(t);
							            $('#code2').html("重新获取");
							            validCode=true;				
							        }
							        if (validCode) {
							        	$('#code2').attr("disabled","true");
							            validCode=false;
							            var t=setInterval(function  () {
							                time--;
							                $('#code2').html(time+"S");
							                if (time==0) {
							                	$('#code2').removeAttr("disabled");
							                    clearInterval(t);
							                    $('#code2').html("重新获取");
							                    validCode=true;	
							                }
							            },1000)
							        }
			                    });
			                }
		                }

		            })
		       }
			}

			
		</script>
		<script type="text/javascript" src="/static/index/js/regi.js"></script>
		<div id="js-atavi-extension-install"></div>
	</body>
</html>