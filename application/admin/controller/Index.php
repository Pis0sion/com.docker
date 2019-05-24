<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Index extends Base{
	public function __construct(){
       parent::__construct();
    }
    public function index(){
        $tongzhi['mission'] = Db::name('mission')->where('mission_state',1)->where('mission_type',1)->where('mission_del',0)->count();
        $this->assign('tongzhi',$tongzhi);
        $this->assign('tongzhi_count',array_sum($tongzhi));
    	return $this->fetch();
    }
    
    public function home(){
    	$count = array();
    	$count['user']  = Db::name('user')->cache(3600)->count();
    	$count['agent'] = Db::name('agent')->cache(3600)->count();
    	$count['benefit'] = Db::name('agentBenefit')->where('benefit_type',0)->cache(360)->count();
    	$count['upgrade'] = Db::name('payUpgrade')->where('upgrade_state',1)->cache(360)->count();//升级订单
    	$this->assign('count',$count);
    	$this->assign('feedback',$this->getFeedback());
    	$this->assign('usertype',$this->countUserType());
    	return $this->fetch();
    }
    protected function getFeedback(){
    	return Db::name('feedback')->where('feedback_state',1)->limit(5)->select();
    }
    
    protected function countUserType(){
    	$type =  Db::name('userType')->select();
    	$arr = array();
    	foreach ($type as $k=>$v){
    		$arr[] = [
    			'name'=>$v['type_name'],
    			'count'=>Db::name('user')->where('user_type_id',$v['type_id'])->cache(3600)->count()
    		];
    	}
    	
    	return $arr;
    }
}
