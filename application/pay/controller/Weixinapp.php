<?php
namespace app\pay\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;

class Weixinapp extends Pay
{
    
    /**
     * @Date   2019-01-04
     * @param  string     $config  通道配置信息
     * @param  string     $order_no   订单信息
     * @param  string     $paymoney 支付金额
     * @return [type]               [description]
     */
    public function init($order_no)
    {
      	header("content-type:text/html;charset=utf-8"); 
        if($order_no==''){
            return json(['error'=>1,'msg'=>'订单号错误']);
        }

        $list = Db::name('PayUpgrade')->where('upgrade_form_no',$order_no)->where('upgrade_state',0)->find();
        if(empty($list))
        {
          return json(['error'=>1,'msg'=>'订单信息错误']);
        }
        $notify = 'http://'.$_SERVER['SERVER_NAME'].'/pay/Weixinapp/notify';
        $money = $list['upgrade_money'];

        $Payment = Db::name('Payment')->where('payment_id',$list['upgrade_pay_id'])->find();
        if(empty($Payment))
        {
          return json(['error'=>1,'msg'=>'支付通道信息错误']);
        }
      	
      	$pconfig = configJsonToArr($Payment['payment_config']);
      	// 下单必要的参数
        $params['appid']            = $pconfig['appid'];
        $params['mch_id']           = $pconfig['mch_id'];
        $params['notify_url']       = $notify;
        $params['key']              = $pconfig['key'];
        $params['body']             = 'APP支付';    //商品描述
        $params['out_trade_no']     = $order_no;  // 本地订单
        $params['total_fee']        = $money*100;  // 充值金额 微信支付单位为分
        $params['nonce_str']        = uniqid();//随机数
        $params['spbill_create_ip'] = $this->get_client_ip();
        $params['trade_type'] 	    = 'APP';   //交易类型 JSAPI | NATIVE | APP | WAP
      
        //统一下单
        $result = $this->unifiedOrder($params);
      	// 检测是否下单成功
      	if($result['return_code'] == 'FAIL'){
          	//$code = $this->error_code($result['err_code']);
          	return json_encode(['error'=>1, 'msg'=>$result['return_msg']]);
        }
        //创建APP端预支付参数
        $data = $this->getAppPayParams($result, $params['appid'], $params['mch_id'], $params['key']);
      	//dumP($data);
      	return ['error'=>0, 'msg'=>'下单成功，已创建预支付参数', 'data'=>$data];
    }
	
  	public function get_client_ip(){
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv("REMOTE_ADDR")) {
            $cip = getenv("REMOTE_ADDR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $cip = getenv("HTTP_CLIENT_IP");
        } else {
            // $cip = "unknown";
            $cip = "127.0.0.1";
        }
        return $cip;
    }
  
