<?php
namespace app\pay\controller;
use think\Controller;
use think\Db;

class Base extends Controller
{
    /**
     * 通道公用参数
     * @Author tw
     * @Date   2018-10-10
     * @param  [type]     $pay_id      [渠道id]
     * @param  [type]     $uid         [用户id]
     * @param  string     $user_prefix [user 前缀]
     * @param  string     $type        [1商户注册 2查询]
     * @param  string     $user_type   1还款 2收款 
     * @return [type]                  [description]
     */
    protected function payment_public($pay_id,$uid,$user_prefix='',$type='',$user_type='1')
    {
        $payment = Db::name('payment')->where('payment_id',$pay_id)->find();
        if(empty($payment))
        {
            return ['error'=>1,'msg'=>'通道不存在'];
        }
        $payment_config = configJsonToArr($payment['payment_config']);
        if($payment['payment_type']==1)
        {
            $user_type = 2;
        }
        elseif($payment['payment_type']==2)
        {
            $user_type = 1;
        }
        else
        {
            $user_type = $payment['payment_type'];
        }
        $payment_user = Db::name('payment_user')->where('user_pay_id',$pay_id)->where('user_uid',$uid)->where('user_type',$user_type)->find();
        if($type==1)
        {
            if(empty($payment_user))
            {
                $payment_user['user_uid']= $uid;
                $payment_user['user_pay_id']= $pay_id;
                $payment_user['user_number']= $user_prefix.$pay_id.date('ymd') . sprintf("%04d", $uid);
                // $payment_user['user_name']= $card['card_city'].$card['card_district'].$user['user_name'];
                // $payment_user['user_shortname']= $card['card_district'].$user['user_name'];
                $payment_user['user_type']= $user_type;
                $payment_user['user_state']= '0';
                $payment_user['user_time']= time();
                $payment_user['user_id'] = Db::name('payment_user')->insertGetId($payment_user);
            }
            elseif($payment_user['user_state']==1)
            {
                return ['error'=>1,'msg'=>'商户已注册'];
            }
            elseif($payment_user['user_state']==3)
            {
                return ['error'=>1,'msg'=>'商户审核中'];
            }
        }
        elseif($type==2)
        {
            if(empty($payment_user) || $payment_user['user_state']==2)
            {
                return ['error'=>1,'msg'=>'商户未注册'];
            }
            elseif($payment_user['user_state']==3)
            {
                return ['error'=>1,'msg'=>'商户审核中'];
            }
            elseif($payment_user['user_state']==4)
            {
                return ['error'=>1,'msg'=>'商户冻结'];
            }
        }
        elseif($type==3)
        {
            if(empty($payment_user))
            {
                return ['error'=>1,'msg'=>'商户未注册'];
            }
        }
        

        $user = Db::name('user')->where('user_id',$uid)->where('user_state',0)->find();
        if(empty($user))
        {
            return ['error'=>1,'msg'=>'用户不存在'];
        }
        elseif ($user['user_real'] != 1) {
            return ['error'=>1,'msg'=>'请先完成实名认证'];
        }
        $user_rate_hk = Db::name('user_rate')->where('rate_uid',$uid)->where('rate_type',1)->find();
        if(empty($user_rate_hk))
        {
            return ['error'=>1,'msg'=>'用户还款费率不存在'];
        }
        $user_rate_sk = Db::name('user_rate')->where('rate_uid',$uid)->where('rate_type',2)->find();
        if(empty($user_rate_sk))
        {
            return ['error'=>1,'msg'=>'用户收款费率不存在'];
        }
        
        $card = Db::name('user_card')->where('card_uid',$uid)->where('card_type',2)->find();
        if(empty($card))
        {
            return ['error'=>1,'msg'=>'结算卡不存在'];
        }

        return [
                    'error'=>0,
                    'msg'=>'成功',
                    'payment'=>$payment,
                    'payment_config'=>$payment_config,
                    'payment_user'=>$payment_user,
                    // 'payment_card'=>$payment_card,
                    'user'=>$user,
                    'user_rate_hk'=>$user_rate_hk,
                    'user_rate_sk'=>$user_rate_sk,
                    'card'=>$card,
                    // 'pay_card'=>$pay_card
                ];
    }
    /**
     * 商户报件
     * @Author tw
     * @Date   2018-10-10
     * @param  [type]     $code          [description]
     * @param  [type]     $user_msg      [description]
     * @param  [type]     $user_merchant [description]
     * @return [type]                    [description]
     */
    protected function payment_register($payment_user='',$user_state='',$user_msg='',$user_merchant='',$data=array())
    {
        $data['user_state'] = $user_state;
        if($user_msg)
        {
            $data['user_msg'] = $user_msg;
        }
        if($user_merchant)
        {
            $data['user_merchant'] = $user_merchant;
        }
        $data['user_time'] = time();
        Db::name('payment_user')->where('user_id',$payment_user['user_id'])->where('user_pay_id',$payment_user['user_pay_id'])->update($data);
    }

