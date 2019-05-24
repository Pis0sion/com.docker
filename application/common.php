<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use qrcodes\QRcode as QRcode;
use alysms\SignatureHelper;
use ocr\AipOcr;
// 应用公共文件
function ip2bin($ip) {
    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) return base_convert(ip2long($ip),10,2);
    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) return false;
    if(($ip_n = inet_pton($ip)) === false) return false;
    $bits = 15; // 16 x 8 bit = 128bit (ipv6)
    while ($bits >= 0) {
        $bin = sprintf("%08b",(ord($ip_n[$bits])));
        $ipbin = $bin.$ipbin;
        $bits--;
    }
    return $ipbin;
}
//系统默认日志文件
function sitelog($path,$logId,$msg,$type=0){
        $pathnews =   $_SERVER['DOCUMENT_ROOT'].'/logs/';
        //创建类型
        if (! is_dir($pathnews)) {
            mkdir($pathnews,0777);
        }
        $pathnews =   $_SERVER['DOCUMENT_ROOT'].'/logs/site/';
        //创建类型
        if (! is_dir($pathnews)) {
            mkdir($pathnews,0777);
        }
		$pathnews =  $pathnews.$path;
		//创建类型
		if (! is_dir($pathnews)) {
			mkdir($pathnews,0777);
		}
		$filename = $pathnews .'/' . $logId . '.txt';
  		if($type==0){
           $content = date("Y-m-d H:i:s",time())."\r\n".$msg."\r\n \r\n \r\n ";
			file_put_contents($filename, $content, FILE_APPEND);
        }else{
        	file_put_contents($filename, PHP_EOL.date('Y-m-d H:i:s').PHP_EOL.'数据'.PHP_EOL.var_export($msg, true).PHP_EOL, FILE_APPEND);
        }
	
}

//获取header 头信息
 function get_all_header()
  {
    // 忽略获取的header数据。这个函数后面会用到。主要是起过滤作用
    $ignore = array('host','accept','content-length','content-type');

    $headers = array();
    //这里大家有兴趣的话，可以打印一下。会出来很多的header头信息。咱们想要的部分，都是‘http_'开头的。所以下面会进行过滤输出。

    foreach($_SERVER as $key=>$value){
      if(substr($key, 0, 5)==='HTTP_'){
      //这里取到的都是'http_'开头的数据。
      //前去开头的前5位
        $key = substr($key, 5);
        //把$key中的'_'下划线都替换为空字符串
        $key = str_replace('_', ' ', $key);
        //再把$key中的空字符串替换成‘-’
        $key = str_replace(' ', '-', $key);
        //把$key中的所有字符转换为小写
        $key = strtolower($key);

    //这里主要是过滤上面写的$ignore数组中的数据
        if(!in_array($key, $ignore)){
          $headers[$key] = $value;
        }
      }
    }
    //输出获取到的header
    return $headers;

  }
/*
 * 获取配置json 转一维数组
 */
function configJsonToArr($json){

    $arr = json_decode($json,true);
    if(count($arr)==0){
        return false ;
    }
    $newsarr = array();
    foreach ($arr as $k=>$v){
        $newsarr[$v[0]] = $v[1];
    }
    return $newsarr;
}

//格式化数据库中的时间
function getData($time){
    if($time==0){
        return '无';
    }else{
        return date('Y-m-d H:i:s',$time);
    }
}
/**
 * 转换bin地址为IPv6 或IPv4
 * @param long $bin 返回类型 0 IPv4 IPv6地址
 * @return mixed
 */
