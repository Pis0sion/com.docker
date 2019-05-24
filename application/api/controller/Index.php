<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;

class Index extends Base{
	/**
	 * 通知
	 * @Author tw
	 * @Date   2018-09-14
	 */
	public function message()
	{
        if($this->request->isPost()) {
            $post = input('post.');
            $uid = isset($this->uid)?$this->uid:'0';//用户id

    		$list = Db::name('message')->where('message_uid','in',[0,$uid])->order('message_id')->find();
    		if(empty($list))
    		{
                return json(['error'=>1,'msg'=>'没有信息']);
    		}
            return json(['error'=>0,'msg'=>'成功','data'=>$list]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
	}

    /**
     * 首页轮播图、启动图
     * @Author 
     * @Date   
     */
    public function brcasts(){
        if($this->request->isPost()) {
            $imgts = input('post.imgtype');
            if($imgts!=='2' || $imgts!=='3' || $imgts!==''){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            $where = array();
            if($imgts=='2'){ // 轮播图
                $where = ['img_type'=>$imgts];
            }else if($imgts=='3'){ // 启动图
                $where = ['img_type'=>$imgts, 'img_start_switch'=>1];
            }
            
            $brimg = Db::name('img')->where($where)->select();
            if(empty($brimg)){
                return json(['error'=>1,'msg'=>'获取失败']);
            }else{
                return json(['error'=>0,'data'=>$brimg]);
            }
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    
    public function shangqiao(){
    	$url = 'http://p.qiao.baidu.com/cps/chat?siteId=12597197&userId=26464222';
    	return json(['error'=>0,'msg'=>'请求成功','url'=>$url]);
    }
}
