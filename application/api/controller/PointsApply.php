<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\facade\Session;


class PointsApply extends Controller
{
	
	/**
	 * 获取网申支持的银行卡列表
	 * 2018年10月30日11:39:41
	 * 刘媛媛
	 */
		
	public function getBank(){
		
		$dataApply = Db::name('bankApply')->where('apply_type',3)->where('apply_use',1)->find();
		if(!$dataApply){
			return json(['error'=>1,'msg'=>'渠道不存在']);
		}
		$event = controller('bank/'.$dataApply['payment_controller']);
		$arr   =  $event->getBank();
		return json($arr);
	}
	/*
	 * 获取银行通道类型
	 * 2018年11月16日08:46:32
	 * 刘媛媛
	 */
	public function banklist(){
		
		$get  = input('get.');
		
		$id   = $get['id'];
		
		$dataApply = Db::name('bankApply')->where('apply_type',3)->where('apply_use',1)->find();
		if(!$dataApply){
			return json(['error'=>1,'msg'=>'渠道不存在']);
		}
		$event = controller('bank/'.$dataApply['payment_controller']);
		$arr   =  $event->convChannelDetails($id);
		return json($arr);
	}
	
	
	public function prlist(){
		
		$get  = input('get.');
		
		$id   = $get['id'];
	
		$dataApply = Db::name('bankApply')->where('apply_type',3)->where('apply_use',1)->find();
		if(!$dataApply){
			return json(['error'=>1,'msg'=>'渠道不存在']);
		}
		$event = controller('bank/'.$dataApply['payment_controller']);
		$arr   =  $event->convTagsList($id);
		return json($arr);
		
		
	}
	/*
	 * 查看兑换详情
	 * 2018年11月16日09:19:16
	 * 刘媛媛
	 */
	public function info(){
		
		$get  = input('get.');
		$id   = $get['id'];
		$dataApply = Db::name('bankApply')->where('apply_type',3)->where('apply_use',1)->find();
		if(!$dataApply){
			return json(['error'=>1,'msg'=>'渠道不存在']);
		}
		$event = controller('bank/'.$dataApply['payment_controller']);
		$arr   =  $event->convTagsDetails($id);
		return json($arr);
		
	}
	
	public function bankdo(){
		
		if($this->request->isPost()) {
			$post   	= input('post.');
			$token      = $post['token'];
			$type 		= $post['type'];
			$content  	= $post['content']?:'';
			$TagId	 	= $post['channelTagId'];
			$channelId  = $post['oemChannelId'];
			$clientNo   = rand().time();
			 
			$dataApply = Db::name('bankApply')->where('apply_type',3)->where('apply_use',1)->find();
			if(!$dataApply){
				return json(['error'=>1,'msg'=>'渠道不存在']);
			}
		
            $user =  Db::name('user')->where('user_token',$token)->find();
            if(!$user){
            	return json(['error'=>1,'msg'=>'登陆失效请重新登录']);
            }
            $data = array();
            $data['log_user'] 		= $user['user_id'];
            $data['log_bank'] 		= $dataApply['apply_id'];
            $data['log_bank_name']  = '积分兑换';
            $data['log_sn'] 		= $clientNo;
            $data['log_time'] 		= time();
            $data['log_bank_user'] 	= $user['user_name'];
            $data['log_bank_idcard']= $user['user_idcard'];
            $data['log_bank_phone'] = $user['user_phone'];
            $data['log_state']		= 0;
			$data['log_type']		= 3;
            $data['log_expand']		= json_encode($post);
            
            Db::name('bankApplyLog')->insert($data);
            
            $callbackUrl = $_SERVER['SERVER_NAME']."/index.php/api/".$dataApply['payment_controller']."/callback";
			
			if($type =='EXCHANGE_CODE'){
				
				if($content==''){
					return json(['error'=>1,'msg'=>'请输入兑换码']);
				}
				$params = Array(
			        "oemChannelId"  => $channelId,  //通道id  通道列表上的id
			        "channelTagId"  => $TagId, //类目id  类目列表上的id
			        "type" 			=> $type, //通道类型  通道列表上的通道类型
			        "content" 		=> $content, //兑换码 当通道类型为EXCHANGE_CODE才填写该参数
			        "callbackUrl"   => $callbackUrl,
			        "clientNo" 		=> $clientNo,
			    );
			}
			
			if($type =='QR_CODE'){
				
				$params = Array(
			        "oemChannelId"  => $channelId,  //通道id  通道列表上的id
			        "channelTagId"  => $TagId, //类目id  类目列表上的id
			        "type" 			=> $type, //通道类型  通道列表上的通道类型
			        "content" 		=> '', //兑换码 当通道类型为EXCHANGE_CODE才填写该参数
			        "file"			=> $post['file'],
			        "callbackUrl"   => $callbackUrl,
			        "clientNo" 		=> $clientNo,
			    );
			}
			
			if($type =='PAYMENT'){
				
				$params = Array(
			        "oemChannelId"  => $channelId,  //通道id  通道列表上的id
			        "channelTagId"  => $TagId, //类目id  类目列表上的id
			        "type" 			=> $type, //通道类型  通道列表上的通道类型
			        "callbackUrl"   => $callbackUrl,
			        "clientNo" 		=> $clientNo,
			    );
			}
			
			$event = controller('bank/'.$dataApply['payment_controller']);
			$arr   =  $event->bankdo($params);
			
			if($arr['error']!=0){
				return json(array('error'=>1,'msg'=>$arr['msg']));
			}
			
			if($dataApply['payment_controller']=='Apply_Zk'){
				return json(['error'=>0,'msg'=>'ok','data'=>$arr['data']['url'],'type'=>$type]);
			}
			
			return json(array('error'=>1,'msg'=>'未配置输出方式'));
		}
		
	}
	
}