    /**
     * 商户报件 新版
     * @Author tw
     * @param  string $payment_user   [报件表信息]
     * @param  string $user_state     [1审核通过 2失败 3审核中]
     * @param  string $user_msg       [提示信息]
     * @param  string $user_merchant  [支付渠道商户号]
     * @param  string $user_name      [商户名称]
     * @param  string $user_shortname [商户简称]
     * @param  string $user_number    [支付渠道的会员编号]
     * @param  array  $data           [其他]
     * @return [type]                 [description]
     */
    protected function payment_register_new($payment_user='',$user_state='',$user_msg='',$user_merchant='',$user_name='',$user_shortname='',$user_number='',$data=array())
    {
        $data['user_state'] = $user_state;
        if($user_msg)
        {
            $data['user_msg'] = $user_msg;
        }
        if($user_merchant)
        {
            $data['user_merchant'] = $user_merchant;
        }
        if($user_name)
        {
            $data['user_name'] = $user_name;
        }
        if($user_shortname)
        {
            $data['user_shortname'] = $user_shortname;
        }
        if($user_number)
        {
            $data['user_number'] = $user_number;
        }
        $data['user_time'] = time();
        Db::name('payment_user')->where('user_id',$payment_user['user_id'])->where('user_pay_id',$payment_user['user_pay_id'])->update($data);
    }
    /**
     * 绑定支付卡
     * @Author tw
     */
    protected function payment_bind_card($payment_card='',$card_state='',$card_msg='',$card_pay_cid='',$card_form_no='')
    {
        $data['card_state'] = $card_state;
        $data['card_time'] = time();
        if($card_msg)
        {
            $data['card_msg'] = $card_msg;
        }
        if($card_pay_cid)
        {
            $data['card_pay_cid'] = $card_pay_cid;
        }
        if($card_form_no)
        {
            $data['card_form_no'] = $card_form_no;
        }
        Db::name('payment_card')->where('card_id',$payment_card['card_id'])->update($data);
    }
    /**
     * 解绑卡
     * @Author tw
     */
    protected function payment_unbind_card($payment_card='')
    {
        if(empty($payment_card))
        {
            return ['error'=>1,'msg'=>'卡不存在'];
        }
        Db::name('payment_card')->where('card_id',$payment_card['card_id'])->delete();
    }
    /**
     * 收款订单
     * @Author tw
     * @Date   2018-10-12
     * @param  [type]     $id     [订单id]
     * @param  [type]     $uid    [用户id]
     * @param  [type]     $type   [订单状态]
     * @param  [type]     $up_no  [返回单号]
     * @param  [type]     $number [付款单号]
     */
    protected function payment_kj($id='',$uid='',$type='',$up_no='',$number='',$msg='',$info='',$pay_time='')
    {
        if(empty($id) || empty($uid) || empty($type))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }

