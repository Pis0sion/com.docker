<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\facade\Session;


class Sms extends Controller
{
//短信验证码验证
    public function Verification($code,$send_target,$type){
        if(intval($code)==0){
            return json(['error'=>1,'msg'=>'请输入短信验证码']);
        }
        $retSend = Db::name('UserVerify')
        ->where('send_target','eq',$send_target)
        ->where('send_state','eq',0)
        ->where('send_type','eq',$type)
        ->order('send_time','desc')
        ->find();
        if(!$retSend){
            return json(['error'=>1,'msg'=>'验证码错误或失效']);
        }
        if($retSend['send_code']==$code){
            Db::name('UserVerify')->where('send_id',$retSend['send_id'])->update(['send_state'=>1]);
            
            return json(['error'=>0,'msg'=>'验证码正确']); 
        }else{
            return json(['error'=>1,'msg'=>'验证码错误或失效']);
        }
    }
}
