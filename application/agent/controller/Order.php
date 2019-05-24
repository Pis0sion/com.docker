<?php
namespace app\agent\controller;
use think\Controller;
use think\facade\Session;
use think\Db;

class Order extends Base
{
    /*
     * 还款订单列表
    */
    public function missionlist(){ // 时间 类型 用户名 手机号
    	$keywords = input('get.');
		
        if(isset($keywords) && !empty($keywords)){
        	$map  = [];
        	$map  = $this->missionwheres($keywords);
        }
		
    	$user = Db::name('user')->field('user_id')->where(['user_agent_id'=>session::get('agent_id')])->select();
    	$ids  = array_column($user, 'user_id');
    	$list = Db::name('mission')->alias('m')
            // ->where('mission_del',0)
    		->where('mission_uid','in',$ids)
    		->where($map)
            ->join('user u','u.user_id = m.mission_uid','LEFT')
            ->join('user_card uc','uc.card_id=m.mission_cid','LEFT')
            ->join('bank_list bank','bank.list_id=uc.card_bank_id','LEFT')
            ->order('mission_id desc')
            ->paginate(10,false,['query'=> $keywords]);

        $this->assign('getdata',$keywords);
        $this->assign('list',$list);
        return view();
    }

    // 还款订单 查询条件过滤
	public function missionwheres($keywords){
        $where = [];
		if($keywords['txts']){
            // $where[] = ['profit_form_no','=',$keywords['account']];
            $where[] = array('user_name|user_phone|user_account|mission_form_no','like','%'.$keywords['txts']."%");
    	}
    	if($keywords['type']){
            if($keywords['type']=='l'){
                $keywords['type'] = 0;
            }else{
                $keywords['type'] = $keywords['type'];
            }
            $where[] = array('mission_state','eq',$keywords['type']);
    	}
        // if(isset($keywords['starttime'])){
        //     if(empty($keywords['endtime'])){
        //       $where[] = ['mission_time', 'between time', [$keywords['starttime'], date('Y-m-d', time())]];
        //     }else{
        //       $where[] = ['mission_time', 'between time', [$keywords['starttime'], $keywords['endtime']]];
        //     }
          
        // }elseif(isset($keywords['endtime'])){
        //     if(empty($keywords['starttime'])){
        //       $where[] = ['mission_time', 'between time', ['1970-10-1', $keywords['endtime']]];
        //     }else{
              
        //     }
        // }
        return $where;
	}
    // 还款订单计划明细
    public function missdetails(){
        $get = input('param.',0);
        
        if(!$get['id']){
           return json(['error'=>1,'msg'=>'参数错误']);
        }
        $getdata = $where = array();
        //搜索条件
        $where[] = array('plan_mid','eq',$get['id']);
        //搜索条件
        if($get['txts']){
            $where[] = array('plan_form_no','like','%'.trim($get['txts'])."%");
        }
        if($get['type']){
            if($get['type']=='l'){
                $get['type']=0;
            }else{
                $get['type'] = $get['type'];
            }
            $where[] = array('plan_state','=',$get['type']);
        }
        $getdata = $get;
        $list = Db::name('plan')->alias('p')
            ->join('mission m','m.mission_id=p.plan_mid','LEFT')
            ->where($where)
            ->order('plan_sort asc ,plan_pay_time asc')
            ->paginate(20,false,['query'=> $getdata]);
        if($list->toArray()) {
            //0未启动 1还款中 2已还完 3还款失败
            foreach($list as $k=>$v){
                $data = array();
                $data = $v;
                if($v['plan_type']==1) {
                    $type='还款';
                } else if($v['plan_type']==2) {
                    $type='消费';
                }
                if($v['plan_state']==0) {
                    $status='未支付';
                } else if($v['plan_state']==1) {
                    $status='已支付';
                } else if($v['plan_state']==2) {
                    $status='还款失败 - '.$v['plan_msg'];
                } else if($v['plan_state']==3) {
                    $status='处理中';
                }

                $data['status'] = $status;
                $data['type_name'] = $type;
                $data['plan_money'] = (string)($v['plan_money']+$v['plan_fee']);
                $list->offsetSet($k,$data);
            }
        }
        $this->assign('list',$list);
        $this->assign('getdata',$getdata);
        return $this->view->fetch();
    }

