<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\facade\Session;


class BankApply extends Controller
{
	
	/**
	 * 获取网申支持的银行卡列表
	 * 2018年10月30日11:39:41
	 * 刘媛媛
	 */
		
	public function getBank(){
		
		$dataApply = Db::name('bankApply')->where('apply_type',1)->where('apply_use',1)->find();
		if(!$dataApply){
			return json(['error'=>1,'msg'=>'渠道不存在']);
		}
		$event = controller('bank/'.$dataApply['payment_controller']);
		$arr   =  $event->getBank();
		return json($arr);
	}
	
	public function banklist(){
		
		$get  = input('get.');
		
		$id   = $get['id'];
		$page = $get['page']?:1;
		
		$dataApply = Db::name('bankApply')->where('apply_type',1)->where('apply_use',1)->find();
		if(!$dataApply){
			return json(['error'=>1,'msg'=>'渠道不存在']);
		}
		$event = controller('bank/'.$dataApply['payment_controller']);
		$arr   =  $event->banklist($id,$page);
		return json($arr);
	}
	
	
	public function bankinfo(){
		
		$get  = input('get.');
		
		$id   = $get['id'];
		
		$dataApply = Db::name('bankApply')->where('apply_type',1)->where('apply_use',1)->find();
		if(!$dataApply){
			return json(['error'=>1,'msg'=>'渠道不存在']);
		}
		$event = controller('bank/'.$dataApply['payment_controller']);
		$arr   =  $event->info($id);
		return json($arr);
		
	}
	
	
	 /**
     * 获取验证码
     * @Author tw
     * @Date   2018-09-14
     */
    public function getsms(){
        if($this->request->isGet()) {
            $mobile   = input('get.phone');   
            if(!checkMobile($mobile)){
                return json(['error'=>1,'msg'=>'您的手机号有误']);
            }
           	$type = 5;
        	if(!$this->getSmsNum($mobile,$type)){
                return json(['error'=>1,'msg'=>'发送次数过多请稍后再试']);
            }
            
            $send['send_code']   = rand(111111,999999);
            $send['send_time']   = time();
            $send['send_target'] = $mobile;
            $send['send_type']   = $type;
            $send['send_state']  = 0;
             $data['log_type']	 = 1;
            $send['send_member'] = $user_id;
            
            $retSend = Db::name('userVerify')->insert($send);
            if($retSend){
                sendsms($mobile,4,$send['send_code']);
            }
            return json(['error'=>0,'msg'=>'发送成功']);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    	
	}
	
	protected function getSmsNum($target,$type){
        $stime = time();
        $etime = time()-(20*60);
        $countNum = Db::name('user_verify')
        ->where('send_target','eq',$target)
        ->whereTime('send_time', 'between', [$etime,$stime])
        ->where('send_type','eq',$type)
        ->count();
        if($countNum>5){
            return false;
        }
        return true ;
    }
    
    
	public function bankdo(){
		
		if($this->request->isPost()) {
			$post   	= input('post.');
			$token      = $post['token'];
			$mobile 	= $post['mobile'];
			$name  	    = $post['name'];
			$idCard 	= $post['idCard'];
			$channelId  = $post['channelId'];
			$bank 		= $post['bank'];
			$code 		= $post['code'];
			$clientNo   = rand().time();
			
			$dataApply = Db::name('bankApply')->where('apply_type',1)->where('apply_use',1)->find();
			if(!$dataApply){
				return json(['error'=>1,'msg'=>'渠道不存在']);
			}
		
			if(!checkMobile($mobile)){
                return json(['error'=>1,'msg'=>'您的手机号有误']);
            }
            if($name==''){
            	return json(['error'=>1,'msg'=>'请输入您的姓名']);
            }
            
            if($idCard==''){
            	return json(['error'=>1,'msg'=>'请输入您的身份证']);
            }
            
            if($bank==''){
            	return json(['error'=>1,'msg'=>'申请银行卡信息错误']);
            }
            $user =  Db::name('user')->where('user_token',$token)->find();
            if(!$user){
            	return json(['error'=>1,'msg'=>'登陆失效请重新登录']);
            }
            $data = array();
            $data['log_user'] 		= $user['user_id'];
            $data['log_bank'] 		= $dataApply['apply_id'];
            $data['log_bank_name']  = $bank;
            $data['log_sn'] 		= $clientNo;
            $data['log_time'] 		= time();
            $data['log_bank_user'] 	= $name;
            $data['log_bank_idcard']= $idCard;
            $data['log_bank_phone'] = $mobile;
            $data['log_state']		= 0;
			$data['log_type']		= 1;
            $data['log_expand']		= $channelId;
            
            Db::name('bankApplyLog')->insert($data);
            
            $callbackUrl = $_SERVER['SERVER_NAME']."/index.php/api/BankApply/callback";
			
			
			$params = Array(
		        "mobile" 			=> $mobile,
		        "name" 				=> $name,
		        "idCard" 			=> $idCard,
		        "stationChannelId"  => $channelId, // 来自银行列表的 stationChannelId
		        "callbackUrl" 		=> $callbackUrl,
		        "clientNo" 			=> $clientNo,
		    );
		    
			
			$event = controller('bank/'.$dataApply['payment_controller']);
			$arr   =  $event->bankdo($params);
			
			if($arr['error']!=0){
				return array('error'=>1,'msg'=>$arr['msg']);
			}
			
			if($dataApply['payment_controller']=='Bank_Zk'){
				return json(['error'=>0,'msg'=>'ok','data'=>$arr['data']['url'],'type'=>1]);
			}
			
			return array('error'=>1,'msg'=>'未配置输出方式');
		}
		
	}
	
}


