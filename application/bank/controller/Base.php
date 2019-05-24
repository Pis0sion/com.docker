<?php
namespace app\bank\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;

class Base extends Controller{
	
	public function saveLog($sn){
		
		$log = Db::name('bankApplyLog')->where('log_sn',$sn)->where('log_state',0)->find();
		
		if(!$log){
			return array('error'=>1,'msg'=>'订单不存在或被处理');
		}
		
		Db::name('bankApplyLog')->where('log_id',$log['log_id'])->update(['log_state'=>5,'log_fre_type'=>1]);
		
		return array('error'=>0,'msg'=>'处理成功待分配');
	}
	
	public function log($path,$logId,$msg){
        $pathnews =  'logs/';
        //创建类型
        if (! is_dir($pathnews)) {
            mkdir($pathnews,0777);
        }
        $pathnews =  'logs/bankLog/';
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
