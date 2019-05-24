<?php
namespace app\agent\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Login extends Controller
{	
	// 登陆模版
    public function index()
    {
    	// 检测是否登录
        if(session::get('agent_id')){
            $this->redirect('Agent/Index/index');
        }
        return view('login');
    }

    // 执行登陆
    public function dologin(){
		if($this->request->isAjax()) {
			$post = input('post.');
			$logname = $post['logname'];
			$logpass = $post['logpass'];
			$code = $post['code'];

			if($logname == '' || $logpass == ''){
				return json(['msg'=>'账户名或密码不可为空!','error'=>'1']);
			}

			$agent = Db::name('agent')->where(array('agent_account'=>$logname))->find();
			if(!$agent){
				return json(['msg'=>'账户不存在!','error'=>'1']);
			}

			if(md5($logpass) !== $agent['agent_password']){
				return json(['msg'=>'密码输入错误!','error'=>'1']);
			}

			if(!captcha_check($code)){
				return json(['msg'=>'验证码输入错误!','error'=>'1']);
			};

			session::set('agent_id',$agent['agent_id']);

            return json(['error'=>0,'msg'=>'登陆成功','url'=>Url('Agent/Index/index')]);

		}else{
			return json(['msg'=>'reqserr!','error'=>'1']);
		}
    }

       //退出登录
    public function logout()
    {
        Session::clear();
        session_unset();
        $this->redirect('/agent');

    }
}