function bin2ip($bin) {
    // 32bits (ipv4)
    if(strlen($bin) <= 32)
        return long2ip(base_convert($bin,2,10));
    if(strlen($bin) != 128)
        return false;
    $pad = 128 - strlen($bin);

    for ($i = 1; $i <= $pad; $i++) {
        $bin = "0".$bin;
    }
    $bits = 0;
    $ipv6='';
    while ($bits <= 7) {
        $bin_part = substr($bin,($bits*16),16);
        $ipv6 .= dechex(bindec($bin_part)).":";
        $bits++;
    }
    return inet_ntop(inet_pton(substr($ipv6,0,-1)));
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip6($type = 0) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2bin($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}
/*
* 检查手机号格式
* @author      liuyuyao<2017年12月5日14:35:39>
* @version     $mobile
* @param
* @return  bool 返回值
*/
function checkMobile($mobile) {
    if(preg_match("/^1[3456789]{1}\d{9}$/",$mobile)){
        return true;
    }else{
        return false;
    }
}
/*
 * 检查是否微信内访问
 */
function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } return false;
}
//生成随机字符串
function createRandomStr($length){
    $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
    $strlen = 62;
    while($length > $strlen){
        $str .= $str;
        $strlen += 62;
    }
    $str = str_shuffle($str);
    return substr($str,0,$length);
}

/*
 * 获取会员type
 * 2018年8月27日11:31:20
 * 刘媛媛
 */
function getUserType($id){
    if($id==0)return '无';
    $field =   Db::name('UserType')->cache(120)->where(array('type_id'=>$id))->value('type_name');
    if(!$field){
        return '无';
    }
    return $field;      
}
/*
 * 获取会员信息
 * 2018年8月27日11:33:15
 * 刘媛媛
 */
function getUser($id,$fied){
    if($id==0)return '无';
    $field = Db::name('user')->cache(120)->where(array('user_id'=>$id))->value($fied);
    if(!$field){
        return '无';
    }
    return $field;  
}
/*
 * 获取代理商信息
 * 2018年8月27日11:33:15
 * 刘媛媛
 */
function getAgent($id,$fied){
    if($id==0)return '无';
    $field = Db::name('agent')->cache(120)->where(array('agent_id'=>$id))->value($fied);
    if(!$field){
        return '无';
    }
    return $field;  
}
function getBankList($id,$fied){
    if($id==0)return '无';
    $field = Db::name('bankList')->cache(120)->where(array('list_id'=>$id))->value($fied);
    if(!$field){
        return $id.'无';
    }
    return $field;  
}

function getPayment($id,$fied){
    if($id==0)return '无';
    $field = Db::name('payment')->cache(120)->where(array('payment_id'=>$id))->value($fied);
    if(!$field){
        return '无';
    }
    return $field;  
}

function getAgentCard($id,$fied){
    if($id==0)return '无';
    $field = Db::name('agentCard')->cache(120)->where(array('card_id'=>$id))->value($fied);
    if(!$field){
        return '无';
    }
    return $field;  
}

/**
 * 阿里云短信
 * @param  [type] $phone [手机号]
 * @param  [type] $type  [类型]
 * @param  [type] $code  [内容]
 * @return [type]        [description]
 */
function sendsms($phone,$type,$code) {


    $params = array ();

    // *** 需用户填写部分 ***
    // fixme 必填：是否启用https
    $security = false;

    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = "LTAIjyo7MfbFxbIS";
    $accessKeySecret = "CGoZRhajLw1DcE87eG7oRnBIwv6bzg";

    // fixme 必填: 短信接收号码
    $params["PhoneNumbers"] = $phone;

    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = "巴巴";

    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    switch ($type) {
        case 1:
            $params["TemplateCode"] = "SMS_152286210";
            // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
            $params['TemplateParam'] = array ("code" => $code);
            break;
        case 2:
        case 3:
        case 4:
        case 5:
            $params["TemplateCode"] = "SMS_152281208";
            $params['TemplateParam'] = array ("code" => $code);
            break;
        case 6:
            $params["TemplateCode"] = "SMS_152286219";
            $params['TemplateParam'] = array ("code" => $code);
            break;
        case 7:
            $params["TemplateCode"] = "SMS_152281210";
            $params['TemplateParam'] = array ("" => "");
            break;
        case 8:
            $params["TemplateCode"] = "SMS_152286223";
            $params['TemplateParam'] = array ("" => "");
            break;
        case 9:
            $params["TemplateCode"] = "SMS_152281214";
            $params['TemplateParam'] = array ("" => "");
            break;
        case 10:
            $params["TemplateCode"] = "SMS_152286230";
            $params['TemplateParam'] = array ("code" => $code);
            break;
        case 11:
            $params["TemplateCode"] = "SMS_152281430";
            $params['TemplateParam'] = array ("code" => $code);
            break;
        default:
            # code...
            break;
    }
    // fixme 可选: 设置发送短信流水号
    $params['OutId'] = "BB".date("Ymd").rand(10000,99999);

    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
    $params['SmsUpExtendCode'] = "1234567";

    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }
    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
    $helper = new SignatureHelper();

    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        )),
        $security
    );
    if($content->Code == 'OK'){
        return true;
    }else{
        return $content->Message;
    }
}

