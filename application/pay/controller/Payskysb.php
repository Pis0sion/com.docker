<?php
// +----------------------------------------------------------------------
// | 银生宝 [ PERFECT SURROGATE SYSTEM ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 All rights reserved.
// +----------------------------------------------------------------------
// | Author: grass <1251700162@qq.com>
// +----------------------------------------------------------------------
//accountId:accountId|openKey:openKey|payurl:http://test.unspay.com/unspay-creditCardRepayment-business/
//http://double.unspay.com/unspay-creditCardRepayment-business/
/*
ALTER TABLE `cc_payment_card`   
  ADD COLUMN `card_no` VARCHAR(4) NULL COMMENT '信用卡尾号' AFTER `card_type`;
*/
//INSERT INTO `cc_payment_channel` (`channel_id`, `channel_name`, `channel_bind`, `channel_use`, `channel_time`) VALUES (NULL, '银生宝', '1', '1', '1541209319')
//INSERT INTO `cc_payment` (`payment_id`, `payment_channel_id`, `payment_name`, `payment_bind`, `payment_bind_way`, `payment_rate`, `payment_close_fee`, `payment_type`, `payment_time`, `payment_controller`, `payment_config`, `payment_use`, `payment_day_num`, `payment_num`, `payment_min_money`, `payment_max_money`, `payment_risk_start`, `payment_risk_end`, `paymentst_entime`, `paymentst_money`, `payment_mode`, `payment_pattern`, `payment_paynow`, `payment_que`, `payment_region`, `payment_mcc`) VALUES (NULL, '16', '大额代还', '1', 'web', '0.0045', '0.50', '2', '1541210317', 'Payskysb', '[["accountId","123"],["openKey","123"],["payurl","http://test.unspay.com/unspay-creditCardRepayment-business/"]]', '1', '5', '30', '10.00', '50.00', '1', '10', '0', '999999.00', '2', '2', '1', '11', '0', '0')
namespace app\pay\controller;
use think\Controller;
use think\Db;

