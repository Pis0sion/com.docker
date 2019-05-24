<?php
namespace app\admin\controller;
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
            $this->error('非法登陆',Url('/'));
           // $this->error('未登录',Url('/_login'));
        }
       	
		$module = $this->request->module();
        $contrname = $this->request->controller();
        $actionname = $this->request->action();
		$type = request()->param('type');
        $this->adminid = session('admin_id');
		$this->admin   = Db::name('admin')->where('admin_id',session('admin_id'))->find();
		 /*if($this->admin['admin_role']!=0){
			$noCheck = array(
				'update_config',
				'uploadImage',
				'_upload',
				'uploadFiles',
				'deleteFiles',
				'cache',
				'ajaxAdminReturn',
				'log_admin',
				'reg_auth',
				'adminError',
				'menu',
				'position',
				'verify',
				'check_verify',
				'ajax_check_verify',
				'ajax_check_phone',
				'cache_class',
				'ajax_class',
				'attribute',
			);
			$Auth = new Auth();
			$module_name = $module.'/'.$contrname.'/'.$actionname;
			if( $contrname != "Index" && !in_array($actionname, $noCheck) && !$Auth->check($module_name,$this->adminid) ){
				if($this->request->isAjax()) {
					return json(['msg'=>'没有权限','error'=>'1']);
				} else {
					header("Content-Type:text/html; charset=utf-8");
					$this->error('没有权限');
				}
			}
		}
		*/
        $this->assign('contrname',$contrname);
        $this->assign('actionname',$actionname);
        $this->assign('leftType',$type);
        $this->assign('contractionname',$contrname.'/'.$actionname);
		$this->assign('config',require CACHE_PATH.'system.php');
		$this->assign('admin',$this->admin);
    }
   
    /*
    * 检查用户是否登录
    * @author      <2017年12月4日13:18:16>
    * @version     $Id$
    * @param
    * @return  array 返回值
    */
    private function  chekLogin(){
        $admin_login_id     = Session::has('admin_id')?session('admin_id'):0;
        if(!$admin_login_id){
            return false;
        }
        if(intval($admin_login_id)==0){
            return false;
        }
        return true;
    }
   public function adminLog($info,$text){
    	$loga['log_adminid'] = $this->admin['admin_id'];
    	$loga['log_name'] = $this->admin['admin_user'];
    	$loga['log_time'] = time();
    	$loga['log_info'] = $info?$info:'默认操作';
    	$loga['log_info'] = $info?$info:'默认操作';
    	$loga['log_text'] = $text?$text:'默认操作';
    	db::name('adminLog')->insert($loga);
    }

}