//post提交
function request_post($url = '', $param = '') {
    if (empty($url) || empty($param)) {
        return false;
    }
    
    $postUrl = $url;
    $curlPost = $param;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    
    return $data;
}
//将返回的json类型参数改为数组
function json_to_array($json_str){
    if(is_null(json_decode($json_str))){
        $json_str = $json_str;
    }else{
        $json_str = json_decode($json_str);
    }
    $json_arr=array();

    foreach($json_str as $k=>$w){
        if(is_object($w)){               
            $json_arr[$k]= json_to_array($w); 
        }else if(is_array($w)){
            $json_arr[$k]= json_to_array($w);
        }else{
            $json_arr[$k]= $w;
        }
    }
    return $json_arr;
}

/**
 * 获取标准二维码格式
 *
 * @param unknown $url            
 * @param unknown $path            
 * @param unknown $ext            
 */
function getQRcode($url, $path, $qrcode_name)
{
    if (! is_dir($path)) {
        $mode = intval('0777', 8);
        mkdir($path, $mode, true);
        chmod($path, $mode);
    }
    $path = $path . '/' . $qrcode_name . '.png';
    if (file_exists($path)) {
        unlink($path);
    }
   
    QRcode::png($url, $path, '', 4, 1);
    return $path;
}

/**
 * 制作推广二维码
 *
 * @param unknown $path
 *            二维码地址
 * @param unknown $thumb_qrcode中继二维码地址            
 * @param unknown $user_headimg
 *            头像
 * @param unknown $shop_logo
 *            店铺logo
 * @param unknown $user_name
 *            用户名
 * @param unknown $data
 *            画布信息 数组
 * @param unknown $create_path
 *            图片创建地址 没有的话不创建图片
 */
