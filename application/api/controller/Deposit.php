<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;

class Deposit extends Base{

	/**
	 * 保证金申退
	 */
	public function Index(){
		if($this->request->isPost()){

			$post = input('post.');
			if(empty($post))
			{
				return json(['error'=>1,'msg'=>'参数错误']);
			}

			$config = require CACHE_PATH.'system.php';
			$member_user = Db::name('User as u')
						 ->join('PayUpgrade p','u.user_id=p.upgrade_uid')
						 ->where('u.user_pid',$this->uid)
						 ->where('p.upgrade_state',1)
						 ->select();
			$usermember = $this->assoc_title($member_user,'user_id');
			if(count($usermember) < $config['DEPOSIT_MEMBER_SUM'])
			{
				return json(['error'=>1,'msg'=>'您邀请的人数未达到哦，暂时还不能申请退保证金']);
			}


			$deposit_id = intval($post['id']);
			$list = Db::name('Deposit')->where(['deposit_id'=>$deposit_id,'deposit_state'=>1])->find();
			if(empty($list))
			{
				return json(['error'=>1,'msg'=>'暂无可退款记录']);
			}
	        Db::startTrans();
	        try{

	        	$data = array();
	        	$data['deposit_state'] = 2;
	        	$data['deposit_time']  = time();
	        	$data['deposit_extract_time']= time();
	            $result = Db::name('Deposit')->where('deposit_id',$deposit_id)->update($data);
	            
	            if($result)
	            {
	            	$res = Db::name('User')->where('user_id',$list['deposit_user'])->setInc('user_moeny',$list['deposit_money']);
            		moneyLog($list['deposit_user'], $list['deposit_money'], '会员保证金退款', 1, time());
	            	if($res)
	            	{
		                // 提交事务
		                Db::commit(); 
		                return json(['error'=>0,'msg'=>'退款成功']);
	            	}

	            }else{
	                return json(['error'=>1,'msg'=>'退款失败']);
	            }
	        } catch (\Exception $e) {
	            // 回滚事务
	            Db::rollback();
	        }

		}else{

			return json(['error'=>1,'msg'=>'参数错误']);
		}
	}

	/**
	 * 保证金信息详情
	 */
	public function DepositInfo(){
		if($this->request->isPost())
		{

			$post = input('post.');
			if(empty($post))
			{
				return json(['error'=>1,'msg'=>'参数错误']);
			}
			$config = require CACHE_PATH.'system.php';
			if($config['DEPOSIT_MEMBER'] == 0)
			{
				return json(['error'=>1,'msg'=>'该通道暂时关闭']);
			}

			// if(intval($post['type_id']) > $config['DEPOSIT_MEMBER_ID'] || intval($post['type_id']) == $config['DEPOSIT_MEMBER_ID']){
			// 	return json(['error'=>1,'msg'=>'你当前等级已超过该等级，无法进行缴纳']);
			// }

			$list = Db::name('UserType as t')
				  ->join('Rate r','t.type_id=r.rate_type_id')
				  ->where('type_id',$config['DEPOSIT_MEMBER_ID'])
				  ->select();
			if(empty($list))
			{
				return json(['error'=>1,'msg'=>'暂无可缴纳保证金通道']);
			}
			foreach ($list as $key => $val) {
				$list[$key]['rate_rate'] = $val['rate_rate']*100;
			}

			$member_user = Db::name('User as u')
						 ->join('PayUpgrade p','u.user_id=p.upgrade_uid')
						 ->where('u.user_pid',$this->uid)
						 ->where('p.upgrade_state',1)
						 ->select();
			$usermember = $this->assoc_title($member_user,'user_id');
			
			$date = Db::name('Deposit')->field('deposit_id,deposit_money,deposit_user,deposit_state')->where('deposit_user',$this->uid)->where('deposit_state','neq','0')->find();
			if(empty($date))
			{
				$date = array();
				$arr = array();
				$arr['member_sum']  = 0;
				$arr['member_user'] = 0;
			}else{
				$arr = array();
				$arr['member_sum']  = $config['DEPOSIT_MEMBER_SUM'];
				$arr['member_user'] = count($usermember);
			}

			return json(['error'=>0,'msg'=>'ok','data'=>$list,'date'=>$date,'arr'=>$arr]);
		}else{

			return json(['error'=>1,'msg'=>'参数错误']);
		}
	}

	private function assoc_title($arr, $key)
	{
	  $tmp_arr = array();
	  foreach ($arr as $k => $v) {
	    if (in_array($v[$key], $tmp_arr)) {
	      unset($arr[$k]);
	    } else {
	      $tmp_arr[] = $v[$key];
	    }
	  }
	  return $arr;
	}

}