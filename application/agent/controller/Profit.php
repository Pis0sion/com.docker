<?php
namespace app\agent\controller;
use think\facade\Session;
use think\Controller;
use think\Db;
class Profit extends Base
{ 
	/**
	 * 分润报表
	 */
	public function Index(){
		//今日消费金额
		$list = Db::name('Agent')->where('agent_pid',session::get('agent_id'))->select();
		// dump($list);die;
		$date = getTree($list,session::get('agent_id'),'agent_pid','agent_id');
		dump($date);die;
		// return view();
	}

	/**
	 * 分润提现
	 */
	public function Putforward(){
		if($this->request->isPost()){

			$post = input('post.');
			if(empty($post)){
				return json(['error'=>1,'msg'=>'数据有误']);
			}

			if(!$post['bank']){
                return json(['error'=>1,'msg'=>'请选择提现银行卡']);
            }

            if($post['order_count']=='0' || $post['money_count']=='0'){
                return json(['error'=>1,'msg'=>'订单不存在或提现余额不足']);
            }

            $card = Db::name('AgentCard as a')
			->where(['card_id'=>$post['bank'],'card_agent_id'=>session::get('agent_id')])
			->find();
			if(!$card){
                return json(['error'=>1,'msg'=>'银行卡不存在']);
			}

			//再次查询数据防止修改页面数据
			$count = Db::name('AgentProfit')
			->where('profit_agent_id',session::get('agent_id'))
			->where(['profit_state'=>1])
            ->where('profit_pay','in','0,2')
			->where('profit_time','between',[strtotime($post['startime']),strtotime($post['endtime'])])
			->count('profit_id');

			$sunmoney = Db::name('AgentProfit')
			->where('profit_agent_id',session::get('agent_id'))
			->where(['profit_state'=>1])
            ->where('profit_pay','in','0,2')
			->where('profit_time','between',[strtotime($post['startime']),strtotime($post['endtime'])])
			->sum('profit_money');

			$date = Db::name('AgentProfit')
			->field('profit_id')
			->where('profit_agent_id',session::get('agent_id'))
			->where(['profit_state'=>1])
            ->where('profit_pay','in','0,2')
			->where('profit_time','between',[strtotime($post['startime']),strtotime($post['endtime'])])
			->select();

			$listarr = array();
			foreach ($date as $key => $val) {
				$listarr[$key] = $val['profit_id'];
			}
			if($post['order_count']!=$count||$post['money_count']!=$sunmoney){
				return json(['error'=>1,'msg'=>'请勿修改数据哦']);
			}

			$result = Db::name('AgentProfit')->where('profit_id','in',$listarr)->update(['profit_pay'=>3]);
			if($result){
				$data = array();
				$data['benefit_cid'] = $post['bank'];
				$data['benefit_agent_id'] = session::get('agent_id');
				$data['benefit_count'] 	  = $count;
				$data['benefit_money']	  = $sunmoney;
				$data['benefit_type']	  = 0;
				$data['benefit_time']	  = time();
				$data['benefit_starttime']= strtotime($post['startime']);
				$data['benefit_endtime']  = strtotime($post['endtime']);
				$data['benefit_ids']      = implode(',',$listarr);

				$res = Db::name('AgentBenefit')->insert($data);
				if($res){
					return json(['error'=>0,'msg'=>'提交成功']);
				}else{
					return json(['error'=>1,'smg'=>'提交失败']);
				}				
			}
		}else{
			//首次加载 默认上周的数据
			$star 	  = date(strtotime('-1 sunday', time()));
			$stratime = date(strtotime('-1 monday', $star));
			$endtime  = date(strtotime('-1 sunday', time()));

			// 订单笔数
			$count    = Db::name('AgentProfit')
			->where('profit_agent_id',session::get('agent_id'))
			->where(['profit_state'=>1])
            ->where('profit_pay','in','0,2')
			->where('profit_time','between',[$stratime,$endtime])
			->count('profit_id');

			$sunmoney = Db::name('AgentProfit')
			->where('profit_agent_id',session::get('agent_id'))
			->where(['profit_state'=>1])
            ->where('profit_pay','in','0,2')
			->where('profit_time','between',[$stratime,$endtime])
			->sum('profit_money');
			//个人的银行卡信息
			$bank = Db::name('AgentCard as a')
			->join('BankList b','a.card_bank_id=b.list_id')
			->field('a.card_id,b.list_name')
			->where('a.card_agent_id',session::get('agent_id'))
			->select();
			$this->assign('bank',$bank);
			$this->assign('count',$count);
			$this->assign('sunmoney',$sunmoney);
			return view();
		}
	}

