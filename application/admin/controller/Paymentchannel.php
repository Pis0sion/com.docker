<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;
/**
 * 支付通道
 */
class Paymentchannel extends Base{
	public function __construct(){
       parent::__construct();
    }
    /**
     * 通道列表
     * @Author   tw
     * @DateTime 2018-08-29
     * @return   [type]     [description]
     */
    public function index(){
    	
        $keywords = trim(request()->param('keywords'));
        $getdata = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            $whereor[] = array('channel_name','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('payment_channel')->where($where)->whereor($whereor)
            ->order('channel_id desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('getdata',$getdata);
        return $this->fetch();
    }



    /*
     * 状态处理
     * 2018年9月4日10:57:10
     * 刘媛媛
     */
    public function updestate(){
        $id = input('get.id',0);
       
        if(!$id){
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        
        $Agent = Db::name('paymentChannel')->where(array('channel_id'=>$id))->find();
        if(!$Agent){
            return json(['error'=>1,'msg'=>'渠道不存在']);
        }
        
        if($Agent['channel_use']==1){
            Db::name('paymentChannel')->where(array('channel_id'=>$id))->update(['channel_use'=>0]);
        }else{
            Db::name('paymentChannel')->where(array('channel_id'=>$id))->update(['channel_use'=>1]);
        }
        return json(['error'=>0,'msg'=>'操作成功']);
    }
    /*
     * 增加渠道
     * 2018年9月4日11:02:22
     * 刘媛媛
     */
    public function addpmt(){
        
         if($this->request->isPost()) {
            $post = input('post.');
            $name = $post['name'];
            $bind = intval($post['bind']);
            $retData = Db::name('paymentChannel')->where('channel_name',$name)->find();
            if($retData){
                return json(['error'=>1,'msg'=>'渠道已经存在']);
            }
            $datas = array();
            $datas['channel_name'] = $name;
            $datas['channel_bind'] = $bind;
            $datas['channel_use']  = 1;
            $datas['channel_time'] = time();
            Db::name('paymentChannel')->insert($datas);
            return json(['error'=>0,'msg'=>'新增渠道成功']);
        }
        
        return $this->fetch();
    }

    /**
     * 编辑文章
     * @Author   tw
     * @DateTime 2018-09-03
     * @return   [type]     [description]
     */
    public function edit()
    {
        if($this->request->isPost())
        {
            $post = $_POST;
            $data['channel_name'] = $post['title'];
            // $data['channel_bind'] = $post['bind'];
            $data['channel_use'] = $post['use'];
            // $data['payment_channel_time'] = time();
            if(Db::name('payment_channel')->where(['channel_id'=>$post['id']])->update($data))
            {
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $payment_channel = Db::name('payment_channel')->where(['channel_id'=>input('param.id')])->find();
        if(empty($payment_channel))
        {
            echo "无内容";
            exit();
        }
        $this->assign('info',$payment_channel);
        return $this->fetch();
    }
   /*
     * 删除渠道
     * 2018年11月8日11:24:21
     * 刘媛媛
     */
    public function delechannel(){
    	$id = input('get.id',0);
       
        if(!$id){
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        
        $Channel = Db::name('paymentChannel')->where(array('channel_id'=>$id))->find();
        if(!$Channel){
            return json(['error'=>1,'msg'=>'渠道不存在']);
        }
        
        if($Channel['channel_use']!=0){
        	return json(['error'=>1,'msg'=>'禁用状态下才允许删除']);
        }
        
        //查询是否有通道
        
        $ispayment =  Db::name('payment')->where(array('payment_channel_id'=>$Channel['channel_id']))->find();
    	if($ispayment){
    		return json(['error'=>1,'msg'=>'此渠道存在支付通道请先删除通道']); 
    	}
    	
    	Db::name('paymentChannel')->where(array('channel_id'=>$id))->delete();
    	
    	return json(['error'=>0,'msg'=>'删除成功']);
    }
}
