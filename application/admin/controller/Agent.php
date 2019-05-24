<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Agent extends Base{
	public function __construct(){
       parent::__construct();
    }
    
    /*
     * 后台添加代理商
     * 2018年9月28日17:42:49
     * 刘媛媛
     */
    
    public function addent(){
        
        $AGENT_GRADE = getconfig('AGENT_GRADE');
        if($this->request->isPost()){
            $post = input('post.');
            
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            if($post['agent_uid'] !== '0'){
                $list = Db::name('AgentRate')->where('rate_agent_id',$post['agent_uid'])->select();
                if(empty($list)){
                    return json(['error'=>1,'msg'=>'上级代理商未有费率，暂时不能进行添加代理']);
                }
            }
            
            if($AGENT_GRADE == 1)
            {
                $grade_id = isset($post['grade_id'])?$post['grade_id']:'0';
                $grate = Db::name('AgentGrade')->where(['grade_id'=>$grade_id,'grade_state'=>0])->find();

                if(empty($grate)){
                    return json(['error'=>1,'msg'=>'该等级费率不存在或已停用']);
                }

                $hk = trim($grate['grade_rate']);
                $sk = trim($grate['grade_rate_close']);
                $sj = trim($grate['grade_rate_upgrade']);
                $capacity = trim($grate['grade_capacity']);
                $agent_grade = $grade_id;

            }else{

                $hk = trim($post['hk']);
                $sk = trim($post['sk']);
                $sj = trim($post['sj']);
                $capacity = trim($post['capacity']);
                $agent_grade = 0;
            }

			if($post['agent_uid'] !== '0'){
				$arr = array();
				foreach ($list as $va) {
					$arr[$va['rate_type']] = $va['rate_rate'];
				}
				if($hk <= $arr['1']){
					return json(['error'=>1,'msg'=>'下级还款费率不能低于该代理当前的还款费率']);
				}
				if($sk <= $arr['2']){
					return json(['error'=>1,'msg'=>'下级收款费率不能低于该代理当前的收款费率']);
				}
                if($sj!=0){
    				if($sj >= $arr['3']){
    					return json(['error'=>1,'msg'=>'下级会员升级费率不能高于该代理当前的会员升级费率']);
                    }
    			}
			
			}
			
            $agentacc = Db::name('Agent')
            ->where(['agent_account'=>trim($post['account'])])
            ->whereOr(['agent_name'=>trim($post['name'])])
            ->whereOr(['agent_phone'=>trim($post['phone'])])
            ->find();
            if($agentacc){
                return json(['error'=>1,'msg'=>'该代理已存在']);
            }
			
			if($post['agent_uid'] !== '0'){
				$agent = Db::name('Agent')->where('agent_id',$post['agent_uid'])->find();

				if($capacity!=0){
					if($agent['agent_can_allot'] < $capacity){

						return json(['error'=>1,'msg'=>'该代理的可用承载量不足，无法进行下级代理划分']);
					}
					Db::name('Agent')->where('agent_id',$post['agent_uid'])->setDec('agent_can_allot',$capacity);
				}
			}
            //数据添加.
            $data = array();
            $data['agent_code']            = rand(1000,9999);
            $data['agent_pid']             = isset($post['agent_uid'])?$post['agent_uid']:'0';
            $data['agent_account']         = trim($post['account']);
            $data['agent_password']        = md5(trim($post['password']));
            $data['agent_name']            = trim($post['name']);
            $data['agent_phone']           = trim($post['phone']);
            $data['agent_idcard']          = trim($post['idcard']);
            $data['agent_capacity']        = $capacity;
            $data['agent_can_allot']       = $capacity;
            $data['agent_state']           = 0;
            $data['agent_time']            = time();
            $data['agent_ip']              = get_client_ip6();
            $data['agent_city']            = trim($post['city']);
            $data['agent_company']         = trim($post['agent_company']);
            $data['agent_grade']           = $agent_grade;
            $data['agent_region_province'] = trim($post['region_province']);
            $data['agent_region_city']     = trim($post['region_city']);
            $res = Db::name('Agent')->insertGetId($data);
            if($res){
                $result1 = Db::name('AgentRate')->insert(['rate_agent_id'=>$res,'rate_rate'=>$hk,'rate_type'=>1,'rate_time'=>time()]);
                $result2 = Db::name('AgentRate')->insert(['rate_agent_id'=>$res,'rate_rate'=>$sk,'rate_type'=>2,'rate_time'=>time()]);
                $result3 = Db::name('AgentRate')->insert(['rate_agent_id'=>$res,'rate_rate'=>$sj,'rate_type'=>3,'rate_time'=>time()]);
                if($result1&&$result2&&$result3){

                    return json(['error'=>0,'msg'=>'添加成功']);
                }

            }else{

                return json(['error'=>1,'msg'=>'添加失败']);
            }

        }


        $list = Db::name('agent')->where('agent_state',0)->select();
        $this->assign('AGENT_GRADE',$AGENT_GRADE);
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    public function Twolinkage(){
        $AGENT_GRADE = getconfig('AGENT_GRADE');
        if($this->request->isPost()){

            $post = input('post.');
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }
            if($post['id']==0){
                $list = Db::name('AgentGrade')->where('grade_state',0)->select();
            }else{
                $data = Db::name('Agent')->where('agent_id',$post['id'])->find();
                if(empty($data)){
                    return json(['error'=>1,'msg'=>'查无此代理']);
                }
                $list = Db::name('AgentGrade')->where('grade_state',0)->where('grade_sort','>',$data['agent_grade'])->select();                
            }

            
            return json(['error'=>0,'msg'=>'ok','data'=>$list]);

        }else{

            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    
  	// 查看银行卡详情
    public function crdsdetail(){
       $id = input('get.id');

       if(!$id){
          return json(['error'=>1,'msg'=>'参数错误']);
       }

       $data = Db::name('agentCard')->where(array('card_id'=>$id))->find();
       $data['bankname'] = Db::name('bankList')->where(['list_id'=>$data['card_bank_id']])->value('list_name');
       $bank = Db::name('bankList')->select();
       $this->assign('data', $data);
       $this->assign('bank', $bank);

       return view();
    }
  
    public function index(){
    	
        $keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $whereor[] = array('agent_account|agent_name|agent_phone|agent_idcard|agent_code','like','%'.$keywords."%");
           	$getdata['keywords'] =$keywords;
        }
       
        $list = Db::name('agent')->where($whereor)
            ->order('agent_time desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        return $this->fetch();
    }
    
    public function disagent(){
    	
    	$keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        $where[]= array('user_state','eq',1);
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $where[] = array('user_name','like','%'.$keywords."%");
            $where[] = array('user_phone','like','%'.$keywords."%");
            $where[] = array('user_idcard','like','%'.$keywords."%");
            $where[] = array('user_tcode','like','%'.$keywords."%");
            $where[] = array('user_account','like','%'.$keywords."%");
            $where[] = array('user_nickname','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('user')->where($where)
            ->order('user_time desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        return $this->fetch('index');
    }
    /*
     * 修改会员状态
     * 2018年8月27日11:24:40
     * 刘媛媛
     */
    public function updestate(){
    	$id = input('get.id',0);
       
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        
        $Agent = Db::name('agent')->where(array('agent_id'=>$id))->find();
        if(!$Agent){
            return json(['error'=>1,'msg'=>'会员不存在']);
        }
        
        if($Agent['agent_state']==1){
            Db::name('agent')->where(array('agent_id'=>$id))->update(['agent_state'=>0]);
        }else{
        	Db::name('agent')->where(array('agent_id'=>$id))->update(['agent_state'=>1]);
        }
        return json(['error'=>0,'msg'=>'操作成功']);
    }
    /*
     * 重置登陆密码
     * 2018年8月27日11:54:40
     * 刘媛媛
     */
    public function resets(){
    	$id = input('get.id',0);
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        $User = Db::name('agent')->where(array('agent_id'=>$id))->find();
        if(!$User){
            return json(['error'=>1,'msg'=>'会员不存在']);
        }
        
        Db::name('agent')->where(array('agent_id'=>$id))->update(['agent_password'=>md5('123456789')]);
        return json(['error'=>0,'msg'=>'重置密码成功<br/>123456789']);
    }
    /*
     * 查看下级会员
     * 2018年9月4日09:29:04
     * 刘媛媛
     */
    public function listus(){
    	$id = input('get.aid',0);
        if(!$id){
        	die('参数错误');
        }
        $Agent = Db::name('agent')->where(array('agent_id'=>$id))->find();
        if(!$Agent){
            die('代理商不存在');
        }
        $getdata[] = array('user_agent_id','eq',$id);
    	$list = Db::name('user')->where($getdata)
            ->order('user_time desc')
            ->paginate(30,false,['query'=> $getdata]);
        $this->assign('list',$list);
        return $this->fetch();
    	
    }
    /*
     * 查看下级代理
     * 2018年9月4日09:42:04
     * 刘媛媛
     */
    public function listag(){
    	
    	$id = input('get.aid',0);
        if(!$id){
        	die('参数错误');
        }
        $Agent = Db::name('agent')->where(array('agent_id'=>$id))->find();
        if(!$Agent){
            die('代理商不存在');
        }
        $getdata[] = array('agent_pid','eq',$id);
    	$list = Db::name('agent')->where($getdata)
            ->order('agent_time desc')
            ->paginate(30,false,['query'=> $getdata]);
        $this->assign('list',$list);
        return $this->fetch('index');
    }
    public function gologin(){
    	$id = input('get.id',0);
        if(!$id){
            $this->error('参数错误');
        }
        
        $agentData = Db::name('agent')->where('agent_id',$id)->find();
        
        if(!$agentData){
        	$this->error('代理商不存在');
        }
		
		session::set('agent_id',$agentData['agent_id']);
        session::set('agent_time',time());
        session::set('agent_user',$agentData['agent_user']);
        
		$this->redirect('/Agent/index');
    }

    // 编辑代理商
    public function aedit(){
        $AGENT_GRADE = getconfig('AGENT_GRADE');
        if($this->request->isPost()) {
            $post = input('post.');
            
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            if($post['agent_uid'] !== '0'){
                $list = Db::name('AgentRate')->where('rate_agent_id',$post['agent_uid'])->select();
                if(empty($list)){
                    return json(['error'=>1,'msg'=>'上级代理商未有费率，暂时不能进行添加代理']);
                }
            }
            if($AGENT_GRADE == 1)
            {
              $grade_id = $post['grade_id'];
              $grate = Db::name('AgentGrade')->where(['grade_id'=>$grade_id,'grade_state'=>0])->find();

              if(empty($grate)){
                return json(['error'=>1,'msg'=>'该等级费率不存在或已停用']);
              }

              $hk = trim($grate['grade_rate']);
              $sk = trim($grate['grade_rate_close']);
              $sj = trim($grate['grade_rate_upgrade']);
              $capacity = trim($grate['grade_capacity']);
              $agent_grade = $grade_id;

            }else{

              $hk = trim($post['hk']);
              $sk = trim($post['sk']);
              $sj = trim($post['sj']);
              $capacity = trim($post['capacity']);
              $agent_grade = 0;
            }
            if($post['agent_uid'] !== '0'){
                $arr = array();
                foreach ($list as $va) {
                  $arr[$va['rate_type']] = $va['rate_rate'];
                }
                if($hk <= $arr['1']){
                  return json(['error'=>1,'msg'=>'下级还款费率不能低于该代理当前的还款费率']);
                }
                if($hk <= $arr['2']){
                  return json(['error'=>1,'msg'=>'下级收款费率不能低于该代理当前的收款费率']);
                }
                if($sj!=0){
                   if($sj >= $arr['3']){
                      return json(['error'=>1,'msg'=>'下级会员升级费率不能高于该代理当前的会员升级费率']);
                    } 
                }
                

                $sonagent = Db::name('Agent')->where(['agent_id'=>trim($post['agent_uid'])])->find();
                $agent    = Db::name('Agent')->where(['agent_id'=>trim($post['aid'])])->value('agent_can_allot');
                $syagent_can_allot = $agent-$capacity+$sonagent['agent_can_allot'];
                if(empty($sonagent)){
                  return json(['error'=>1,'msg'=>'查无此代理信息']);
                }
                if($agent-$capacity < 0){
                    if($syagent_can_allot < 0){
                        return json(['error'=>1,'msg'=>'该代理的可用承载量不足，无法进行下级代理划分']);
                    }
                    if($syagent_can_allot > $sonagent['agent_can_allot']){

                        return json(['error'=>1,'msg'=>'该代理的可用承载量不足，无法进行下级代理划分']);
                    }                
                }
            }

            $data = array();
            $data['agent_pid']       = trim($post['agent_uid']);
            $data['agent_account']   = trim($post['account']);
            $data['agent_name']      = trim($post['name']);
            $data['agent_phone']     = trim($post['phone']);
            $data['agent_idcard']    = trim($post['idcard']);
            // $data['agent_capacity']  = trim($post['capacity']);
            $data['agent_can_allot'] = trim($post['capacity']);
            $data['agent_state']     = trim($post['agent_state']);
            $data['agent_city']      = trim($post['city']);
            $data['agent_company']   = trim($post['agent_company']);

            $res = Db::name('Agent')->where(['agent_id'=>$post['aid']])->update($data);

            $result3 = Db::name('Agent')->where('agent_id',trim($post['agent_uid']))->update(['agent_can_allot'=>$syagent_can_allot]);
            $result  = Db::name('AgentRate')->where(['rate_agent_id'=>$post['aid'],'rate_type'=>1])->update(['rate_rate'=>$hk]);
            $result1 = Db::name('AgentRate')->where(['rate_agent_id'=>$post['aid'],'rate_type'=>2])->update(['rate_rate'=>$sk]);
            $result2 = Db::name('AgentRate')->where(['rate_agent_id'=>$post['aid'],'rate_type'=>3])->update(['rate_rate'=>$sj]);

            if($result || $result1 || $result2 || $res){
              return json(['error'=>0,'msg'=>'修改成功']);
            }else{
              return json(['error'=>1,'msg'=>'修改失败']);
            }

        }

        $id = input('get.id',0);
        if(!$id){
            die('参数错误');
        }

        $re1 = Db::name('AgentRate')->where(['rate_agent_id'=>$id, 'rate_type'=>1])->value('rate_rate');
        $re2 = Db::name('AgentRate')->where(['rate_agent_id'=>$id, 'rate_type'=>2])->value('rate_rate');
        $re3 = Db::name('AgentRate')->where(['rate_agent_id'=>$id, 'rate_type'=>3])->value('rate_rate');

        $Agent = Db::name('agent')->where(array('agent_id'=>$id))->find();
        if(!$Agent){
            die('会员不存在');
        }
        $list = Db::name('agent')->where('agent_state',0)->select();

        $this->assign('AGENT_GRADE',$AGENT_GRADE);
        $this->assign('re1',$re1);
        $this->assign('re2',$re2);
        $this->assign('re3',$re3);
        $this->assign('list',$list);
        $this->assign('agent',$Agent);
        return $this->fetch();
    }

    // 代理商申请
    public function applyagent(){
        $keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $whereor[] = array('agent_recode_name','like','%'.$keywords."%");
            $whereor[] = array('agent_recode_phone','like','%'.$keywords."%");
            $whereor[] = array('agent_recode_company','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('agentRecord')->where($whereor)
            ->order('agent_recode_time desc')
            ->paginate(10,false,['query'=> $getdata]);
        // foreach($list as $k=>$v){
        //     $list[$k]['agent_user_id'] = Db::name('user')->where(['user_id'=>$v['agent_user_id']])->value('user_account');
        // }
        
        $this->assign('list',$list);
        return $this->fetch();
    }

    // 代理商申请批准、拒绝
    public function cagsta(){
        if($this->request->isPost()) {
            $post = input('post.');
            $id   = $post['id'];
            $type = $post['type'];

            if(!$id || !$type){
                return json(['error'=>1,'msg'=>'参数错误!']);
            }

            if($type == '1'){
                $list = Db::name('agentRecord')->where(['agent_recode_id'=>$id])->update(['agent_recode_state'=>1]);
                return json(['error'=>0,'msg'=>'批准成功!']);
            }else if($type == '2'){
                $list = Db::name('agentRecord')->where(['agent_recode_id'=>$id])->update(['agent_recode_state'=>2]);
                return json(['error'=>0,'msg'=>'已拒绝!']);
            }

        }
    }
    /*
     * 查看费率
     * 2018年10月11日14:39:14
     * 刘媛媛
     */
    public function getrate(){
    	$id = input('get.id');
    	
    	$data = Db::name('agent')->where(['agent_id'=>$id])->find();
    	
    	if(!$data){
    		die('代理商不存在');
    	}
    	
    	$list = Db::name('agentRate')->where(['rate_agent_id'=>$id])->select();
    	
    	foreach ($list as $k=>$v){
    		switch ($v['rate_type']) {
			    case 1:
			        $msg = '还款费率';
			        break;
			    case 2:
			        $msg = '收款费率';
			        break;
			    case 3:
			        $msg = '会员升级费率';
			        break;
			}
			
			echo '<p>'.$msg.'为：'.$v['rate_rate'].'</p>';
    	}
    	
    }
  
  	// 分润体现管理
    public function profitlist(){
        $get = input('get.');
        // 过滤分页参数、路径参数、空参数
        unset( $get['page']);
        unset( $get['pagesize']);
        array_shift($get);
        $data = array_filter($get);

        $map  = [];
        $getdata = $where =$map=array();
        if(isset($data) && !empty($data)){
            $getdata = $data;

            $map  = $this->getwhere($data);
        }

        $list = Db::name('agentBenefit')->where($map)->paginate(10, false, ['query'=> $getdata]);
        $this->assign('getdata', $getdata);
        $this->assign('list', $list);
        return view();
    }

    // 分润搜索条件过滤
    public function getwhere($data){
        $where = array();

        if(isset($data['agentid'])){
          $where[] = ['benefit_agent_id', '=', $data['agentid']];
        }

        if(isset($data['benefit_type'])){
            if($data['benefit_type'] == '1'){
               $bentype = '0';
            }else if($data['benefit_type'] == '2'){
               $bentype = '1';

            }else if($data['benefit_type'] == '3'){
               $bentype = '2';
            }
            $where[] =  ['benefit_type', 'eq', $bentype];
        }

        if(isset($data['starttime'])){
            if(empty($data['endtime'])){
              $where[] = ['benefit_time', 'between time', [$data['starttime'], date('Y-m-d', time())]];
            }else{
              $where[] = ['benefit_time', 'between time', [$data['starttime'], $data['endtime']]];
            }

        }elseif(isset($data['endtime'])){
            if(empty($data['starttime'])){
              $where[] = ['benefit_time', 'between time', ['1970-10-1', $data['endtime']]];
            }else{

            }
        }

        return $where;
    }

    // 分润申请处理
    public function profitsta(){

        $get = input('get.');
        $id   = $get['id'];
        $type = $get['type'];
        if(!$id || !$type) {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }
        

        $fine = Db::name('agentBenefit')->where(['benefit_id'=>$id])->find();
        if(!$fine) {
            return json(['error'=>1,'msg'=>'数据不存在']);
        }
        // 开启事务
        Db::startTrans();
        try{

            $date = Db::name('AgentProfit')
            ->field('profit_id')
            ->where('profit_agent_id',session::get('agent_id'))
            ->where(['profit_state'=>1,'profit_pay'=>3])
            ->where('profit_time','between',[$fine['benefit_starttime'],$fine['benefit_endtime']])
            ->select();

            $listarr = array();
            foreach ($date as $key => $val) {
                $listarr[$key] = $val['profit_id'];
            }
            // 修改分润数据状态
            if($type==1){
                $res = Db::name('AgentProfit')->where('profit_id','in',$listarr)->update(['profit_pay'=>1]);
              	Db::name('agentBenefit')->where(['benefit_id'=>$id])->update(['benefit_pay_time'=>time()]);
            }else if($type==2){
                $res = Db::name('AgentProfit')->where('profit_id','in',$listarr)->update(['profit_pay'=>2]);
            }
            // 修改提现记录状态
            $up = Db::name('agentBenefit')->where(['benefit_id'=>$id])->update(['benefit_type'=>$type]);
            if($up && $res){
                // 提交事务
                Db::commit(); 
                return json(['error'=>0,'msg'=>'修改成功']);
            }else{
                return json(['error'=>1,'msg'=>'修改失败']);
            }
            

        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
           return json(['error'=>1,'msg'=>'nills']);
        }
    }
  
  	// 提现详情
    public function withdetail(){
        $get = input('get.');
        // 过滤分页参数、路径参数、空参数
        $getdata = $where =$map=array();
        if(isset($get) && !empty($get)){
            $getdata = $get;
            $map  = [];
            $map  = $this->progetwhere($get);
        }
        $id = Db::name('agentBenefit')->where(['benefit_id'=>$get['id']])->value('benefit_ids');
        $list = Db::name('agentProfit')
        ->field('profit_id,profit_uid,profit_agent_id,profit_form_no,profit_amount,profit_money,profit_user_rate,profit_type,profit_time')
        ->where('profit_id','in',$id)
        ->where($map)
        ->paginate(10, false, ['query'=> $getdata]);
        
        foreach($list as $k=>$v){
            $account = Db::name('user')->where(['user_id'=>$v['profit_uid']])->value('user_account');
            $v['profit_uid'] = getUser($v['profit_uid'],'user_name').'['.$account.']';
            $v['profit_agent_id'] = getAgent($v['profit_agent_id'],'agent_account');
            $v['profit_user_rate'] = $v['profit_user_rate']*100;

            if($v['profit_type']==1){
              $v['profit_type'] = '还款';
            }else if($v['profit_type']==2){
              $v['profit_type'] = '收款分润';
            }else if($v['profit_type']==3){
              $v['profit_type'] = '普通用户激活';
            }else if($v['profit_type']==4){
              $v['profit_type'] = '升级';
            }
            $v['profit_time'] = date('Y-m-d H:i:s', $v['profit_time']);

           $list->offsetSet($k,$v);
        }
        
        $this->assign('getdata', $getdata);
        $this->assign('list', $list);
        return view();
    }

    public function progetwhere($keywords){
      $where = [];
      if($keywords['profit_form_no']){
            $where[] = ['profit_form_no','=',$keywords['profit_form_no']];
      }
      if($keywords['profit_type']){
            $where[] = ['profit_type','=',$keywords['profit_type']];
      }
        if(isset($keywords['starttime'])){
            if(empty($keywords['endtime'])){
              $where[] = ['profit_time', 'between time', [$keywords['starttime'], date('Y-m-d', time())]];
            }else{
              $where[] = ['profit_time', 'between time', [$keywords['starttime'], $keywords['endtime']]];
            }
          
        }elseif(isset($keywords['endtime'])){
            if(empty($keywords['starttime'])){
              $where[] = ['profit_time', 'between time', ['1970-10-1', $keywords['endtime']]];
            }else{
              
            }
        }
        return $where;
  }
  
    // 代理商分润列表
	public function underlevelrun(){
		$get = input('get.');

		if($get['account']){
            $where[] = array('agent_account','like','%'.$get['account']."%");
            $whereor[] = array('agent_name','like','%'.$get['account']."%");
        }
		$list = Db::name('agent')
			->field('agent_id')
			->where($where)
			->whereOr($whereor)
			->paginate(10,false,['query'=> $get]);

		$arrs = [];
		foreach($list as $k=>$v){
            $pid = $this->getAgs($v,'agent_pid');
			$id  = $v['agent_id'];
			$aginfo = $this->getAgs($v,'agent_account').'['.$this->getAgs($id,'agent_name').']';
			$arrs['id']      = $id;
			$arrs['aginfo']  = $aginfo;
          	if($this->getAgs($pid,'agent_account')){
            	$arrs['agsuper'] = $this->getAgs($pid,'agent_account').'--'.$this->getAgs($pid,'agent_name');
            }else{
            	$arrs['agsuper'] = '无';
            }

			$where = array('profit_agent_id'=>$id,'profit_state'=>1,'profit_pay'=>0);
			$arrs['couopens']  = Db::name('AgentProfit')
				->where($where)
				->count('profit_id'); // 订单笔数

			$arrs['couamount'] = Db::name('AgentProfit')
				->where($where)
				->sum('profit_amount'); // 总金额

			$arrs['skcoufenrun'] = Db::name('AgentProfit')
				->where(['profit_type'=>2])
				->where($where)
				->sum('profit_money'); // 总收款分润

			$arrs['hkcoufenrun'] = Db::name('AgentProfit')
				->where(['profit_type'=>1])
				->where($where)
				->sum('profit_money'); // 总还款分润
			$arrs['sjcoufenrun'] = Db::name('AgentProfit')
				->where(['profit_type'=>4])
				->where($where)
				->sum('profit_money'); // 总升级分润

			$arrs['coufenrun'] = Db::name('AgentProfit')
				->where($where)
				->sum('profit_money'); // 总分润

			// 今日总金额
			$todymoney  += Db::name('AgentProfit')
        	->where('profit_time','>',strtotime(date("Y-m-d"),time()))
        	->where('profit_agent_id',$id)
        	->sum('profit_amount');
			// 今日分润金额
			$todyprofit += Db::name('AgentProfit')
	    	->where('profit_time','>',strtotime(date("Y-m-d"),time()))
	    	->where('profit_agent_id',$id)
	    	->sum('profit_money');
	    	// 总金额
			$sunmoney += $arrs['couamount'];
			// 总分润
			$sunprofit += $arrs['coufenrun'];

            $list->offsetSet($k,$arrs);
		}
        $todymoney  = $todymoney?$todymoney:0;
        $todyprofit = $todyprofit?$todyprofit:0;
        $sunmoney   = $sunmoney?$sunmoney:0;
        $sunprofit  = $sunprofit?$sunprofit:0;

		$this->assign('todymoney',$todymoney);
		$this->assign('todyprofit',$todyprofit);
		$this->assign('sunmoney',$sunmoney);
		$this->assign('sunprofit',$sunprofit);
		$this->assign('getdata',$get);
		$this->assign('list',$list);
		return view();
	}
  	// 分润明细 查询条件过滤
	public function getwheres($keywords){
        $where = [];
      	
		if($keywords['profit_form_no']){
            $where[] = ['profit_form_no','=',$keywords['profit_form_no']];
          	
    	}
    	if($keywords['profit_type']){
            $where[] = ['profit_type','=',$keywords['profit_type']];
    	}
        if(isset($keywords['starttime'])){
            if(empty($keywords['endtime'])){
              $where[] = ['profit_time', 'between time', [$keywords['starttime'], date('Y-m-d', time()+999999)]];
            }else{
              $where[] = ['profit_time', 'between time', [$keywords['starttime'], $keywords['endtime']]];
            }
          
        }elseif(isset($keywords['endtime'])){
            if(empty($keywords['starttime'])){
              $where[] = ['profit_time', 'between time', ['1970-10-1', $keywords['endtime']]];
            }else{
              
            }
        }
        return $where;
	}
  
  	// 分润明细
	public function fenrunfine(){

		$keywords = input('get.');
		if(!$keywords['pid']){
			return json(['error'=>1,'msg'=>'参数错误']);
		}

        if(isset($keywords) && !empty($keywords)){
        	$map  = [];
        	$map  = $this->getwheres($keywords);
        }
        $pid   = $keywords['pid'];
        $where = array('profit_state'=>1,'profit_pay'=>0,'profit_agent_id'=>$pid);
		$list  = Db::name('AgentProfit')
			->where($where)
			->where($map)
			->paginate(10,false,['query'=> $keywords]);

		foreach($list as $k=>$v){
			$data = array();
            $data = $v;
            $account = Db::name('user')->where(['user_id'=>$data['profit_uid']])->value('user_account');

            $data['profit_uid'] = getUser($data['profit_uid'],'user_name').'['.$account.']';

            if($data['profit_type']==1){
            	$data['profit_type'] = '还款';
            }else if($data['profit_type']==2){
            	$data['profit_type'] = '收款分润';
            }else if($data['profit_type']==3){
            	$data['profit_type'] = '普通用户激活';
            }else if($data['profit_type']==4){
            	$data['profit_type'] = '升级';
            }
            $data['profit_time'] = date('Y-m-d H:i:s',$data['profit_time']);
            switch ($data['profit_pay'])
            {
			case 0:
			  $data['profit_pay'] = "未结算";
			  break;
			case 1:
			  $data['profit_pay'] = "已结算";
			  break;
			case 2:
			  $data['profit_pay'] = "已拒绝";
			  break;
			case 3:
			  $data['profit_pay'] = "处理中";
			  break;
			default:
			}
			$data['profit_agent_id'] = $this->getAgs($data['profit_agent_id'],'agent_name');
            $list->offsetSet($k,$data);
		}
		if(empty($list)) {
            return json(['error'=>1,'msg'=>'获取失败']);
        }

        // 今日消费金额
        $todymoney = Db::name('AgentProfit')
        	->where('profit_time','>',strtotime(date("Y-m-d"),time()))
        	->where('profit_agent_id',$pid)
        	->sum('profit_amount');
	      //今日分润
	    $todyprofit = Db::name('AgentProfit')
	    	->where('profit_time','>',strtotime(date("Y-m-d"),time()))
	    	->where('profit_agent_id',$pid)
	    	->sum('profit_money');
	      //总消费额
	    $sunmoney = Db::name('AgentProfit')
	    	->where($where)
			->where($map)
	    	->sum('profit_amount');
      	//总分润
      	$sunprofit = Db::name('AgentProfit')
      		->where($where)
			->where($map)
			->sum('profit_money');
		
		$this->assign('todymoney',$todymoney);
		$this->assign('todyprofit',$todyprofit);
		$this->assign('sunmoney',$sunmoney);
		$this->assign('sunprofit',$sunprofit);
		$this->assign('list',$list);
		$this->assign('getdata',$keywords);
		return view();
	}
  
  
  	// 获取代理商信息
	function getAgs($id,$fied){
		$field = Db::name('agent')->where(array('agent_id'=>$id))->value($fied);
	    return $field;	
	}
  
}
