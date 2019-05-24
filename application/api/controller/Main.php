<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
use Endroid\QrCode\QrCode;

class Main extends Controller{
	
			
    /**
     * 注册
     * @Author tw
     * @Date   2018-09-06
     * @return [type]     [description]
     */
    public function register()
    {
        if($this->request->isPost()) {
            $config = require CACHE_PATH.'system.php';

    		$user_pid = 0;
    		$user_agent_id = 0;

            $post = input('post.');
            // 验证器验证
            $result = $this->validate($post, 'app\api\validate\User');
            if (true !== $result) {
                return json(['error'=>1,'msg'=>$result]);
            }
            // if(empty($post))
            // {
            //     return json(['error'=>1,'msg'=>'参数错误']);
            // }
            $smscode = $post["smscode"]; //验证码
            $user_phone = $post["phone"]; //手机号
            $user_password = $post["keywords"]; //密码
            $rekeywords = $post["rekeywords"]; //重复密码
            $recommend = $post["recommend"]; //推荐码
            $longitude = isset($post["Longitude"])?$post["Longitude"]:''; //经度
            $latitude = isset($post["latitude"])?$post["latitude"]:'';  //纬度
            
            if($config['USER_CODE']==1)
            {
                if(empty($recommend)){
                    return json(['error'=>1,'msg'=>'请填写推荐码']);
                }
            }
            $where[] = array('user_phone|user_account','eq',$user_phone);
            if(Db::name('user')->where([['user_state','eq',0]])->where($where)->find())
            {
                return json(['error'=>1,'msg'=>'手机号已注册']);
            }

            $sms = $this->Verification($smscode,$user_phone,1);
            if(!$sms)
            {
                return json(['error'=>1,'msg'=>'验证码错误或者失效']);
            }
            //生成客户推荐码
            $while=true;
            while ($while) {
                $user_code=rand(000000,999999);
                if(empty(Db::name('user')->where(['user_code'=>$user_code])->find()) && strlen($user_code)==6)
                {
                    break;
                }
            }
            //6位为前台用户推广
            if(strlen($recommend)==6)
            {   
                //一级
                $user = Db::name('user')->where(['user_code'=>$recommend])->find();

                if(empty($user))
                {
                    return json(['error'=>1,'msg'=>'推荐码错误']);
                }
                $user_pid = $user['user_id'];

                if($config['USER_INHERIT']==1)
                {
                    if($user['user_agent_id']==0)
                    {   
                        //二级
                        $usertwo = Db::name('user')->where(['user_id'=>$user['user_pid']])->find();

                        if(empty($usertwo)){

                            $user_agent_id = 0;

                        }elseif($usertwo['user_agent_id']==0){
                            //三级
                            $userthe = Db::name('user')->where(['user_id'=>$usertwo['user_pid']])->find();

                            if(empty($userthe) || $userthe['user_agent_id']==0)
                            {
                                $user_agent_id = 0;
                            }else{
                                $user_agent_id = $userthe['user_agent_id'];
                            }

                        }else{
                            $user_agent_id = $usertwo['user_agent_id'];
                        }
                    }else{
                        $user_agent_id = $user['user_agent_id'];
                    }
                }
                else
                {
                    $user_agent_id = 0;
                }
            }
    		elseif(strlen($recommend)==4)
    		{
    			$agent = Db::name('agent')->where(['agent_code'=>$recommend])->find();
    			$user_agent_id = $agent['agent_id'];
    		}
    		else
    		{
                if($config['USER_CODE']==1)
                {
                    if(empty($recommend)){
                        return json(['error'=>1,'msg'=>'请填写推荐码']);
                    }
                    return json(['error'=>1,'msg'=>'推荐码错误']);
                }
            }

            $user_password = md5($user_password);
			if(empty($post['user_isapp'])){
            	$isapp = 1;
            }else{
            	$isapp = $post['user_isapp'];
            }
            $data["user_pid"]          = $user_pid;
            $data["user_agent_id"]     = $user_agent_id;
            $data["user_code"]         = $user_code;
            $data["user_phone"]        = $user_phone;
            $data["user_account"]      = $user_phone;//账户同手机号
            $data["user_password"]     = $user_password;
            $data["user_type_id"]      = 10;
            $data['user_time']         = time();
            $data['user_ip']           = get_client_ip6();
          	$data['user_isapp'] = $isapp;//是否网页APP注册 2网页 1app
            $data['user_token']        = md5(time().rand().'test');
            $data['user_longitude']    = $longitude;
            $data['user_latitude']     = $latitude;
			$data['user_isactivation'] = $config['USER_ACTIVATION'];//判断是否激活
			
            $id = Db::name('user')->insertGetId($data);
            if($id)
            {
                
                //创建用户费率
                user_rate($id,$data["user_type_id"]);
                //更改验证码为已验证
                Db::name('user_verify')->where(['send_id'=>$sms['send_id']])->update(['send_state'=>1,'send_member'=>$id]);
				
				
				//判断是否开启注册送验证码
				// $Coupon_0 = getCoupon(0);
				// if($Coupon_0){
				// 	couponLog($Coupon_0['cou_id'],$id,'注册赠送',time());
				// }
				// //判断是否开启注册上级赠送代金券
				// if(!empty($user)){
	   //              $Coupon_2 = getCoupon(2);
				// 	if($Coupon_2){
				// 		couponLog($Coupon_2['cou_id'],$user['user_id'],'推广会员注册赠送',time());
				// 	}
    //             }
				$regdata           = array();
                $regdata['header'] = get_all_header();
                $regdata['data']   = $data;
                $regdata['post']   = $post;
                sitelog('register',$id,$regdata,2);	
                return json(['error'=>0,'msg'=>'注册成功']);
            }
            else
            {
                return json(['error'=>1,'msg'=>'注册失败']);
            }
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }

    /**
     * 用户登陆
     * @Author tw
     * @Date   2018-09-07
     * @return [type]     [description]
     */
    public function login()
    {
        if($this->request->isPost()) {
            $post = input('post.');
            if(empty($post))
            {
                return json(['error'=>1,'msg'=>'参数错误']);
            }
            $phone = $post["phone"];
            $smscode = isset($post['smscode'])?$post['smscode']:'';
            $password = isset($post['password'])?$post['password']:'';
            $type = isset($post['type'])?$post['type']:'1';

            
            //判断手机号
            if(empty($phone))
            {
                return json(['error'=>1,'msg'=>'手机号必须填写']);
            }
            if(empty(checkMobile($phone)))
            {
                return json(['error'=>1,'msg'=>'手机号不正确']);
            }


            $user = Db::name('user')->where(['user_phone'=>$phone,'user_state'=>0])->find();
            if(empty($user))
            {
                return json(['error'=>1,'msg'=>'用户不存在']);
            }

            if($type==1)
            {

                if(empty($smscode))
                {
                    return json(['error'=>1,'msg'=>'请填写验证码']);
                }
                $sms = $this->Verification($smscode,$phone,0);
                if(!$sms)
                {
                    return json(['error'=>1,'msg'=>'验证码错误或者失效']);
                }
            }
            elseif($type == 2)
            {
                if(empty($password))
                {
                    return json(['error'=>1,'msg'=>'请输入密码']);
                }
                $password = md5($password);
                if($password != $user['user_password'])
                {
                    return json(['error'=>1,'msg'=>'密码错误']);
                }
            }

            $time = time();
            $token = md5($phone.$time.$user['user_id']);
            //测试阶段不更新token
            if($user['user_token'])
            {
                $token = $user['user_token'];
            }
            //更改用户登陆时间
            Db::name('user')->where(['user_id'=>$user['user_id']])->update(['user_login_time'=>$time,'user_token'=>$token]);
            //用户登陆日志
            $this->user_log($user['user_id']);
            return json(['error'=>0,'msg'=>'登录成功','token'=>$token,'time'=>$time]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }

    }
    /**
     * 退出登录
     * @Author tw
     * @Date   2018-09-11
     * @return [type]     [description]
     */
    public function out()
    {
        $token = input('post.token');
        if(empty($token)){
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        // $uid = $this->uid;//用户id
        $ret = Db::name('user')->where(['user_token'=>$token])->find();
        if($ret){
            Db::name('user')->where(['user_token'=>$token])->update(['user_token'=>'']);
        }
        return json(['error'=>0,'msg'=>'退出成功']);
    }

    /**
     * 登陆日志
     * @Author tw
     * @Date   2018-09-14
     */
    public function user_log($user_id='')
    {
        $data["log_uid"] = $user_id;
        $data['log_ip'] = get_client_ip6();
        $data["log_time"] = time();
        Db::name('user_log')->insert($data);
    }
    /**
     * 用户费率
     * @Author tw
     * @Date   2018-09-14
     * @return [type]     [description]
     */
    public function rate()
    {
        $post = $_POST;
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $uid = $post['uid'];
        $user = Db::name('user')->where(['user_id'=>$uid])->find();
        if(empty($user))
        {
            return json(['error'=>1,'msg'=>'会员不存在']);
        }
        $rate = Db::name('rate')->where(['rate_type_id'=>$user['user_type_id']])->order('rate_type asc')->select();
        if(empty($rate))
        {
            return json(['error'=>1,'msg'=>'费率模板不存在']);
        }
        foreach ($rate as $key => $value) {
            $user_rate = Db::name('user_rate')->where(['rate_uid'=>$uid,'rate_type'=>$value['rate_type']])->find();

            $data['rate_rate'] = $value['rate_rate'];
            $data['rate_close_rate'] = $value['rate_close_rate'];
            $data['rate_time'] = time();
            if($user_rate)
            {
                Db::name('user_rate')->where(['rate_id'=>$user_rate['rate_id']])->update($data);
                continue;
            }
            $data['rate_uid'] = $uid;
            $data['rate_type'] = $value['rate_type'];
            Db::name('user_rate')->insert($data);

        }
        return json(['error'=>0,'msg'=>'成功']);

    }

    /**
     * 获取验证码
     * @Author tw
     * @Date   2018-09-14
     */
    public function getsms(){
        if($this->request->isGet()) {
            if(input('get.')) {
                $get   = input('get.');
                $mobile = $get['phone'];
                $type = isset($get['type'])?$get['type']:'0';
            }
            if(!checkMobile($mobile)){
                return json(['error'=>1,'msg'=>'您的手机号有误']);
            }
            if($type==0)
            {
                //登陆
                $user = Db::name('user')
                            ->where('user_phone','eq',$mobile)
                            ->where('user_state','eq',0)
                            ->find();
                if(empty($user))
                {
                    return json(['error'=>1,'msg'=>'用户不存在']);
                }
                $user_id = $user['user_id'];
            }
            elseif($type==1)
            {
                //注册
                $where[] = array('user_phone|user_account','eq',$mobile);
                if(Db::name('user')->where([['user_state','eq',0]])->where($where)->find())
                {
                    return json(['error'=>1,'msg'=>'手机号已注册']);
                }
                $user_id = 0;
            }
            elseif($type==2)
            {
                //找回密码
                $user = Db::name('user')
                            ->where('user_phone','eq',$mobile)
                            ->where('user_state','eq',0)
                            ->find();
                if(empty($user))
                {
                    return json(['error'=>1,'msg'=>'用户不存在']);
                }
                $user_id = $user['user_id'];
            }



        if(!$this->getSmsNum($mobile,$type)){
                return json(['error'=>1,'msg'=>'发送次数过多请稍后再试']);
            }
            
            $send['send_code']   = rand(111111,999999);
            $send['send_time']   = time();
            $send['send_target'] = $mobile;
            $send['send_type']   = $type;
            $send['send_state']  = 0;
            $send['send_member'] = $user_id;
            
            $retSend = Db::name('user_verify')->insert($send);
            if($retSend){
                sendsms($mobile,4,$send['send_code']);
            }
            return json(['error'=>0,'msg'=>'发送成功']);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }

    protected function getSmsNum($target,$type){
        $stime = time();
        $etime = time()-(20*60);
        $countNum = Db::name('user_verify')
        ->where('send_target','eq',$target)
        ->whereTime('send_time', 'between', [$etime,$stime])
        ->where('send_type','eq',$type)
        ->count();
        if($countNum>5){
            return false;
        }
        return true ;
    }
    /**
     * 注册协议
     */
    public function protocol()
    {
        return $this->fetch();
    }

    //验证码验证
    public function Verification($code,$send_target,$type){
        if(intval($code)==0){
            return false;
        }
        $retSend = Db::name('UserVerify')
        ->where('send_target','eq',$send_target)
        ->where('send_state','eq',0)
        ->where('send_type','eq',$type)
        ->order('send_time','desc')
        ->find();

        if(!$retSend){
            return false;
        }
        if($retSend['send_code']==$code){
            Db::name('UserVerify')->where('send_id',$retSend['send_id'])->update(['send_state'=>1]);
            
            return true; 
        }else{
            return false;
        }
    }

    /**
     * 忘记密码
     */
    public function ForgetPass(){
        if($this->request->isPost()){

            $post = input('post.');
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }
            $phone  = $post['phone'];
            $code   = $post['code'];
            $newpas = $post['newpassword'];

            $sms = $this->Verification($code,$phone,2);
            if(!$sms)
            {
                return json(['error'=>1,'msg'=>'验证码错误或者失效']);
            }

            $list = Db::name('User')->where('user_phone',$phone)->find();
            if(empty($list)){
                return json(['error'=>1,'msg'=>'查无此会员信息']);
            }

            $result = Db::name('User')->where('user_phone',$phone)->update(['user_password'=>md5($newpas)]);
            if($result){
                return json(['error'=>0,'msg'=>'重置成功']);
            }else{
                return json(['error'=>1,'msg'=>'重置失败']);
            }
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    /**
     * 自动清理过期优惠卷
     */
    public function ClearTask(){

        $list = Db::name('CouponLog')->where('coul_state',0)->where('coul_time','>',time())->limit(100)->select();
        if(empty($list)){
            return json(['error'=>1,'msg'=>'暂无数据']);
        }

        foreach ($list as $val) {

            if($val['coul_time']!=0){
                
               Db::name('CouponLog')->where('coul_id',$val['coul_id'])->update(['coul_state'=>2]);  
            }
           
        }
        return json(['error'=>0,'msg'=>'成功']);
    }
}
