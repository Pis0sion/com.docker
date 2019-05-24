<?php
namespace app\pay\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;

class Pinganwx extends Pay
{
    
    /**
     * 
     * @Author tw
     * @Date   2018-09-26
     * @param  string     $config  通道配置信息
     * @param  string     $order_no   订单信息
     * @param  string     $paymoney 支付金额
     * @return [type]               [description]
     */
    public function init()
    {
        $get = input('get.');
        if($get['order_no']==''){
            return json(['error'=>1,'msg'=>'订单号错误']);
        }
        $order_no = $get['order_no'];
        $type = isset($get['type'])?$get['type']:'';

        if($type == 1){

            $list = Db::name('UserActivation')->where('act_form_no',$order_no)->where('cat_state',0)->find();
            if(empty(($list)))
            {
                return json(['error'=>1,'msg'=>'订单信息错误']);
            }
            $notify = 'http://'.$_SERVER['SERVER_NAME'].'/pay/Pinganwx/anotify';
            $money = $list['act_money'];

            $Payment = Db::name('Payment')->where('payment_id',$list['act_pay_id'])->find();
            if(empty($Payment))
            {
                return json(['error'=>1,'msg'=>'支付通道信息错误']);
            }
        }else{

            $list = Db::name('PayUpgrade')->where('upgrade_form_no',$order_no)->where('upgrade_state',0)->find();
            if(empty($list))
            {
                return json(['error'=>1,'msg'=>'订单信息错误']);
            }
            $notify = 'http://'.$_SERVER['SERVER_NAME'].'/pay/Pinganwx/notify';
            $money = $list['upgrade_money'];
            
            $Payment = Db::name('Payment')->where('payment_id',$list['upgrade_pay_id'])->find();
            if(empty($Payment))
            {
                return json(['error'=>1,'msg'=>'支付通道信息错误']);
            }
        }



        $pconfig = configJsonToArr($Payment['payment_config']);
        $data['seller']    = $pconfig['seller']; // 商户号
        $data['openKey']   = $pconfig['openKey']; // 商户key
        $data['payType']   = $pconfig['payType'];  // 支付方式 
        $data['orderId']   = $order_no;  // 本地订单
        $data['transTime'] = time();   // 支付时间
        $data['amount']    = $money;
        $data['notifyUrl'] = $notify; // 异步通知地址
        $data['backUrl']   = 'http://'.$_SERVER['SERVER_NAME'].'/pay/Pinganwx/callback'; // 前台通知地址
        $data['userId']    = '12'.substr(rand(),6);// 商户自定义用户号
        $data['signType']  = 'md5'; // 签名方式
        $data['ordeDev']   = '123';
        $data['orderDesc'] = '123';
        $data['signData']  = $this->getSign($data);  // 生成签名
        unset($data['openKey']);
        
        $payUrl = 'http://pay.jintangpay.top:88/payorder';//支付地址
        $ret = $this->curl($payUrl,$data);


        echo $ret;
        exit;
    }
/**
 * 参数加密
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
    private  function getSign($data){
       
        return strtoupper(md5($this->formatBizQueryParaMap($data,false)));
    }
/**
 * 数据拼接
 * @param  [type] $paraMap   [description]
 * @param  [type] $urlencode [description]
 * @return [type]            [description]
 */
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
        $reqPar='';
        if (strlen($buff) > 0){
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    #使用post的传输
    private function curl($url,$data){

        //启动一个CURL会话
        $ch = curl_init();
        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        //忽略证书
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL,$url);
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_HEADER,0);//是否需要头部信息（否）
        // 执行操作
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }

    public function notify(){
        $post = $_POST;
        if(!$post){
            die('禁止外部访问');
        }
        $post['openKey'] = '09295d97bfdb5cf7e002b9c440df117d';

        //开始验证签名
        $this->save_log('log','加key以后的数据'.json_encode($post));
        if($post['status']==1){
            $signStr = $post['signData'];
            unset($post['signData']);
            $sign = $this->getSign($post);
            
            //开始验证签名
            if($signStr == $sign){
                $this->save_log('log','订单验签成功！');
                $result = $this->Notifys($post['orderId'],$post['orderSn'],$post['amount']);
                $res = json_decode($result,true);
                if($res['error']==0){
                    $this->save_log('log','回调执行成功');
                    echo json_encode(array('errno'=>'0','content'=>'success'));
                    exit;                    
                }else{
                    $this->save_log('log','回调执行失败！');
                    echo '回调失败！';
                    exit;
                }

            }else{
                $this->save_log('log','验签失败！');
                echo '验签失败';
                exit;
            }
            
            
        }else{
            $this->save_log('log','订单未支付成功状态status非1');
            echo '订单未支付成功状态status非1';
            exit;
        }
    }

    /**
     * 激活回调
     * @return [type] [description]
     */
    public function anotify(){
        $post = $_POST;
        if(!$post){
            die('禁止外部访问');
        }
        $post['openKey'] = '3608b298866636b1a51a53930ade706d';

        //开始验证签名
        $this->save_log('log','加key以后的数据'.json_encode($post));
        if($post['status']==1){
            $signStr = $post['signData'];
            unset($post['signData']);
            $sign = $this->getSign($post);
            
            //开始验证签名
            if($signStr == $sign){
                $this->save_log('log','订单验签成功！');
                $result = $this->SjNotify($post['orderId'],$post['orderSn'],$post['amount']);
                $res = json_decode($result,true);
                if($res['error']==0){
                    $this->save_log('log','回调执行成功');
                    echo json_encode(array('errno'=>'0','content'=>'success'));
                    exit;                    
                }else{
                    $this->save_log('log','回调执行失败！');
                    echo '回调失败！';
                    exit;
                }

            }else{
                $this->save_log('log','验签失败！');
                echo '验签失败';
                exit;
            }
            
            
        }else{
            $this->save_log('log','订单未支付成功状态status非1');
            echo '订单未支付成功状态status非1';
            exit;
        }
    }

    public function callback(){
        echo "支付成功";
    }
    /*
     * 记录日志文件
     * linux 请补全路径
     */
    private function save_log($path, $msg){
   
        if (! is_dir($path)) {
            mkdir($path);
        }
        $filename = $path . '/' . date('YmdHi') . '.txt';
        $content = date("Y-m-d H:i:s")."\r\n".$msg."\r\n \r\n \r\n ";
        file_put_contents($filename, $content, FILE_APPEND);
    }
}