    /*
     * 收款订单列表
    */
    public function recordslist(){
    	$keywords = input('get.');

        if(isset($keywords) && !empty($keywords)){
        	$map = [];
        	$map = $this->recordswheres($keywords);
        }
      	if(!$keywords['type']){
        	$map[] = array('records_state','eq',1);
        }
    	$user = Db::name('user')->field('user_id')->where(['user_agent_id'=>session::get('agent_id')])->select();
    	$ids  = array_column($user, 'user_id');
    	$list = Db::name('payRecords')->alias('pr')
    		->where('records_uid','in',$ids)
    		->where($map)
            ->field('pr.*,u.*,bank.list_id as back_id,bank.list_name as back_name')
            ->join('user u','u.user_id = pr.records_uid','LEFT')
            ->join('user_card uc','uc.card_id = pr.records_cid','LEFT')
            ->join('bank_list bank','bank.list_id = uc.card_bank_id','LEFT')
            ->order('records_id desc')
    		->paginate(10,false,['query'=> $keywords]);

        $this->assign('getdata',$keywords);
        $this->assign('list',$list);
        return view();
    	
    }

    // 收款订单 查询条件过滤
    public function recordswheres($keywords){
        $where = [];
        if($keywords['txts']){
            // $where[] = ['profit_form_no','=',$keywords['account']];
            $where[] = array('user_name|user_phone|user_account|records_form_no','like','%'.$keywords['txts']."%");
        }
        if($keywords['type']){
            $where[] = array('records_state','eq',$keywords['type']);
        }
        if(isset($keywords['starttime'])){
            if(empty($keywords['endtime'])){
              $where[] = ['records_time', 'between time', [$keywords['starttime'], date('Y-m-d', time()+9999999999)]];
            }else{
              $where[] = ['records_time', 'between time', [$keywords['starttime'], $keywords['endtime']]];
            }
          
        }elseif(isset($keywords['endtime'])){
            if(empty($keywords['starttime'])){
              $where[] = ['records_time', 'between time', ['1970-10-1', $keywords['endtime']]];
            }else{
              
            }
        }
        return $where;
    }

    /*
     * 升级订单列表
    */
    public function upgradelist(){
        $keywords = input('get.');

        if(isset($keywords) && !empty($keywords)){
        	$map  = [];
        	$map  = $this->upgradewheres($keywords);
        }
    	$user = Db::name('user')->field('user_id')->where(['user_agent_id'=>session::get('agent_id')])->select();
    	$ids  = array_column($user, 'user_id');
    	$list = Db::name('payUpgrade')->alias('pu')
    		->where('upgrade_uid','in',$ids)
    		->where($map)
          	->order('upgrade_id desc')
            ->join('user u','u.user_id = pu.upgrade_uid','LEFT')
    		->paginate(10,false,['query'=> $keywords]);
        $this->assign('getdata',$keywords);
        $this->assign('list',$list);
        return view();

    }

    // 升级订单 查询条件过滤 
    public function upgradewheres($keywords){
    	$where = [];
        if($keywords['txts']){
            // $where[] = ['profit_form_no','=',$keywords['account']];
            $where[] = array('user_name|user_phone|user_account|upgrade_form_no','like','%'.$keywords['txts']."%");
        }
        if($keywords['type']){
            if($keywords['type']=='l'){
                $keywords['type'] = 0;
            }else{
                $keywords['type'] = $keywords['type'];
            }
            $where[] = array('upgrade_state','eq',$keywords['type']);
        }
        if(isset($keywords['starttime'])){
            if(empty($keywords['endtime'])){
              $where[] = ['upgrade_time', 'between time', [$keywords['starttime'], date('Y-m-d', time()+9999999999)]];
            }else{
              $where[] = ['upgrade_time', 'between time', [$keywords['starttime'], $keywords['endtime']]];
            }
        }elseif(isset($keywords['endtime'])){
            if(empty($keywords['starttime'])){
              $where[] = ['upgrade_time', 'between time', ['1970-10-1', $keywords['endtime']]];
            }else{
              
            }
        }
        return $where;
    }
}
