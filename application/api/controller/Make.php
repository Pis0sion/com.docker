<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Make extends Base{
	//看文档
	public function index(){
		
		$config = require CACHE_PATH.'system.php';
		
		$data= array();
		//判断是否绑卡
		$isbinCard = Db::name('userCard')->where('card_uid',$this->uid)->where('card_type',1)->find();
		if($isbinCard){
			$data['card'] = [
				'isbinCard'=> 1,
				'binCard'  =>$config['POINTS_CARD'],
			];
		}else{
			$data['card'] = [
				'isbinCard'=> 0,
				'binCard'  =>$config['POINTS_CARD'],
			];
		}
		//判断是否还款计划
		$ismission = Db::name('mission')->where('mission_uid',$this->uid)->where('mission_state',2)->find();
		if($ismission){
			$data['mission'] = [
				'ismission'=> 1,
				'mission'  =>$config['POINTS_REPAYMENT'],
			];
		}else{
			$data['mission'] = [
				'ismission'=> 0,
				'mission'  =>$config['POINTS_REPAYMENT'],
			];
		}
		//判断是否收款套现 
		$isRecords = Db::name('payRecords')->where('records_uid',$this->uid)->where('records_state',5)->find();
		if($isRecords){
			$data['records'] = [
				'isrecords'=> 1,
				'records'  =>$config['POINTS_RECEIVABLES'],
			];
		}else{
			$data['records'] = [
				'isrecords'=> 0,
				'records'  =>$config['POINTS_RECEIVABLES'],
			];
		}
		$list = Db::name('userType')->where('type_fee',0)->where('type_state',0)->order('type_sort asc')->select();
		
		$data['user'] = [
			'count' => Db::name('user')->where('user_pid',$this->uid)->where('user_state',0)->count(),
			'moeny' =>$this->user['user_moeny'],
			'integral' =>$this->user['user_integral'],
		];
		
		//isok 0 不可以升级 1可以升级
		
		$type = Db::name('userType')->where('type_id',$this->user['user_type_id'])->find();
		$NewsArr = array();
		foreach ($list as $k=>$v){
			
			$rete1 = $this->getRate($v['type_id'],1);
			$rete2 = $this->getRate($v['type_id'],2);
			$msg   = '收款'.($rete2['rate_rate']*100).'‰+'.$rete2['rate_close_rate'].'笔,';
			$msg  .= '还款'.($rete1['rate_rate']*100).'‰+'.$rete1['rate_close_rate'].'笔.';
			if($v['type_sort']<=$type['type_sort']){
				/*
				$NewsArr[] = [
					'id'	=> $v['type_id'],
					'name'	=> $v['type_name'],
					'count' => $v['type_free_count'],
					'amount'=> $v['type_free_amount'],
					'msg'   => $msg,
					'isok'=> 0,
				];
				*/
			}else{
				$NewsArr[] = [
					'id'	=> $v['type_id'],
					'name'	=> $v['type_name'],
					'count' => $v['type_free_count'],
					'amount'=> $v['type_free_amount'],
					'msg'   => $msg,
					'isok'=> $this->isok($this->uid,$v['type_free_count'], $v['type_free_amount']),
				];
			}
			unset($rete1);
			unset($rete2);
			unset($msg);
		}
		
		return json(['error'=>0,'msg'=>'成功','data'=>$data,'list'=>$NewsArr,'news'=>$this->getTel(8)]);
		
	}
	
	public function maketo(){
		if($this->request->isPost()) {
           
            $id 	  = input('post.id');
            $userType = Db::name('userType')->where('type_id',$id)->where('type_state',0)->find();
            if(!$userType){
            	return json(['error'=>1,'msg'=>'会员类型不存在']);
            }
            
            if($userType['type_fee']>0){
            	return json(['error'=>1,'msg'=>'此是费用升级']);
            }
            
           $isok =  $this->isok($this->uid,$userType['type_free_count'],$userType['type_free_amount']);
           
           if($isok = '1'){
           		//可以升级 但是需要判断下顺序
           		$type = Db::name('userType')->where('type_id',$this->user['user_type_id'])->find();
           		if($userType['type_sort']<=$type['type_sort']){
           			return json(['error'=>1,'msg'=>'您的等级已经超过了此等级']);
           		}
           		Db::name('user')->where('user_id',$this->uid)->update(['user_type_id'=>$id]);
           		
           		$rateList = Db::name('rate')->where('rate_type_id',$id)->select();
           		
           		foreach($rateList as $rk=>$rk){
           			
           			Db::name('userRate')
           			->where('rate_uid',$this->uid)
           			->where('rate_type',$rk['rate_type'])
           			->update(['rate_rate'=>$rk['rate_rate'],'rate_close_rate'=>$rk['rate_close_rate']]);
           			
           		}
           		
           		return json(['error'=>0,'msg'=>'升级成功']);
           }else{
           		//不能升级
           		return json(['error'=>1,'msg'=>'您不满足条件']);
           }
            
        }
		
	}
	
	
	public function getRate($id,$type){
		return Db::name('rate')->where('rate_type_id',$id)->where('rate_type',$type)->find();
	}
	/*
	 * 判断是否满足条件
	 * 2018年9月27日20:42:01
	 * 刘媛媛
	 */
	public function isok($uid,$count,$money){
		
		//获取用户推荐人数
		$countPuser  = Db::name('user')->where('user_pid',$uid)->where('user_state',0)->count();
		//获取用户总消费
		if($count>0){
			if($countPuser <= $count){
				return '0';
			}
		}
		if($money>0){
			if($this->user['user_repay_amount'] <= $money){
				return '0';
			}
		}
		
		return '1';
	}
	
	//看文档
	public function getTel($num){
		
		
		$text = ['的好友刷了一笔钱','的好友使用了一键还款','升级了会员等级','推广好友'];
		$arr = array();
		$tel    = [3,4,5,6,7,8,9];
		
		for($i=$num;$i>0;$i--){
			
			$msg = '1'.$this->getrand($tel).rand(1,9).'****'.rand(1111,9999).$this->getrand($text).'获得了'.rand(20,100).'.'.rand(1,99);
			$arr[] =$msg;
			unset($msg);
		}
		return $arr;
	}
	/*
	 * 取随机KEY
	 * 2018年9月27日19:53:29
	 * 刘媛媛
	 */
	
	public function getrand($arr){
		$reand =  rand(0,count($arr));
		return $arr[$reand];
	}
	
}