	/**
	 * 下级分润表报
	 */
	public function underlevelrun(){
		$get = input('get.');

		if($get['pid']){
			$pid = $get['pid'];
		}else{
			$pid = session::get('agent_id');
		}
		// 获取下级关系代理
		// $proagent = $this->getagentSubs(session::get('agent_id'));

		if($get['account']){
            $where[] = array('agent_account','like','%'.$get['account']."%");
            $whereor[] = array('agent_name','like','%'.$get['account']."%");
        }
		$list = Db::name('agent')
			->field('agent_id')
			->where(['agent_pid'=>$pid])
			->where($where)
			->whereOr($whereor)
			->paginate(10,false,['query'=> $get]);

		$arrs = [];
		foreach($list as $k=>$v){
			$v = $v['agent_id'];
			$pid = $this->getAgs($v,'agent_pid');
			if($pid==session::get('agent_id')){
				$aginfo = '[直]'.$this->getAgs($v,'agent_account').'['.$this->getAgs($v,'agent_name').']';
			}else{
				$aginfo = $this->getAgs($v,'agent_account').'['.$this->getAgs($v,'agent_name').']';
			}

			$arrs['id']      = $v;
			$arrs['aginfo']  = $aginfo;
			$arrs['agsuper'] = $this->getAgs($pid,'agent_account').'--'.$this->getAgs($pid,'agent_name');

			$where = array('profit_agent_id'=>$v,'profit_state'=>1,'profit_pay'=>0);
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

			$agop = Db::name('agent')->where(['agent_pid'=>$v])->find();
			if($agop){
				$arrs['agop'] = 'yeval';
			}else{
				$arrs['agop'] = 'noeval';
			}

			// 今日总金额
			$todymoney  += Db::name('AgentProfit')
        	->where('profit_time','>',strtotime(date("Y-m-d"),time()))
        	->where('profit_agent_id',$v)
        	->sum('profit_amount');
			// 今日分润金额
			$todyprofit += Db::name('AgentProfit')
	    	->where('profit_time','>',strtotime(date("Y-m-d"),time()))
	    	->where('profit_agent_id',$v)
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

	// 我的分润明细
	public function perrunfine(){
		$keywords = input('get.');

        if(isset($keywords) && !empty($keywords)){
        	$map  = [];
        	$map  = $this->getwheres($keywords);
        }
        
      	if($keywords['texts']){
          	$wheres[] = array('user_name|user_phone|user_account|profit_form_no','like','%'.$keywords['texts']."%");
    	}
      
        $where = array('profit_state'=>1,'profit_pay'=>0,'profit_agent_id'=>session::get('agent_id'));
		$list  = Db::name('AgentProfit')->alias('p')
          	->where($where)
          	->where($wheres)
			->where($map)
            ->join('user u','u.user_id=p.profit_uid','LEFT')
          	->order('profit_id desc')
			->paginate(10,false,['query'=> $keywords]);
		foreach($list as $k=>$v){
			$data = array();
            $data = $v;

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
	    $sunmoney = Db::name('AgentProfit')->alias('p')
	    	->where($where)
          	->where($wheres)
			->where($map)
          	->join('user u','u.user_id=p.profit_uid','LEFT')
	    	->sum('profit_amount');
      	//总分润
      	$sunprofit = Db::name('AgentProfit')->alias('p')
      		->where($where)
          	->where($wheres)
			->where($map)
          	->join('user u','u.user_id=p.profit_uid','LEFT')
			->sum('profit_money');

		$this->assign('todymoney',$todymoney);
		$this->assign('todyprofit',$todyprofit);
		$this->assign('sunmoney',$sunmoney);
		$this->assign('sunprofit',$sunprofit);

		$this->assign('getdata',$keywords);
		$this->assign('list',$list);

        return view();
	}

	/**
	 * 选择时间，查询时间段数据
	 */
	public function Putforwardsub(){
		if($this->request->isAjax()){
			$post = input('post.');
			if(empty($post)){
				return json(['error'=>1,'msg'=>'请选择时间']);
			}

			$data 		   = array();
			$data['count'] = Db::name('AgentProfit')
			->where('profit_agent_id',session::get('agent_id'))
			->where(['profit_state'=>1,'profit_pay'=>0,'profit_pay'=>2])
			->where('profit_time','between',[strtotime($post['stra']),strtotime($post['end'])])
			->count('profit_id');

			$data['sunmoney'] = Db::name('AgentProfit')
			->where('profit_agent_id',session::get('agent_id'))
			->where(['profit_state'=>1,'profit_pay'=>0,'profit_pay'=>2])
			->where('profit_time','between',[strtotime($post['stra']),strtotime($post['end'])])
			->sum('profit_money');
			return json(['error'=>0,'msg'=>'ok','data'=>$data]);
		}else{
			return json(['error'=>1,'msg'=>'非法请求']);
		}
	}

	// 获取代理商信息
	function getAgs($id,$fied){
		if($id==0)return '无';
		$field = Db::name('agent')->where(array('agent_id'=>$id))->value($fied);
	    if(!$field){
	    	return '无';
	    }
	    return $field;	
	}
}