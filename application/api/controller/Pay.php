<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;

class Pay extends Controller
{
    /**
     * 会员升级回调
     */
    public function Notifys($upgrade_form_no,$upgrade_sn,$upgrade_money){

        $list = Db::name('PayUpgrade')->where(['upgrade_form_no'=>$upgrade_form_no,'upgrade_state'=>0])->find();
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'查无此订单或订单已支付完成']);
        }

        if($list['upgrade_money'] != $upgrade_money)
        {
            return json(['error'=>1,'msg'=>'订单金额不一致']);
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
            {
                $res = Db::name('User')->where('user_id',$list['upgrade_uid'])->update(['user_type_id'=>$list['upgrade_type_id']]);
                //押金升级启用 则该等级不进行三级分销
                // echo $list['upgrade_type_id'];die;
                if($config['DEPOSIT_MEMBER']==1 and $list['upgrade_type_id']== $config['DEPOSIT_MEMBER_ID']){
                    
                    $resulte = $this->Depositmember($list['upgrade_type_id'],$upgrade_money,$list['upgrade_uid'],$list['upgrade_pay_id'],$upgrade_form_no,$upgrade_sn);
                    
                }else{
                
                    $resulta = $this->Distribution($list['upgrade_uid'],$list['upgrade_money']);
                     // dump($resulta);die;
                }
                //代理分销 1开启
                if($config['AGENT_ACTIVATION_CZ'] == 1)
                {	
                    $user_agent_id = Db::name('User')->where(['user_id'=>$list['upgrade_uid'],'user_state'=>0])->value('user_agent_id');
                    $agentresult = $this->DistritAgent($user_agent_id,false,$upgrade_money);
					

                }
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


    /**
     * 会员三级分销设置
     * @param [type] $user_id [上级ID]
     * @param [type] $money   [充值金额]
     */
    private function Distribution($user_id , $money){
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
                    
                }
                if(count($res)==count($result)){
                    return true;
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
                    
                }
                if(count($res)==count($result)){
                    return true;
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

    /**
     * 代理商分销结算
     * @param [type] $agent_id [代理商ID]
     * @param [type] $fasle    [状态 默认false]
     * @param [type] $money    [待分润金额]
     */
    // private function DistritAgent($agent_id, $fasle = fasle, $money){
    //     $config = require CACHE_PATH.'system.php';

    //     if($config['AGENT_ACTIVATION_CZ'] == 1)
    //     {   

    //         $list = getagentSups($agent_id,$fasle);
    //         $agentuser = Db::name('Agent')->where('agent_id',$agent_id)->find();
    //         array_unshift($list,$agentuser['agent_id']);
    //         if(empty($list)){
    //             return false;
    //         }
			
    //         //根据后台设置等级计算
    //         if($config['AGENT_DISTRI_TYPE_CZ_1'] != 0)
    //         {
    //             $agent_id_list = array_slice($list,0,$config['AGENT_DISTRI_TYPE_CZ_1']);

    //             $agent_rate = array();
    //             foreach ($agent_id_list as $key => $val) {
    //                 $agent_rate[] = Db::name('agent_rate')->field('rate_agent_id,rate_rate')->where(['rate_agent_id'=>$val,'rate_type'=>3])->find();
    //             }
    //             $fagent_rate = array_reverse($agent_rate);
    //             if(empty($fagent_rate)){
    //                 return false;
    //             }
    //             $end = end($fagent_rate);
    //             Db::startTrans();
    //             try{
    //                 //第一级代理分销结算
    //                 $date = array();
    //                 $date['profit_agent_id']   = $end['rate_agent_id'];
    //                 $date['profit_form_no']    = $end['rate_agent_id'].rand(1000,9999).time();
    //                 $date['profit_money']      = sprintf("%.2f",$money*$end['rate_rate']);
    //                 $date['profit_rate']       = $end['rate_rate'];
    //                 $date['profit_agent_rate'] = $end['rate_rate'];
    //                 $date['profit_state']      = 1;
    //                 $date['profit_type']       = 4;
    //                 $date['profit_time']       = time();
    //                 $date['profit_pay']        = 0;
    //                 $res = Db::name('AgentProfit')->insert($date);
    //                 if($res){
    //                     $result = array();
    //                     foreach ($fagent_rate as $key => $v) {
    //                         if($key>0){
    //                             $rate = $fagent_rate[$key-1]['rate_rate']- $v['rate_rate'];
    //                             if($rate >= 0){
    //                                 $data = array();
    //                                 $data['profit_agent_id']   = $fagent_rate[$key-1]['rate_agent_id'];
    //                                 $data['profit_form_no']    = $fagent_rate[$key-1]['rate_agent_id'].rand(1000,9999).time();
    //                                 $data['profit_money']      = sprintf("%.2f",$money*$rate);
    //                                 $data['profit_rate']       = $rate;
    //                                 $data['profit_agent_rate'] = $fagent_rate[$key-1]['rate_rate'];
    //                                 $data['profit_state']      = 1;
    //                                 $data['profit_type']       = 4;
    //                                 $data['profit_time']       = time();
    //                                 $data['profit_pay']        = 0;
    //                                 $result[] = Db::name('AgentProfit')->insert($data);                                    
    //                             }

    //                         }

    //                     }
    //                     if(count($result) == count($fagent_rate)-1){
    //                         Db::commit();
    //                         return true;
    //                     }                        
    //                 }

    //             } catch (\Exception $e) {
    //                 // 回滚事务
    //                 Db::rollback();
    //                 return false;
    //             }

    //         }else{
    //             //代理无限分销计算
    //             $agent_rate = array();
    //             foreach ($list as $key => $val) {
    //                 $agent_rate[] = Db::name('agent_rate')->field('rate_agent_id,rate_rate')->where(['rate_agent_id'=>$val,'rate_type'=>3])->find();
    //             }
    //             $fagent_rate = array_reverse($agent_rate);

    //             if(empty($fagent_rate)){
    //                 return false;
    //             }
				
    //             $end = end($fagent_rate);
    //             Db::startTrans();
    //             try{
    //                 //第一级代理分销结算
    //                 $date = array();
    //                 $date['profit_agent_id']   = $end['rate_agent_id'];
    //                 $date['profit_form_no']    = $end['rate_agent_id'].rand(1000,9999).time();
    //                 $date['profit_money']      = sprintf("%.2f",$money*$end['rate_rate']);
    //                 $date['profit_rate']       = $end['rate_rate'];
    //                 $date['profit_agent_rate'] = $end['rate_rate'];
    //                 $date['profit_state']      = 1;
    //                 $date['profit_type']       = 4;
    //                 $date['profit_time']       = time();
    //                 $date['profit_pay']        = 0;
    //                 $res = Db::name('AgentProfit')->insert($date);
					
    //                 if($res){
						
    //                     $result = array();
    //                     foreach ($fagent_rate as $key => $v) {
    //                         if($key>0){
    //                             $rate = $fagent_rate[$key-1]['rate_rate']-$v['rate_rate'];
								
    //                             if($rate >= 0){
    //                                 $data = array();
    //                                 $data['profit_agent_id']   = $fagent_rate[$key-1]['rate_agent_id'];
    //                                 $data['profit_form_no']    = $fagent_rate[$key-1]['rate_agent_id'].rand(1000,9999).time();
    //                                 $data['profit_money']      = sprintf("%.2f",$money*$rate);
    //                                 $data['profit_rate']       = $rate;
    //                                 $data['profit_agent_rate'] = $fagent_rate[$key-1]['rate_rate'];
    //                                 $data['profit_state']      = 1;
    //                                 $data['profit_type']       = 4;
    //                                 $data['profit_time']       = time();
    //                                 $data['profit_pay']        = 0;
								
    //                                 $result[] = Db::name('AgentProfit')->insert($data);     
									
    //                             }

    //                         }

    //                     }
						
    //                     if(count($result) == count($fagent_rate)-1){
    //                         Db::commit();
    //                         return true;
    //                     }                        
    //                 }

    //             } catch (\Exception $e) {
    //                 // 回滚事务
    //                 Db::rollback();
    //                 return false;
    //             }
    //         }
    //     }
    // }
    public function DistritAgent($agent_id, $fasle = false, $money){
        $config = require CACHE_PATH.'system.php';

        if($config['AGENT_ACTIVATION_CZ'] == 1)
        {   

            $list = getagentSups($agent_id,$fasle);
            $agentuser = Db::name('Agent')->where('agent_id',$agent_id)->find();
            array_unshift($list,$agentuser['agent_id']);
            if(empty($list)){
                return false;
            }
            $agent_id_list = array_slice($list,0,3);

            Db::startTrans();
            try{
                $result = array();
                //百分比分润
                if($config['AGENT_DISTRI_TYPE_CZ']==1){
                    $count =0;
                    foreach ($agent_id_list as $key => $v) {
                        if($config['AGENT_DISTRI_TYPE_CZ_'.($key+1)] > 0){
                            $data = array();
                            $data['profit_agent_id']   = $agent_id_list[$key];
                            $data['profit_form_no']    = $agent_id_list[$key].rand(1000,9999).time();
                            $data['profit_money']      = sprintf("%.2f",$money*$config['AGENT_DISTRI_TYPE_CZ_'.($key+1)]);
                            $data['profit_rate']       = $config['AGENT_DISTRI_TYPE_CZ_'.($key+1)];
                            $data['profit_agent_rate'] = $config['AGENT_DISTRI_TYPE_CZ_'.($key+1)];
                            $data['profit_state']      = 1;
                            $data['profit_type']       = 4;
                            $data['profit_time']       = time();
                            $data['profit_pay']        = 0;
                            $result[] = Db::name('AgentProfit')->insert($data);                                    
                        }else{

                            $count = count($agent_id_list)-1;
                        }
                    }
                }else{
                    //固定分润金额
                    $count =0;
                    foreach ($agent_id_list as $key => $v) {
                        if($config['AGENT_DISTRI_TYPE_CZ_'.($key+1)] > 0){
                            $data = array();
                            $data['profit_agent_id']   = $agent_id_list[$key];
                            $data['profit_form_no']    = $agent_id_list[$key].rand(1000,9999).time();
                            $data['profit_money']      = sprintf("%.2f",$config['AGENT_DISTRI_TYPE_CZ_'.($key+1)]);
                            $data['profit_rate']       = $config['AGENT_DISTRI_TYPE_CZ_'.($key+1)];
                            $data['profit_agent_rate'] = $config['AGENT_DISTRI_TYPE_CZ_'.($key+1)];
                            $data['profit_state']      = 1;
                            $data['profit_type']       = 4;
                            $data['profit_time']       = time();
                            $data['profit_pay']        = 0;
                            $result[] = Db::name('AgentProfit')->insert($data);                                    
                        }else{

                            $count = count($agent_id_list)-1;
                        }
                    }
                }
                if(count($result) == $count){
                    Db::commit();
                    return true;
                }                        


            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return false;
            }

            // }
           
        }

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
        $config = require CACHE_PATH.'system.php';
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


    public function ReturnDivide(){
        $config = require CACHE_PATH.'system.php';

        if($config['RETURN_AND_DIVIDE']==1){
            
        }
    }
}