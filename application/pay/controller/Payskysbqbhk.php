<?php
// +----------------------------------------------------------------------
// | 银生宝钱包版 [ PERFECT SURROGATE SYSTEM ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 All rights reserved.
// +----------------------------------------------------------------------
// | Author: grass <1251700162@qq.com>
// +----------------------------------------------------------------------
//accountId:accountId|openKey:openKey|payurl:http://test.unspay.com/unspay-creditCardRepayment-business/
//http://double.unspay.com/credit-wallet/

namespace app\pay\controller;
use think\Controller;
use think\Db;

class Payskysbqbhk extends Base
{
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
        $payment_public = $this->payment_public($pay_id,$uid,'hkyqb_',1,1);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }

        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        // $payment_card = $payment_public['payment_card'];
        $user = $payment_public['user'];
        $user_rate = $payment_public['user_rate_hk'];
        $card = $payment_public['card'];

        $hk_pay_id = Db::name('payment')->where('payment_controller','Payhkysbqb')->where('payment_use',1)->value('payment_id');
        if(empty($hk_pay_id))
        {
            return ['error'=>1,'msg'=>'商户注册失败'];
        }
        $hk_user = Db::name('payment_user')->where('user_pay_id',$hk_pay_id)->where('user_uid',$uid)->where('user_state',1)->find();
        if($hk_user)
        {
            unset($data);
            $data['user_number'] = $hk_user['user_number'];
            $data['user_name'] = $hk_user['user_name'];
            $data['user_shortname'] = $hk_user['user_shortname'];
            $data['user_merchant'] = $hk_user['user_merchant'];
            $data['user_state'] = $hk_user['user_state'];
            $data['user_time'] = time();
            $data['user_msg'] = '同步还款报件';
            Db::name('payment_user')->where('user_id',$payment_user['user_id'])->update($data);
            unset($data);
        }
        else
        {
            Controller('pay/Payhkysbqb')->register($hk_pay_id,$uid);
            $this->register($pay_id,$uid);
        }
        return ['error'=>0,'msg'=>'同步报件'];
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
        return ['error'=>1,'msg'=>'接口未开放'];
        $this->pay_logs($data,$res,'Payskysbqbhk','update_fee');
    }
    
    /**
     * 查询子商户余额
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function register_balance_query($pay_id='',$uid='')
    {
        $this->pay_logs($data,$res,'Payskysbqbhk','balance_query');
        return ['error'=>1,'msg'=>'接口未开放'];
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
            return ['error'=>1,'msg'=>'通道错误,错误代码['.$pay_id.'-'.$uid.'-'.$cid.']'];
        }

        $payment_public = $this->payment_public($pay_id,$uid,'',2,1);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];
        $card = $payment_public['card'];

        //绑定储蓄卡
        $payment_card = Db::name('payment_card')->where('card_cid',$card['card_id'])->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if(empty($payment_card))
        {
            $payment_card['card_uid'] = $uid;
            $payment_card['card_pay_id'] = $pay_id;
            $payment_card['card_pay_uid'] = $payment_user['user_id'];
            $payment_card['card_cid'] = $card['card_id'];
            $payment_card['card_type'] = 2;
            $payment_card['card_no'] = substr($card['card_no'],-4,4);
            $payment_card['card_state'] = 3;
            $payment_card['card_time'] = time();
            $payment_card['card_id'] = Db::name('payment_card')->insertGetId($payment_card);
        }
        if($payment_card['card_state'] != 1)
        {
            //绑卡查询
            $bind_card_query = $this->bind_card_query($pay_id,$uid,$card['card_id']);
            if($bind_card_query['error']==0)
            {
                return $bind_card_query;
            }

            $data['accountId']= $payment_config['accountId']; // 商户编号
            $data['memberId'] = $payment_user['user_number'];//平台会员号
            $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
            $data['name'] = $card['card_name'];//用户姓名
            $data['certNo'] = $user['user_idcard'];//证件号
            $data['cardNo'] = $card['card_no'];//银行卡号
            $data['phone'] = $card['card_phone'];//手机号
            $data['isSendMsg'] = '2';//默认为 2，是否需要发短信（1=是，2= 否） 

            //公共参数
            $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
            $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $post_url = $payment_config['payurl'].'bind/intfBind';
            $this->pay_logs($data,$post_url,'Payskysbqbhk','bind_card');
            $res = $this->post_json($post_url,$json_data);
            $this->pay_logs($json_data,$res,'Payskysbqbhk','bind_card');
            $obj = json_decode($res,true);
            if($obj['result_code']!='0000')
            {
                $this->payment_bind_card($payment_card,2,$obj['result_msg'],'','');
                return ['error'=>1,'msg'=>$obj['result_msg']];
            }
            $obj = $obj['data'];
            $this->payment_bind_card($payment_card,1,'绑卡成功',$obj['token'],$obj['responseOrderNo']);
            unset($data);
            unset($obj);
            // return ['error'=>0,'msg'=>'绑卡成功'];
        }






        $card = Db::name('user_card')->where('card_uid',$uid)->where('card_id',$cid)->where('card_type',1)->find();

        if(empty($card))
        {
            return ['error'=>1,'msg'=>'信用卡不存在'];
        }
        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if(empty($payment_card))
        {
            $payment_card['card_uid'] = $uid;
            $payment_card['card_pay_id'] = $pay_id;
            $payment_card['card_pay_uid'] = $payment_user['user_id'];
            $payment_card['card_cid'] = $cid;
            $payment_card['card_type'] = 1;
            $payment_card['card_no'] = substr($card['card_no'],-4,4);
            $payment_card['card_state'] = 3;
            $payment_card['card_time'] = time();
            $payment_card['card_id'] = Db::name('payment_card')->insertGetId($payment_card);
        }
        elseif($payment_card['card_state']== 1)
        {
            return ['error'=>1,'msg'=>'绑卡成功,请继续操作'];
        }
        $hk_pay_id = Db::name('payment')->where('payment_controller','Payhkysbqb')->where('payment_use',1)->value('payment_id');
        if($hk_pay_id)
        {
            $hk_card = Db::name('payment_card')->where('card_pay_id',$hk_pay_id)->where('card_uid',$uid)->where('card_cid',$cid)->where('card_state',1)->find();
            if($hk_card)
            {
                unset($data);
                $data['card_pay_cid'] = $hk_card['card_pay_cid'];
                $data['card_form_no'] = $hk_card['card_form_no'];
                $data['card_state'] = $hk_card['card_state'];
                $data['card_time'] = time();
                $data['card_msg'] = '同步还款绑卡';
                Db::name('payment_card')->where('card_id',$payment_card['card_id'])->update($data);
                unset($data);
                return ['error'=>0,'msg'=>'绑卡成功'];
            }
            else
            {
                Controller('pay/Payhkysbqb')->register($hk_pay_id,$uid);
                return Controller('pay/Payhkysbqb')->bind_card($hk_pay_id,$uid,$cid,$type);
                // Controller('pay/Payskysbqbhk')->register($hk_pay_id,$uid);
                // return Controller('pay/Payskysbqbhk')->bind_card($hk_pay_id,$uid,$cid,$type);
            }
        }

    }

    /**
     * 绑卡验证码 验证
     * @Author tw
     * @Date   2018-09-30
     * @return [type]     [description]
     */
    public function bind_smscode($pay_id='',$uid='',$cid='',$smscode='',$type='2')
    {
        if(empty($pay_id) || empty($uid) || empty($cid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        if(empty($smscode))
        {
            return ['error'=>1,'msg'=>'请填写验证码'];
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

        $card = Db::name('user_card')->where('card_uid',$uid)->where('card_id',$cid)->where('card_type',1)->find();
        if(empty($card))
        {
            return ['error'=>1,'msg'=>'支付卡不存在'];
        }

        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if(empty($payment_card))
        {
            return ['error'=>1,'msg'=>'请先绑定卡片'];
        }
        elseif($payment_card['card_state']==1)
        {
            return ['error'=>1,'msg'=>'该卡已绑定'];
        }
        

        $hk_pay_id = Db::name('payment')->where('payment_controller','Payhkysbqb')->where('payment_use',1)->value('payment_id');
        if(empty($hk_pay_id))
        {
            return ['error'=>1,'msg'=>'通道获取错误,错误代码['.$pay_id.'-'.$uid.'-'.$cid.']'];
        }
        $hk_card = Db::name('payment_card')->where('card_pay_id',$hk_pay_id)->where('card_uid',$uid)->where('card_cid',$cid)->where('card_state',1)->find();
        if($hk_card)
        {
            unset($data);
            $data['card_pay_cid'] = $hk_card['card_pay_cid'];
            $data['card_form_no'] = $hk_card['card_form_no'];
            $data['card_state'] = $hk_card['card_state'];
            $data['card_time'] = time();
            $data['card_msg'] = '同步还款绑卡';
            Db::name('payment_card')->where('card_id',$payment_card['card_id'])->update($data);
            unset($data);
            return ['error'=>0,'msg'=>'绑卡成功'];
        }
        else
        {
            $hk_bind_smscode =  Controller('pay/Payhkysbqb')->bind_smscode($hk_pay_id,$uid,$cid,$smscode);
            if($hk_bind_smscode['error'] != 0)
            {
                return $hk_bind_smscode;
            }

            return $this->bind_smscode($pay_id,$uid,$cid,$smscode);
        }
    }

    /**
     * 发验证码
     * @Author tw
     * @Date   2018-10-11
     */
    public function bind_retry_smscode($pay_id='',$uid='',$cid='',$type='2')
    {
        $pay_id = Db::name('payment')->where('payment_controller','Payhkysbqb')->where('payment_use',1)->value('payment_id');
        return Controller('pay/Payhkysbqb')->bind_retry_smscode($pay_id,$uid,$cid);
    }
    /**
     * 绑卡查询
     * @Author tw
     * @param  string $pay_id [description]
     * @param  string $uid    [description]
     * @param  string $cid    [description]
     * @return [type]         [description]
     */
    public function bind_card_query($pay_id='',$uid='',$cid='')
    {
        if(empty($pay_id) || empty($uid) || empty($cid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }

        $payment_public = $this->payment_public($pay_id,$uid,'',2,1);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];

        $payment_card = Db::name('payment_card')->where('card_uid',$uid)->where('card_pay_id',$pay_id)->where('card_cid',$cid)->find();

        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'bind/queryCardInfo',$json_data);
        $obj = json_decode($res,true);

        $this->pay_logs($json_data,$res,'Payskysbqbhk','bind_card_query');
        if($obj['result_code']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }
        $list = $obj['data']['cardInfos'];
        foreach ($list as $key => $value) {
            if ($payment_card['card_no']==$value['cardNo']) {
                $this->payment_bind_card($payment_card,1,'绑卡成功',$value['token'],$value['signNo']);
                return ['error'=>0,'msg'=>'绑卡成功'];
            }
        }
        return ['error'=>1,'msg'=>'未绑定'];

    }
    /**
     * 解除绑卡信用卡
     * @Author tw
     * @Date   2018-09-30
     * @return [type]     [description]
     */
    public function unbind_card($pay_id='',$uid='',$cid='')
    {

        if(empty($pay_id) || empty($uid) || empty($cid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        $payment_public = $this->payment_public($pay_id,$uid,'',2,1);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];

        // $card = Db::name('user_card')->where('card_uid',$uid)->where('card_id',$cid)->where('card_type',1)->find();
        // if(empty($card))
        // {
        //     return ['error'=>1,'msg'=>'支付卡不存在'];
        // }
        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if($payment_card['card_state']!=1)
        {
            $this->payment_unbind_card($payment_card);
            return ['error'=>0,'msg'=>'解绑成功'];
        }
        elseif ($payment_card['card_type']==2) {
            $data['accountId']= $payment_config['accountId']; // 商户编号
            $data['memberId'] = $payment_user['user_number'];//平台会员号
            $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
            $data['token'] = $payment_card['card_pay_cid'];
            //公共参数
            $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
            $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
            $res = $this->post_json($payment_config['payurl'].'bind/intfUnbind',$json_data);
            $obj = json_decode($res,true);
            $this->pay_logs($json_data,$res,'Payskysbqbhk','unbind_card');

            if($obj['result_code']!='0000')
            {
                return ['error'=>1,'msg'=>$obj['result_msg']];
            }
            $obj = $obj['data'];
            switch ($obj['unbindCode']) {
                case '1030':
                    # 解绑成功
                    $this->payment_unbind_card($payment_card);
                    return ['error'=>0,'msg'=>$obj['unbindMsg']];
                    break;
                case '1031':
                    # 解绑失败
                return ['error'=>1,'msg'=>$obj['unbindMsg']];
                    break;
                default:
                    return ['error'=>1,'msg'=>$obj['unbindMsg']];
                    break;
            }
            
        }

        $this->payment_unbind_card($payment_card);
        return ['error'=>0,'msg'=>'解绑成功'];

    }

    /**
     * 支付交易
     * @Author tw
     * @Date   2018-10-08
     * @return [type]     [description]
     */
    public function pay($pay_id='',$uid='',$cid='',$id='')
    {

        if(empty($pay_id) || empty($uid) || empty($cid) || empty($id))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        $pay_records = Db::name('pay_records')->where('records_id',$id)->where('records_uid',$uid)->where('records_pay_cid',$cid)->find();
        if(empty($pay_records))
        {
            return ['error'=>1,'msg'=>'订单错误'];
        }
        elseif($pay_records['records_state']!=0)
        {
            return ['error'=>1,'msg'=>'请重新提交支付'];
        }
        $payment_public = $this->payment_public($pay_id,$uid,'',2,2);
        
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];
        $user_rate_sk = $payment_public['user_rate_sk'];
        $card = Db::name('user_card')->where('card_uid',$uid)->where('card_id',$cid)->where('card_blocked',0)->where('card_type',1)->find();
        if(empty($card))
        {
            return ['error'=>1,'msg'=>'支付卡不存在'];
        }
        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();

        if($payment_card['card_state']!=1)
        {
            return ['error'=>1,'msg'=>'该卡未绑定,错误代码['.$pay_id.'-'.$uid.'-'.$cid.']'];
        }
        $region = explode('-', $pay_records['records_region']);



        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['orderNo'] = $pay_records['records_form_no'];//订单号 
        $data['amount'] = (string)(($pay_records['records_money']));//金额
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        $data['deductCardToken'] = $payment_card['card_pay_cid'];//扣款卡授权码
        $data['profitScale'] = (string)round((($pay_records['records_rate'] - $payment['payment_rate'])*100),2);//分润金比例
        $data['profitSingleFee'] = (string)($pay_records['records_close_rate'] - $payment['payment_close_fee']);//分润金单笔
        $data['signNo'] = $payment_card['card_form_no'];//协议号
        $data['purpose'] = $pay_records['records_form_no'];//目的
        $data['responseUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysbqbhk/pay_notify';//扣款结果通知地址
        $data['version'] = '1.0.1';

        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = request_post_urlencode($payment_config['payurl'].'payInterface/pay',$data);
        $obj = json_decode($res,true);
        $this->pay_logs($data,$res,'Payskysbqbhk','pay_kj');


        //记录
        $log['log_pay_id'] = $pay_id;
        $log['log_pay_records_id'] = $id;
        $log['log_uid'] = $uid;
        $log['log_cid'] = $cid;
        $log['log_no'] = $data['orderNum'];
        $log['log_form_no'] = $data['orderNum'];
        $log['log_type'] = 1;//'支付';
        $log['log_body'] = json_encode($data,JSON_UNESCAPED_UNICODE);
        $log['log_result'] = $res;
        $log['log_time'] = time();
        Db::name('pay_records_log')->insert($log);

        if($obj['result_code']!='0000')
        {
            $this->payment_kj($id,$uid,2,'','',$obj['result_msg'],'');
            return ['error'=>'1','msg'=>$obj['result_msg']];
        }
        $obj = $obj['data'];
        switch ($obj['status']) {
            case '00':
                # 交易成功
                $this->payment_kj($id,$uid,1,'','',$obj['desc'],'');
                return ['error'=>'0','msg'=>$obj['desc']];
                break;
            case '10':
                # 处理中
                $this->payment_kj($id,$uid,3,'','',$obj['desc'],'');
                return ['error'=>'0','msg'=>$obj['desc']];
                break;
            case '20':
                # 交易失败
                $this->payment_kj($id,$uid,2,'','',$obj['desc'],'');
                return ['error'=>'1','msg'=>$obj['desc']];
                break;
            
            default:
                # code...
                break;
        }
    }
    /**
     * 支付扣款 回调
     * @Author tw
     * @return [type] [description]
     */
    public function pay_notify()
    {
        $post = $obj = input('post.');
        $this->notify_logs($post);
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }

        $pay_records = Db::name('pay_records')->where('records_form_no',$post['orderNo'])->find();
        if(empty($pay_records))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        elseif($pay_records['records_state']!=3)
        {
            return 'SUCCESS';
        }
        if($obj['result_code']=='0000')
        {
            $pay_id = $pay_records['records_pay_id'];
            $uid = $pay_records['records_uid'];
            $cid = $pay_records['records_pay_cid'];
            $id = $pay_records['records_id'];
            $this->pay_df($pay_id,$uid,$cid,$id);
        }
        else
        {
            $this->pay_records($pay_records['records_id'],$pay_records['records_uid'],2,'');
        }
        echo 'success';
    }

    /**
     * 收款订单 代付
     * @Author tw
     * @Date   2018-10-24
     * @param  string     $pay_id [description]
     * @param  string     $uid    [description]
     * @param  string     $cid    [description]
     * @param  string     $id     [description]
     * @return [type]             [description]
     */
    public function pay_df($pay_id='',$uid='',$cid='',$id='')
    {
        if(empty($pay_id) || empty($uid) || empty($cid) || empty($id))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        $pay_records = Db::name('pay_records')->where('records_id',$id)->where('records_uid',$uid)->where('records_pay_cid',$cid)->find();
        if(empty($pay_records) || $pay_records['records_state']!=3)
        {
            return ['error'=>1,'msg'=>'订单错误'];
        }
        $cid = $pay_records['records_cid'];
        $payment_public = $this->payment_public($pay_id,$uid,'',2,2);

        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];


        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();

        if($payment_card['card_state']!=1)
        {
            return ['error'=>1,'msg'=>'该卡未绑定'];
        }

        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['orderNo'] = $pay_records['records_form_number']?$pay_records['records_form_number'].'F':$pay_records['records_form_no'].'F'; //订单号-唯一
        $data['amount'] = (string)(($pay_records['records_amount'] + $payment['payment_close_fee']));//金额
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        $data['repayToken'] = $payment_card['card_pay_cid'];//收款卡授权码
        // $data['profitScale'] = (string)round((($pay_records['records_rate'] - $payment['payment_rate'])*100),2);//分润金比例
        // $data['profitSingleFee'] = (string)($pay_records['records_close_rate']-$payment['payment_close_fee']);//分润金单笔
        $data['profitScale'] = '0';//分润金比例
        $data['profitSingleFee'] = '0';//分润金单笔
        $data['purpose'] = $pay_records['records_form_no'];//目的
        $data['responseUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysbqbhk/df_notify';//扣款结果通知地址
        
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $url = $payment_config['payurl'].'creditWalletWithdraw/withdraw';
        $res = $this->post_json($url,$json_data);
        $obj = json_decode($res,true);
        $this->pay_logs($json_data,$url,'Payskysbqbhk','pay_df');
        $this->pay_logs($data,$res,'Payskysbqbhk','pay_df');

        if($obj['result_code']!='0000')
        {
            if($obj['result_code']=='2053')
            {
                $balance = $this->balance_query($pay_records);
                if($balance['error']==0)
                {
                    $obj['result_msg'] = $obj['result_msg'] . '余额:' .$balance['amount'];
                    //Db::name('pay_records')->where('records_id',$pay_records['records_id'])->update(['records_amount'=>$balance['amount']-$payment['payment_close_fee']]);
                    //$this->payment_kj($pay_records['records_id'],$uid,3,'',$data['orderNo'],$obj['result_msg'],'','');
                    //return ['error'=>1,'msg'=>$obj['result_msg']];
                }
            }
            $this->payment_kj($pay_records['records_id'],$uid,5,'',$data['orderNo'],$obj['result_msg'],'','');
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }
        $obj = $obj['data'];
        switch ($obj['status']) {
            case '0000':
                # 交易成功
                $this->payment_kj($pay_records['records_id'],$uid,1,'',$data['orderNo'],'交易成功','','');
                return ['error'=>0,'msg'=>$obj['desc']];
                break;
            case '9000':
                # 处理中
                $this->payment_kj($pay_records['records_id'],$uid,4,'',$data['orderNo'],'代付处理中','','');
                return ['error'=>0,'msg'=>$obj['desc']];
                break;
            case '9999':
                # 交易失败
                $this->payment_kj($pay_records['records_id'],$uid,5,'',$data['orderNo'],$obj['result_msg'],'','');
                return ['error'=>1,'msg'=>$obj['desc']];
                break;
            
            default:
                return ['error'=>1,'msg'=>$obj['desc']];
                break;
        }

    }

    /**
     * 代付补单
     * @Author tw
     * @param  string $pay_records [description]
     * @return [type]              [description]
     */
    public function order_db($pay_records='')
    {
        if($pay_records['records_state']!=5)
        {
            return ['error'=>1,'msg'=>'只有代付失败订单允许补单'];
        }

        $pay_id = $pay_records['records_pay_id'];
        $uid = $pay_records['records_uid'];
        $cid = $pay_records['records_pay_cid'];
        $id = $pay_records['records_id'];
        $this->payment_kj($pay_records['records_id'],$uid,3,'','','代付补单处理中','','');
        return $this->pay_df($pay_id,$uid,$cid,$id);

    }

    /**
     * 付款结果通知
     * @Author tw
     * @return [type] [description]
     */
    public function df_notify()
    {
        $post = $obj = input('post.');
        $this->notify_logs($post);
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $pay_records = Db::name('pay_records')->where('records_form_number',$post['orderNo'])->find();
        if(empty($pay_records))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        elseif($pay_records['records_state']!=4)
        {
            return 'SUCCESS';
        }
        if($obj['status']=='0000')
        {
            $this->pay_records($pay_records['records_id'],$pay_records['records_uid'],1);
        }
        else
        {
            $this->pay_records($pay_records['records_id'],$pay_records['records_uid'],5);
        }
        echo 'SUCCESS';
    }

    /**
     * 交易状态查询
     * @Author tw
     * @Date   2018-10-20
     */
    public function query_order_status($pay_records='')
    {
        switch($pay_records['records_state']){
            case '1':
                # 交易成功
                return $this->pay_state_kj($pay_records);
                break;
            case '2':
                # 支付失败
                return $this->pay_state_kj($pay_records);
            case '3':
                # 支付中
                return $this->pay_state_kj($pay_records);
                break;
            case '4':
                # 代付中
                return $this->pay_state_df($pay_records);
            case '5':
                # 代付失败
                return $this->pay_state_df($pay_records);
                break;
            
            default:
                return ['error'=>1,'msg'=>'未知状态'];
                break;
        }

    }
    /**
     * 支付状态查询
     * @Author tw
     * @Date   2018-10-23
     * @param  [type]     $plan [description]
     * @return [type]           [description]
     */
    public function pay_state_kj($order)
    {
        $pay_id = $order['records_pay_id'];//支付通道id
        $uid = $order['records_uid'];//用户id

        if(empty($pay_id) || empty($uid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        $payment_public = $this->payment_public($pay_id,$uid,'',2,2);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];


        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['orderNo'] = $order['records_form_no'];//订单号-唯一
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'payInterface/queryOrder',$json_data);
        $obj = json_decode($res,true);
        $this->pay_logs($data,$res,'Payskysbqbhk','pay_state_kj');
        if($obj['result_code']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }

        $obj = $obj['data'];
        switch ($obj['status']) {
            case '00':
                # 交易成功
                // $this->pay_records($pay_records['records_id'],$pay_records['records_uid'],1);
                $cid = $order['records_pay_cid'];
                $id = $order['records_id'];
                $this->pay_df($pay_id,$uid,$cid,$id);
                return ['error'=>0,'msg'=>'支付成功'];
                break;
            case '10':
                # 处理中
                return ['error'=>0,'msg'=>'支付处理中'];
                break;
            case '20':
                # 交易失败
                $this->payment_kj($order['records_id'],$uid,2,'','',$obj['desc'],'','');
                return ['error'=>1,'msg'=>'支付失败'];
                break;
            default:
                return ['error'=>1,'msg'=>$obj['desc']];
                break;
        }
    }
    /**
     * 代付状态查询
     * @Author tw
     * @Date   2018-10-23
     * @param  [type]     $order [description]
     * @return [type]           [description]
     */
    public function pay_state_df($order)
    {
        $pay_id = $order['records_pay_id'];//支付通道id
        $uid = $order['records_uid'];//用户id

        if(empty($pay_id) || empty($uid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        $payment_public = $this->payment_public($pay_id,$uid,'',2,2);
        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        $user = $payment_public['user'];


        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['orderNo'] = $order['records_form_number'];//订单号 
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'creditWalletWithdraw/queryWithdrawOrder',$json_data);
        $obj = json_decode($res,true);
        $this->pay_logs($data,$res,'Payskysbqbhk','pay_state_df');
        $this->pay_logs($data,$order,'Payskysbqbhk','pay_state_df');
        if($obj['result_code']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }
        $obj = $obj['data'];
        switch ($obj['status']) {
            case '0000':
                # 交易成功
                $this->pay_records($order['records_id'],$order['records_uid'],1);
                return ['error'=>0,'msg'=>'代付成功'];
                break;
            case '9000':
                # 处理中
                return ['error'=>0,'msg'=>'代付处理中'];
                break;
            case '9999':
                # 交易失败
                $this->payment_kj($order['records_id'],$uid,5,'','',$obj['desc'],'','');
                return ['error'=>1,'msg'=>'代付失败'];
                break;
            default:
                return ['error'=>1,'msg'=>$obj['desc']];
                break;
        }
    }



    /**
     * 商户余额
     * @Author tw
     * @Date   2018-10-23
     * @param  [type]     $pay_id [description]
     * @param  [type]     $uid    [description]
     * @param  [type]     $cid    [description]
     * @return [type]             [description]
     */
    public function balance_query($order)
    {

        $pay_id = $order['records_pay_id'];//支付通道id
        $uid = $order['records_uid'];//用户id

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

        $data['accountId']= $payment_config['accountId']; // 商户编号
        // $data['orderNo'] = $order['records_form_no'];//订单号 
        $data['merchantNo']=$payment_user['user_merchant'];//商户号
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        // dump($data);
        // exit();
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'mch/queryMerchantAmount',$json_data);
        $obj = json_decode($res,true);
        $this->pay_logs($data,$res,'Payskysbqbhk','balance_query');
        $this->pay_logs($payment_config['payurl'].'mch/queryMerchantAmount',$obj,'Payskysbqbhk','balance_query');
        if($obj['result_code']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }
        $obj = $obj['data'];
        
        return ['error'=>0,'msg'=>'总额度'.$obj['curAmount'].'可用余额:'.$obj['avaiableAmount'],'amount'=>$obj['avaiableAmount']];

        
    }
    

    /**
     * config json 转字符串[字符串格式：name:张三|time:20180116]
     * @author yan  2018-01-17
     * @return [str] [字符串格式：name:张三|time:20180116
     */
    protected function jsonTostr($configstr){

        $jsonarr =json_decode($configstr);
        $str2 ='';
        if($jsonarr){
            $json_main=array();
            foreach ($jsonarr as $arr1){
                $str =implode(':',$arr1);
                $json_main[]=$str;
            }
            $str2 =implode('|',$json_main);
        }
        return $str2;
    }

    private  function getSign($data,$openKey){
        // echo $this->formatBizQueryParaMap($data,false).'&key='.$openKey;
        // exit();
        return strtoupper(md5($this->formatBizQueryParaMap($data,false).'&key='.$openKey));
    }
    private function formatBizQueryParaMap($paraMap, $urlencode){

        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($v != null) {
                if($urlencode){
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = rtrim($buff, "&");
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