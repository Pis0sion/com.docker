<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Coupon extends Base{
	//优惠券功能
	public function index(){
		
		$list = Db::name('couponLog')
		->alias('a')
		 ->field('w.cou_name,a.*')
		->where('coul_user',$this->uid)
		->join('coupon w','a.coul_cou = w.cou_id')
		->select();
		
		$listNews['wsy'] = array();
		$listNews['log'] = array();
		$listNews['out'] = array();
		foreach ($list as $k=>$v){
			
			//领取时间
			if($v['coul_receive_time']>0){
				$v['coul_receive_time'] = date('Y-m-d H:i',$v['coul_receive_time']);
			}
			//使用时间
			if($v['coul_use_time']>0){
				$v['coul_use_time'] = date('Y-m-d H:i',$v['coul_use_time']);
			}
			//代金券过期时间
			if($v['coul_time']=='0'){
				$v['coul_time'] ='长期';
			}else{
				$v['coul_time'] = date('Y-m-d H:i',$v['coul_time']);
			}
			
			
			switch ($v['coul_state'])
			{
			case '0':
				$listNews['wsy'][] = $v;
				break;  
			case '1':
				$listNews['log'][] = $v;
				break;
			case '2':
				$listNews['out'][] = $v;
				break;
			
			}
			
		}
		$count = ['wsy'=>count($listNews['wsy']),'log'=>count($listNews['log']),'out'=>count($listNews['out'])];
		return json(['error'=>0,'msg'=>'成功','count'=>$count,'wsy'=>$listNews['wsy'],'log'=>$listNews['log'],'out'=>$listNews['out']]);
		
	}
}
