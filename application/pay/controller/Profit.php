<?php
namespace app\pay\controller;
use think\Controller;
use think\Db;

class Profit extends controller
{
    public function __construct(){
        parent::__construct();
        $this->config = require CACHE_PATH.'system.php';
    }
    /**
     * 修改分润
     * @Author tw
     * @return [type] [description]
     */
    public function profit_up()
    {
        exit();
        $list = Db::name('agent_profit')->whereIn('profit_type',['1,2'])->where('profit_pay',0)->select();
        foreach ($list as $key => $value) {
            $agent_rate = Db::name('agent_rate')->where('rate_agent_id',$value['profit_agent_id'])->where('rate_type',1)->value('rate_rate');
            if($value['profit_agent_id']!='-1')
            {
                //代理分润
                /*$agent_rate = $agent_rate / 100;
                $rate = $value['profit_user_rate'] - $agent_rate;
                if($rate<= 0)
                {
                    $rate = 0;
                }
                $profit = $rate * $value['profit_amount'];*/
            }
            else
            {
                //系统分润
                /*if($value['profit_type']==1)
                {
                    $orderInfo = Db::name('plan')->where(['plan_form_no'=>$value['profit_form_no']])->find();

                    if(empty($orderInfo['plan_pay_id']))
                    {
                        $mission = Db::name('mission')->where('mission_id',$orderInfo['plan_mid'])->find();
                        $orderInfo['plan_pay_id'] = $mission['mission_pay_id'];
                        Db::name('plan')->where('plan_id',$orderInfo['plan_id'])->update(['plan_pay_id'=>$orderInfo['plan_pay_id']]);

                    }

                    $payment = Db::name('payment')->where('payment_id',$orderInfo['plan_pay_id'])->find();

                    $close_rate =1;//结算费率



                    $user_profit_money = Db::name('trading')->where('trading_orderid',$value['profit_orderid'])->where('trading_type',1)->sum('trading_money');
                    $agent_profit_money = Db::name('agent_profit')->where('profit_orderid',$value['profit_orderid'])->where('profit_state',1)->whereNotIn('profit_agent_id','-1')->where('profit_type',1)->sum('profit_money');


                }
                elseif($value['profit_type']==2)
                {
                    $orderInfo = Db::name('pay_records')->where(['records_form_no'=>$value['profit_form_no']])->find();
                    $payment = Db::name('payment')->where('payment_id',$orderInfo['records_pay_id'])->find();
                    $close_rate =2;//结算费率

                    $user_profit_money = Db::name('trading')->where('trading_orderid',$value['profit_orderid'])->where('trading_type',2)->sum('trading_money');
                    $agent_profit_money = Db::name('agent_profit')->where('profit_orderid',$value['profit_orderid'])->where('profit_state',1)->whereNotIn('profit_agent_id','-1')->where('profit_type',2)->sum('profit_money');
                }
                $payment_rate = $payment['payment_rate'];//通道费率
                $payment_close_fee = $payment['payment_close_fee'];//通道结算费率

                //系统分润
                $agent_rate = $payment_rate;
                $high_rate = $value['profit_user_rate'] - $payment_rate;
                $rate = $high_rate;
                $profit = $high_rate * $value['profit_amount'] - $user_profit_money - $agent_profit_money + $close_rate - $payment_close_fee;

                $profit_money = substr_num($profit);*/

            }
            $profit_money = substr_num($profit);
            if($profit_money<= 0)
            {
                $profit_money = 0;
            }
            $data['profit_money'] = $profit_money;
            $data['profit_rate'] = $rate;
            $data['profit_agent_rate'] = $agent_rate;

            Db::name('agent_profit')->where('profit_id',$value['profit_id'])->update($data);
            dump($value);
        }
            

    }
    /**
     * 还款分润
     * @Author tw
     * @Date   2018-09-14
     * @return [type]     [description]
     */
    public function repayment($form_no='')
    {
        if(empty($form_no))
        {
            return ['error'=>1,'msg'=>'订单错误'];
        }
        $orderInfo = Db::name('plan')->where(['plan_form_no'=>$form_no])->find();
        if($orderInfo['plan_type'] != 1 || $orderInfo['plan_state'] != 1)
        {
            return ['error'=>1,'msg'=>'单号不存在'];
        }
        $money = $orderInfo['plan_money'];

        //获取用户信息
        $user = Db::name('user')->where(['user_id'=>$orderInfo['plan_uid']])->find();
        if(empty($user))
        {
            return ['error'=>1,'msg'=>'用户信息错误'];
        }

        //获取用户费率
        $mission = Db::name('mission')->where('mission_id',$orderInfo['plan_mid'])->find();
        $user_rate = $mission['mission_rate'];//用户费率
        $close_rate = $mission['mission_close_rate'];//结算费率

        if(empty($orderInfo['plan_pay_id']))
        {
            $orderInfo['plan_pay_id'] = $mission['mission_pay_id'];
        }
        $payment = Db::name('payment')->where('payment_id',$orderInfo['plan_pay_id'])->find();
        $payment_rate = $payment['payment_rate'];//通道费率
        $payment_close_fee = $payment['payment_close_fee'];//通道结算费率
        $high_rate = $user_rate - $payment_rate;//高签费用
        $agent_id = $user['user_agent_id'];
        $admin_ids = getagentSups($agent_id);
        $up_rate = 0;//上级费率
        
        foreach ($admin_ids as $key => $value) {
            $admin = Db::name('agent')->where(['agent_id'=>$value,'agent_state'=>0])->find();
            
            if(empty($admin))
            {
                continue;
            }


            $agent_rate = Db::name('agent_rate')->where('rate_agent_id',$value)->where('rate_type',1)->value('rate_rate');
            //自定义费率或者代理等级
            if($admin['agent_grade']>0 && empty($agent_rate))
            {

                $agent_rate = Db::name('agent_grade')->where('grade_id',$admin['agent_grade'])->where('grade_state',0)->value('grade_rate');
            }
            $agent_rate = $agent_rate / 100;
            if($key==0)
            {
                $one_agent_rate = $agent_rate;
            }
            if(empty($agent_rate))
            {
                continue;
            }
            if($user_rate < $agent_rate)
            {
                $rate = 0;
                $up_rate = $user_rate;
            }
            elseif($key==0)
            {
                $rate = $user_rate - $agent_rate;
                $up_rate = $agent_rate;
            }
            else
            {
                $rate = $up_rate - $agent_rate;
                $up_rate = $agent_rate;
            }
            if($rate<= 0)
            {
                $rate = 0;
                continue;
            }

            //代理分润
            $profit = $rate * $money;
            $profit_money = substr_num($profit);
            if($profit_money<= 0)
            {
                $profit_money = 0;
            }
            $agency_profit = Db::name('agent_profit')
                            ->where('profit_orderid',$orderInfo['plan_id'])
                            ->where('profit_form_no',$form_no)
                            ->where('profit_agent_id',$value)
                            ->where('profit_state','1')
                            ->where('profit_type','1')
                            ->find();

            if ($profit_money>0 && empty($agency_profit)) {
                unset($data);
                $data['profit_uid']=$user['user_id'];
                $data['profit_agent_id']=$value;
                $data['profit_orderid']=$orderInfo['plan_id'];
                $data['profit_form_no']=$form_no;
                $data['profit_amount']=$orderInfo['plan_money'];
                $data['profit_money']=$profit_money;
                $data['profit_rate']=$rate;
                $data['profit_user_rate']=$user_rate;
                $data['profit_agent_rate']=$agent_rate;
                $data['profit_state']=1;
                $data['profit_type']=1;
                $data['profit_pay']=0;
                $data['profit_time'] = time();
                Db::name('agent_profit')->insert($data);
            }
        }
        //会员分销
        if($this->config['USER_DISTRI']==1)
        {
            
            //自己分润 //首次还款
            $profit = $this->config['POINTS_REPAYMENT'];
            if(!empty($profit) && $profit>0)
            {
                $profit_money = substr_num($profit);
                if($profit_money<= 0)
                {
                    $profit_money = 0;
                }
                $user_profit = Db::name('trading')
                        ->where('trading_orderid',$orderInfo['plan_id'])
                        ->where('trading_form_no',$form_no)
                        ->where('trading_uid',$orderInfo['plan_uid'])
                        ->where('trading_type','21')
                        ->count();

                if($profit_money>0 && empty($user_profit))
                {
                    // Db::name('user')->where('user_id',$orderInfo['plan_uid'])->setInc('user_moeny',$profit_money);
                    unset($data);
                    $data['trading_uid']=$orderInfo['plan_uid'];
                    $data['trading_title']='还款分润';
                    $data['trading_type']=21;
                    $data['trading_orderid']=$orderInfo['plan_id'];
                    $data['trading_form_no']=$form_no;
                    $data['trading_money']=$profit_money;
                    $data['trading_time'] = time();
                    Db::name('trading')->insert($data);
                    //分润记录
                    bonuslog($orderInfo['plan_uid'],$profit_money,$data['trading_time'],3,$data['trading_title'],0,0,0);
                }
            }
            //会员分润模式 模式一 值 0 模式二 值1
            $USER_DISTRI_MODE = $this->config['USER_DISTRI_MODE'];
            if($USER_DISTRI_MODE==0)
            {
                //模式一 三级分销 取等级值* 金额
                $user_ids = getuserlevel($user['user_pid']);
                foreach ($user_ids as $k => $uid) {
                    $profit = $this->config['USER_DISTRI_TYPE_HK_'.($k+1)];
                    
                    if($profit<= 0 || empty($profit))
                    {
                        continue;
                    }
                    if($this->config['USER_DISTRI_TYPE']==1)
                    {
                        //百分百分润
                        $profit = $profit / 100 * $money;
                    }
                    
                    $profit_money = substr_num($profit);
                    if($profit_money<= 0)
                    {
                        $profit_money = 0;
                    }
    
                    $user_profit = Db::name('trading')
                            ->where('trading_orderid',$orderInfo['plan_id'])
                            ->where('trading_form_no',$form_no)
                            ->where('trading_uid',$uid)
                            ->where('trading_type','1')
                            ->find();
    
                    if($profit_money>0 && empty($user_profit))
                    {
                        // Db::name('user')->where('user_id',$uid)->setInc('user_moeny',$profit_money);
                        unset($data);
                        $data['trading_uid']=$uid;
                        $data['trading_title']='还款分润';
                        $data['trading_type']=1;
                        $data['trading_orderid']=$orderInfo['plan_id'];
                        $data['trading_form_no']=$form_no;
                        $data['trading_money']=$profit_money;
                        $data['trading_time'] = time();
                        Db::name('trading')->insert($data);
                        //分润记录
                        if($k==0)
                        {
                            $style = 0;
                        }
                        else
                        {
                            $style = 1;
                        }
                        bonuslog($uid,$profit_money,$data['trading_time'],3,$data['trading_title'],$style,$orderInfo['plan_uid'],0);
                    }
                }
            }
            elseif($USER_DISTRI_MODE==1)
            {
                $user_ids = getuserlevel($user['user_pid']);
                if(empty($one_agent_rate) ||  $one_agent_rate<= 0)
                {
                    $one_agent_rate = $payment_rate;
                }
                $high_profit_money = ($user_rate - $one_agent_rate) * $money; //高签基础费率
                foreach ($user_ids as $k => $uid) {
                    $user_type_id = getUser($uid,'user_type_id');
                    $user_type = get_user_type($user_type_id);
                    // $profit = $this->config['USER_DISTRI_MODE_HK_V_1'.($k+1)];
                    if($user_type['type_profit']==0)
                    {
                        continue;
                    }
                    elseif($user_type['type_profit']==1)
                    {
                        
                        if($this->config['USER_DISTRI_TYPE']==1)
                        {
                            $profit = $high_profit_money * $this->config['USER_DISTRI_MODE_HK_'.($k+1)]/100;
                        }
                        else
                        {
                            $profit = $this->config['USER_DISTRI_MODE_HK_'.($k+1)];
                        }
                        
                    }
                    elseif($user_type['type_profit']==2)
                    {
                        if($this->config['USER_DISTRI_TYPE']==1)
                        {
                            $profit = $high_profit_money * $this->config['USER_DISTRI_MODE_HK_V_'.($k+1)]/100;
                        }
                        else
                        {
                            $profit = $this->config['USER_DISTRI_MODE_HK_V_'.($k+1)];
                        }
                    }
                    if($profit<= 0 || empty($profit))
                    {
                        continue;
                    }
                    $profit_money = substr_num($profit);
                    if($profit_money<= 0)
                    {
                        $profit_money = 0;
                    }
                    $user_profit = Db::name('trading')
                                ->where('trading_orderid',$orderInfo['plan_id'])
                                ->where('trading_form_no',$form_no)
                                ->where('trading_uid',$uid)
                                ->where('trading_type','1')
                                ->find();
        
                    if($profit_money>0 && empty($user_profit))
                    {
                        unset($data);
                        $data['trading_uid']=$uid;
                        $data['trading_title']='还款分润';
                        $data['trading_type']=1;
                        $data['trading_orderid']=$orderInfo['plan_id'];
                        $data['trading_form_no']=$form_no;
                        $data['trading_money']=$profit_money;
                        $data['trading_time'] = time();
                        Db::name('trading')->insert($data);
                        
                        //分润记录
                        if($k==0)
                        {
                            $style = 0;
                        }
                        else
                        {
                            $style = 1;
                        }
                        bonuslog($uid,$profit_money,$data['trading_time'],3,$data['trading_title'],$style,$orderInfo['plan_uid'],0);
                    }
                }

                
            }
            // elseif($USER_DISTRI_MODE==1)
            // {
            //     $uid = $user['user_pid'];
            //     $user_type_id = getUser($uid,'user_type_id');
            //     if(empty($one_agent_rate) ||  $one_agent_rate<= 0)
            //     {
            //         $one_agent_rate = $payment_rate;
            //     }
            //     $profit = ($user_rate - $one_agent_rate) * $money;
            //     if($user_type_id>1)
            //     {
            //         if($this->config['USER_DISTRI_TYPE']==1)
            //         {
            //             $profit = $profit * $this->config['USER_DISTRI_MODE_HK_V_1']/100;
            //         }
            //         else
            //         {
            //             $profit = $this->config['USER_DISTRI_MODE_HK_V_1'];
            //         }
                    
            //     }
            //     else
            //     {
            //         if($this->config['USER_DISTRI_TYPE']==1)
            //         {
            //             $profit = $profit * $this->config['USER_DISTRI_MODE_HK_1']/100;
            //         }
            //         else
            //         {
            //             $profit = $this->config['USER_DISTRI_MODE_HK_1'];
            //         }
            //     }
            //     $profit_money = substr_num($profit);
            //     $user_profit = Db::name('trading')
            //                 ->where('trading_orderid',$orderInfo['plan_id'])
            //                 ->where('trading_form_no',$form_no)
            //                 ->where('trading_uid',$uid)
            //                 ->where('trading_type','1')
            //                 ->find();
    
            //     if($profit_money>0 && empty($user_profit))
            //     {
            //         // Db::name('user')->where('user_id',$uid)->setInc('user_moeny',$profit_money);
            //         unset($data);
            //         $data['trading_uid']=$uid;
            //         $data['trading_title']='还款分润';
            //         $data['trading_type']=1;
            //         $data['trading_orderid']=$orderInfo['plan_id'];
            //         $data['trading_form_no']=$form_no;
            //         $data['trading_money']=$profit_money;
            //         $data['trading_time'] = time();
            //         Db::name('trading')->insert($data);
            //         bonuslog($uid,$profit_money,$data['trading_time'],3,$data['trading_title'],0,$orderInfo['plan_uid'],0);
            //     }
                
            // }
            

        }
        $user_profit_money = Db::name('trading')->where('trading_orderid',$orderInfo['plan_id'])->whereIn('trading_type',[1,21])->sum('trading_money');
        $agent_profit_money = Db::name('agent_profit')->where('profit_orderid',$orderInfo['plan_id'])->where('profit_state',1)->where('profit_type',1)->sum('profit_money');
        //系统分润
        $agent_id = '-1';
        $agent_rate = $payment_rate;
        $rate = $high_rate;
        $profit = $high_rate * $money - $user_profit_money - $agent_profit_money + $close_rate - $payment_close_fee;

        $profit_money = substr_num($profit);
        if($profit_money<= 0)
        {
            $profit_money = 0;
        }
        $agency_profit = Db::name('agent_profit')
                            ->where('profit_orderid',$orderInfo['plan_id'])
                            ->where('profit_form_no',$form_no)
                            ->where('profit_agent_id',$agent_id)
                            ->where('profit_state','1')
                            ->find();
        if ($profit_money > 0 && empty($agency_profit)) {
            unset($data);
            $data['profit_uid']=$user['user_id'];
            $data['profit_agent_id']=$agent_id;
            $data['profit_orderid']=$orderInfo['plan_id'];
            $data['profit_form_no']=$form_no;
            $data['profit_amount']=$orderInfo['plan_money'];
            $data['profit_money']=$profit_money;
            $data['profit_rate']=$rate;
            $data['profit_user_rate']=$user_rate;
            $data['profit_agent_rate']=$agent_rate;
            $data['profit_state']=1;
            $data['profit_type']=1;
            $data['profit_pay']=0;
            $data['profit_time'] = time();
            Db::name('agent_profit')->insert($data);
        }
    }

