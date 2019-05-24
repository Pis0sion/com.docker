<?php
namespace app\pay\controller;
use think\Controller;
use think\Db;

class Payskhqkj extends Base
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
        $payment_public = $this->payment_public($pay_id,$uid,'kj_',1,2);

        if($payment_public['error'] != 0)
        {
            return $payment_public;
        }
        $payment = $payment_public['payment'];
        $payment_config = $payment_public['payment_config'];
        $payment_user = $payment_public['payment_user'];
        // $payment_card = $payment_public['payment_card'];
        $user = $payment_public['user'];
        $user_rate = $payment_public['user_rate_sk'];
        $card = $payment_public['card'];

        
        $bank = Db::name('bank')->where('bank_pay_id',$pay_id)->where('bank_bid',$card['card_bank_id'])->find();
        if(empty($bank['bank_code']))
        {
            $bank_list = Db::name('bank_list')->where('list_id',$card['card_bank_id'])->find();
            $bank['bank_code'] = $bank_list['list_code'];
            if(empty($bank['bank_code']))
            {
                return ['error'=>1,'msg'=>'不支持此银行或code为空'];
            }

        }
        $data['futureRateValue'] = (string)($user_rate['rate_rate']*100);//每笔费率
        $data['fixAmount'] = (string)($user_rate['rate_close_rate']*100);//单笔价格
        $data['accountName'] = $card['card_name'];//真实姓名
        $data['idcard'] = $user['user_idcard'];//身份证号
        $data['settleBankCard'] = $card['card_no'];//收款银行卡号
        $data['settleBankName'] = getBankList($card['card_bank_id'],'list_name');//收款银行卡名称
        $data['settleBankCode'] = $bank['bank_code'];//银行代码
        $data['mobile'] = $card['card_phone'];//预留手机号

        //公共参数
        $data['transcode']= '025'; // 交易码 
        $data['version'] = '0100'; // 版本号
        $data['ordersn'] = get_order_sn("",$uid); //流水号
        $data['merchno'] = $payment_config['merchno'];//商户号
        $data['dsorderid'] = get_order_sn("J",$uid);//商户订单号
        ksort($data);
        $data['sign'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);

        $res = $this->post_json($payment_config['payurl'].'merchant/register.do',$json_data);
        $this->pay_logs($data,$res,'Payskhqkj','register');
        $obj = json_decode($res,true);
        //记录
        $log['log_pay_id'] = $pay_id;
        $log['log_pay_uid'] = $uid;
        $log['log_no'] = $obj['ordersn'];
        $log['log_form_no'] = $obj['dsorderid'];
        $log['log_name'] = '收款商户注册';
        $log['log_body'] = $json_data;
        $log['log_result'] = $res;
        $log['log_state'] = 1;
        $log['log_time'] = time();
        Db::name('payment_user_log')->insert($log);
        if($obj['returncode']=='0000')
        {
            unset($data);
            $data['user_name'] = $obj['merchantName'];
            $data['user_shortname'] = $obj['merchantName'];
            $this->payment_register($payment_user,1,$obj['errtext'],$obj['subMerchantNo'],$data);
            return ['error'=>0,'msg'=>'入驻成功'];
        }
        else
        {
            //失败
            $this->payment_register($payment_user,2,$obj['errtext']);
            return ['error'=>1,'msg'=>$obj['errtext']];
        }

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
        return ['error'=>0,'msg'=>'不支持查询'];
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
        // $payment_card = $payment_public['payment_card'];
        $user = $payment_public['user'];
        $user_rate = $payment_public['user_rate_sk'];
        $card = $payment_public['card'];
        $bank = Db::name('bank')->where('bank_pay_id',$pay_id)->where('bank_bid',$card['card_bank_id'])->find();
        $data['futureRateValue'] = (string)($user_rate['rate_rate']*100);//每笔费率
        $data['fixAmount'] = (string)($user_rate['rate_close_rate']*100);//单笔价格
        $data['settleBankCard'] = $card['card_no'];//收款银行卡号
        $data['settleBankCode'] = $bank['bank_code'];//银行代码
        $data['settleBankName'] = getBankList($card['card_bank_id'],'list_name');//收款银行卡名称
        $data['accountName'] = $card['card_name'];//真实姓名
        $data['mobile'] = $card['card_phone'];//预留手机号
        $data['subMerchantNo'] = $payment_user['user_merchant'];//子商户号

        //公共参数
        $data['transcode']= '027'; // 交易码 
        $data['version'] = '0100'; // 版本号
        $data['ordersn'] = get_order_sn("",$uid); //流水号
        $data['merchno'] = $payment_config['merchno'];//商户号
        $data['dsorderid'] = get_order_sn("J",$uid);//商户订单号
        ksort($data);
        $data['sign'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);

        $res = $this->post_json($payment_config['payurl'].'update/fee.do',$json_data);
        $this->pay_logs($json_data,$res,'Payskhqkj','update_fee');
        $obj = json_decode($res,true);
        //记录
        $log['log_pay_id'] = $pay_id;
        $log['log_pay_uid'] = $uid;
        $log['log_no'] = $obj['ordersn'];
        $log['log_form_no'] = $obj['dsorderid'];
        $log['log_name'] = '修改商户费率';
        $log['log_body'] = $json_data;
        $log['log_result'] = $res;
        $log['log_state'] = 1;
        $log['log_time'] = time();
        Db::name('payment_user_log')->insert($log);
        if($obj['returncode']=='0000')
        {
            return ['error'=>0,'msg'=>'修改成功'];
        }
        else
        {
            //失败
            return ['error'=>1,'msg'=>$obj['errtext']];
        }
    }
    /**
     * 扣款
     * @Author tw
     * @Date   2018-10-09
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
        // $region = explode('-', $pay_records['records_region']);



        $data['amount'] = (string)($pay_records['records_money']*100);//交易金额
        $data['bankcard'] = $card['card_no'];//消费银行卡号
        $data['accountName'] = $card['card_name'];//真实姓名
        $data['mobile'] = $card['card_phone'];//手机号
        $data['cvn2'] = $card['card_cvn'];//cvn2
        $data['expireDate'] = $card['card_exp_date'];//过期日期
        $data['subMerchantNo'] = $payment_user['user_merchant'];//子商户号
        $data['returnUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskhqkj/callback';//前台异步通知地址
        $data['notifyUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskhqkj/pay_notify';//后台异步通知地址

        //公共参数
        $data['transcode']= '026'; // 交易码 
        $data['version'] = '0100'; // 版本号
        $data['ordersn'] = get_order_sn("",$uid); //流水号
        $data['merchno'] = $payment_config['merchno'];//商户号
        $data['dsorderid'] = $pay_records['records_form_no'];//商户订单号

        ksort($data);
        $data = array_filter($data);
        $data['sign'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名

        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json($payment_config['payurl'].'quick/pay.do',$json_data);
        $this->pay_logs($json_data,$res,'Payskhqkj','pay');

        // $res ='{"transcode":"026","merchno":"erzd2018100901","dsorderid":"KJ1810275597095004567","sign":"da84e1d53a15b0c3189181ac58fd6c87","ordersn":"1810275769097004512","returncode":"0000","errtext":"下单成功","orderid":"2018102722363600100156671","amount":"50000","transtype":"18","pay_info":"http://pay.huanqiuhuiju.com/authsys/api/tl/bindCardPage.do","pay_code":"{\"orderid\":\"2018102722363600100156671\",\"merchno\":\"erzd2018100901\",\"bankcard\":\"62257888 **** 1197\",\"accountName\":\"王*月\",\"mobile\":\"133 **** 8078\",\"returnUrl\":\"http://shenyang.sdshengyixing.com/pay/Payskhqkj/callback\",\"notifyUrl\":\"http://shenyang.sdshengyixing.com/pay/Payskhqkj/pay_notify\",\"sign\":\"5b943e922649aeab8f789da245c51038\"}"}';
        $obj = json_decode($res,true);
        if($obj['returncode']!='0000')
        {
            $this->payment_kj($id,$uid,2,$obj['transtype'],'',$obj['errtext'],$res,'');
            return ['error'=>1,'msg'=>$obj['errtext']];
        }
        $this->payment_kj($id,$uid,3,$obj['transtype'],'',$obj['errtext'],$res,'');
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/pay/Payskhqkj/webpay/id/'.$id;
        return ['error'=>'10','msg'=>'支付跳转','type'=>'web','title'=>'支付','url'=>$url];
        
    }
    public function webpay()
    {
        $id = input('param.id');
        $pay_records = Db::name('pay_records')->where('records_id',$id)->find();
        if(empty($pay_records))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        $obj = json_decode($pay_records['records_info'],true);
        $pay_code = json_decode($obj['pay_code'],true);
        echo '<form method="post" id="myform" name="myform" action="'.$obj['pay_info'].'">';
        foreach ($pay_code as $key => $value) {
            echo '<input type="hidden" name="'.$key.'" value="'.urldecode($value).'">';
    
        }
        // echo '<input type="submit" value="提交表单">';
        echo '</form>
            <script type="text/javascript">
                document.myform.submit()
            </script>';
    }
    /**
     * 支付返回
     */
    public function callback()
    {
        $post = input('post.');
       
        if(empty($post))
        {	echo '<h1>支付中,请稍后查看结果</h1>';exit;
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $this->notify_logs($post,'Payskhqkj','callback');
        

        $pay_records = Db::name('pay_records')->where('records_form_no',$post['dsorderid'])->find();
        if(empty($pay_records))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        elseif($pay_records['records_state']!=3)
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        if($post['status']!='00')
        {
            //支付失败
            $this->pay_records($pay_records['records_id'],$pay_records['records_uid'],2,$obj['dsorderid']);
            echo "支付失败";
            exit();
        }
            $this->pay_records($pay_records['records_id'],$pay_records['records_uid'],1,$obj['dsorderid']);
            echo "支付成功";
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
        $this->notify_logs($post,'Payskhqkj','pay_notify');
        

        $pay_records = Db::name('pay_records')->where('records_form_no',$post['dsorderid'])->find();
        if(empty($pay_records))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        elseif($pay_records['records_state']!=3)
        {
            return 'SUCCESS';
        }
        if($post['status']!='00')
        {
            //支付失败
            $this->pay_records($pay_records['records_id'],$pay_records['records_uid'],2,$obj['dsorderid']);
        }
            $this->pay_records($pay_records['records_id'],$pay_records['records_uid'],1,$obj['dsorderid']);
            return 'SUCCESS';
    }

    /**
     * 交易状态查询
     * @Author tw
     * @Date   2018-10-20
     */
    public function query_order_status($pay_records='')
    {
        $id=$pay_records['records_id'];
        $uid=$pay_records['records_uid'];
        $pay_id=$pay_records['records_pay_id'];

        if(empty($id) || empty($uid) || empty($pay_id) || empty($pay_records))
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
        $user_rate_sk = $payment_public['user_rate_sk'];
        //公共参数
        $data['transcode']= '902'; // 交易码
        $data['version'] = '0100'; // 版本号
        $data['ordersn'] = get_order_sn("",$uid); //流水号
        $data['merchno'] = $payment_config['merchno'];//商户号
        $data['dsorderid'] = $pay_records['records_form_no'];//商户订单号
        $data['transtype'] = $pay_records['records_form_up_no'];//交易类型
        ksort($data);
        $data['sign'] = $this->getSign($data,$payment_config['openKey']);  // 生成签名
        $json_data = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = $this->post_json('http://pay.huanqiuhuiju.com/authsys/api/auth/execute.do',$json_data);
        $this->pay_logs($json_data,$res,'Payskhqkj','query_order_status');
        $obj = json_decode($res,true);
        if($obj['returncode']!='0000')
        {
            return ['error'=>1,'msg'=>$obj['errtext']];
        }
        switch ($obj['status']) {
            case '00':
                # 成功
                $this->payment_kj($id,$uid,1,'','',$obj['message'],'',time());
                return ['error'=>0,'msg'=>'支付成功'];
                break;
            case '01':
                # 处理中
                // if(time() < (strtotime($pay_records['records_time'])+1200))
                // {
                //     $this->payment_kj($id,$uid,3,'','',$obj['message'],'');
                //     return ['error'=>1,'msg'=>'支付处理中'];
                // }
                // else
                // {
                //     $this->payment_kj($id,$uid,2,'','',$obj['message'],'',time());
                //     return ['error'=>1,'msg'=>'订单失败:'.$obj['message']];
                // }
                $this->payment_kj($id,$uid,3,'','',$obj['message'],'');
                return ['error'=>1,'msg'=>'支付处理中'];
                break;
            case '02':
                # 失败
                $this->payment_kj($id,$uid,2,'','',$obj['message'],'',time());
                return ['error'=>1,'msg'=>$obj['message']];
                break;
            case '04':
                # 订单关闭
                $this->payment_kj($id,$uid,2,'','',$obj['message'],'',time());
                return ['error'=>1,'msg'=>'订单关闭'];
                break;
            case '06':
                # 待代付
                $this->payment_kj($id,$uid,4,'','',$obj['message'],'',time());
                return ['error'=>0,'msg'=>$obj['message'],];
                break;
            case '99':
                # 订单号不存在
                $this->payment_kj($id,$uid,2,'','',$obj['message'],'',time());
                return ['error'=>1,'msg'=>'订单号不存在'];
                break;
            default:
                $this->payment_kj($id,$uid,2,'','',$obj['message'],'',time());
                return ['error'=>1,'msg'=>$obj['errtext']];
                break;
        }

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
                $buff .= $k . "=" . $v;
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
    public function city_all()
    {
        return ['error'=>1,'msg'=>'不支持地区'];
    }
}