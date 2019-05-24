<?php
namespace app\agent\controller;
use think\Controller;
use think\Db;
use auth\Auth;
use think\facade\Session;
//use think\Request;
class Base extends Controller
{	
    public function __construct(){
        parent::__construct();
        if(!$this->chekLogin()){
          //$this->error('非法登陆',Url('/'));
          $this->error('未登录',Url('/Agent/Login/index'));
        }

		    $this->agent  = Db::name('agent')->where('agent_id',session('agent_id'))->find();
    		$this->assign('agent',$this->agent);
    }
   
    /*
    * 检查用户是否登录
    * @author      <2017年12月4日13:18:16>
    * @version     $Id$
    * @param
    * @return  array 返回值
    */
    private function  chekLogin(){
        $agent_login_id     = Session::has('agent_id')?session('agent_id'):0;
        if(!$agent_login_id){
            return false;
        }
        if(intval($agent_login_id)==0){
            return false;
        }
        return true;
    }

    // 用户操作日志
   // public function adminLog($info,$text){
   //  	$loga['log_adminid'] = $this->admin['admin_id'];
   //  	$loga['log_name'] = $this->admin['admin_user'];
   //  	$loga['log_time'] = time();
   //  	$loga['log_info'] = $info?$info:'默认操作';
   //  	$loga['log_info'] = $info?$info:'默认操作';
   //  	$loga['log_text'] = $text?$text:'默认操作';
   //  	db::name('adminLog')->insert($loga);
   //  }

}