        $pay_records = Db::name('pay_records')->where('records_id',$id)->where('records_uid',$uid)->find();
        if(empty($pay_records))
        {
            return ['error'=>1,'msg'=>'订单错误'];
        }
        if($up_no)
        {
            $data['records_form_up_no'] = $up_no;
        }
        if($number)
        {
            $data['records_form_number'] = $number;
        }
        if($msg)
        {
            $data['records_msg'] = $msg;
        }
        if($info)
        {
            $data['records_info'] = $info;
        }
        if($pay_time)
        {
            $data['records_pay_time'] = $pay_time;
        }
        $data['records_state'] = $type;
        Db::name('pay_records')->where('records_id',$id)->where('records_uid',$uid)->update($data);
        if($type==1)
        {
            Controller('pay/Profit')->payrecords($pay_records['records_form_no']);
        }
    }


    /**
     * 收款代付状态修改
     * @Author tw
     * @Date   2018-10-15
     * @param  string     $id     [description]
     * @param  string     $uid    [description]
     * @param  string     $state  [description]
     * @param  string     $number [description]
     * @return [type]             [description]
     */
    protected function pay_records($id='',$uid='',$state='',$number='')
    {
        if(empty($uid) || empty($id) || empty($state))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        $pay_records = Db::name('pay_records')->where('records_id',$id)->where('records_uid',$uid)->find();
        if(empty($pay_records) || !in_array($pay_records['records_state'],[2,3,4,5]))
        {
            return ['error'=>1,'msg'=>'订单错误'];
        }
        if($number)
        {
            $data['records_form_number'] = $number;
        }
        
        $data['records_state'] = $state;

        Db::name('pay_records')->where('records_id',$id)->where('records_uid',$uid)->update($data);
        if($state==1)
        {
            Controller('pay/Profit')->payrecords($pay_records['records_form_no']);
        }
    }

    /**
     * 支付状态
     * @Author tw
     * @Date   2018-10-11
     */
    public function payment_pay($plan_state='',$mission='',$plan='',$plan_msg='',$pay_id='',$k='',$no1='')
    {
        $data['plan_state'] = $plan_state;
        if($plan_msg)
        {
            $data['plan_msg'] = $plan_msg;
        }
        if($pay_id)
        {
            $data['plan_pay_id'] = $pay_id;
        }
        if($k)
        {
            $data['plan_k'] = $k;
        }
        if($no1)
        {
            $data['plan_form_no1'] = $no1;
        }
        if($plan_state==3)
        {
            $mission_type = 3;
        }
        else
        {
            $mission_type = 1;
        }
        $data['plan_time'] = date('Y-m-d H:i:s');
        Db::name('plan')->where('plan_id',$plan['plan_id'])->update($data);
        Db::name('mission')->where('mission_id',$plan['plan_mid'])->update(['mission_type'=>$mission_type]);
    }
    /**
     * @Author QQ
     */
    protected function notify_logs($data='',$controller='')
    {
        $path =  'logs/';
        if($controller)
        {
            $path =  $path.$controller.'/';
        }
        else
        {
            $path =  $path.request()->controller().'/';
        }
        //创建类型
        if (! is_dir($path)) {
            mkdir($path,0777);
        }
        $path =  $path.request()->action().'/';
        //创建类型
        if (! is_dir($path)) {
            mkdir($path,0777);
        }
        //支付渠道回调
        file_put_contents($path.date("Ymd",time()).'.log', PHP_EOL.date('Y-m-d H:i:s').PHP_EOL.var_export($data, true).PHP_EOL, FILE_APPEND);

    }

    public function pay_logs($data='',$obj='',$controller='',$action='')
    {
        $path =  'logs/';
        //创建类型
        if (! is_dir($path)) {
            mkdir($path,0777);
        }
        if($controller)
        {
            $path =  $path.$controller.'/';
        }
        else
        {
            $path =  $path.request()->controller().'/';
        }
        //创建类型
        if (! is_dir($path)) {
            mkdir($path,0777);
        }
        if($action)
        {
            $path =  $path.$action.'/';
        }
        else
        {
            $path =  $path.request()->action().'/';
        }
        //创建类型
        if (! is_dir($path)) {
            mkdir($path,0777);
        }
        //支付渠道回调
        file_put_contents($path.date("Ymd",time()).'.log', PHP_EOL.date('Y-m-d H:i:s').PHP_EOL.'发送'.PHP_EOL.var_export($data, true).PHP_EOL, FILE_APPEND);
        file_put_contents($path.date("Ymd",time()).'.log', '返回'.PHP_EOL.var_export($obj, true).PHP_EOL, FILE_APPEND);

    }

    
    /**
     * 还款计划回调处理
     * @Author tw
     * @Date   2018-10-10
     * @param  [type]     $plan [description]
     * @param  [type]     $type 1支付成功
     * @param  [plan_msg]     $plan_msg 提示信息
     * @param  [bot]     $bot 模式 1 修改mission状态 2不修改
     */
    public function repay_notify($plan='',$type='',$plan_msg='',$bot='1')
    {
     	if($plan['plan_state']==1){
        	 Controller('pay/Profit')->repayment($plan['plan_form_no']);
          	 return ;
        }
      
        // if(empty(Db::name('plan')->where('plan_state',3)->where('plan_id',$plan['plan_id'])->find()))
        // {
        //     return;
        // }

        $plan_fee = Db::name('plan')->where('plan_mid',$plan['plan_mid'])->where('plan_state',1)->where('plan_type',2)->sum('plan_fee');
        Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->update(['mission_at_fee'=>$plan_fee]);
        $mission = Db::name('mission')->where('mission_id',$plan['plan_mid'])->find();
        if($type==1)
        {
            if($plan['plan_type']=='1')
            {
                //还款
                Db::name('plan')->where('plan_id',$plan['plan_id'])->update(['plan_state'=>1,'plan_msg'=>$plan_msg,'plan_time'=>date('Y-m-d H:i:s')]);
                Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->setInc('mission_repayment_number');
                // Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->setDec('mission_at_fee',$plan['plan_money']);
                if($bot==1)
                {
                    $payTime = Db::name('plan')->where(['plan_state'=>0,'plan_type'=>2,'plan_mid'=>$plan['plan_mid']])->order('plan_pay_time asc')->value('plan_pay_time');
                    if($payTime)
                    {
                        Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->update(['mission_type'=>0,'mission_current_state'=>2,'mission_pay_time'=>$payTime,'mission_queues'=>0]);
                    }
                    else
                    {
                        //计划完成
                        Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->update(['mission_type'=>0,'mission_current_state'=>3,'mission_state'=>2,'mission_queues'=>0]);
                        //更改信用卡状态
                        $cid = Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->value('mission_cid');
                        Db::name('user_card')->where(['card_id'=>$mission['mission_cid']])->update(['card_state'=>0]);
                    }


                    switch ($mission['mission_close']) {
                        case '2':
                            //用户提交关闭计划
                            Db::name('mission')->where('mission_id',$plan['plan_mid'])->update(['mission_state'=>4]);
                            Db::name('user_card')->where('card_id',$mission['mission_cid'])->update(['card_state'=>0]);
                            break;
                        case '3':
                            //用户提交删除
                            Db::name('mission')->where('mission_id',$plan['plan_mid'])->update(['mission_del'=>1,'mission_state'=>3]);
                            Db::name('user_card')->where('card_id',$mission['mission_cid'])->update(['card_state'=>0]);
                            break;
                        default:
                            Db::name('mission')->where('mission_id',$plan['plan_mid'])->update(['mission_close'=>1]);
                            break;
                    }

                }
                
                //增加用户还款金额
                Db::name('user')->where(['user_id'=>$plan['plan_uid']])->setInc('user_repay_amount',$plan['plan_money']);

                //分润
                Controller('pay/Profit')->repayment($plan['plan_form_no']);
                // unset($data);
                // $data['form_no'] = $plan['plan_form_no'];
                // api_post('api/Profit/repayment',$data);
            }
            elseif($plan['plan_type']=='2')
            {
                //消费
                Db::name('plan')->where('plan_id',$plan['plan_id'])->update(['plan_state'=>1,'plan_msg'=>$plan_msg,'plan_time'=>date('Y-m-d H:i:s')]);
                Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->setInc('mission_consume_number');
                // Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->setInc('mission_at_fee',$plan['plan_money']);
                $payTime = Db::name('plan')->where(['plan_state'=>0,'plan_type'=>2,'plan_mid'=>$plan['plan_mid'],'plan_sort'=>$plan['plan_sort']])->order('plan_pay_time asc')->value('plan_pay_time');
                if($payTime)
                {
                    $mission_current_state = 2;
                }
                else
                {
                    $payTime = Db::name('plan')->where(['plan_state'=>0,'plan_type'=>1,'plan_mid'=>$plan['plan_mid'],'plan_sort'=>$plan['plan_sort']])->order('plan_pay_time asc')->value('plan_pay_time');
                    $mission_current_state = 1;
                }
                unset($data);
                if($bot==1)
                {
                    $data['mission_type'] = 0;
                }
                
                $data['mission_current_state'] = $mission_current_state;
                $data['mission_pay_time'] = $payTime;
                $data['mission_queues'] = 0;
                if($mission['mission_close'] == '1')
                {
                    $data['mission_close'] = 0;
                }

                Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->update($data);
                
            }
        }
        elseif($type==2)
        {
            Db::name('plan')->where('plan_id',$plan['plan_id'])->update(['plan_state'=>2,'plan_msg'=>$plan_msg,'plan_time'=>date('Y-m-d H:i:s')]);
            Db::name('mission')->where('mission_id',$plan['plan_mid'])->update(['mission_type'=>1]);
        }
        elseif($type==3)
        {
            Db::name('plan')->where('plan_id',$plan['plan_id'])->update(['plan_k'=>1,'plan_state'=>0,'plan_msg'=>$plan_msg,'plan_time'=>date('Y-m-d H:i:s')]);
        }
        
    }
}