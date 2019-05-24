<?php
namespace app\api\job;
use think\Controller;
use think\Db;
use think\facade\Session;
//use think\Request;
class Base extends Controller
{

    public function __construct(){
       
    }
	
	public function queueLog($path,$logId,$msg){
        $pathnews =  'public/logs/';
        //创建类型
        if (! is_dir($pathnews)) {
            mkdir($pathnews,0777);
        }
        $pathnews =  'public/logs/queueLog/';
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
		$content = date("Y-m-d H:i:s",time())."\r\n".$msg."\r\n \r\n \r\n ";
		file_put_contents($filename, $content, FILE_APPEND);
	}
}