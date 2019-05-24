<?php
namespace app\pay\controller;
use think\Controller;
use think\Db;

class Payhkhqb extends Base
{
    //merchno:jjkb|openKey:ca60d333bc7|payurl:http://pay.huanqiuhuiju.com/authsys/smartrepay/execute.do
    
    /**
     * 商户注册  储蓄卡
     * @Author tw
     * @Date   2018-09-29
     * @return [type]     [description]
     */
    public function register($pay_id='',$uid='')
    {
        if(empty($pay_id) || empty($uid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        $payment_public = $this->payment_public($pay_id,$uid,'hb',1,1);

        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment_user = $payment_public['payment_user'];

        $this->payment_register($payment_user,1,'通道无需注册',$payment_user['user_number']);
        return ['error'=>0,'msg'=>'入驻成功'];
    }
    
    /**
     * 修改卡号和费率
     * @Author tw
     * @param  string $pay_id [description]
     * @param  string $uid    [description]
     * @return [type]         [description]
     */
    public function update_fee($pay_id='',$uid='')
    {
    }
    /**
     * 子商户入网查询
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function network_query()
    {
        return json(['error'=>0,'msg'=>'入驻成功']);
    }

    /**
     * 查询子商户余额
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function balance_query($pay_id='',$uid='')
    {
        return ['error'=>0,'msg'=>'成功','amount'=>0];
    }

    /**
     * 绑定信用卡
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function bind_card($pay_id='',$uid='',$cid='',$type='1')
    {
        if(empty($pay_id) || empty($uid) || empty($cid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }

        $payment_public = $this->payment_public($pay_id,$uid,'',2,$type);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];

        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if(empty($payment_card))
        {
            $payment_card['card_uid'] = $uid;
            $payment_card['card_pay_id'] = $pay_id;
            $payment_card['card_pay_uid'] = $payment_user['user_id'];
            $payment_card['card_cid'] = $cid;
            $payment_card['card_type'] = 1;
            $payment_card['card_state'] = 3;
            $payment_card['card_time'] = time();
            $payment_card['card_id'] = Db::name('payment_card')->insertGetId($payment_card);
        }
        elseif($payment_card['card_state']== 1)
        {
            return ['error'=>1,'msg'=>'卡已绑定'];
        }
        elseif($payment_card['card_state']== 3 && (time()-$payment_card['card_time']) < 10*60 )
        {
            return ['error'=>1,'msg'=>'验证已提交,等待审核，10分钟后重试'];
        }
        $card = Db::name('user_card')->where('card_uid',$uid)->where('card_id',$cid)->where('card_type',1)->find();
        if(empty($card))
        {
            return ['error'=>1,'msg'=>'信用卡不存在'];
        }

        $data['userId'] = $payment_user['user_merchant'];//商户账号
        $data['username'] = $card['card_name'];//用户名
        $data['mobile'] = $card['card_phone'];//手机号
        $data['idCard'] = $user['user_idcard'];//身份证
        $data['bankCardNo'] = $card['card_no'];//卡号
        $data['bankcardName'] = getBankList($card['card_bank_id'],'list_name');//银行名称
        $data['methodname'] = 'BindUnionCardNaked';//交易类型 固定BindUnionCardNaked
        $data['notifyUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payhkhqb/bind_card_notify';//异步通知地址


        //公共参数
        $data['transcode']= '032'; // 交易码
        $data['version'] = '0100'; // 版本号
        $data['ordersn'] = get_order_sn("",$uid); //流水号
        $data['merchno'] = $payment_config['merchno'];//商户号
        $data['dsorderid'] = get_order_sn("B",$uid);//商户订单号
        
        ksort($data);
        $data['sign'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名

        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'],$json_data);
        $this->pay_logs($json_data,$res,'Payhkhqb','bind_card');


        //记录
        $log['log_c_id'] = $payment_card['card_id'];
        $log['log_pay_id'] = $pay_id;
        $log['log_pay_uid'] = $uid;
        $log['log_pay_cid'] = $cid;
        $log['log_no'] = $data['ordersn'];
        $log['log_form_no'] = $data['dsorderid'];
        $log['log_name'] = '还款绑卡提交';
        $log['log_body'] = $json_data;
        $log['log_result'] = $res;
        $log['log_state'] = 1;
        $log['log_time'] = time();
        Db::name('payment_card_log')->insert($log);


        $obj = json_decode($res,true);
        if($obj['returncode']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['errtext']];
        }
        $this->payment_bind_card($payment_card,3,'提交网页绑卡',$obj['bindId'],$obj['dsorderid']);
        return ['error'=>0,'msg'=>'成功','returnUrl'=>$obj['returnUrl']];
        
    }

    /**
     * 绑卡回调
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function bind_card_notify()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $this->notify_logs($post,'Payhkhqb');
        $payment_card = Db::name('payment_card')->where('card_form_no',$post['dsorderid'])->find();
        if(empty($payment_card) || $payment_card['card_state']!=3)
        {
            echo '请先提交绑卡信息';
            exit();
        }
        elseif($post['respCode'] != '00')
        {
            $this->payment_bind_card($payment_card,2,$post['respMsg'],$post['bindId']);
        }
        else
        {
            $this->payment_bind_card($payment_card,1,$post['respMsg'],$post['bindId']);
        }
        echo 'SUCCESS';
    }

    /**
     * 交易状态查询
     * @Author tw
     * @Date   2018-10-20
     */
    public function state_query($id='',$uid='',$pay_id='',$form_no='',$up_no='',$date='')
    {
        return ['error'=>1,'msg'=>'请联系管理员'];
    }



    /**
     * 扣款
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function pay_kj($mission,$plan)
    {
        $region = array();
        $cid = $mission['mission_cid'];//信用卡id
        $pay_id = $mission['mission_pay_id'];//支付通道id
        $uid = $mission['mission_uid'];//用户id

        if(empty($pay_id) || empty($uid) || empty($cid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }

        if($plan['plan_type']==2)
        {
            $plan = Db::name('plan')->where('plan_type',1)->where('plan_state',0)->where('plan_sort',$plan['plan_sort'])->where('plan_mid',$plan['plan_mid'])->find();
        }
        $payment_public = $this->payment_public($pay_id,$uid,'',2);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];

        $card = Db::name('user_card')->where('card_uid',$uid)->where('card_id',$cid)->where('card_blocked',0)->where('card_type',1)->find();
        if(empty($card))
        {
            return ['error'=>1,'msg'=>'支付卡不存在'];
        }

        $bank = Db::name('bank')->where('bank_pay_id',$pay_id)->where('bank_bid',$card['card_bank_id'])->find();
        if(empty($bank))
        {
            $this->payment_pay(2,$mission,$plan,'不支持此银行',$pay_id);
            return ['error'=>1,'msg'=>'不支持此银行'];
        }
        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if(empty($payment_card))
        {
            $this->payment_pay(2,$mission,$plan,'通道没有绑卡',$pay_id);
            return ['error'=>1,'msg'=>'通道没有绑卡'];
        }
        $data['mobile'] = $card['card_phone'];//手机号
        $data['bankCardNo'] = $card['card_no'];//卡号
        $data['bankcardName'] = getBankList($card['card_bank_id'],'list_name');//银行名称
        $data['bankcardCode'] = $bank['bank_code'];//银行编码
        $data['userId'] = $payment_user['user_merchant'];//商户账号
        $data['methodname'] = 'CreateRepayPlan';//交易类型 固定BindUnionCardNaked
        $data['bindId'] = $payment_card['card_pay_cid'];//绑卡成功返回的bindId 
        $data['futureRateValue'] = (string)($mission['mission_rate']*100);//费率
        $data['fixAmount'] = (string)($mission['mission_close_rate']*100);//手续费
        $data['notifyUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payhkhqb/pay_notify';//回调地址

        if($mission['mission_flag']>1)
        {
            $data['fixAmount'] = (string)(($mission['mission_close_rate']/$mission['mission_flag'])*100);//手续费
            $data['repayMode'] = '2';//当 repayMode为 1时,表示走一扣一还模式，当 repayMode为 2时,表示走多扣一还模式，

            $planlist = Db::name('plan')->where('plan_state',0)->where('plan_sort',$plan['plan_sort'])->where('plan_mid',$plan['plan_mid'])->where('plan_sort',$plan['plan_sort'])->order('plan_type desc')->select();
            $region = explode('-', $planlist[0]['plan_mcc']);
            $plan_pay_time = date('Y-m-d H:i:s',strtotime("+5 minute"));
            foreach ($planlist as $key => $value) {

                Db::name('plan')->where(['plan_id'=>$value['plan_id']])->update(['plan_pay_time'=>$plan_pay_time]);//消费时间
                if($value['plan_type']==1)
                {
                    $repayPlanList[$key]['tradeMoney'] = '0';//代扣金额
                    $repayPlanList[$key]['transferMoney'] = '0';//代还金额
                    $repayPlanList[$key]['tradeTime'] = $plan_pay_time;//代扣时间
                    $repayPlanList[$key]['transferTime'] = $plan_pay_time;//代还时间,
                    // $repayPlanList[$key]['tradeTime'] = $value['plan_pay_time'];//代扣时间
                    // $repayPlanList[$key]['transferTime'] = $value['plan_pay_time'];//代还时间,
                    $repayPlanList[$key]['rateMoney'] = '0';//该笔手续费
                    $repayPlanList[$key]['repayOrderId'] = $value['plan_form_no'];//每笔交易订单号
                    $repayPlanList[$key]['transferRepayOrderId'] = $plan['plan_form_no'];//扣款记录对应还款记录的订单号
                    $repayPlanList[$key]['repayOrderFlag'] = '2';//扣款记录对应还款记录的订单号

                }
                elseif($value['plan_type']==2)
                {
                    $rateMoney = number_format(($value['plan_money'] * $mission['mission_rate']),2);//该笔手续费
                    $repayPlanList[$key]['tradeMoney'] = (string)(($value['plan_money'] + $rateMoney) * 100);//代扣金额
                    $repayPlanList[$key]['transferMoney'] = (string)($value['plan_money'] * 100);//代还金额
                    $repayPlanList[$key]['tradeTime'] = $plan_pay_time;//代扣时间
                    $repayPlanList[$key]['transferTime'] = $plan_pay_time;//代还时间,
                    // $repayPlanList[$key]['tradeTime'] = $value['plan_pay_time'];//代扣时间
                    // $repayPlanList[$key]['transferTime'] = $value['plan_pay_time'];//代还时间,
                    $repayPlanList[$key]['rateMoney'] = (string)($rateMoney*100);//该笔手续费
                    $repayPlanList[$key]['repayOrderId'] = $value['plan_form_no'];//每笔交易订单号
                    $repayPlanList[$key]['transferRepayOrderId'] = $plan['plan_form_no'];//扣款记录对应还款记录的订单号
                    $repayPlanList[$key]['repayOrderFlag'] = '1';//扣款记录对应还款记录的订单号

                }
                $plan_pay_time = date('Y-m-d H:i:s',strtotime($plan_pay_time) + rand(3000,4500));
                // $plan_pay_time = date("Y-m-d H:i:s",strtotime("+1 hours",strtotime($plan_pay_time)));
            }
        }
        else
        {
            $data['repayMode'] = '1';//当 repayMode为 1时,表示走一扣一还模式，当 repayMode为 2时,表示走多扣一还模式，


            $planlist = Db::name('plan')->where('plan_type',2)->where('plan_state',0)->where('plan_sort',$plan['plan_sort'])->where('plan_mid',$plan['plan_mid'])->whereIn('plan_id',$plan['plan_oids'])->select();

            $region = explode('-', $planlist[0]['plan_mcc']);
            foreach ($planlist as $key => $value) {
                
                $value['plan_pay_time'] = date('Y-m-d H:i:s',strtotime("+5 minute"));
                $plan['plan_pay_time'] = date("Y-m-d H:i:s",strtotime("+1 hours",strtotime($value['plan_pay_time'])));


                Db::name('plan')->where(['plan_id'=>$value['plan_id']])->update(['plan_pay_time'=>$value['plan_pay_time']]);//消费时间
                Db::name('plan')->where(['plan_id'=>$plan['plan_id']])->update(['plan_pay_time'=>$plan['plan_pay_time']]);//支付时间

                $rateMoney = number_format(($value['plan_money'] * $mission['mission_rate']),2);//该笔手续费
                $repayPlanList[$key]['tradeMoney'] = (string)(($value['plan_money'] + $rateMoney)*100);//代扣金额
                $repayPlanList[$key]['transferMoney'] = (string)($plan['plan_money']*100);//代还金额
                $repayPlanList[$key]['tradeTime'] = $value['plan_pay_time'];//代扣时间
                $repayPlanList[$key]['transferTime'] = $plan['plan_pay_time'];//代还时间,
                $repayPlanList[$key]['rateMoney'] = (string)($rateMoney*100);//该笔手续费
                $repayPlanList[$key]['repayOrderId'] = $value['plan_form_no'];//每笔交易订单号
                $repayPlanList[$key]['transferRepayOrderId'] = $plan['plan_form_no'];//扣款记录对应还款记录的订单号
                $repayPlanList[$key]['repayOrderFlag'] = 1;//扣款记录对应还款记录的订单号
            }

        }
        if(count($region)<1)
        {
            $region[0] = '北京市';
            $region[1] = '北京市';
        }
        $data['chantype'] = '1';//通道类型，目前支持1,1-新通道, 小额通道,小额,支持所有银行,0-老通道,北京银联,大额,支持部分银行;不传默认走老通道

        $data['province'] = $region[0];//省份
        $data['city'] = $region[1];//城市
        $data['deviceType'] = md5($payment_user['user_merchant']);//设备号,取手机的设备号(如取不到，可先填一个固定字符串标识)

        $data['repayPlanList'] = $repayPlanList;

        //公共参数
        $data['transcode']= '032'; // 交易码 
        $data['version'] = '0100'; // 版本号
        $data['ordersn'] = get_order_sn("",$uid); //流水号
        $data['merchno'] = $payment_config['merchno'];//商户号
        $data['dsorderid'] = $plan['plan_form_no'];//商户订单号

        ksort($data);
        $data = array_filter($data);
        $data['sign'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);

        $res = $this->post_json($payment_config['payurl'],$json_data);
        $this->pay_logs($json_data,$res,'Payhkhqb','pay_kj');
        if(empty($res))
        {
            return ['error'=>1,'msg'=>'通道未返回数据'];
        }
        $obj = json_decode($res,true);

        if($obj['returncode']=='0000')
        {
            $this->payment_pay(3,$mission,$plan,'计划提交成功',$pay_id);
            foreach ($planlist as $key => $value) {
                $this->payment_pay(3,$mission,$value,'计划提交成功',$pay_id);
            }
            return ['error'=>0,'msg'=>'成功'];

        }
        elseif($obj['returncode']=='0001')
        {
            // Db::name('plan')->where('plan_id',$plan['plan_id'])->update(['plan_state'=>2,'plan_msg'=>$obj['errtext']]);
            // Db::name('mission')->where('mission_id',$mission['mission_id'])->update(['mission_type'=>1]);
            $this->payment_pay(2,$mission,$plan,$obj['errtext'],$pay_id);
            foreach ($planlist as $key => $value) {
                $this->payment_pay(2,$mission,$value,$obj['errtext'],$pay_id);
            }
            return ['error'=>1,'msg'=>$obj['errtext']];
        }
        elseif($obj['returncode']=='0003')
        {
            return ['error'=>0,'msg'=>'处理中'.$obj['errtext']];
        }
        else
        {
            return ['error'=>0,'msg'=>'通道返回：'.$obj['errtext']];
        }
    }

    /**
     * 扣款回调
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function pay_notify()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $this->notify_logs($post,'Payhkhqb');
        if($post['respCode'] !='00')
        {
            echo 'error';
            exit();
        }
        switch ($post['status']) {
            case '1':
                # 初始化
                break;
            case '2':
                # 支付中
                break;
            case '3':
                # 支付成功
                $plan = Db::name('plan')->where('plan_form_no',$post['repayOrderId'])->find();
                if($plan['plan_type']==2)
                {
                    $this->repay_notify($plan,1,'支付成功',2);
                }
                break;
            case '4':
                # 支付失败
                $plan = Db::name('plan')->where('plan_form_no',$post['repayOrderId'])->find();
                $this->repay_notify($plan,2,'支付失败',2);
                break;
            case '5':
                # 转账中
                break;
            case '6':
                # 转账成功
                break;
            case '7':
                # 转账失败
                $plan = Db::name('plan')->where('plan_form_no',$post['dsorderid'])->find();
                $this->repay_notify($plan,2,'转账失败',2);
                break;
            case '8':
                # 交易最终失败
                break;
            case '9':
                $plan = Db::name('plan')->where('plan_form_no',$post['dsorderid'])->find();
                $this->repay_notify($plan,1,'交易成功',1);
                # 交易成功
                break;
            case '10':
                # 自动终止
                break;
            case '11':
                # 手动终止
                break;
            case '12':
                # 交易结果未知
                break;
            case '13':
                # 重出款中
                break;
            case '14':
                $plan = Db::name('plan')->where('plan_form_no',$post['dsorderid'])->find();
                $this->repay_notify($plan,1,'交易成功',1);
                # 重出款成功
                break;
            case '15':
                # 重出款失败
                break;
            default:
                break;
        }
        return 'SUCCESS';
    }


    /**
     * 支付状态查询
     * @Author tw
     * @Date   2018-10-23
     * @param  [type]     $plan [description]
     * @return [type]           [description]
     */
    public function query_order_status($plan)
    {
        $mission['mission_id'] = $plan['plan_mid'];
        $pay_id = $plan['plan_pay_id'];//支付通道id
        $uid = $plan['plan_uid'];//用户id

        if(empty($pay_id) || empty($uid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        $payment_public = $this->payment_public($pay_id,$uid,'',2);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];


        $data['userId'] = $payment_user['user_merchant'];//商户账号
        $data['methodname'] = 'QueryRepayItemStatus';//交易类型

        //公共参数
        $data['transcode']= '032'; // 交易码 
        $data['version'] = '0100'; // 版本号
        $data['ordersn'] = get_order_sn("",$uid); //流水号
        $data['merchno'] = $payment_config['merchno'];//商户号
        $data['repayOrderId'] = $plan['plan_form_no'];//商户订单号
        ksort($data);
        $data = array_filter($data);
        $data['sign'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);

        $res = $this->post_json($payment_config['payurl'],$json_data);
        if(empty($res))
        {
            return ['error'=>1,'msg'=>'通道未返回数据'];
        }

        $this->pay_logs($json_data,$res,'Payhkhqb','query_order_status');
        $obj = json_decode($res,true);
        if($obj['returncode'] !='0000')
        {
            $this->repay_notify($plan,2,'支付失败',2);
            return ['error'=>1,'msg'=>$obj['errtext']];
        }

        switch ($obj['status']) {
            case '1':
                # 初始化
                break;
            case '2':
                # 支付中
                break;
            case '3':
                # 支付成功
                if($plan['plan_type']==1)
                {
                    return ['error'=>0,'msg'=>'还款处理中'];
                }
                elseif($plan['plan_type']==2)
                {
                    $this->repay_notify($plan,1,'消费成功',2);
                }
                return ['error'=>0,'msg'=>'消费成功'];
                break;
            case '4':
                # 支付失败
                $this->repay_notify($plan,2,'支付失败',2);
                return ['error'=>1,'msg'=>'支付失败'.$obj['errtext']];
                break;
            case '5':
                # 转账中
                break;
            case '6':
                # 转账成功
                break;
            case '7':
                # 转账失败
                $this->repay_notify($plan,2,'转账失败',2);
                return ['error'=>1,'msg'=>'转账失败'.$obj['errtext']];
                break;
            case '8':
                # 交易最终失败
                break;
            case '9':
                $this->repay_notify($plan,1,'交易成功',1);
                return ['error'=>0,'msg'=>'交易成功'.$obj['errtext']];
                # 交易成功
                break;
            case '10':
                # 自动终止
                break;
            case '11':
                # 手动终止
                break;
            case '12':
                # 交易结果未知
                break;
            case '13':
                # 重出款中
                break;
            case '14':
                $this->repay_notify($plan,1,'重出款成功',1);
                return ['error'=>0,'msg'=>'重出款成功'.$obj['errtext']];
                # 重出款成功
                break;
            case '15':
                # 重出款失败
                break;
            default:
                return ['error'=>1,'msg'=>$obj['errtext']];
                break;
        }
        return ['error'=>1,'msg'=>'错误:'.$obj['errtext']];

    }
    public function pay_state_df($plan)
    {
        return $this->query_order_status($plan);
    }
    public function pay_state_kj($plan)
    {
        return $this->query_order_status($plan);
    }



    public function mcc($pay_id='',$uid='',$cid='',$city_id='0',$region='')
    {
        return ['error'=>0,'msg'=>'成功','data'=>[['mcc'=>$region,'name'=>$region]]];

    }

    /**
     * 查询市的行业信息 -传入示例山东省-济宁市
     * @Author tw
     * @param  string $pay_id   [description]
     * @param  string $uid      [description]
     * @param  string $cid      [description]
     * @param  string $province [description]
     * @param  string $city     [description]
     * @return [type]           [description]
     */
    public function query_city_mcc($pay_id='',$uid='',$cid='',$province='',$city='',$region='')
    {
        return ['error'=>0,'msg'=>'成功','data'=>[['mcc'=>$region,'name'=>$region]]];

    }

    /**
     * 获取所有城市
     * @Author tw
     * @param  string $pay_id [description]
     * @param  string $uid    [description]
     * @param  string $id     [description]
     * @return [type]         [description]
     */
    public function city_all($pay_id='',$uid='',$id='0')
    {
        $city = '{"error":0,"msg":"成功","data":[{"value":"110100","text":"北京","children":[{"value":"110100","text":"北京市"}]},{"value":"120100","text":"天津","children":[{"value":"120100","text":"天津市"}]},{"value":"130000","text":"河北省","children":[{"value":"130100","text":"石家庄市"},{"value":"130200","text":"唐山市"},{"value":"130300","text":"秦皇岛市"},{"value":"130400","text":"邯郸市"},{"value":"130500","text":"邢台市"},{"value":"130600","text":"保定市"},{"value":"130700","text":"张家口市"},{"value":"130800","text":"承德市"},{"value":"130900","text":"沧州市"},{"value":"131000","text":"廊坊市"},{"value":"131100","text":"衡水市"}]},{"value":"140000","text":"山西省","children":[{"value":"140100","text":"太原市"},{"value":"140200","text":"大同市"},{"value":"140300","text":"阳泉市"},{"value":"140400","text":"长治市"},{"value":"140500","text":"晋城市"},{"value":"140600","text":"朔州市"},{"value":"140700","text":"晋中市"},{"value":"140800","text":"运城市"},{"value":"140900","text":"忻州市"},{"value":"141000","text":"临汾市"},{"value":"141100","text":"吕梁市"}]},{"value":"150000","text":"内蒙古自治区","children":[{"value":"150100","text":"呼和浩特市"},{"value":"150200","text":"包头市"},{"value":"150300","text":"乌海市"},{"value":"150400","text":"赤峰市"},{"value":"150500","text":"通辽市"},{"value":"150600","text":"鄂尔多斯市"},{"value":"150700","text":"呼伦贝尔市"},{"value":"150800","text":"巴彦淖尔市"},{"value":"150900","text":"乌兰察布市"},{"value":"152200","text":"兴安盟"},{"value":"152500","text":"锡林郭勒盟"},{"value":"152900","text":"阿拉善盟"}]},{"value":"210000","text":"辽宁省","children":[{"value":"210100","text":"沈阳市"},{"value":"210200","text":"大连市"},{"value":"210300","text":"鞍山市"},{"value":"210400","text":"抚顺市"},{"value":"210500","text":"本溪市"},{"value":"210600","text":"丹东市"},{"value":"210700","text":"锦州市"},{"value":"210800","text":"营口市"},{"value":"210900","text":"阜新市"},{"value":"211000","text":"辽阳市"},{"value":"211100","text":"盘锦市"},{"value":"211200","text":"铁岭市"},{"value":"211300","text":"朝阳市"},{"value":"211400","text":"葫芦岛市"}]},{"value":"220200","text":"吉林省","children":[{"value":"220100","text":"长春市"},{"value":"220200","text":"吉林市"},{"value":"220300","text":"四平市"},{"value":"220400","text":"辽源市"},{"value":"220500","text":"通化市"},{"value":"220600","text":"白山市"},{"value":"220700","text":"松原市"},{"value":"220800","text":"白城市"},{"value":"222400","text":"延边朝鲜族自治州"}]},{"value":"230000","text":"黑龙江省","children":[{"value":"230100","text":"哈尔滨市"},{"value":"230200","text":"齐齐哈尔市"},{"value":"230300","text":"鸡西市"},{"value":"230400","text":"鹤岗市"},{"value":"230500","text":"双鸭山市"},{"value":"230600","text":"大庆市"},{"value":"230700","text":"伊春市"},{"value":"230800","text":"佳木斯市"},{"value":"230900","text":"七台河市"},{"value":"231000","text":"牡丹江市"},{"value":"231100","text":"黑河市"},{"value":"231200","text":"绥化市"},{"value":"232700","text":"大兴安岭地区"}]},{"value":"310100","text":"上海","children":[{"value":"310100","text":"上海市"}]},{"value":"320000","text":"江苏省","children":[{"value":"320100","text":"南京市"},{"value":"320200","text":"无锡市"},{"value":"320300","text":"徐州市"},{"value":"320400","text":"常州市"},{"value":"320500","text":"苏州市"},{"value":"320600","text":"南通市"},{"value":"320700","text":"连云港市"},{"value":"320800","text":"淮安市"},{"value":"320900","text":"盐城市"},{"value":"321000","text":"扬州市"},{"value":"321100","text":"镇江市"},{"value":"321200","text":"泰州市"},{"value":"321300","text":"宿迁市"}]},{"value":"330000","text":"浙江省","children":[{"value":"330100","text":"杭州市"},{"value":"330200","text":"宁波市"},{"value":"330300","text":"温州市"},{"value":"330400","text":"嘉兴市"},{"value":"330500","text":"湖州市"},{"value":"330600","text":"绍兴市"},{"value":"330700","text":"金华市"},{"value":"330800","text":"衢州市"},{"value":"330900","text":"舟山市"},{"value":"331000","text":"台州市"},{"value":"331100","text":"丽水市"}]},{"value":"340000","text":"安徽省","children":[{"value":"340100","text":"合肥市"},{"value":"340200","text":"芜湖市"},{"value":"340300","text":"蚌埠市"},{"value":"340400","text":"淮南市"},{"value":"340500","text":"马鞍山市"},{"value":"340600","text":"淮北市"},{"value":"340700","text":"铜陵市"},{"value":"340800","text":"安庆市"},{"value":"341000","text":"黄山市"},{"value":"341100","text":"滁州市"},{"value":"341200","text":"阜阳市"},{"value":"341300","text":"宿州市"},{"value":"341500","text":"六安市"},{"value":"341600","text":"亳州市"},{"value":"341700","text":"池州市"},{"value":"341800","text":"宣城市"}]},{"value":"350000","text":"福建省","children":[{"value":"350100","text":"福州市"},{"value":"350200","text":"厦门市"},{"value":"350300","text":"莆田市"},{"value":"350400","text":"三明市"},{"value":"350500","text":"泉州市"},{"value":"350600","text":"漳州市"},{"value":"350700","text":"南平市"},{"value":"350800","text":"龙岩市"},{"value":"350900","text":"宁德市"}]},{"value":"360000","text":"江西省","children":[{"value":"360100","text":"南昌市"},{"value":"360200","text":"景德镇市"},{"value":"360300","text":"萍乡市"},{"value":"360400","text":"九江市"},{"value":"360500","text":"新余市"},{"value":"360600","text":"鹰潭市"},{"value":"360700","text":"赣州市"},{"value":"360800","text":"吉安市"},{"value":"360900","text":"宜春市"},{"value":"361000","text":"抚州市"},{"value":"361100","text":"上饶市"}]},{"value":"370000","text":"山东省","children":[{"value":"370100","text":"济南市"},{"value":"370200","text":"青岛市"},{"value":"370300","text":"淄博市"},{"value":"370400","text":"枣庄市"},{"value":"370500","text":"东营市"},{"value":"370600","text":"烟台市"},{"value":"370700","text":"潍坊市"},{"value":"370800","text":"济宁市"},{"value":"370900","text":"泰安市"},{"value":"371000","text":"威海市"},{"value":"371100","text":"日照市"},{"value":"371200","text":"莱芜市"},{"value":"371300","text":"临沂市"},{"value":"371400","text":"德州市"},{"value":"371500","text":"聊城市"},{"value":"371600","text":"滨州市"},{"value":"371700","text":"菏泽市"}]},{"value":"410000","text":"河南省","children":[{"value":"410100","text":"郑州市"},{"value":"410200","text":"开封市"},{"value":"410300","text":"洛阳市"},{"value":"410400","text":"平顶山市"},{"value":"410500","text":"安阳市"},{"value":"410600","text":"鹤壁市"},{"value":"410700","text":"新乡市"},{"value":"410800","text":"焦作市"},{"value":"410900","text":"濮阳市"},{"value":"411000","text":"许昌市"},{"value":"411100","text":"漯河市"},{"value":"411200","text":"三门峡市"},{"value":"411300","text":"南阳市"},{"value":"411400","text":"商丘市"},{"value":"411500","text":"信阳市"},{"value":"411600","text":"周口市"},{"value":"411700","text":"驻马店市"}]},{"value":"420000","text":"湖北省","children":[{"value":"420100","text":"武汉市"},{"value":"420200","text":"黄石市"},{"value":"420300","text":"十堰市"},{"value":"420500","text":"宜昌市"},{"value":"420600","text":"襄阳市"},{"value":"420700","text":"鄂州市"},{"value":"420800","text":"荆门市"},{"value":"420900","text":"孝感市"},{"value":"421000","text":"荆州市"},{"value":"421100","text":"黄冈市"},{"value":"421200","text":"咸宁市"},{"value":"421300","text":"随州市"},{"value":"422800","text":"恩施土家族苗族自治州"}]},{"value":"430000","text":"湖南省","children":[{"value":"430100","text":"长沙市"},{"value":"430200","text":"株洲市"},{"value":"430300","text":"湘潭市"},{"value":"430400","text":"衡阳市"},{"value":"430500","text":"邵阳市"},{"value":"430600","text":"岳阳市"},{"value":"430700","text":"常德市"},{"value":"430800","text":"张家界市"},{"value":"430900","text":"益阳市"},{"value":"431000","text":"郴州市"},{"value":"431100","text":"永州市"},{"value":"431200","text":"怀化市"},{"value":"431300","text":"娄底市"},{"value":"433100","text":"湘西土家族苗族自治州"}]},{"value":"440000","text":"广东省","children":[{"value":"440100","text":"广州市"},{"value":"440200","text":"韶关市"},{"value":"440300","text":"深圳市"},{"value":"440400","text":"珠海市"},{"value":"440500","text":"汕头市"},{"value":"440600","text":"佛山市"},{"value":"440700","text":"江门市"},{"value":"440800","text":"湛江市"},{"value":"440900","text":"茂名市"},{"value":"441200","text":"肇庆市"},{"value":"441300","text":"惠州市"},{"value":"441400","text":"梅州市"},{"value":"441500","text":"汕尾市"},{"value":"441600","text":"河源市"},{"value":"441700","text":"阳江市"},{"value":"441800","text":"清远市"},{"value":"441900","text":"东莞市"},{"value":"442000","text":"中山市"},{"value":"0","text":"东沙群岛"},{"value":"445100","text":"潮州市"},{"value":"445200","text":"揭阳市"},{"value":"445300","text":"云浮市"}]},{"value":"450000","text":"广西壮族自治区","children":[{"value":"450100","text":"南宁市"},{"value":"450200","text":"柳州市"},{"value":"450300","text":"桂林市"},{"value":"450400","text":"梧州市"},{"value":"450500","text":"北海市"},{"value":"450600","text":"防城港市"},{"value":"450700","text":"钦州市"},{"value":"450800","text":"贵港市"},{"value":"450900","text":"玉林市"},{"value":"451000","text":"百色市"},{"value":"451100","text":"贺州市"},{"value":"451200","text":"河池市"},{"value":"451300","text":"来宾市"},{"value":"451400","text":"崇左市"}]},{"value":"460000","text":"海南省","children":[{"value":"460100","text":"海口市"},{"value":"460200","text":"三亚市"},{"value":"460300","text":"三沙市"}]},{"value":"500100","text":"重庆","children":[{"value":"500100","text":"重庆市"}]},{"value":"510000","text":"四川省","children":[{"value":"510100","text":"成都市"},{"value":"510300","text":"自贡市"},{"value":"510400","text":"攀枝花市"},{"value":"510500","text":"泸州市"},{"value":"510600","text":"德阳市"},{"value":"510700","text":"绵阳市"},{"value":"510800","text":"广元市"},{"value":"510900","text":"遂宁市"},{"value":"511000","text":"内江市"},{"value":"511100","text":"乐山市"},{"value":"511300","text":"南充市"},{"value":"511400","text":"眉山市"},{"value":"511500","text":"宜宾市"},{"value":"511600","text":"广安市"},{"value":"511700","text":"达州市"},{"value":"511800","text":"雅安市"},{"value":"511900","text":"巴中市"},{"value":"512000","text":"资阳市"},{"value":"513200","text":"阿坝藏族羌族自治州"},{"value":"513300","text":"甘孜藏族自治州"},{"value":"513400","text":"凉山彝族自治州"}]},{"value":"520000","text":"贵州省","children":[{"value":"520100","text":"贵阳市"},{"value":"520200","text":"六盘水市"},{"value":"520300","text":"遵义市"},{"value":"520400","text":"安顺市"},{"value":"520600","text":"铜仁市"},{"value":"522300","text":"黔西南布依族苗族自治州"},{"value":"520500","text":"毕节市"},{"value":"522600","text":"黔东南苗族侗族自治州"},{"value":"522700","text":"黔南布依族苗族自治州"}]},{"value":"530000","text":"云南省","children":[{"value":"530100","text":"昆明市"},{"value":"530300","text":"曲靖市"},{"value":"530400","text":"玉溪市"},{"value":"530500","text":"保山市"},{"value":"530600","text":"昭通市"},{"value":"530700","text":"丽江市"},{"value":"530800","text":"普洱市"},{"value":"530900","text":"临沧市"},{"value":"532300","text":"楚雄彝族自治州"},{"value":"532500","text":"红河哈尼族彝族自治州"},{"value":"532600","text":"文山壮族苗族自治州"},{"value":"532800","text":"西双版纳傣族自治州"},{"value":"532900","text":"大理白族自治州"},{"value":"533100","text":"德宏傣族景颇族自治州"},{"value":"533300","text":"怒江傈僳族自治州"},{"value":"533400","text":"迪庆藏族自治州"}]},{"value":"540000","text":"西藏自治区","children":[{"value":"540100","text":"拉萨市"},{"value":"540300","text":"昌都市"},{"value":"540500","text":"山南地区"},{"value":"540200","text":"日喀则市"},{"value":"542400","text":"那曲地区"},{"value":"542500","text":"阿里地区"},{"value":"540400","text":"林芝市"}]},{"value":"610000","text":"陕西省","children":[{"value":"610100","text":"西安市"},{"value":"610200","text":"铜川市"},{"value":"610300","text":"宝鸡市"},{"value":"610400","text":"咸阳市"},{"value":"610500","text":"渭南市"},{"value":"610600","text":"延安市"},{"value":"610700","text":"汉中市"},{"value":"610800","text":"榆林市"},{"value":"610900","text":"安康市"},{"value":"611000","text":"商洛市"}]},{"value":"620000","text":"甘肃省","children":[{"value":"620100","text":"兰州市"},{"value":"620200","text":"嘉峪关市"},{"value":"620300","text":"金昌市"},{"value":"620400","text":"白银市"},{"value":"620500","text":"天水市"},{"value":"620600","text":"武威市"},{"value":"620700","text":"张掖市"},{"value":"620800","text":"平凉市"},{"value":"620900","text":"酒泉市"},{"value":"621000","text":"庆阳市"},{"value":"621100","text":"定西市"},{"value":"621200","text":"陇南市"},{"value":"622900","text":"临夏回族自治州"},{"value":"623000","text":"甘南藏族自治州"}]},{"value":"630000","text":"青海省","children":[{"value":"630100","text":"西宁市"},{"value":"630200","text":"海东市"},{"value":"632200","text":"海北藏族自治州"},{"value":"632300","text":"黄南藏族自治州"},{"value":"460000","text":"海南藏族自治州"},{"value":"632600","text":"果洛藏族自治州"},{"value":"632700","text":"玉树藏族自治州"},{"value":"632800","text":"海西蒙古族藏族自治州"}]},{"value":"640000","text":"宁夏回族自治区","children":[{"value":"640100","text":"银川市"},{"value":"640200","text":"石嘴山市"},{"value":"640300","text":"吴忠市"},{"value":"640400","text":"固原市"},{"value":"640500","text":"中卫市"}]},{"value":"650000","text":"新疆维吾尔自治区","children":[{"value":"650100","text":"乌鲁木齐市"},{"value":"650200","text":"克拉玛依市"},{"value":"650400","text":"吐鲁番市"},{"value":"650500","text":"哈密地区"},{"value":"652300","text":"昌吉回族自治州"},{"value":"652700","text":"博尔塔拉蒙古自治州"},{"value":"652800","text":"巴音郭楞蒙古自治州"},{"value":"652900","text":"阿克苏地区"},{"value":"653000","text":"克孜勒苏柯尔克孜自治州"},{"value":"653100","text":"喀什地区"},{"value":"653200","text":"和田地区"},{"value":"654000","text":"伊犁哈萨克自治州"},{"value":"654200","text":"塔城地区"},{"value":"654300","text":"阿勒泰地区"}]},{"value":"0","text":"台湾","children":[{"value":"0","text":"台北市"},{"value":"0","text":"高雄市"},{"value":"0","text":"台南市"},{"value":"0","text":"台中市"},{"value":"0","text":"金门县"},{"value":"0","text":"南投县"},{"value":"0","text":"基隆市"},{"value":"0","text":"新竹市"},{"value":"0","text":"嘉义市"},{"value":"0","text":"新北市"},{"value":"0","text":"宜兰县"},{"value":"0","text":"新竹县"},{"value":"0","text":"桃园县"},{"value":"0","text":"苗栗县"},{"value":"0","text":"彰化县"},{"value":"0","text":"嘉义县"},{"value":"0","text":"云林县"},{"value":"0","text":"屏东县"},{"value":"0","text":"台东县"},{"value":"0","text":"花莲县"},{"value":"0","text":"澎湖县"},{"value":"0","text":"连江县"}]},{"value":"0","text":"香港特别行政区","children":[{"value":"0","text":"香港岛"},{"value":"0","text":"九龙"},{"value":"0","text":"新界"}]},{"value":"0","text":"澳门特别行政区","children":[{"value":"0","text":"澳门半岛"},{"value":"0","text":"离岛"}]},{"value":"0","text":"海外","children":[{"value":"0","text":"海外"}]}]}';
        return json_decode($city,true);

        $province = Db::name('region')->where('region_type',1)->select();

        foreach ($province as $k => $v) {

            $city = Db::name('region')->where('region_type',2)->where('region_pid',$v['region_id'])->select();
            
            foreach ($city as $key => $value) {
                $children_city[$key]['value'] = $value['region_adcode'];
                $children_city[$key]['text'] = $value['region_name'];
            }
            $list[$k]['value'] = $v['region_adcode'];
            $list[$k]['text'] = $v['region_name'];
            $list[$k]['children']=$children_city;
            unset($children_city);

        }
        $list = array_values($list);
        // $list = json_encode($list,JSON_UNESCAPED_UNICODE);
        return ['error'=>0,'msg'=>'成功','data'=>$list];
    }


    private  function getSign($data,$openKey){
       
        return md5($this->formatBizQueryParaMap($data,false).$openKey);
    }
    private function formatBizQueryParaMap($paraMap, $urlencode){

        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($v != null) {
                if($urlencode){
                    $v = urlencode($v);
                }
                if($k=='repayPlanList')
                {
                        // $buff = '';
                    if(count($v)>1)
                    {
                        $buff .= $k . "=[";
                        foreach ($v as $kk => $vv) {
                            $buff .= "{";
                            foreach ($vv as $key => $value) {
                                if($value != null) {
                                    $buff .= $key . "=" . $value.', ';
                                }
                            }
                            $buff    = rtrim($buff, ", ");
                            $buff .= "}, ";
                        }
                        $buff    = rtrim($buff, ", ");
                        $buff .= "]";

                    }
                    else
                    {
                        // $buff = '';
                        $buff .= $k . "=[{";
                        foreach ($v as $kk => $vv) {
                            foreach ($vv as $key => $value) {
                                if($value != null) {
                                    $buff .= $key . "=" . $value.', ';
                                }
                            }
                        }
                        $buff    = rtrim($buff, ", ");
                        $buff .= "}]";
                    }
                    /*dump($buff);


                    $buff = '';

                    $v = json_encode($v);
                    $v = str_replace('{"',"{",$v);
                    $v = str_replace('":"',"=",$v);
                    $v = str_replace('":',"=",$v);
                    $v = str_replace('","',", ",$v);
                    $v = str_replace(',"',", ",$v);
                    // $v = str_replace(', ','',$v);
                    $buff .= $k . "=" . $v;
                    dump($buff);*/
                }
                else
                {
                    $buff .= $k . "=" . $v;
                }
            }
        }
        return $buff;
    }
    public function verify($data,$openKey)
    {
        $signStr = $data['sign'];
        unset($data['sign']);
        $sign = $this->getSign($data,$openKey);
        //开始验证签名//或者直接使用checkSign 
        if($signStr != $sign){
            return ['error'=>1,'msg'=>'验签失败'];
        }
        return ['error'=>0,'msg'=>'success'];
    }
    /**
     * 支付post+json提交
     * @author QQ
     */
    function post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }

}