class Payskysb extends Base
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

        $hk_pay_id = Db::name('payment')->where('payment_controller','Payhkysb')->where('payment_use',1)->value('payment_id');
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
            Controller('pay/Payhkysb')->register($hk_pay_id,$uid);
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
        $this->pay_logs($data,$res,'Payskysb','update_fee');
    }
    
    /**
     * 商户注册查询
     * @Author tw
     * @param  [type] $uid            [description]
     * @param  [type] $payment_config [description]
     * @param  [type] $payment_user   [description]
     * @param  [type] $user_idcard    [description]
     * @param  [type] $type           [1更改失败状态]
     * @return [type]                 [description]
     */
    public function register_query($uid='',$payment_config='',$payment_user='',$user_idcard='',$type='')
    {


        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'report/queryInfo',$json_data);
        $this->pay_logs($data,$res,'Payskysb','network_query');
        $obj = json_decode($res,true);
        if($obj['result_code']!='0000')
        {
            $this->payment_register($payment_user,2,$obj['result_msg']);
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }
        switch ($obj['aduitCode']) {
            case '1017':
                # 商户会员报件待审核
                $this->payment_register($payment_user,3,$obj['aduitMsg']);
                return ['error'=>1,'msg'=>$obj['aduitMsg']];
                break;
            case '1018':
                # 审核通过
                $this->payment_register($payment_user,1,$obj['aduitMsg'],$obj['merchantNo']);
                return ['error'=>0,'msg'=>$obj['aduitMsg']];
                break;
            case '1019':
                # 审核不通过
                $this->payment_register($payment_user,2,$obj['aduitMsg']);
                return ['error'=>1,'msg'=>$obj['aduitMsg']];
                break;
            
            default:
                return ['error'=>1,'msg'=>$obj['aduitMsg']];
                break;
        }
    }

    /**
     * 查询子商户余额
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function register_balance_query($pay_id='',$uid='')
    {
        $this->pay_logs($data,$res,'Payskysb','balance_query');
    }

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
            // if($bind_card_query['error']==0)
            // {
            //     return $bind_card_query;
            // }
            if($bind_card_query['error']!=0)
            {
                $url = 'http://'.$_SERVER['HTTP_HOST']."/pay/Payskysb/bind_card_web?pay_id=$pay_id&uid=$uid&cid=".$card['card_id']."&type=$type";
                return ['error'=>0,'msg'=>'绑定储蓄卡','url'=>$url];
            }
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
            return ['error'=>1,'msg'=>'信用卡蓄卡绑卡成功,请继续操作'];
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
                return ['error'=>0,'msg'=>'绑卡成功,请继续操作'];
            }
            else
            {
                $url = 'http://'.$_SERVER['HTTP_HOST']."/pay/Payhkysb/bind_card_web?pay_id=$pay_id&uid=$uid&cid=$cid&type=$type";
                return ['error'=>0,'msg'=>'web绑卡','url'=>$url];
            }
        }
        
    }
    /**
     * 绑定信用卡
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function bind_card_web()
    {
        $get = input('get.');
        $pay_id = $get['pay_id'];
        $uid = $get['uid'];
        $cid = $get['cid'];
        if(empty($pay_id) || empty($uid) || empty($cid))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
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
            return json(['error'=>1,'msg'=>'卡已绑定']);
        }


        $bind_card_query = $this->bind_card_query($pay_id,$uid,$cid);
        if($bind_card_query['error']==0)
        {
            return json($bind_card_query);
        }
        /*elseif($payment_card['card_state']== 3 && (time()-strtotime($payment_card['card_time'])) < 10*60 )
        {
            return ['error'=>1,'msg'=>'验证已提交,等待审核，10分钟后重试'];
        }*/
        //暂时不要删除
        //绑卡第一步
        /*$data['accountId'] = $payment_config['accountId'];//商户编号
        $data['isCashOnly'] = '1';//可为空，默认为 2，当前卡是否仅为收款卡(1=是，2=否) 
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        $data['requestNo'] = '';//外部请求号，可为空，如果非空时，必须保证唯一
        $data['responseUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysb/bind_card_notify';//回调地址 
        $data['terminalType'] = 'H5';//可为空，默认为 H5(H5/WEB)
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $this->pay_logs($data,'','Payskysb','bind_card');*/

        //第一步
        /*echo '<form method="post" id="myform" name="myform" action="'.$payment_config['payurl'].'bind/unifiedBind'.'">';
        foreach ($data as $key => $value) {
            echo '<input type="hidden" name="'.$key.'" value="'.urldecode($value).'">';
    
        }
        echo '</form>
            <script type="text/javascript">
                document.myform.submit()
            </script>';*/

        //绑卡第二步
        /*$data['cardNo'] = $card['card_no'];//银行卡号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        $data['name'] = $card['card_name'];//名字
        $data['responseUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysb/bind_card_notify';//回调地址
        $data['isCashOnly'] = '1';//可为空，默认为 2，当前卡是否仅为收款卡(1=是，2=否) 
        $data['requestNo'] = '';//外部请求号，可为空，如果非空时，必须保证唯一
        echo '<form method="post" id="myform" name="myform" action="'.$payment_config['payurl'].'bind/h5bindInfo'.'">';
        foreach ($data as $key => $value) {
            echo '<input type="hidden" name="'.$key.'" value="'.urldecode($value).'">';
    
        }
        echo '</form>
            <script type="text/javascript">
                document.myform.submit()
            </script>';*/
        
        $bank_indo = BankType($card['card_no']);
        $data['name'] = $card['card_name'];//名字
        $data['bankName'] = $bank_indo['showapi_res_body']['bankName'];//信用卡名称
        $data['cardNo'] = $card['card_no'];//银行卡号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        $data['cardType'] = '0'; //1信用卡
        $data['responseUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysb/bind_card_notify';//回调地址
        $data['isCashOnly'] = '1';//可为空，默认为 2，当前卡是否仅为收款卡(1=是，2=否) 
        $data['requestNo'] = '';//外部请求号，可为空，如果非空时，必须保证唯一
        $data['idCardNo'] = $user['user_idcard'];//身份证
        $data['validityPeriod'] = $card['card_exp_date'];//有效期 月年
        $data['cvv2'] = $card['card_cvn'];//cvn2
        $data['phone'] = $card['card_phone'];//手机号
        echo '<form method="post" id="myform" name="myform" action="'.$payment_config['payurl'].'bind/checkCardInfo'.'">';
        foreach ($data as $key => $value) {
            echo '<input type="hidden" name="'.$key.'" value="'.urldecode($value).'">';
    
        }
        echo '</form>
            <script type="text/javascript">
                document.myform.submit()
            </script>';
    }
    /**
     * 绑卡回调
     * @Author tw
     * @return [type] [description]
     */
    public function bind_card_notify()
    {
        $post = $obj = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        // $this->notify_logs($post,'Payskysb');

        //查询payment_card
        $payment_user = Db::name('payment_user')->where('user_merchant',$obj['merchantNo'])->where('user_number',$obj['memberId'])->where('user_type',2)->find();
        $payment_card = Db::name('payment_card')->where('card_pay_uid',$payment_user['user_id'])->where('card_no',$obj['cardNo'])->find();
        if($payment_card['card_state']==1)
        {
            return json(['error'=>1,'msg'=>'卡已绑定']);
        }
        if($obj['result_code']!='0000')
        {
            $this->payment_bind_card($payment_card,2,$obj['result_msg'],$obj['token'],'');
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }

        switch ($obj['bindCode']) {
            case '1025':
                # 绑卡信息与报件信息不匹配 
            case '1026':
                # 绑卡鉴权失败
            case '1027':
                # 短信验证码验证失败 
            case '1029':
                # 绑卡失败
                $this->payment_bind_card($payment_card,2,$obj['bindMsg'],$obj['token'],'');
                return json(['error'=>1,'msg'=>$obj['bindMsg']]);
                break;
            case '1028':
                # 绑卡成功
                $this->payment_bind_card($payment_card,1,$obj['bindMsg'],$obj['token'],'');
                return json(['error'=>0,'msg'=>$obj['bindMsg'].'请继续操作']);
                break;
            
            default:
                return json(['error'=>1,'msg'=>$obj['bindMsg']]);
                break;
        }
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
        if($payment_card['card_state'] == 1)
        {
            return ['error'=>1,'msg'=>'卡已绑定'];
        }

        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'bind/queryCardInfo',$json_data);
        $obj = json_decode($res,true);
        $this->pay_logs($json_data,$res,'Payskysb','bind_card_query');
        if($obj['result_code']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }
        $list = $obj['infoList'];
        foreach ($list as $key => $value) {
            if ($payment_card['card_no']==$value['cardNo']) {
                $this->payment_bind_card($payment_card,1,'绑卡成功',$value['token'],'');
                return ['error'=>0,'msg'=>'绑卡成功,请继续操作'];
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

        $card = Db::name('user_card')->where('card_uid',$uid)->where('card_id',$cid)->where('card_type',1)->find();
        if(empty($card))
        {
            return ['error'=>1,'msg'=>'支付卡不存在'];
        }
        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        
        if($payment_card['card_state']!=1)
        {
            return ['error'=>1,'msg'=>'该卡未绑定'];
        }

        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        $data['token'] = $payment_card['card_pay_cid'];
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'bind/unbindCard',$json_data);
        $obj = json_decode($res,true);
        $this->pay_logs($json_data,$res,'Payskysb','unbind_card');

        if($obj['result_code']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }

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
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysb/webpay/id/'.$id;
        return ['error'=>'10','msg'=>'支付跳转','type'=>'web','title'=>'支付','url'=>$url];
    }
    public function webpay()
    {
        $id = input('param.id');
        if (empty($id)) {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $pay_records = Db::name('pay_records')->where('records_id',$id)->where('records_state',0)->find();
        if(empty($pay_records))
        {
            return "订单错误";
        }

        $cid = $pay_records['records_cid'];//储蓄卡id
        $pay_cid = $pay_records['records_pay_cid'];//信用卡id
        $pay_id = $pay_records['records_pay_id'];//支付通道id
        $uid = $pay_records['records_uid'];//用户id
        if(empty($pay_id) || empty($uid) || empty($cid))
        {
            return '参数错误';
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

        //储蓄卡绑定
        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if($payment_card['card_state']!=1)
        {
            return '储蓄卡未绑定';
        }

        //储蓄卡绑定
        $payment_pay_card = Db::name('payment_card')->where('card_cid',$pay_cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if($payment_pay_card['card_state']!=1)
        {
            return '信用卡未绑定';
        }

        $data['accountId']= $payment_config['accountId']; // 商户编号
        // $data['repayVersion'] = '2.0';//版本号
        $data['orderNo'] = $pay_records['records_form_no'];//订单号 

        $data['amount'] = $pay_records['records_money'];//金额

        $data['memberId'] = $payment_user['user_number'];//平台会员号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        $data['deductCardToken'] = $payment_pay_card['card_pay_cid'];//扣款卡授权码
        $data['repayCardToken'] = $payment_card['card_pay_cid'];//付款卡授权码
        $data['repayCycle'] = 'D0';
        $data['purpose'] = $pay_records['records_form_no'];//目的
        $data['quickPayResponseUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysb/pay_notify';//扣款结果通知地址
        $data['delegatePayResponseUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysb/df_notify';//付款结果通知地址 
        $data['pageResponseUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskysb/web_notify';//前台页面跳转地址

        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $this->pay_logs($data,$res,'Payskysb','pay_web');
        echo '<form method="post" id="myform" name="myform" action="'.$payment_config['payurl'].'quickPayWap/prePay'.'">';
        foreach ($data as $key => $value) {
            echo "<input type='hidden' name='".$key."' value='".urldecode($value)."'>";
    
        }
        echo '</form>
            <script type="text/javascript">
                document.myform.submit()
            </script>';

    }


    public function web_notify()
    {
        echo "支付完成请关闭页面";
    }
    /**
     * 支付扣款
     * @Author tw
     * @return [type] [description]
     */
    public function pay_notify()
    {
        $post = $obj = input('post.');
        if(empty($post))
        {
            return 'success';
        }
        $this->notify_logs($post);

        $pay_records = Db::name('pay_records')->where('records_form_no',$obj['orderNo'])->find();
        if (empty($pay_records)) {
            return 'success';
        }
        if($obj['result_code']!='0000')
        {
            $this->payment_kj($id,$uid,2,$obj['batchNo'],'','失败-'.$obj['result_msg'],'');
        }
        else
        {
            $this->payment_kj($id,$uid,2,$obj['batchNo'],'','代付中','');
        }
        echo 'success';
    }

    /**
     * 付款结果通知
     * @Author tw
     * @return [type] [description]
     */
    public function df_notify()
    {
        $post = $obj = input('param.');
        if(empty($post))
        {
            return 'success';
        }
        $this->notify_logs($post);
        $pay_records = Db::name('pay_records')->where('records_form_no',$obj['orderNo'])->find();
        if (empty($pay_records)) {
            return 'success';
        }
        if($obj['result_code']!='0000')
        {
            $this->payment_kj($id,$uid,5,$obj['batchNo'],'','代付失败-'.$obj['result_msg'],'');
        }
        else
        {
            $this->payment_kj($id,$uid,1,$obj['batchNo'],'','交易成功','');
        }
        echo 'success';
    }
    public function query_order_status($plan)
    {
            return ['error'=>1,'msg'=>'参数错误'];
        if($plan['plan_type']==1)
        {

            return $this->pay_state_df($plan);
        }
        elseif($plan['plan_type']==2)
        {
            return $this->pay_state_kj($plan);
        }
    }
    
    /**
     * 支付状态查询
     * @Author tw
     * @Date   2018-10-23
     * @param  [type]     $plan [description]
     * @return [type]           [description]
     */
    public function pay_state_kj($plan)
    {
        $mission['mission_id'] = $plan['plan_mid'];
        $pay_id = $plan['plan_pay_id'];//支付通道id
        $uid = $plan['plan_uid'];//用户id

        if(empty($pay_id) || empty($uid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }


        if($plan['plan_sort']==1)
        {
            if(time() < (strtotime($plan['plan_pay_time'])+600))
            {
                return ['error'=>1,'msg'=>'未到支付时间'];
            }

            $mission = Db::name('mission')->where('mission_id',$plan['plan_mid'])->find();

            if(empty(Db::name('mission')->where('mission_id',$mission['mission_id'])->where('mission_uid',$uid)->where('mission_del',0)->update(['mission_state'=>5])))
            {
                return ['error'=>1,'msg'=>'计划关闭失败'];
            }
            Db::name('plan')->where('plan_mid',$mission['mission_id'])->where('plan_sort',1)->update(['plan_state'=>2,'plan_msg'=>'第一笔需要web支付']);
            Db::name('user_card')->where('card_id',$mission['mission_cid'])->update(['card_state'=>0]);
            return ['error'=>1,'msg'=>'第一笔需要web支付，计划已关闭'];
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

        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['orderNo'] = $plan['plan_form_no'];//订单号 
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $res = request_post_urlencode($payment_config['payurl'].'query/queryQuickPayOrderStatus',$data);
        $this->pay_logs($data,$res,'Payskysb','pay_state_kj');
        $obj = json_decode($res,true);
        if($obj['result_code']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }

        $mission = Db::name('mission')->where('mission_id',$plan['plan_mid'])->find();
        if($obj['batchNo'])
        {
            if ($mission['mission_form_no']=='') {
                Db::name('mission')->where('mission_id',$mission['mission_id'])->update(['mission_form_no'=>$obj['batchNo']]);
            }
        }
        
        switch ($obj['status']) {
            case '00':
                $this->repay_notify($plan,1,'支付成功',1);
                return ['error'=>0,'msg'=>'支付成功'];
                break;
            case '10':
                return ['error'=>1,'msg'=>'支付处理中'];
                break;
            case '20':
                $this->repay_notify($plan,2,'支付失败',1);
                return ['error'=>1,'msg'=>'支付失败'];
                break;
            default:
                $this->repay_notify($plan,2,$obj['desc'],1);
                return ['error'=>1,'msg'=>$obj['desc']];
                break;
        }
    }
    /**
     * 代付状态查询
     * @Author tw
     * @Date   2018-10-23
     * @param  [type]     $plan [description]
     * @return [type]           [description]
     */
    public function pay_state_df($plan)
    {
        $mission['mission_id'] = $plan['plan_mid'];
        $pay_id = $plan['plan_pay_id'];//支付通道id
        $uid = $plan['plan_uid'];//用户id

        if(empty($pay_id) || empty($uid))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }


        if($plan['plan_sort']==1)
        {
            if(time() < (strtotime($plan['plan_pay_time'])+600))
            {
                return ['error'=>1,'msg'=>'未到支付时间'];
            }

            $mission = Db::name('mission')->where('mission_id',$plan['plan_mid'])->find();

            if(empty(Db::name('mission')->where('mission_id',$mission['mission_id'])->where('mission_uid',$uid)->where('mission_del',0)->update(['mission_state'=>5])))
            {
                return ['error'=>1,'msg'=>'计划关闭失败'];
            }
            Db::name('plan')->where('plan_mid',$mission['mission_id'])->where('plan_sort',1)->update(['plan_state'=>2,'plan_msg'=>'第一笔需要web支付']);
            Db::name('user_card')->where('card_id',$mission['mission_cid'])->update(['card_state'=>0]);
            return ['error'=>1,'msg'=>'第一笔需要web支付，计划已关闭'];
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

        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['orderNo'] = $plan['plan_form_no'];//订单号 
        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $res = request_post_urlencode($payment_config['payurl'].'query/queryDelegatePayOrderStatus',$data);

        $this->pay_logs($data,$res,'Payskysb','pay_state_df');
        $obj = json_decode($res,true);
        if($obj['result_code']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }
        switch ($obj['status']) {
            case '00':
                $this->repay_notify($plan,1,'支付成功',1);
                return ['error'=>0,'msg'=>'支付成功'];
                break;
            case '10':
                return ['error'=>1,'msg'=>'支付处理中'];
                break;
            case '20':
                $this->repay_notify($plan,2,'支付失败',1);
                return ['error'=>1,'msg'=>'支付失败'];
                break;
            default:
                $this->repay_notify($plan,2,$obj['desc'],1);
                return ['error'=>1,'msg'=>$obj['desc']];
                break;
        }
    }

    /**
     * 扣款 无用
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    public function batch_balance()
    {
        $cid = '75';//信用卡id
        $pay_id = '19';//支付通道id
        $uid = '37';//用户id

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

        $payment_card = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->where('card_pay_id',$pay_id)->find();
        if($payment_card['card_state']!=1)
        {
            return ['error'=>1,'msg'=>'该卡未绑定'];
        }

        $data['accountId']= $payment_config['accountId']; // 商户编号
        $data['memberId'] = $payment_user['user_number'];//平台会员号
        $data['merchantNo'] = $payment_user['user_merchant'];//电商助手系统分配的用户编号
        $data['batchNo'] = '2010000000000000103208';//批次号

        //公共参数
        $data['mac'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'batch/batchBalance',$json_data);
        $obj = json_decode($res,true);
        $this->pay_logs($data,$res,'Payskysb','batch_balance');
dump($obj );
exit();
        if($obj['result_code']!='0000')
        {
            $this->payment_pay(2,$mission,$plan,$obj['result_msg'],$pay_id);
            foreach ($planlist as $key => $value) {
                $this->payment_pay(2,$mission,$value,$obj['result_msg'],$pay_id);
            }
            return ['error'=>1,'msg'=>$obj['result_msg']];
        }

        $this->payment_pay(3,$mission,$plan,'计划提交成功',$pay_id);
        foreach ($planlist as $key => $value) {
            $this->payment_pay(3,$mission,$value,'计划提交成功',$pay_id);
        }
            return ['error'=>0,'msg'=>$obj['result_msg']];
        
    }
    /**
     * 扣款回调
     * @Author tw
     * @Date   2018-10-09
     * @return [type]     [description]
     */
    /*public function pay_notify()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $this->notify_logs($post,'Payskysb');
        
        $plan = Db::name('plan')->where('plan_form_no',$post['dsorderid'])->find();
        if(empty($plan))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        elseif($plan['plan_state']!=3)
        {
            return 'SUCCESS';
        }

        if($post['code']=='SUCCESS' && $post['amount']==$plan['plan_money'])
        {
            $this->repay_notify($plan,1);
            return 'SUCCESS';
        }
    }*/

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
        return strtoupper(md5($this->formatBizQueryParaMap($data,false).'&key='.$openKey));
    }
    private function formatBizQueryParaMap($paraMap, $urlencode){

        $buff = "";
        // ksort($paraMap);
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