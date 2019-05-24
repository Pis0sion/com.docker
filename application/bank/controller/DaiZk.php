<?php
namespace app\bank\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;
use zhongkong\RSAUtils;
use zhongkong\Config;
class DaiZk extends Base{
	/**
	 * 网贷列表
	 */
	public function loanList(){
		
	    $rsaUtils = new RSAUtils(file_get_contents(Config::$publicKey), file_get_contents(Config::$privateKey));
	    $url = Config::$baseHost . "/loan/open/loan/product/price";
	    $timestamp = Config::getMillisecond();
	    $sign = $rsaUtils->encrypt($timestamp);
	
	    $headers = Array(
	        "X-Auth-OEM:" . Config::$oemID,
	        "X-Open-Sign:" . $sign,
	        "X-Open-Merchant:" . Config::$merchant,
	        "X-Open-Timestamp:" . $timestamp,
	    );
	
	    $result = $this->post($url, $headers, Array());
		$arr =  json_decode($result,true);
		
		if($arr['status']!=200){
			return array('error'=>1,'msg'=>$arr['message']);
		}
		$data = array();
		foreach($arr['result'] as $k=>$v){
			$data[] = [
				'name'		=> $v['name'],
				'id'		=> $v['id'],
				'pid'		=> $v['oemChannelId'],
				'imagePath' => $v['platformProduct']['iconPath'],
				'remark'		=> $v['platformChannel']['remark']?:'',
				'quotaMax' =>$v['platformProduct']['quotaMax']
			];
			
		}
		return array('error'=>0,'msg'=>$arr['message'],'data'=>$data); 
	}
	
	public function bankdo($params){
		
		$rsaUtils    = new RSAUtils(file_get_contents(Config::$publicKey), file_get_contents(Config::$privateKey));
	    $url		 = Config::$baseHost . "/loan/open/loan/product/save";
	    $timestamp 	 = Config::getMillisecond();
	    $sign	     = $rsaUtils->encrypt($timestamp);
	
	    $headers = Array(
	        "X-Auth-OEM:" . Config::$oemID,
	        "X-Open-Sign:" . $sign,
	        "X-Open-Merchant:" . Config::$merchant,
	        "X-Open-Timestamp:" . $timestamp,
	    );
	
	    
	    $result = $this->post($url, $headers, $params);
	    $arr =  json_decode($result,true);
	    if($arr['status']!=200){
			return array('error'=>1,'msg'=>$arr['message']);
		}
		
		return array('error'=>0,'msg'=>'请求成功','data'=>$arr['result']);
	}
	
	public function Notify(){
		
		$this->log('zk',time(),'开始接数据');
		$data =  input('post.');
		$this->log('zk',time(),$data);
		//$data ='{"callbackType":"CALLBACK_SUCCESS","clientNo":"20180923143802570618","sign":"c3llSKhXoYYg91OhN8CoLK+n7vDTKdph50LxhJEsp9x6GDaVfT/RKbZHZ6MGgiS/eo+Qutu/6OM4rMJCzwyrdrEmDxwP2zeZ/dxOWIM8Vndsq69kjaW9Ji+KF6guIg5ZDEUS/F28m26za2THEZw+Z2MrN/ElVMB3EzIF4oBUvvk=","timestamp":"1529026610813","tradeNo":"1043751325537402880","userPrice":7000}';
		$post = json_decode($data,true);
		
		if($post['callbackType']!='CALLBACK_SUCCESS'){
			return 'callbackTypeError';
		}
		/*
			$rsaUtils   = new RSAUtils(file_get_contents(Config::$publicKey), file_get_contents(Config::$privateKey));
			$pubKey = openssl_pkey_get_public(file_get_contents(Config::$publicKey));
			$issign =(bool)openssl_verify($post['timestamp'],base64_encode($post['sign']), $pubKey,OPENSSL_ALGO_SHA256);
			//$issign =$rsaUtils->verify($timestamp,$sign);
			//$issign =$rsaUtils->verify($post['timestamp'],$post['sign']);
			dump($issign);exit;
		*/
		$issign= true;//验证签名省略掉
		if($issign){
			$arrs = $this->saveLog($post['clientNo']);
			if($arrs['error']==0){
				$arrR['result'] = ['message'=>'ok','callbackType'=>'CALLBACK_SUCCESS'];
				echo json_encode($arrR);
			}
			$this->log('zk',time(),$arrs['msg']);
		}
		
	}
	
	protected function post($url, $headers, $params){
	    $data = json_encode($params);
	
	    $curl = curl_init();
	
	    array_push($headers, "Content-Type: application/json");
	    array_push($headers, "Content-Length: " . strlen($data));
	
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
	   // print_r("=========请求信息 start =========\n");
	  //  print_r($url . "\n");
	  //  print_r(json_encode($headers) . "\n");
	  //  print_r($data  . "\n");
	    $response = curl_exec($curl);
	    curl_close($curl);
	  //  print_r("==============================\n");
	 //   print_r($response);
	 //   print_r("\n=========请求信息 end =========\n");
	    return $response;
	}
	
	
		
	protected function form($url, $headers, $params){
		
	    $data = "";
	    foreach ($params as $k => $v) {
	        $data .= "$k=" . urlencode($v) . "&";
	    }
	    $data = substr($data, 0, -1);
	
	    $curl = curl_init();
	
	    array_push($headers, "Content-Type: application/x-www-form-urlencoded");
	
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	
	    print_r("=========请求信息 start =========\n");
	    print_r($url . "\n");
	    print_r(json_encode($headers) . "\n");
	    print_r($data  . "\n");
	    $response = curl_exec($curl);
	    curl_close($curl);
	    print_r("==============================\n");
	    print_r($response);
	    print_r("\n=========请求信息 end =========\n");
	    return $response;
	}
		
	protected function get($url, $headers){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
	    //print_r("=========请求信息 start =========\n");
	    //print_r($url . "\n");
	   // print_r($headers);
	    $response = curl_exec($curl);
	    curl_close($curl);
	   // print_r("==============================\n");
	   // print_r($response);
	   // print_r("\n=========请求信息 end =========\n");
	    return $response;
	}	
}