function showUserQecode($upload_path, $path, $thumb_qrcode, $user_headimg, $shop_logo, $user_name, $data, $create_path)
{
    
    // 暂无法生成
    if (! strstr($path, "http://") && ! strstr($path, "https://")) {
        if (! file_exists($path)) {
            $path = "public/static/images/template_qrcode.png";
        }
    }
    
    if (! file_exists($upload_path)) {
        $mode = intval('0777', 8);
        mkdir($upload_path, $mode, true);
    }
    
    // 定义中继二维码地址
    
    $image = \think\Image::open($path);
    // 生成一个固定大小为360*360的缩略图并保存为thumb_....jpg
    $image->thumb(288, 288, \think\Image::THUMB_CENTER)->save($thumb_qrcode);
    // 背景图片
    $dst = $data["background"];
    
    if (! strstr($dst, "http://") && ! strstr($dst, "https://")) {
        if (! file_exists($dst)) {
            $dst = "static/images/qrcode_bg/shop_qrcode_bg.png";
        }
    }
    // dump($dst);die;
    // $dst = "http://pic107.nipic.com/file/20160819/22733065_150621981000_2.jpg";
    // 生成画布
    list ($max_width, $max_height) = getimagesize($dst);
    // $dests = imagecreatetruecolor($max_width, $max_height);
    $dests = imagecreatetruecolor(640, 1134);

    $dst_im = getImgCreateFrom($dst);

    imagecopy($dests, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
    // ($dests, $dst_im, 0, 0, 0, 0, 640, 1134, $max_width, $max_height);
    imagedestroy($dst_im);
    // 并入二维码
    // dump($thumb_qrcode);die;
    // $src_im = imagecreatefrompng($thumb_qrcode);
    $src_im = getImgCreateFrom($thumb_qrcode);
    $src_info = getimagesize($thumb_qrcode);
    // imagecopy($dests, $src_im, $data["code_left"] * 2, $data["code_top"] * 2, 0, 0, $src_info[0], $src_info[1]);
    imagecopy($dests, $src_im, $data["code_left"] * 2, $data["code_top"] * 2, 0, 0, $src_info[0], $src_info[1]);
    imagedestroy($src_im);
    // 并入用户头像
    
    if (! strstr($user_headimg, "http://") && ! strstr($user_headimg, "https://")) {
        if (! file_exists($user_headimg)) {
            $user_headimg = "uploads/qrcode/promoteuser/thumb_template/6.png";
        }
    }
    $src_im_1 = getImgCreateFrom($user_headimg);
    $src_info_1 = getimagesize($user_headimg);
    // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
    // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
    imagecopyresampled($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, 80, 80, $src_info_1[0], $src_info_1[1]);
    imagedestroy($src_im_1);
    
    // 并入网站logo
    if ($data['is_logo_show'] == '1') {
        if (! strstr($shop_logo, "http://") && ! strstr($shop_logo, "https://")) {
            if (! file_exists($shop_logo)) {
                $shop_logo = "uploads/qrcode/promoteuser/thumb_template/6.png";
            }
        }
        $src_im_2 = getImgCreateFrom($shop_logo);
        $src_info_2 = getimagesize($shop_logo);
        // imagecopy($dests, $src_im_2, $data['logo_left'] * 2, $data['logo_top'] * 2, 0, 0, $src_info_2[0], $src_info_2[1]);
        imagecopyresampled($dests, $src_im_2, $data['logo_left'] * 2, $data['logo_top'] * 2, 0, 0, 200, 80, $src_info_2[0], $src_info_2[1]);
        imagedestroy($src_im_2);
    }
    // 并入用户姓名
    if ($user_name == "") {
        $user_name = "用户";
    }

    $rgb = hColor2RGB($data['nick_font_color']);
    $bg = imagecolorallocate($dests, $rgb['r'], $rgb['g'], $rgb['b']);
    $name_top_size = $data['name_top'] * 2 + $data['nick_font_size'];
    @imagefttext($dests, $data['nick_font_size'], 0, $data['name_left'] * 2, $name_top_size, $bg, "/static/font/Microsoft.ttf", $user_name);
    header("Content-type: image/jpeg");
    if ($create_path == "") {
        imagejpeg($dests);
    } else {
        imagejpeg($dests, $create_path);
    }
}
// 分类获取图片对象
function getImgCreateFrom($img_path)
{
    $ename = getimagesize($img_path);
    $ename = explode('/', $ename['mime']);
    $ext = $ename[1];
    switch ($ext) {
        case "png":
            
            $image = imagecreatefrompng($img_path);
            break;
        case "jpeg":
            
            $image = imagecreatefromjpeg($img_path);
            break;
        case "jpg":
            
            $image = imagecreatefromjpeg($img_path);
            break;
        case "gif":
            
            $image = imagecreatefromgif($img_path);
            break;
    }
    return $image;
}
/**
 * 颜色十六进制转化为rgb
 */
function hColor2RGB($hexColor)
{
    $color = str_replace('#', '', $hexColor);
    if (strlen($color) > 3) {
        $rgb = array(
            'r' => hexdec(substr($color, 0, 2)),
            'g' => hexdec(substr($color, 2, 2)),
            'b' => hexdec(substr($color, 4, 2))
        );
    } else {
        $color = str_replace('#', '', $hexColor);
        $r = substr($color, 0, 1) . substr($color, 0, 1);
        $g = substr($color, 1, 1) . substr($color, 1, 1);
        $b = substr($color, 2, 1) . substr($color, 2, 1);
        $rgb = array(
            'r' => hexdec($r),
            'g' => hexdec($g),
            'b' => hexdec($b)
        );
    }
    return $rgb;
}

/**
 * XiaoMi消息推送接口
 * @Author jd
 * style：是否发送所有；0群发，1指定userAccount发送
 * notifyId：通知编号
 * userAccount：指定userAccount群发 的 userAccount数组（[1,2,3,4]）  单条消息体最多携带1000个设备ID
 * title：推送标题
 * desc：推送内容
 * payload：推送携带数据→键值对
 */
function pushfun($style, $notifyId, $userAccount, $title, $desc, $payload){

    include_once(dirname(__FILE__) . '/../extend/xmpush/autoload.php');

    // http://127.0.0.1/mipush/gsw_android_mipush.php?style=0&notifyId=0&userAccount=&title=标题111&desc=内容222&payload=          // 给全部用户发送
    // http://127.0.0.1/mipush/gsw_android_mipush.php?style=1&notifyId=0&userAccount=123,333&title=标题333&desc=内容444&payload=   // 群发给123,333

    $secret = 'nDKYl5+XVpVAQIbpHM6xCA==';      // 推送秘钥
    $package = 'io.dcloud.sHBuilder.Hello';        // 包名
    Constants::setPackage($package);        // 常量设置必须在new Sender()方法之前调用
    Constants::setSecret($secret);          // 常量设置必须在new Sender()方法之前调用
    
    $sender = new Sender();
    $message1 = new Builder();
    $message1->title($title);                           // 通知栏的title
    $message1->description($desc);                      // 通知栏的descption
    $message1->passThrough(0);                          // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
    $message1->payload($payload);                       // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
    $message1->extra(Builder::notifyForeground, 1);     // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
    $message1->notifyId((int)$notifyId);                // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存   左边的官方文档，实际测试，随便取值0-9999999都可以。
    $message1->build();
    $id = (int)$style;
    if ($id == 0) {
        dump($sender->broadcastAll($message1)->getRaw());                        // 向所有设备发送消息。
    }else if ($id == 1){
        dump($sender->sendToUserAccount($message1, $userAccount)->getRaw());     // 指定userAccount群发。userAccount：Android端自定义，10个设备可是使用相同的userAccount，第11个设备会顶掉第1个设备
    }
    echo 'success';
}
/*==========================================================公共方法结束==========================================================================*/

/**
 * 会员等级及费率
 * @param  [type] $id   [description]
 * @param  [type] $fied [description]
 * @return [type]       [description]
 */
function ClassRates(){

    $field = Db::name('UserType as u')
    ->join('Rate r','u.type_id=r.rate_type_id')
    ->cache(1800)
    ->where(array('u.type_state'=>0))
    ->select();
    if(!$field){
        return '无';
    }
    return $field;  
}


/**
 * 银行卡实名认证查询(四要素)
 * @param  string $acct_pan  [银行卡号]
 * @param  string $acct_name [用户名]
 * @param  string $cert_id   [身份证号]
 * @param  string $phone_num [电话号]
 * @return [type]            [description]
 */
function proving($acct_pan='',$acct_name='',$cert_id='',$phone_num=''){
    if($acct_pan == '' || $acct_name == '' || $cert_id == ''){
        return false;
    }
    header("Content-Type:text/html;charset=UTF-8");
    date_default_timezone_set("PRC");
    $showapi_appid  = '81331';  //替换此值,在官网的"我的应用"中找到相关值
    $showapi_secret = '2caa99e2c9e64adb8b2881bf3ba00b66';  //替换此值,在官网的"我的应用"中找到相关值
    $paramArr = array(
    'showapi_appid'=> $showapi_appid,
        'acct_pan' => $acct_pan,
        'acct_name'=> $acct_name,
        'phone_num'=> $phone_num,
        'cert_type'=> "01",
        'cert_id'  => $cert_id,
        'needBelongArea'=> "true"
    //添加其他参数
    );
    $param = createParam($paramArr,$showapi_secret);
    if($phone_num!=''){
        $url = 'http://route.showapi.com/1072-5';
    }else{
        $url = 'http://route.showapi.com/1072-4';
    }
    
    $result = request_post($url,$param);
    $results = json_decode($result,true);

    return $results;
}

//创建参数(包括签名的处理)
function createParam ($paramArr,$showapi_secret) {
    $paraStr = "";
    $signStr = "";
    ksort($paramArr);
    foreach ($paramArr as $key => $val) {
        if ($key != '' && $val != '') {
            $signStr .= $key.$val;
            $paraStr .= $key.'='.urlencode($val).'&';
        }
    }
    $signStr .= $showapi_secret;//排好序的参数加上secret,进行md5
    $sign = strtolower(md5($signStr));
    $paraStr .= 'showapi_sign='.$sign;//将md5后的值作为参数,便于服务器的效验
    return $paraStr;
}

/**
 * 身份证姓名验证（身份证实名）
 * @param [type] $cardNo   [description]
 * @param [type] $realName [description]
 */
function UsernameIs($cardNo,$realName){
    //目前关闭
    return true;
    $url = "https://v.apistore.cn/api/a1";
    $data = [
        "key"=>"8fcdc602afde6fa5b79b83cc1869eede",
        "cardNo"=>$cardNo,
        "realName"=>$realName,
        "information"=>"1"
    ];
    $result = curl_post_https($url,$data);
    $res = json_decode($result,true);
    // dumP($res);
    if($res['error_code']==0 && $res['reason']=='认证通过'){
        return true;
    }else{
        return false;
    }
}

//https提交
function curl_post_https($url,$data){ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式
}

//身份证ocr验证
function IdOcr($img,$type){

    $client = new AipOcr('15039663', 'moVEw5GSU2VbHdoCgGWaIfln', 'IbkFSXdPtdUvnsTt9GkU7ldAUpFErqgS');
    // $imgs = file_get_contents($img);
    if($type == 1){
        $idCardSide = "front";
    }else{
        $idCardSide = "back";
    }
    
    // 如果有可选参数
    $options = array();
    $options["detect_direction"] = "true";
    $options["detect_risk"] = "false";

    // 带参数调用身份证识别
    $result = $client->idcard($img, $idCardSide, $options);
    return $result;
    
}

//银行卡类型识别
function BankType($cardNum){

    if($cardNum == '' ){
        return false;
    }
    header("Content-Type:text/html;charset=UTF-8");
    date_default_timezone_set("PRC");
    $showapi_appid = '81331';  //替换此值,在官网的"我的应用"中找到相关值
    $showapi_secret = '2caa99e2c9e64adb8b2881bf3ba00b66';  //替换此值,在官网的"我的应用"中找到相关值
    $paramArr = array(
    'showapi_appid'=> $showapi_appid,
        'cardNum' => $cardNum
    //添加其他参数
    );
    $param = createParam($paramArr,$showapi_secret);

    $url = 'http://route.showapi.com/30-7';
    
    
    $result = request_post($url,$param);
    $results = json_decode($result,true);

    return $results;

}

/*
 * 确认收货完成函数
 * 2018年10月9日11:44:59
 * 刘媛媛
 */
function confirmOrder($order_ID){
    
    $order = Db::nam('order_id')->where('order_id',$order_ID)->where('order_order_state',40)->find();
    
    if(!$order){
        return false;
    }
    
    $goods = Db::nam('goods')->where('goods_id',$order['order_goods'])->find();
    
    if(!$goods){
        return false;
    }
    //判断商品是否赠送自身积分
    if($goods['goods_give_integral']>0){
        
        //增加余额
        $integra = $goods['goods_give_integral']*$order['order_goods_num'];
        Db::name('user')->where('user_id',$order['order_buyer_id'])->setInc('user_integral',$integra);
        Db::name('user')->where('user_id',$order['order_buyer_id'])->setInc('user_total_integral',$integra);
        //增加余额记录
        $msg = '商城购买赠送积分';
        integralLog($order['order_buyer_id'],$integra,$msg,1,time());
    }
    
    //判断是否参与分销
    if($goods['goods_distribution']==1){
        
        $level = getuserlevel($order['order_buyer_id'],false);
        
        if(count($level)>0){
            
            foreach ($level as $lk=>$val){
                //根据Key获取应该返还值
                $newsMoeny = $goods['goods_distribution_'.($lk+1)]*$order['order_goods_num'];
                if($newsMoeny>0){
                    
                    if($goods['goods_distribution_type']==1){
                        //积分
                        $integra = $newsMoeny;
                        Db::name('user')->where('user_id',$val)->setInc('user_integral',$integra);
                        Db::name('user')->where('user_id',$val)->setInc('user_total_integral',$integra);
                        $msg = '下级商城购买获得返佣积分';
                        integralLog($val,$integra,$msg,1,time());
                    }else{
                        //余额
                        $moeny = $newsMoeny;
                        Db::name('user')->where('user_id',$val)->setInc('user_moeny',$moeny);
                        $msg = '下级商城购买获得返佣余额';
                        moneyLog($val,$moeny,$msg,1,time());
                    }
                    
                    unset($newsMoeny);
                    unset($msg);
                }
                
            }
            
        }
        
    }
    
    Db::nam('order_id')->where('order_id',$order_ID)->where('order_order_state',40)->update(['order_order_state'=>50]);
    return true;
}

/*
 * 用户积分记录添加
 * [[[只添加记录]]]
 * 2018年10月9日11:51:29
 * 刘媛媛
 */

function integralLog($uid,$num,$content,$type,$time){
    
    $log = array();
    $log['integral_uid']      = $uid;
    $log['integral_type']     = $type;
    $log['integral_point']    = $num;
    $log['integral_surplus']  = Db::name('user')->where('user_id',$uid)->value('user_integral');
    $log['integral_time']     = $time;
    $log['integral_content']  = $content;
    Db::name('userIntegral')->insert($log);
    //是否需要二次添加至表 cc_trading？？
    
    return true;
}
/*
 * 用户余额记录添加
 * [[[只添加记录]]]
 * 2018年10月9日14:02:58
 * 刘媛媛
 */
function moneyLog($uid,$num,$content,$type,$time){
    
    $log = array();
    $log['presentation_uid']      = $uid;
    $log['presentation_type']     = $type;
    $log['presentation_point']    = $num;
    $log['presentation_surplus']  = Db::name('user')->where('user_id',$uid)->value('user_integral');
    $log['presentation_time']     = $time;
    $log['presentation_content']  = $content;
    Db::name('userPresentation')->insert($log);
    //是否需要二次添加至表 cc_trading？？
    return true;
}

//过滤空格
 function myTrim($str){
    $search = array(" ","　","\n","\r","\t");
    $replace = array("","","","","");
    return str_replace($search, $replace, $str);
}
/*
 * 查询配置文件
 * 2018年10月10日10:10:20
 * 刘媛媛
 */
function getconfig($key){
    
    $config = require CACHE_PATH.'system.php';
    if(!$config){
        return false;
    }
    return $config[$key];
}
/*
 * 获取类型代金券
 * 2018年10月10日16:12:44
 * 刘媛媛
 */
function getCoupon($type,$userType=0){
    if($type==3){
        $coupon = Db::name('coupon')->where('cou_rule',$type)->where('cou_user_type',$userType)->where('cou_state',1)->select();
    }else{
        $coupon = Db::name('coupon')->where('cou_rule',$type)->where('cou_state',1)->select();
    }
    
    if(!$coupon){
        return false;
    }
    if(count($coupon)==0){
        return false;
    }
    //只有一个
    if(count($coupon)==1){
        $couponNews = $coupon[0];
    }
    //如果存在多个随机
    if(count($coupon)>1){
        $couponNews = $coupon[rand(0,count($coupon)-1)];
    }
    return $couponNews;
}
/*
 * 插入领取代金券记录
 * 2018年10月10日16:16:57
 * 刘媛媛
 * $cou_id 代金券ID
 * $uid  会员ID
 * $test 领取说明
 * $type 升级会员类型ID cou_rule 值等于3生效
 * $time 领取时间戳
 */
function couponLog($cou_id,$uid,$test,$time){
    
    $coupon = Db::name('coupon')->where('cou_id',$cou_id)->where('cou_state',1)->find();
    if(!$coupon){
        return false;
    }
    $logs = array();
    $logs['coul_cou']                = $coupon['cou_id'];
    $logs['coul_user']               = $uid;
    $logs['coul_receive_time']       = $time;//领取时间
    $logs['coul_receive_test']       = $test;//领取说明
    $logs['coul_money']              = $coupon['cou_value'];//代金券金额
    $logs['coul_type']               = $coupon['cou_type'];//优惠券类型 
    if( $coupon['cou_time']==0){
        $logs['coul_time']           = 0;//过期时间
    }else{
        $logs['coul_time']           = $time+($coupon['cou_time']*86400);//过期时间
    }
    $logs['coul_rule']               = $coupon['cou_rule'];//赠送来源 0 注册 1分享 2推广 3推广升级 
    
    $logs['coul_user_type']          = intval($logs['cou_user_type']);//升级会员类型ID 
    $logs['coul_state']              = 0;//代金券使用时间
    
    Db::name('couponLog')->insert($logs);
    
}
/*
 * 获取代理类型
 * 2018年10月11日15:40:48
 * 刘媛媛
 */
 function getAentGrade($Gradeid){
    
    if($Gradeid==0){
        return '无';
    }
    return Db::name('agentGrade')->where('grade_id',$Gradeid)->value('grade_name');
}
/*
 * 操作会员分润
 * 2018年10月11日15:41:08
 * 刘媛媛
 * $uid 升级会员的ID
 * $money 获取分润的金额
 * $time  获取分润的时间
 * $type 分润类型
 * $content  分润内容
 * $style   分润来源
 * $touser 关联会员 
 * $state  分润方式
 * 
 */
function bonuslog($uid,$money,$time,$type,$content,$style,$touser,$state){
    
    //增加分润
    $bonuslog = array();
    $bonuslog['blog_user'] = $uid;
    $bonuslog['blog_money'] = $money;
    $bonuslog['blog_time'] = $time;//分润时间
    $bonuslog['blog_type'] = $type;
    $bonuslog['blog_content'] = $content;
    $bonuslog['blog_style'] = $style;
    $bonuslog['blog_touser'] = $touser;//关联会员
    $bonuslog['blog_state'] = $state;
    Db::name('userBonuslog ')->insert($bonuslog);
    //积分
    if($state==1){
        
        Db::name('user')->where('user_id', $uid)->setInc('user_integral',$money);
        Db::name('user')->where('user_id', $uid)->setInc('user_total_integral',$money);
        integralLog($uid,$money,$content,1,$time);
    }
    //余额
    if($state==0){
        Db::name('user')->where('user_id', $uid)->setInc('user_moeny',$money);
        moneyLog($uid,$money,$content,1,$time);
    }
    return true;
}
/**
 * 获取银行卡 
 * @param  String $url    请求的地址
 * @param  Array  $param  请求的参数
 * @return JSON
 */
function getCard($id,$fle){
    return Db::name('userCard')->cache(3600)->where('card_id',$id)->value($fle);
}

/**
  * 对银行卡号进行掩码处理
  * @Author tw
  * @Date   2018-09-15
  * @param  [type]     $bankCardNo [description]
  * @return [type]                 [description]
  */
function formatCardNo($bankCardNo){
  //截取银行卡号后4位
  $suffix = substr($bankCardNo,-4,4);
  $maskBankCardNo = substr($bankCardNo,0,4). "**** **** ".$suffix;
  return $maskBankCardNo;
}
//临时防冲突
include 't.php';