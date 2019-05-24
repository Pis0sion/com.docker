<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Userbank extends Base{
	public function __construct(){
       parent::__construct();
    }
    public function index(){
    	$bid = request()->param('bid');
        $keywords = request()->param('keywords');
        $getdata  = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $whereor[] = array('card_no','like','%'.$keywords."%");
            $whereor[] = array('card_name','like','%'.$keywords."%");
            $whereor[] = array('card_phone','like','%'.$keywords."%");
            $whereor[] = array('card_province','like','%'.$keywords."%");
            $whereor[] = array('card_city','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        
        if(isset($bid) && !empty($bid)){
           $where[]   = array('card_uid','=',$bid);
        }
        $list = Db::name('userCard')->where($where)->whereor($whereor)
	        ->alias('ni')
	        ->join('cc_bank_list nc','nc.list_id = ni.card_bank_id','LEFT')
	        ->join('cc_user u','u.user_id = ni.card_uid','LEFT')
            ->order('card_id desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 修改银行卡状态
     * 2018年8月27日15:08:39
     * 刘媛媛
     */
    public function updestate(){
    	$id = input('get.id',0);
       
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        
        $User = Db::name('userCard')->where(array('card_id'=>$id))->find();
        if(!$User){
            return json(['error'=>1,'msg'=>'银行卡不存在']);
        }
        
        if($User['card_blocked']==1){
            Db::name('userCard')->where(array('card_id'=>$id))->update(['card_blocked'=>0]);
        }else{
        	Db::name('userCard')->where(array('card_id'=>$id))->update(['card_blocked'=>1]);
        }
        return json(['error'=>0,'msg'=>'操作成功']);
    }
    /*
     * 修改银行卡
     * 2018年8月27日15:24:09
     * 刘媛媛
     */
    public function upbank(){
    	
    	if($this->request->isPost()) {
            $post = input('post.');
            $id = input('get.id',0);
	        if(!$id){
	        	return json(['error'=>1,'msg'=>'参数错误']);
	        }
	        $card = Db::name('userCard')->where(array('card_id'=>$id))->find();
	        if(!$card){
	            return json(['error'=>1,'msg'=>'银行卡不存在']);
	        }
        	Db::name('userCard')->where(array('card_id'=>$id))->update($post);
       		return json(['error'=>0,'msg'=>'修改成功']);
        }
    	
    	$id = input('get.id',0);
        if(!$id){
        	die('参数错误');
        }
        $card = Db::name('userCard')->where(array('card_id'=>$id))->find();
        if(!$card){
            die('会员不存在');
        }
    	
    	$this->assign('card',$card);
        return $this->fetch();
    }
    /**
     * 删除新增的银行卡(不是新增的则无法进行删除)
     * 
     */
    public function DeleBank(){
        if($this->request->isPost()) {

            $post = input('post.');
            $uid = floatval($post['uid']);
            $cid = floatval($post['cid']);
            //计划记录
            $mission = Db::name('mission')->where(['mission_uid'=>$uid,'mission_cid'=>$cid])->select();
            if(!empty($mission)){
                return json(['error'=>1,'msg'=>'该银行卡已有计划生成，无法进行删除操作']);
            }
            //收款记录
            $records_cid = Db::name('payRecords')->where(['records_uid'=>$uid,'records_cid'=>$cid])->select();
            $records_pay_cid = Db::name('payRecords')->where(['records_uid'=>$uid,'records_pay_cid'=>$cid])->select();
            if(!empty($records_cid) || !empty($records_pay_cid)){
                return json(['error'=>1,'msg'=>'该银行卡已有收款记录，无法进行删除操作']);
            }
            //通道绑卡
            $payment_card = Db::name('paymentCard')->where(['card_uid'=>$uid,'card_cid'=>$cid])->select();
            if(!empty($payment_card)){
                return json(['error'=>1,'msg'=>'该银行卡已绑定支付通道，无法进行删除操作']);
            }

            $result = Db::name('userCard')->where(['card_uid'=>$uid,'card_id'=>$cid])->delete();
            if($result){
                return json(['error'=>0,'msg'=>'删除成功']);
            }else{
                return json(['error'=>1,'msg'=>'删除失败']);
            }
        }else{

            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
}
