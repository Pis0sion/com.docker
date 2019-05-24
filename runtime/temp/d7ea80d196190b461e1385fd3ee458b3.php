<?php /*a:1:{s:35:"../Theme/adminsys/agent\addent.html";i:1550187480;}*/ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="renderer" content="webkit|ie-comp|ie-stand">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta http-equiv="Cache-Control" content="no-siteapp" />
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
		<script src="/plugins/jquery-1.9.1.min.js"></script>
		<script src="/static/admin/assets/js/bootstrap.min.js"></script>
		<script src="/static/admin/assets/js/typeahead-bs2.min.js"></script>
		<script src="/static/admin/assets/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="/static/admin/assets/js/jquery.ui.touch-punch.min.js"></script>
		<script src="/static/admin/assets/js/ace-elements.min.js"></script>
		<script src="/static/admin/assets/js/ace.min.js"></script>
		<title>添加</title>
		<script type="text/javascript">
			var s_Prov = <?php echo $data['agent_region_province']=="" ? '15' : htmlentities($data['agent_region_province']); ?>;
			var s_City = <?php echo $data['agent_region_city']=="" ? '142' : htmlentities($data['agent_region_city']); ?>;
			//var s_Dist = <?php echo $data['agent_areaid']=="" ? '1288' : htmlentities($data['agent_areaid']); ?>;
			var province_url = "<?php echo Url('/Api/Common/ajax_province'); ?>";
			var city_url     = "<?php echo Url('/Api/Common/ajax_city'); ?>";
			var district_url = "<?php echo Url('/Api/Common/ajax_district'); ?>";
			</script>
		<script src="/plugins/layer/layer.js"></script>
		<script src="/plugins/common.js"></script>

	<body>
		<div class="margin clearfix">
			<div class="stystems_style">
				<div class="tabbable">
					<div class="tab-content">
						<form name="agent-form" id="agent-form" action="<?php echo Url('Admin/Agent/addent'); ?>">
						<div id="home" class="tab-pane active">
							<?php if($AGENT_GRADE == 1): ?>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>上级代理： </label>
								<div class="col-sm-8">
									<select name='agent_uid' id="agent_uid" class="col-xs-10 " style="margin-left:10px ">
										<option value="">请选择</option>
										<option value="0">系统代理</option>
										<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
											<option value="<?php echo htmlentities($v['agent_id']); ?>"><?php echo htmlentities($v['agent_name']); ?>-<?php echo htmlentities($v['agent_phone']); ?></option>
										<?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</div>
							</div>
							<?php else: ?>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>上级代理： </label>
								<div class="col-sm-8">
									<select name='agent_uid'  class="col-xs-10 " style="margin-left:10px ">
										<option value="0">系统代理</option>
										<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
											<option value="<?php echo htmlentities($v['agent_id']); ?>"><?php echo htmlentities($v['agent_name']); ?>-<?php echo htmlentities($v['agent_phone']); ?></option>
										<?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</div>
							</div>
							<?php endif; ?>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>用户名： </label>
								<div class="col-sm-8">
									<input  type="text" name="account"  id="account"  class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>密码： </label>
								<div class="col-sm-8">
									<input type="text" name="password" id="password"  class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>姓名： </label>
								<div class="col-sm-8">
									<input type="text" name="name" id="name" class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>手机号： </label>
								<div class="col-sm-8">
									<input type="number" name="phone" id="phone"   class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>身份证： </label>
								<div class="col-sm-8">
									<input type="text" value="" id="idcard" name="idcard" class="col-xs-10 ">
								</div>
							</div>
		                  <?php if($AGENT_GRADE == 1): ?>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>添加代理等级： </label>
								<div class="col-sm-8">
									<select name='grade_id' id="grade_id" class="col-xs-10 " style="margin-left:10px ">
										<option value="">请选择</option>
									</select>
								</div>
							</div>
		                  <?php else: ?>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>客户容纳人数： </label>
								<div class="col-sm-8">
									<input type="number" id="capacity" step="0" name="capacity" class="col-xs-10 ">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>签署还款费率% </label>
								<div class="col-sm-8">
									<input type="number" step="0.01" value="" id="hk" name="hk" class="col-xs-10 ">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>签署收款款费率% </label>
								<div class="col-sm-8">
									<input type="number"   step="0.01" name="sk" id="sk" class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>代理升级费率‰ </label>
								<div class="col-sm-8">
									<input type="number"   step="0.01" name="sj" id="sj" class="col-xs-10 ">
								</div>
							</div>
							<?php endif; ?>
							
							<div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" >代理商地址</label>
                                <div class="col-sm-8">
                                	<div class="input-group col-sm-12">
										<select id="prov" name="region_province" class="form-control" style="width:13%"></select>
										<select id="city" name="region_city" class="form-control" style="width:13%"></select>
										<!--<select id="dist" name="dist" class="form-control" style="width:14%"></select>-->
                                	</div>
                                </div>
                            </div>
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>代理城市 </label>
								<div class="col-sm-8">
									<input type="text"  id="city" name="city" class="col-xs-10 ">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i>*</i>公司名 </label>
								<div class="col-sm-8">
									<input type="text"   id="agent_company" name="agent_company" class="col-xs-10 ">
								</div>
							</div>
							<div class="form-group"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"><i></i>状态： </label>
								<div class="col-sm-8">
									<select name='agent_state' class="col-xs-10 " style="margin-left:10px ">
										<option value="0">正常</option>
										<option value="1">冻结</option>
									</select>
								</div>
							</div>
							
							<div class="Button_operation">
								<button  onclick="saveSubmit();" id="agent-btn" class="btn btn-primary radius" type="button"><i class="fa fa-save "></i>&nbsp;保存</button>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>

		</div>
		
		<script>
			
			$(function(){
				autoLoad();
				$("#prov").on("change",function(){
					provinceID = $(this).val();
					uri = city_url + "?id="+provinceID;
					getCitys($("#city"), uri, function(){
						if(!isNaN(s_City) && s_City != 0) {
							$("#city").val(s_City);
							s_City = 0;
						}
					});
				});
				/*
				$("#city").on("change",function(){
					cityID = $(this).val();
					uri = district_url + "?id="+cityID
					getDistrict($("#dist"),uri, function(){
						if(!isNaN(s_Dist) && s_Dist != 0) {
							$("#dist").val(s_Dist);
							s_Dist = 0;
						}
					});
				});
				*/
			});
			function autoLoad() {
				var provURI = province_url;
				getProvince($("#prov"), provURI, function(){
					if(!isNaN(s_Prov) && s_Prov != 0) {
						$("#prov").val(s_Prov);
						s_Prov = 0;
					}
				});
			}
			function  saveSubmit () {
				var url    = $("#agent-form").attr('action');
				
				var agent_uid = $('#agent_uid').val();
				if(agent_uid==''){
					layer.msg('请选择上级代理');
					return false;
				}
				var account = $('#account').val();
				if(account==''){
					layer.msg('请输入账号');
					return false;
				}
				var password = $('#password').val();
				if(password==''){
					layer.msg('请输入登陆密码');
					return false;
				}
				var name = $('#name').val();
				if(name==''){
					layer.msg('请输入代理姓名');
					return false;
				}
				var phone = $('#phone').val();
				if(phone==''){
					layer.msg('请输入手机号');
					return false;
				}
				var idcard = $('#idcard').val();
				if(idcard==''){
					layer.msg('请输入身份证号码');
					return false;
				}
				var capacity = $('#capacity').val();
				if(capacity==''){
					layer.msg('请输入承载量');
					return false;
				}
				var hk = $('#hk').val();
				if(hk==''){
					layer.msg('请输入还款费率');
					return false;
				}
				var sk = $('#sk').val();
				if(sk==''){
					layer.msg('请输入收款款费率');
					return false;
				}
				var sj = $('#sj').val();
				if(sj==''){
					layer.msg('请输入会员升级费率');
					return false;
				}
				var city = $('#city').val();
				if(city==''){
					layer.msg('请输入代理所在城市');
					return false;
				}
				var grade_id = $("#grade_id").val();
				if(grade_id==''){
					layer.msg('请选择代理等级');
					return false;
				}
				$("#home").on("click","")
				var data   = $("#agent-form").serialize();

				ajaxPost(url,$("#agent-btn"),data,function (r) {
					if(r.error == '0'){
						// layer.msg(r.msg);
	                    // setTimeout(function(){
						window.location.href="<?php echo Url('agent/index'); ?>"
	                    // },1500) 
		            	$("#agent-btn").removeAttr('disabled');

		            }

		            //location.reload();
		        })
			}

        $("#agent_uid").change(function(){  //监听下拉列表的change事件
            var address = $(this).val();  //获取下拉列表选中的值
            var url    = "<?php echo Url('agent/Twolinkage'); ?>";
            $.ajax({
                type:'post',
                url:url,
                data:{id:address},
                dataType:'json',
                success:function(data){  //请求成功回调函数
                    
                    if(data.error == 0){  //判断状态码，200为成功
                        var option = '<option value="">请选择</option>';
                        for(var i=0;i<data.data.length;i++){  //循环获取返回值，并组装成html代码
                            option +='<option value="'+data.data[i].grade_id+'">'+data.data[i].grade_name+'</option>';
                        }
                        $("#grade_id").html(option);  //js刷新第二个下拉框的值
                    }else{
                        layer.msg(data.msg);
						return false;
                    }
                },
            });
        });
		</script>
	</body>

</html>