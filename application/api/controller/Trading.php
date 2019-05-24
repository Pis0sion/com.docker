<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Exception;
use think\Queue;
use think\facade\Hook;

class Trading extends Base{
	
	/**
     * 我的交易
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function trading(){
    
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $getdata = array();
        $uid = $this->uid;//用户id
        $list = Db::name('trading')->where(['trading_uid'=>$uid])->order('trading_id desc')->paginate(10,false,['query'=> $getdata]);
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'无交易信息']);
        }
        foreach($list as $k=>$v){
            $data = array();
            $data = $v;
            $data['img'] = 'http://'.$_SERVER['HTTP_HOST'].'/uploads/images/articletype/articletype_'.$v['trading_type'].'.png';
            switch ($v['trading_type'])
            {
                case 1:
                $type = '还款';
                break;
                case 2:
                $type = '收款';
                break;
                case 3:
                $type = '升级';
                break;
                case 4:
                $type = '积分提现';
                break;
            }
            $data['type'] = $type;
            $list->offsetSet($k,$data);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }
    
    /*
     * 我的积分记录
     * 2018年10月15日09:45:49
     * 刘媛媛
     */
    public function integralog(){
    	
    	$post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $getdata = array();
        $uid = $this->uid;//用户id
        $list = Db::name('userIntegral')->where(['integral_uid'=>$uid])->order('integral_time desc')->paginate(10,false,['query'=> $getdata]);
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'无交易信息']);
        }
        
        foreach($list as $k=>$v){
            $data = array();
            $data = $v;
            switch ($v['integral_type'])
            {
                case 1:
                $type = '增加';
                break;
                case 2:
                $type = '减少';
                break;
            }
            $data['integral_type'] = $type;
            $list->offsetSet($k,$data);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }
    
    /*
     * 我的余额变动记录
     * 2018年10月15日09:48:11
     * 其他
     */
    public function  presentation(){
    	
    	$post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $getdata = array();
        $uid = $this->uid;//用户id
        $list = Db::name('userPresentation')->where(['presentation_uid'=>$uid])->order('presentation_time desc')->paginate(10,false,['query'=> $getdata]);
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'无交易信息']);
        }
        foreach($list as $k=>$v){
            $data = array();
            $data = $v;
            switch ($v['presentation_type'])
            {
                case 1:
                $type = '增加';
                break;
                case 2:
                $type = '减少';
                break;
            }
            $data['presentation_type'] = $type;
            $list->offsetSet($k,$data);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    	
    }
    
    /*
     * 我的分润记录
     * 2018年10月15日09:49:49
     * 刘媛媛
     */
    public function bonuslog(){
    	
    	$post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $getdata = array();
        $uid = $this->uid;//用户id
        $list = Db::name('userBonuslog')->where(['blog_user'=>$uid])->order('blog_time desc')->paginate(10,false,['query'=> $getdata]);
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'无交易信息']);
        }
        foreach($list as $k=>$v){
            $data = array();
            $data = $v;
            switch ($v['blog_type'])
            {
                case 1:
                $type = '升级分润';
                break;
                case 2:
                $type = '收款分润';
                break;
                case 3:
                $type = '还款分润';
                break;
                case 4:
                $type = '交易分润';
                break;
                case 5:
                $type = '贷款分润';
                break;
                case 6:
                $type = '办信用卡分润';
                break;
                case 7:
                $type = '其他分润';
                break;
                
            }
            $data['blog_type'] = $type;
            $list->offsetSet($k,$data);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }
    
    /*
     * 我的升级
     * 2018年10月15日10:08:41
     * 刘媛媛
     */
    public function upgrade(){
    	$post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $getdata = array();
        $uid = $this->uid;//用户id
        $list = Db::name('payUpgrade')->where(['upgrade_uid'=>$uid])->order('upgrade_time desc')->paginate(10,false,['query'=> $getdata]);
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'无交易信息']);
        }
        foreach($list as $k=>$v){
            $data = array();
            $data = $v;
            switch ($v['upgrade_state'])
            {	
                case 0:
                $type = '未支付';
                break;
                case 1:
                $type = '支付成功';
                break;
                case 2:
                $type = '支付失败';
                break;
                case 3:
                $type = '处理中';
                break;
                case 4:
                $type = '已退款';
                break;
                 case 5:
                $type = '免费升级';
                break;
                case 6:
                $type = '关系升级';
                break;
            }
            $data['upgrade_state'] = $type;
            $list->offsetSet($k,$data);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    	
    }
    
    /*
     * 我的提现
     * 2018年10月15日10:08:59
     * 刘媛媛
     */ 
    public function cash(){
    	
    	
    	$post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $getdata = array();
        $uid = $this->uid;//用户id
        $list = Db::name('userProfit')->where(['profit_uid'=>$uid])->order('profit_time desc')->paginate(10,false,['query'=> $getdata]);
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'无交易信息']);
        }
        foreach($list as $k=>$v){
            $data = array();
            $data = $v;
            switch ($v['profit_type'])
            {		
                case 1:
                $type = '申请中';
                break;
                case 2:
                $type = '成功打款';
                break;
                case 3:
                $type = '打款失败';
                break;
                case 4:
                $type = '处理中';
                break;
            }
            $data['profit_type'] = $type;
            $list->offsetSet($k,$data);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }
    
}