    /**
     * 收款分润
     * @Author tw
     * @Date   2018-09-25
     * @return [type]     [description]
     */
    public function payrecords($form_no='')
    {
        if(empty($form_no))
        {
            return ['error'=>1,'msg'=>'订单错误'];
        }

        $orderInfo = Db::name('pay_records')->where(['records_form_no'=>$form_no])->find();
        if($orderInfo['records_state'] != 1)
        {
            return ['error'=>1,'msg'=>'订单错误'];
        }

        $money = $orderInfo['records_money'];//订单金额
        $user_rate = $orderInfo['records_rate'];//用户费率
        $close_rate = $orderInfo['records_close_rate'];//用户结算费率

        $payment = Db::name('payment')->where('payment_id',$orderInfo['records_pay_id'])->find();
        $payment_rate = $payment['payment_rate'];//通道费率
        $payment_close_fee = $payment['records_pay_id'];//通道结算费率

        //获取用户信息
        $user = Db::name('user')->where(['user_id'=>$orderInfo['records_uid']])->find();
        
        if(empty($user))
        {
            return ['error'=>1,'msg'=>'用户信息错误'];
        }

        $agent_id = $user['user_agent_id'];
        $admin_ids = getagentSups($agent_id);
        $up_rate = 0;//上级费率
        foreach ($admin_ids as $key => $value) {
            $admin = Db::name('agent')->where(['agent_id'=>$value,'agent_state'=>0])->find();
            if(empty($admin))
            {
                continue;
            }

            $agent_rate = Db::name('agent_rate')->where('rate_agent_id',$value)->where('rate_type',2)->value('rate_rate');
            //自定义费率或者代理等级
            if($admin['agent_grade']>0 && empty($agent_rate))
            {

                $agent_rate = Db::name('agent_grade')->where('grade_id',$admin['agent_grade'])->where('grade_state',0)->value('grade_rate_close');
            }
            $agent_rate = $agent_rate / 100;
            if(empty($agent_rate))
            {
                continue;
            }
            if($user_rate < $agent_rate)
            {
                $rate = 0;
                $up_rate = $user_rate;
            }
            elseif($key==0)
            {
                $rate = $user_rate - $agent_rate;
                $up_rate = $agent_rate;
            }
            else
            {
                $rate = $up_rate - $agent_rate;
                $up_rate = $agent_rate;
            }
            if($rate<= 0)
            {
                $rate = 0;
                continue;
            }
            //代理分润
            $profit = $rate * $money;
            $profit_money = substr_num($profit);
            if($profit_money < 0)
            {
                $profit_money = 0;
            }

            $agency_profit = Db::name('agent_profit')
                            ->where('profit_orderid',$orderInfo['records_id'])
                            ->where('profit_form_no',$form_no)
                            ->where('profit_agent_id',$value)
                            ->where('profit_state','1')
                            ->where('profit_type','2')
                            ->find();
            if (empty($agency_profit)) {
                $data['profit_uid']=$user['user_id'];
                $data['profit_agent_id']=$value;
                $data['profit_orderid']=$orderInfo['records_id'];
                $data['profit_form_no']=$form_no;
                $data['profit_amount']=$orderInfo['records_money'];
                $data['profit_money']=$profit_money;
                $data['profit_rate']=$rate;
                $data['profit_user_rate']=$user_rate;
                $data['profit_agent_rate']=$agent_rate;
                $data['profit_state']=1;
                $data['profit_type']=2;
                $data['profit_pay']=0;
                $data['profit_time'] = time();
                Db::name('agent_profit')->insert($data);
            }
        }

        //会员分销
        if($this->config['USER_DISTRI']==1)
        {
            //自己分润 //首次收款
            $profit = $this->config['POINTS_RECEIVABLES'];
            if(!empty($profit) && $profit>0)
            {
                $profit_money = substr_num($profit);
                if($profit_money<= 0)
                {
                    $profit_money = 0;
                }
                $user_profit = Db::name('trading')
                        ->where('trading_orderid',$orderInfo['records_id'])
                        ->where('trading_form_no',$form_no)
                        ->where('trading_uid',$orderInfo['records_uid'])
                        ->where('trading_type','22')
                        ->count();

                if($profit_money>0 && empty($user_profit))
                {
                    // Db::name('user')->where('user_id',$orderInfo['records_uid'])->setInc('user_moeny',$profit_money);
                    unset($data);
                    $data['trading_uid']=$orderInfo['records_uid'];
                    $data['trading_title']='收款分润';
                    $data['trading_type']=22;
                    $data['trading_orderid']=$orderInfo['records_id'];
                    $data['trading_form_no']=$form_no;
                    $data['trading_money']=$profit_money;
                    $data['trading_time'] = time();
                    Db::name('trading')->insert($data);
                    //分润记录
                    bonuslog($orderInfo['records_uid'],$profit_money,$data['trading_time'],2,$data['trading_title'],0,0,0);
                }
            }

            //会员分润模式 模式一 值 0 模式二 值1
            $USER_DISTRI_MODE = $this->config['USER_DISTRI_MODE'];
            if($USER_DISTRI_MODE==0)
            {
                //上级分润
                $user_ids = getuserlevel($user['user_pid']);
                foreach ($user_ids as $k => $uid) {
                    $profit = $this->config['USER_DISTRI_TYPE_'.($k+1)];
                    if($this->config['USER_DISTRI_TYPE']==1)
                    {
                        //百分百分润
                        $profit = $profit / 100 * $money;
                    }
                    $profit_money = substr_num($profit);
                    if($profit_money<= 0)
                    {
                        $profit_money = 0;
                    }

                    $user_profit = Db::name('trading')
                            ->where('trading_orderid',$orderInfo['records_id'])
                            ->where('trading_form_no',$form_no)
                            ->where('trading_uid',$uid)
                            ->where('trading_type','2')
                            ->find();

                    if($profit_money>0 && empty($user_profit))
                    {
                        // Db::name('user')->where('user_id',$uid)->setInc('user_moeny',$profit_money);
                        unset($data);
                        $data['trading_uid']=$uid;
                        $data['trading_title']='收款分润';
                        $data['trading_type']=2;
                        $data['trading_orderid']=$orderInfo['records_id'];
                        $data['trading_form_no']=$form_no;
                        $data['trading_money']=$profit_money;
                        $data['trading_time'] = time();
                        Db::name('trading')->insert($data);

                        //分润记录
                        if($k==0)
                        {
                            $style = 0;
                        }
                        else
                        {
                            $style = 1;
                        }
                        bonuslog($uid,$profit_money,$data['trading_time'],2,$data['trading_title'],$style,$orderInfo['records_uid'],0);
                    }
                }
            }
            elseif($USER_DISTRI_MODE==1)
            {
                $user_ids = getuserlevel($user['user_pid']);
                if(empty($one_agent_rate) ||  $one_agent_rate<= 0)
                {
                    $one_agent_rate = $payment_rate;
                }
                $high_profit_money = ($user_rate - $one_agent_rate) * $money; //高签基础费率*金额
                foreach ($user_ids as $k => $uid) {
                    
                    $user_type_id = getUser($uid,'user_type_id');
                    $user_type = get_user_type($user_type_id);
                    if($user_type['type_profit']==0)
                    {
                        continue;
                    }
                    elseif($user_type['type_profit']==1)
                    {
                        if($this->config['USER_DISTRI_TYPE']==1)
                        {
                            $profit = $high_profit_money * $this->config['USER_DISTRI_MODE_SK_'.($k+1)]/100;
                        }
                        else
                        {
                            $profit = $this->config['USER_DISTRI_MODE_SK_'.($k+1)];
                        }
                        
                    }
                    elseif($user_type['type_profit']==2)
                    {
                        if($this->config['USER_DISTRI_TYPE']==1)
                        {
                            $profit = $high_profit_money * $this->config['USER_DISTRI_MODE_SK_V_'.($k+1)]/100;
                        }
                        else
                        {
                            $profit = $this->config['USER_DISTRI_MODE_SK_V_'.($k+1)];
                        }
                    }

                    
                    if($profit<= 0 || empty($profit))
                    {
                        continue;
                    }
                    
                    $profit_money = substr_num($profit);
                    if($profit_money<= 0)
                    {
                        $profit_money = 0;
                    }
                    $user_profit = Db::name('trading')
                            ->where('trading_orderid',$orderInfo['records_id'])
                            ->where('trading_form_no',$form_no)
                            ->where('trading_uid',$uid)
                            ->where('trading_type','2')
                            ->find();
                    if($profit_money>0 && empty($user_profit))
                    {
                        unset($data);
                        $data['trading_uid']=$uid;
                        $data['trading_title']='收款分润';
                        $data['trading_type']=2;
                        $data['trading_orderid']=$orderInfo['records_id'];
                        $data['trading_form_no']=$form_no;
                        $data['trading_money']=$profit_money;
                        $data['trading_time'] = time();
                        Db::name('trading')->insert($data);
                        //分润记录
                        if($k==0)
                        {
                            $style = 0;
                        }
                        else
                        {
                            $style = 1;
                        }
                        bonuslog($uid,$profit_money,$data['trading_time'],2,$data['trading_title'],$style,$orderInfo['records_uid'],0);
                    }
                }
            }
            // elseif($USER_DISTRI_MODE==1)
            // {
                
            //     $uid = $user['user_pid'];
            //     $user_type_id = getUser($uid,'user_type_id');
            //     $profit = ($user_rate - $one_agent_rate) * $money;
                
            //     if($user_type_id>1)
            //     {
            //         if($this->config['USER_DISTRI_TYPE']==1)
            //         {
            //             $profit = $profit * $this->config['USER_DISTRI_MODE_SK_2']/100;
            //         }
            //         else
            //         {
            //             $profit = $this->config['USER_DISTRI_MODE_SK_2'];
            //         }
                    
            //     }
            //     else
            //     {
            //         if($this->config['USER_DISTRI_TYPE']==1)
            //         {
            //             $profit = $profit * $this->config['USER_DISTRI_MODE_SK_1']/100;
            //         }
            //         else
            //         {
            //             $profit = $this->config['USER_DISTRI_MODE_SK_1'];
            //         }
            //     }
            //     $profit_money = substr_num($profit);
            //     $user_profit = Db::name('trading')
            //             ->where('trading_orderid',$orderInfo['records_id'])
            //             ->where('trading_form_no',$form_no)
            //             ->where('trading_uid',$uid)
            //             ->where('trading_type','2')
            //             ->find();

            //     if($profit_money>0 && empty($user_profit))
            //     {
            //         // Db::name('user')->where('user_id',$uid)->setInc('user_moeny',$profit_money);
            //         unset($data);
            //         $data['trading_uid']=$uid;
            //         $data['trading_title']='收款分润';
            //         $data['trading_type']=2;
            //         $data['trading_orderid']=$orderInfo['records_id'];
            //         $data['trading_form_no']=$form_no;
            //         $data['trading_money']=$profit_money;
            //         $data['trading_time'] = time();
            //         Db::name('trading')->insert($data);

            //         //分润记录
            //         if($k==0)
            //         {
            //             $style = 0;
            //         }
            //         else
            //         {
            //             $style = 1;
            //         }
            //         bonuslog($uid,$profit_money,$data['trading_time'],2,$data['trading_title'],$style,$orderInfo['records_uid'],0);
            //     }

            // }
        }

        $user_profit_money = Db::name('trading')->where('trading_orderid',$orderInfo['records_id'])->whereIn('trading_type',[2,22])->sum('trading_money');
        $agent_profit_money = Db::name('agent_profit')->where('profit_orderid',$orderInfo['records_pay_id'])->where('profit_state',1)->where('profit_type',2)->sum('profit_money');
        //系统分润
        $agent_id = '-1';
        $agent_rate = $payment_rate;
        $rate = $high_rate;
        $profit = $high_rate * $money - $user_profit_money - $agent_profit_money + $close_rate - $payment_close_fee;
        $profit_money = substr_num($profit);
        if($profit_money<= 0)
        {
            $profit_money = 0;
        }

        $agency_profit = Db::name('agent_profit')
                            ->where('profit_orderid',$orderInfo['records_id'])
                            ->where('profit_form_no',$form_no)
                            ->where('profit_agent_id',$agent_id)
                            ->where('profit_state','1')
                            ->where('profit_type','2')
                            ->find();

        if ($profit_money > 0 && empty($agency_profit)) {
            unset($data);
            $data['profit_uid']=$user['user_id'];
            $data['profit_agent_id']=$agent_id;
            $data['profit_orderid']=$orderInfo['records_id'];
            $data['profit_form_no']=$form_no;
            $data['profit_amount']=$orderInfo['records_money'];
            $data['profit_money']=$profit_money;
            $data['profit_rate']=$rate;
            $data['profit_user_rate']=$user_rate;
            $data['profit_agent_rate']=$agent_rate;
            $data['profit_state']=1;
            $data['profit_type']=2;
            $data['profit_pay']=0;
            $data['profit_time'] = time();
            Db::name('agent_profit')->insert($data);
        }
    }

}
