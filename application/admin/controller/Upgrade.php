<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Upgrade extends Base{
	public function __construct(){
       parent::__construct();
    }
    
    public function index(){
    	
    	$data = input('get.');
        $getdata = $where =array();
        //验证搜索数据
        $res = $this->ecickdata($data);
        if(!empty($res)){
            $where = $res['where'];
            $getdata = $res['getdata'];
        }
        $list = Db::name('payUpgrade')->where($where)->order('upgrade_time desc')->paginate(10,false,['query'=> $getdata]);
        
    	$count['count'] = Db::name('payUpgrade')->cache('3600')->count();
    	$count['sum'] = Db::name('payUpgrade')->cache('3600')->where('upgrade_state','in',array(1,5,6))->sum('upgrade_money');
    	$count['today'] = Db::name('payUpgrade')->cache('3600')->whereTime('upgrade_time', 'today')->where('upgrade_state','in',array(1,5,6))->sum('upgrade_money');
    	
    	$listType = Db::name('userType')->cache('3600')->select();
    	$this->assign('count',$count);
    	$this->assign('listType',$listType);
    	$this->assign('list',$list);
    	$this->assign('getdata',$getdata);
    	return $this->fetch();
    }
    
    
    /**
     * 订单搜索数据验证
     * @author yan  2018-01-16
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function ecickdata($data)
    {
		
        $where = array();
        $getdata = array();
      	
      	// 用户ID
        if(isset($data['phone']) && !empty($data['phone'])){
          	$uid = Db::name('user')->where(array('user_phone'=>$data['phone']))->value("user_id");
          	if($uid){
            	$where[] = array('upgrade_uid','=',$uid);
            	$getdata['phone'] = $data['phone'];
            }else{
            	$where[] = array('upgrade_uid','=','');
            	$getdata['phone'] = $data['phone'];
            }
        }

        //支付状态
        if(isset($data['state']) && !empty($data['state'])){
            $where[] = array('upgrade_state','=',$data['state']);
            $getdata['state'] = $data['state'];
        }
        //订单号
        if(isset($data['form_no']) && !empty($data['form_no'])){
            $where[] = array('upgrade_form_no','=',$data['form_no']);
            $getdata['form_no'] = $data['form_no'];
        }
        //上游订单号
        if(isset($data['sn']) && !empty($data['sn'])){
            $where[] = array('upgrade_sn','=',$data['sn']);
            $getdata['sn'] = $data['sn'];
        }

        //通道名称
        if(isset($data['type_id']) && !empty($data['type_id'])){
            $where[] = array('upgrade_type_id','=',$data['type_id']);
            $getdata['type_id'] = $data['type_id'];
        }
         
        //时间搜索
        if(isset($data['time']) && !empty($data['time'])){
            
            $where[] = array('upgrade_time','between time',array($data['time'],$data['time']+86399));
            $getdata['time'] = $data['time'];
        }
        

        $res['where'] = $where;
        $res['getdata'] = $getdata;
        return $res;
    }
    
    /*
     * 未支付订单修改为成功并修改状态
     * 2018年8月30日11:08:50
     * 刘媛媛
     */
    public function supply(){
    	
    	$id = input('get.id',0);
       
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        
        $upData = Db::name('payUpgrade')->where('upgrade_state',0)->where(array('upgrade_id'=>$id))->find();
        if(!$upData){
            return json(['error'=>1,'msg'=>'订单不存在或状态不允许此操作']);
        }
        Db::startTrans();
		try {
		    Db::name('payUpgrade')->where(array('upgrade_id'=>$upData['upgrade_id']))->update(['upgrade_state'=>1,'upgrade_oktime'=>time(),'upgrade_sn'=>time().'系统后台补单']);
		      	
		    Db::name('user')->where('user_id',$upData['upgrade_uid'])->update(['user_type_id'=>$upData['upgrade_type_id']]);
		   
          	//修改费率
            update_rate($upData['upgrade_uid'],$upData['upgrade_type_id']);
          	
           // 提交事务
		    Db::commit();
		     return json(['error'=>0,'msg'=>'操作成功']);
		} catch (\Exception $e) {
		    // 回滚事务
		    Db::rollback();
		    return json(['error'=>1,'msg'=>'操作失败']);
		}
       	
       	return json(['error'=>1,'msg'=>'操作失败ERROR']);
    }
    /*
     * 删除订单操作
     * 2018年8月30日11:15:07
     * 刘媛媛
     */
    public function delete(){
    	$id = input('get.id',0);
       
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        
        $upData = Db::name('payUpgrade')->where(array('upgrade_id'=>$id))->find();
        if(!$upData){
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        
        Db::name('payUpgrade')->where(array('upgrade_id'=>$id))->delete();
    	return json(['error'=>0,'msg'=>'删除成功']);
    }
    
    /*
     * 自动升级
     * 2018年8月30日11:15:34
     * 刘媛媛
     */
    public function upgrade(){
    	
    	if($this->request->isPost()) {
    		$id		  = input('get.uid',0);
	       	$userData = Db::name('user')->where(array('user_id'=>$id))->find();
	        if(!$userData){
	            return json(['error'=>1,'msg'=>'会员不存在']);
	        }
    		$type	  = input('post.type',0);
    		$state	  = input('post.state',0);
    		$liyou	  = input('post.liyou');
    		$typeData = Db::name('userType')->where(array('type_id'=>$type))->find();
    		
    		if(!$typeData){
    			return json(['error'=>1,'msg'=>'升级不存在']);
    		}
    		
    		if($userData['user_type_id']==$type){
    			return json(['error'=>1,'msg'=>'不能升级相同的类型']);
    		}

            Db::name('user')->where(array('user_id'=>$userData['user_id']))->update(['user_type_id'=>$type]);
            $upgrade = array();
            $upgrade['upgrade_uid'] 	= $userData['user_id'];
            $upgrade['upgrade_pay_id']  = 0;
            $upgrade['upgrade_form_no'] = 'system'.time();
            $upgrade['upgrade_sn'] 		= $liyou;
            $upgrade['upgrade_money']   = 0;
            $upgrade['upgrade_type_id'] = $type;
            $upgrade['upgrade_state']   = $state;
            $upgrade['upgrade_time'] 	= time();
            $upgrade['upgrade_oktime']  = time();
            Db::name('payUpgrade')->insert($upgrade);
            /*
            $reteList = Db::name('rate')->where('rate_type_id',$type)->select();
            foreach ($reteList as $rk=>$rv){
                
                //判断是否存在 
                if(Db::name('userRate')->where('rate_uid',$userData['user_id'])->where('rate_type',$rv['rate_type'])->find()){
                    //存在 直接修改
                    Db::name('userRate')
                    ->where('rate_uid',$userData['user_id'])
                    ->where('rate_type',$rv['rate_type'])
                    ->update(['rate_rate'=>$rv['rate_rate'],'rate_close_rate'=>$rv['rate_close_rate']]);
                }else{
                    //不存在直接新增
                    $retedata = array();
                    $retedata['rate_uid']        = $userData['user_id'];
                    $retedata['rate_rate']       = $rv['rate_rate'];
                    $retedata['rate_close_rate'] = $rv['rate_close_rate'];
                    $retedata['rate_type']       = $rv['rate_type'];
                    $retedata['rate_time']       = time();
                    Db::name('userRate')->insert($retedata);
                }
            }
	    */
            //修改会员费率
            update_rate($upgrade['upgrade_uid'],$upgrade['upgrade_type_id']);

            return json(['error'=>0,'msg'=>'升级成功']);
    	}
    	$id 		= input('get.uid',0);
       	$userData   = Db::name('user')->where(array('user_id'=>$id))->find();
        if(!$userData){
           $isUser  = '不存在';
        }else{
        	$isUser = '存在';
        }
        
        $listType = Db::name('userType')->cache('3600')->select();
    	$this->assign('listType',$listType);
        $this->assign('isUser',$isUser);
    	$this->assign('userData',$userData);
    	return $this->fetch();
    }
    
}