  	/**
     * 生成APP端支付参数
     * @param  $prepayid  预支付id
     */
    public function getAppPayParams($prepayid, $appid, $mch_id, $key){
        $data['appid']     = $appid;
        $data['partnerid'] = $mch_id;
        $data['prepayid']  = $prepayid['prepay_id'];
        $data['package']   = 'Sign=WXPay';
        $data['noncestr']  = $prepayid['nonce_str'];
        $data['timestamp'] = time();
        $data['sign'] = $this->MakeSign($data, $key);
        return $data;
    }
  
  
  	// 会员升级回调
    public function notify(){
 		
      	//获取回调参数
        $testxml = file_get_contents("php://input");
        
        //将xml转化为json格式
        $jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $this->save_log('wxapplog',$jsonxml);
      	
      	//$jsonxml = '';
      	
        //转成数组
        $result = json_decode($jsonxml, true);
      
        $sign_return = $result['sign'];
        //如果成功返回了
        if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){

            // 开始验签
            $key = "ba9590a5agbbE35912442AS544e456Al";
            unset($result['sign']);
            $sign = $this->appgetSign($result,$key);
            if($sign == $sign_return) {
              $this->save_log('wxapplog','订单验签成功！');

              // 执行回调程序
              $amount = $result['total_fee']/100;
              $res = $this->Notifys($result['out_trade_no'], $result['transaction_id'], $amount);
              if($res['error']==0){
                $this->save_log('wxapplog','回调执行成功');
                echo exit('<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>');
              }else{
                $this->save_log('wxapplog','回调执行失败！：'.$res['msg']);
                return "fail";
              }         

            }else{
              $this->save_log('wxapplog','验签失败！');
              return "fail";    
            }
        }else{
            $this->save_log('wxapplog','订单未支付成功状态return_code非SUCCESS');
            echo '订单未支付成功状态return_code非SUCCESS';
            exit;
        }

    }
  
  	/*
     * 格式化参数格式化成url参数  生成签名sign
    */
    public function appgetSign($Obj,$appwxpay_key){
     
        foreach ($Obj as $k => $v) {
          $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
         if($appwxpay_key){
           $String = $String."&key=".$appwxpay_key;
         }
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
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
  
    /**
     * 统一下单
     * @param  $params    下单参数
    */
    public function unifiedOrder($params){
 
        $data['appid']       	  = $params['appid'];
        $data['mch_id']           = $params['mch_id'];
        $data['nonce_str'] 	      = $this->genRandomString();
        $data['body'] 			  = $params['body'];
        $data['out_trade_no'] 	  = $params['out_trade_no'];
        $data['total_fee'] 		  = $params['total_fee'];
        $data['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['notify_url'] 	  = $params['notify_url'];
        $data['trade_type'] 	  = $params['trade_type'];
 
        //获取签名数据
        $data['sign'] = $this->MakeSign($data, $params['key']);
        $xml = $this->data_to_xml($data);
        $response = $this->postXmlCurl($xml, 'https://api.mch.weixin.qq.com/pay/unifiedorder');
        if(!$response){
            return false;
        }
        $result = $this->xml_to_data( $response );
        return $result;
    }

    /**
     * 产生一个指定长度的随机字符串,并返回给用户
     * @param type $len 产生字符串的长度
     * @return string 随机字符串
    */
    public function genRandomString($len = 16) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        // 将数组打乱
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }

    /**
     * 生成签名
     *  @return 签名
    */
    public function MakeSign($params, $key){
        //签名步骤一：按字典序排序数组参数
        ksort($params);
        $string = $this->ToUrlParams($params);
        //签名步骤二：在string后加入KEY
        $string = trim($string . "&key=".$key) ;
         //var_dump($string);die;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 将参数拼接为url: key=value&key=value
     * @param  $params
     * @return string
    */
    public function ToUrlParams( $params ){
        $string = '';
        if( !empty($params) ){
            $array = array();
            foreach( $params as $key => $value ){
                $array[] = $key.'='.$value;
            }
            $string = implode("&",$array);
        }
        return  $string;
    }

    /**
     * 输出xml字符
     * @param  $params       参数名称
     * return  string    返回组装的xml
    **/
    public function data_to_xml( $params ){
        if(!is_array($params)|| count($params) <= 0)
        {
            return false;
        }
        $xml = "<xml>";
        foreach ($params as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }


    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
    */
    public function postXmlCurl($xml, $url, $useCert = false, $second = 30){
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
 
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
 
        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            //curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            //curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return false;
        }
    }

    /**
     * 将xml转为array
     * @param string $xml
     * return array
    */
    public function xml_to_data($xml){
        if(!$xml){
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }
  
  /**
     * 错误代码
     * @param  $code     服务器输出的错误代码
     * return string
     */
    public function error_code( $code ){
        $errList = array(
          	'INVALID_REQUEST'       =>'参数格式有误或者未按规则上传',
            'NOAUTH'                => '商户未开通此接口权限',
            'NOTENOUGH'             => '用户帐号余额不足',
            'ORDERNOTEXIST'         => '订单号不存在',
            'ORDERPAID'             => '商户订单已支付，无需重复操作',
            'ORDERCLOSED'           => '当前订单已关闭，无法支付',
            'SYSTEMERROR'           => '系统错误!系统超时',
            'APPID_NOT_EXIST'       => '参数中缺少APPID',
            'MCHID_NOT_EXIST'       => '参数中缺少MCHID',
            'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',
            'LACK_PARAMS'           => '缺少必要的请求参数',
            'OUT_TRADE_NO_USED'     => '同一笔交易不能多次提交',
            'SIGNERROR'             => '参数签名结果不正确',
            'XML_FORMAT_ERROR'      => 'XML格式错误',
            'REQUIRE_POST_METHOD'   => '未使用post传递参数 ',
            'POST_DATA_EMPTY'       => 'post数据不能为空',
            'NOT_UTF8'              => '未使用指定编码格式',
        );
        if( array_key_exists( $code , $errList ) ){
            return $errList[$code];
        }
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