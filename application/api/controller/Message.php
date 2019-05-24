<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\facade\Session;

class Message extends Base{
	
	/**
	 * 获取列表消息
	 * 2018年11月5日16:04:33
	 * 刘媛媛
	 */
	public function index(){
		
		$list = Db::name('message')->where('message_uid',$this->uid)->order('message_time','desc')->paginate(10);
		return json(['msg'=>'ok','error'=>0,'data'=>$list]);
	}
	
	 /**
     * 获取详情
     * @Author 刘媛媛
     * @Date   2018年11月5日16:12:20
     */
    public function info(){
        $id  = input('post.id');   
        $data= Db::name('message')->where('message_uid',$this->uid)->where('message_id',$id)->find();
		if(!$data){
			return json(['error'=>1,'msg'=>'信息不存在']);
		}
		if($data['message_read']==0){
			Db::name('message')->where('message_id',$data['message_id'])->update(['message_read'=>1]); 
		}
        return json(['error'=>0,'msg'=>'获取成功','data'=>$data]);
	}
  	
  	// 获取未读消息
  	public function getnumber(){
      	$mess = Db::name('message')->where(['message_uid'=>$this->uid, 'message_read'=>0])->count();
      	if($mess){
        	return json(['error'=>0,'msg'=>'获取成功','data'=>$mess]);
        }else{
        	return json(['error'=>1,'msg'=>'没有未读消息','data'=>0]);
        }
      	
    }
}


