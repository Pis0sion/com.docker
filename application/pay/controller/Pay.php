<?php
namespace app\pay\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;

class Pay extends Base
{	
  	public function index(){
      //手动处理支付订单
   	 // $ss = $this->Notifys('458431542356853',rand(),'99.00');
    //  dump($ss);
    }
    /**
     * 会员升级回调
     */
    public function Notifys($upgrade_form_no,$upgrade_sn,$upgrade_money){

        $list = Db::name('PayUpgrade')->where(['upgrade_form_no'=>$upgrade_form_no,'upgrade_state'=>0])->find();
		
        if(empty($list)){
          return ['error'=>1,'msg'=>'查无此订单或订单已支付完成'];
        }
		
        if($list['upgrade_money'] != $upgrade_money){
            return ['error'=>1,'msg'=>'订单金额不一致'];
        }
		
        $config = require CACHE_PATH.'system.php';
		
       Db::startTrans();
        try{
            
            $data = array();
            $data['upgrade_sn']    = $upgrade_sn;
            $data['upgrade_state'] = 1;
            $data['upgrade_oktime']= time();
            $result = Db::name('PayUpgrade')->where('upgrade_form_no',$upgrade_form_no)->update($data);
			
			 
            if($result)
            {	$user = Db::name('User')->where('user_id',$list['upgrade_uid'])->find();
                $res = Db::name('User')->where('user_id',$list['upgrade_uid'])->update(['user_type_id'=>$list['upgrade_type_id']]);
                
				//修改会员费率
				$reteList = Db::name('rate')->where('rate_type_id',$list['upgrade_type_id'])->select();
				
				foreach ($reteList as $rk=>$rv){
					
					//判断是否存在 
					if(Db::name('userRate')->where('rate_uid',$list['upgrade_uid'])->where('rate_type',$rv['rate_type'])->find()){
						//存在 直接修改
						Db::name('userRate')
						->where('rate_uid',$list['upgrade_uid'])
						->where('rate_type',$rv['rate_type'])
						->update(['rate_rate'=>$rv['rate_rate'],'rate_close_rate'=>$rv['rate_close_rate']]);
					}else{
						//不存在直接新增
						$retedata = array();
						$retedata['rate_uid'] 		 = $list['upgrade_uid'];
						$retedata['rate_rate'] 		 = $rv['rate_rate'];
						$retedata['rate_close_rate'] = $rv['rate_close_rate'];
						$retedata['rate_type'] 	     = $rv['rate_type'];
						$retedata['rate_time']       = time();
						Db::name('userRate')->insert($retedata);
					}
				}
				
				//押金升级启用 则该等级不进行三级分销
                
				//是否开启押金会员 DEPOSIT_MEMBER
				//押金会员等级的ID DEPOSIT_MEMBER_ID
				//判断是否开启押金 是制定的等级和制定的特推代理 
                if($config['DEPOSIT_MEMBER']==1 and $list['upgrade_type_id']== $config['DEPOSIT_MEMBER_ID'] and $config['DEPOSIT_AGENT_ID'] ==$user['user_agent_id'] ){
                 
                    $resulte = $this->Depositmember($list['upgrade_type_id'],$upgrade_money,$list['upgrade_uid'],$list['upgrade_pay_id'],$upgrade_form_no,$upgrade_sn);
                    return ['error'=>0,'msg'=>'null'];
                }
				 $UpgradetData = Db::name('PayUpgrade')->where('upgrade_form_no',$upgrade_form_no)->find();
				//判断是否特推代理
				if($user['user_pid'] !=0 ){
					
					$resulta = $this->Distribution($list['upgrade_uid'],$list['upgrade_money']);
					
					if(!$resulta){
						return ['error'=>1,'msg'=>'特推代理分润计算错误'];
					}
					$MoneyTagnt  = $upgrade_money  - $resulta;
					if($MoneyTagnt < 0){
						return ['error'=>1,'msg'=>'特推代理计算分润为负数'];
					}
					
					$user_agent_id = Db::name('User')->where(['user_id'=>$list['upgrade_uid'],'user_state'=>0])->value('user_agent_id');
               		$agentresult = $this->DistritAgent($user_agent_id,true,$MoneyTagnt,$UpgradetData);
					
				}else{
                	$user_agent_id = Db::name('User')->where(['user_id'=>$list['upgrade_uid'],'user_state'=>0])->value('user_agent_id');
               		$agentresult = $this->DistritAgent($user_agent_id,true,$upgrade_money,$UpgradetData);
                }
			
                if($res){
                    // 提交事务
                    Db::commit(); 
                    return ['error'=>0,'msg'=>'修改成功'];
                }

            }else{
                return ['error'=>1,'msg'=>'修改失败'];
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
           return ['error'=>1,'msg'=>'nills'];
        }
    }
    /**
     * 会员三级分销设置
     * @param [type] $user_id [上级ID]
     * @param [type] $money   [充值金额]
     */
    private function Distribution($user_id , $money){
		$money  = 0; 
        $config = require CACHE_PATH.'system.php';
        //判断是否开启分销 1 开启
        if($config['USER_ACTIVATION_CZ']==1 && $config['DEPOSIT_MEMBER']!=1)
        {  
            $users = Db::name('User')->where('user_id',$user_id)->find();
            if(empty($users))
            {   
                return false;
            }
            $res = getuserlevel($users['user_pid'],true);
        
            if(empty($res))
            {
                return false;
            }
         
            //判断是百分比还是固定值返 1百分比  0固定值
            if($config['USER_DISTRI_TYPE_CZ']==1)
            {  
                $result = array();
                foreach ($res as  $key => $val) {
                    if($key==0){
                        $result[] = bonuslog($val, $money*$config['USER_DISTRI_TYPE_CZ_'.($key+1)]/100, time(), 1, '会员升级'.($key+1).'级分润', 0, 0, 0);
                    }else{
                        $result[] = bonuslog($val, $money*$config['USER_DISTRI_TYPE_CZ_'.($key+1)]/100, time(), 1, '会员升级'.($key+1).'级分润', 1, 0, 0);
                    }
                    $money += $money*$config['USER_DISTRI_TYPE_CZ_'.($key+1)]/100;
                }
                if(count($res)==count($result)){
                    return $money;
                }else{
                    return false;
                }

            }else{
             
                $result = array();
                foreach ($res as  $key => $val) {
                    if($key==0){
                        $result[] = bonuslog($val, $config['USER_DISTRI_TYPE_CZ_'.($key+1)], time(), 1, '会员升级'.($key+1).'级分润', 0, 0, 0);
                    }else{

                        $result[] = bonuslog($val, $config['USER_DISTRI_TYPE_CZ_'.($key+1)], time(), 1, '会员升级'.($key+1).'级分润', 1, 0, 0);
                    }
                  
                     $money += $config['USER_DISTRI_TYPE_CZ_'.($key+1)];
                }
                if(count($res)==count($result)){
                    return $money;
                }else{
                    return false;
                }
            }
        }
    }

    /**
     * 查询上级
     * @param [type]  $where   [字段条件]
     * @param [type]  $whereid [查询条件]
     * @param [type]  $pid     [父级ID]
     * @param integer $level   [等级]
     */
    private function Treture($where,$whereid,$pid,$level=0){
        $userpid  = Db::name('User')->where($whereid,$pid)->find();

        if($level>=3){
            return false;
        }

        if($userpid[$where]==0){

            $userpid['level'] = $level+1;
            $userpid['son'] = '';
            return $userpid;
        }else{

            $userpid['level'] = $level;
            $userpid['son']   = $this->Treture($where,$whereid,$userpid[$where],$level+1);                

        }
        return $userpid;
    }

    /**
     * 押金升级
     * @param [type] $user_type_id    [升级类型ID]
     * @param [type] $upgrade_money   [升级金额]
     * @param [type] $user_id         [会员ID]
     * @param [type] $deposit_pay_id  [通道ID]
     * @param [type] $deposit_form_no [订单号]
     * @param [type] $deposit_sn      [上游订单号]
     */
     private function Depositmember($user_type_id,$upgrade_money,$user_id,$deposit_pay_id,$deposit_form_no,$deposit_sn){
        $config = require CACHE_PATH.'system.php';

         if($config['DEPOSIT_MEMBER'] == 1)
         {
             if($config['DEPOSIT_MEMBER_ID'] == $user_type_id)
             {
                 $data = array();
                 $data['deposit_money']   = $upgrade_money;
                 $data['deposit_user']    = $user_id;
                 $data['deposit_state']   = 1;
                 $data['deposit_pay_id']  = $deposit_pay_id;
                 $data['deposit_form_no'] = $deposit_form_no;
                 $data['deposit_sn']      = $deposit_sn;
                 $data['deposit_type_id'] = $config['DEPOSIT_MEMBER_ID'];
                 $data['deposit_oktime']  = time();
                 if(Db::name('Deposit')->insert($data))
                {   
                     $result = array();
                     $list = Db::name('Rate')->where('rate_type_id',$user_type_id)->select();
                     foreach ($list as $key => $va) {
                        if($va['rate_type']==1){
                            $result[] = Db::name('UserRate')->where(['rate_uid'=>$user_id,'rate_type'=>1])->update(['rate_rate'=>$va['rate_rate'],'rate_close_rate'=>$va['rate_close_rate']]);
                        }else{
                            $result[] = Db::name('UserRate')->where(['rate_uid'=>$user_id,'rate_type'=>2])->update(['rate_rate'=>$va['rate_rate'],'rate_close_rate'=>$va['rate_close_rate']]);
                        }
                        
                     }
                     if(count($list)==count($result)){
                         return true;
                     }else{
                         return false;
                     }
                    
                 }else{
                     return false;
                 }
             }
         }
    }
  	
	//分级结算代理商升级费率
	//2018年11月16日16:04:46
	//刘媛媛
    private function DistritAgent($agent_id, $fasle = false, $money,$upgrade){
		
        $config = require CACHE_PATH.'system.php';
      
		$list = getagentSups($agent_id,$fasle);
		$up_rate = 0;
      //  dump($list);
		
		foreach ($list as $key => $value) {
				
			$admin = Db::name('agent')->where(['agent_id'=>$value,'agent_state'=>0])->find();
			if(empty($admin)){
				continue;
			}
			//代理升级费率
			$agent_rate = Db::name('agent_rate')->where('rate_agent_id',$value)->where('rate_type',3)->value('rate_rate');
			//echo $agent_rate;
			if(empty($agent_rate)){
				continue;
			}
			
			if($key==0){
             
				$rate    = $agent_rate;
				$up_rate = $agent_rate;
             //  echo 'key:'.$key.'当前费率是'.$agent_rate.'计算以后是'.$up_rate.'<br/>';
			}else{
				$rate    =  $agent_rate - $up_rate;
				$up_rate = $agent_rate;
               // echo 'key:'.$key.'当前费率是'.$agent_rate.'计算以后是'.$rate.'<br/>';
			}
			if($rate<= 0){
				$rate = 0;
				continue;
			}
			//代理分润
			$profit = $rate * $money;
			$profit_money = substr_num($profit);
		
			//增加代理分润
			$date = array();
			$date['profit_uid']		   = $upgrade['upgrade_uid'];
			$date['profit_agent_id']   = $value;
			$date['profit_orderid']	   = $upgrade['upgrade_id'];
			$date['profit_form_no']    = $upgrade['upgrade_form_no'];
			$data['profit_amount']	   = $upgrade['upgrade_money'];
			$date['profit_money']      = $profit_money;
			$date['profit_state']      = 1;
			$date['profit_type']       = 4;
			$date['profit_time']       = time();
			$date['profit_pay']        = 0;
          
			Db::name('AgentProfit')->insert($date);
		}
		return true;
    }
    /**
     * 会员激活回调
     * @param [type] $upgrade_form_no [支付订单号]
     * @param [type] $act_sn          [第三方订单号]
     * @param [type] $act_money       [支付金额]
     */
    public function SjNotify($act_form_no,$act_sn,$act_money){

        $list = Db::name('UserActivation')->where(['act_form_no'=>$act_form_no,'cat_state'=>0])->find();
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'查无此订单或订单已支付完成']);
        }

        if($list['act_money'] != $act_money)
        {
            return json(['error'=>1,'msg'=>'订单金额不一致']);
        }
        
        Db::startTrans();
        try{
            
            $data = array();
            $data['act_sn']    = $act_sn;
            $data['cat_state'] = 1;
            $data['act_oktime']= time();
            $result = Db::name('UserActivation')->where('act_form_no',$act_form_no)->update($data);
         
            if($result)
            {
                $res = Db::name('User')->where('user_id',$list['act_uid'])->update(['user_isactivation'=>0]);
               
                if($res)
                {
                    // 提交事务
                    Db::commit(); 
                    return json(['error'=>0,'msg'=>'修改成功']);
                }

            }else{
                return json(['error'=>1,'msg'=>'修改失败']);
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }
}