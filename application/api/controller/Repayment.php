<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;
use think\facade\Cache;
class Repayment extends Base
{
    /**
     * 计划列表
     * @Author tw
     * @Date   2018-09-13
     */
    public function index()
    {
        if($this->request->isPost()) {
            $post  = input('post.');
            $uid   = $this->uid;//用户id
            $cid = $post['cid'];//信用卡id
            $state = isset($post['state'])?$post['state']:'';//计划状态
            $getdata = $where =array();
            $where['mission_uid'] = $uid;
            
            if($state<>''){
            
                $where['mission_state'] = $state;
            }
            if($cid){
               $where['mission_cid'] = $cid;
            }
            $list = Db::name('mission')->where($where)->order('mission_id desc')->paginate(10,false,['query'=> $getdata]);
            if(empty($list))
            {
                return json(['error'=>1,'msg'=>'没有计划']);
            }
            return json(['error'=>0,'msg'=>'成功','data'=>$list]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }

    /**
     * 银行卡列表
     * @Author tw
     * @Date   2018-09-13
     */
    public function card()
    {
        if($this->request->isPost()) { 

            $post = input('post.');
            $uid = $this->uid;//用户id
            $where['card_uid'] = $uid;
            $where['card_type'] = 1;
            $where['card_blocked'] = 0;
            $list = Db::name('user_card')->alias('c')
                    ->join('bank_list b','b.list_id=c.card_bank_id','LEFT')
                    ->where($where)
                    ->order('card_state desc')
                    ->select();
            if(empty($list))
            {
                return json(['error'=>1,'msg'=>'没有信用卡']);
            }

            return json(['error'=>0,'msg'=>'成功','data'=>$list]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    /*
     * 获取渠道
     * 2018年9月18日23:48:52
     * 刘媛媛
     */
    public function getChannel(){
        if($this->request->isPost()){
        	$bankid = input('post.bankid');

        	 
        	if(empty($bankid)){
                return json(['error'=>1,'msg'=>'参数错误']);
        	}
        	//->field('card_type,card_no,card_phone,card_name')

        	$bank  = Db::name('userCard')->where(['card_id'=>$bankid])->where('card_type',1)->where('card_uid',$this->uid)->find();
        	if(!$bank){
        		return json(['error'=>1,'msg'=>'银行卡不存在']); 
        	}

        	$bankList = Db::name('bankList')->where(['list_id'=>$bank['card_bank_id']])->find();
        	//$bank['bank_name'] = $bname;
			$bankList['list_logo'] = 'http://'.$_SERVER['HTTP_HOST'].$bankList['list_logo'];
			//2018-9-21 修改
            /*$list = Db::name('paymentChannel')->where('channel_use',1)->select();
            
            foreach($list as $key=>$vs){
            	$payment = Db::name('payment')
            	->where('payment_channel_id',$vs['channel_id'])
            	->where('payment_type',2)
            	->where('payment_use',1)->find();
            	if(!$payment){
            		unset($list['$key']);
            	}
            }*/
            //查询支付通道
            $list = Db::name('payment')->field('payment_id,payment_name,payment_bind')->where('payment_use','eq','1')->where('payment_type','eq','2')->select();
			if(empty($list))
			{
	            return json(['error'=>1,'msg'=>'无可用通道']);
			}
			foreach ($list as $k => $v) {
				//查询通道支持银行
				if(empty(Db::name('bank')->where(['bank_bid'=>$bank['card_bank_id'],'bank_pay_id'=>$v['payment_id']])->find()))
				{
					unset($list[$k]);
					continue;
				}

				if($v['payment_bind']==0)
				{
					$list[$k]['state'] = 1;//可用
                    $list[$k]['state_msg'] = '正常';
				}
				elseif($v['payment_bind']==1)
				{
					$payment_card = Db::name('payment_card')
											->where('card_pay_id','eq',$v['payment_id'])
											->where('card_pay_uid','eq',$uid)
											->where('card_cid','eq',$cid)
											->where('card_type','eq','1')
											->find();
					if(empty($payment_card) || $payment_card['card_state']==2)
					{
						$list[$k]['state'] = 0;//需要绑卡
                        $list[$k]['state_msg'] = '需要绑卡';
                        if($v['payment_bind_way']=='web')
                        {
                            $list[$k]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/repayment/bind_web/?pay_id='.$v['payment_id'].'&uid='.$this->uid.'&cid='.$bank['card_id'].'&token='.$this->token;
                        }
                        elseif($v['payment_bind_way']=='api')
                        {

                            $list[$k]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/repayment/bind_api';
                        }
					}
					elseif($payment_card['card_state']==3)
					{
						$list[$k]['state'] = 2;//绑卡中
                        $list[$k]['state_msg'] = '绑卡中';
					}
					else
					{
						$list[$k]['state'] = 1;//可用
                    	$list[$k]['state_msg'] = '正常';
					}
				}
			}
            $list = array_values($list);
			$datas = array();
			foreach ($list as $ks=>$vs){
				$datas[] = $vs;
			}
			unset($list);
            return json(['error'=>0,'msg'=>'成功','data'=>$datas,'bank'=>$bank,'bankList'=>$bankList]);
            


        }else{
            return json(['error'=>1,'msg'=>'参数错误']);
        }
    }
	/*
	 * 根据银行卡以渠道获取还款参数
	 * 2018年9月18日23:56:54
	 * 刘媛媛
	 */
	public function requests(){
		
		if($this->request->isPost()) { 
		 	$post   = input('post.');
		 	$pay_id = $post['pay_id'];//支付通道id
		 	$cid    = $post['cid'];
		 	$repayAmount    = $post['money'];
		 	$uid = $this->uid;
			//获取支付渠道配置信息
			//tw 2018-9-20
			/*$ChannelData =  Db::name('paymentChannel')->where('channel_id',$pay_id)->where('channel_use',1)->find();
			
			if(!$ChannelData){
				return json(['error'=>1,'msg'=>'渠道不存在']);
			}


			$payment =  Db::name('payment')
			->where('payment_channel_id',$ChannelData['channel_id'])
			->where('payment_type',2)
			->where('payment_use',1)
			->find();*/
			
			$payment =  Db::name('payment')
			->where('payment_id',$pay_id)
			->where('payment_type',2)
			->where('payment_use',1)
			->find();
			
			if(!$payment){
				return json(['error'=>1,'msg'=>'支付通道不存在']);
			}
			
			//通道是否需要绑卡
/*			if($payment['payment_bind']==1)
			{
				$payment_card = Db::name('payment_card')->where(['card_pay_id'=>$pay_id,'card_cid'=>$cid,'card_type'=>'1','card_state'=>'1'])->find();
				if(empty($payment_card))
				{
					return json(['error'=>1,'msg'=>'未绑定通道']);
				}
			}*/
			//获取卡信息
			$userBank = Db::name('userCard')
			->where('card_id',$cid)
			->where('card_blocked',0)
			->where('card_uid',$uid)
			->find();
			
			if(!$userBank){
				return json(['error'=>1,'msg'=>'银行卡不存在']);
			}


			if($userBank['card_type']!=1){
				return json(['error'=>1,'msg'=>'额..只支持信用卡哦']);
			}
			
			//绑卡的时候 生成的时候在去操作
			
			if($repayAmount>$payment['paymentst_money']){
                return json(['error'=>1,'msg'=>'最高计划金额为'.$payment['paymentst_money']]);
            }

			$rate = Db::name('userRate')->where(['rate_uid'=>$this->uid,'rate_type'=>1])->find();
			if(!$rate){
				return json(['error'=>1,'msg'=>'费率错误']);
			}

			//判断支付通道或者渠道是否支持此卡 暂时没写
			$bank = Db::name('bank')->where(['bank_bid'=>$userBank['card_bank_id'],'bank_pay_id'=>$payment['payment_id']])->find();
			if(empty($bank))
			{
				return json(['error'=>1,'msg'=>'通道不支持此银行']);
			}
			
			//通道允许最小金额
			if(!empty($bank['bank_min_money']) && $bank['bank_min_money']>=$payment['payment_min_money'] && $bank['bank_min_money']!='0.00')
			{
				$min_money = $bank['bank_min_money'];
			}
			else
			{
				$min_money = $payment['payment_min_money'];
			}
			//通道允许最大金额
			if(!empty($bank['bank_max_money']) && $bank['bank_max_money']<=$payment['payment_max_money'] && $bank['bank_max_money']!='0.00')
			{
				$max_money = $bank['bank_max_money'];
			}
			else
			{
				$max_money = $payment['payment_max_money'];
			}
			//开始生成计划任务算法 
			
			//计算平均数取整
			
			$list = array();

			if($payment['payment_mode']==1)
			{
				$min_money = $min_money * $payment['payment_pattern'];
				// $max_money = $max_money * $payment['payment_pattern'];
			}
			//根据数据库笔计算数量
			for($i=$payment['payment_num'];$i>0;$i--){
				if($max_money!=0){
					if($repayAmount/$i > $max_money){
						continue;
					}
				}
				
				if($min_money!=0){
					
					if($repayAmount/$i < $min_money){
						continue;
					}
				}
				
				$list[] = $i;//$this->dispersed($i,$repayAmount,$payment['payment_min_money'],$payment['payment_max_money'],$payment['payment_risk_start'],$payment['payment_risk_end'],$repayAmount/$i);
			}
			if(count($list)==0){
				return  json(['error'=>1,'msg'=>'此金额无法生成计划']);
			}
			//减少笔数
			if(count($list)>6){
				foreach ($list as $ks =>$v){
					if($ks==0 && ($repayAmount/$v)==$min_money && count($list)>1)
					{
						unset($list[$ks]);
					}
					if($ks%4!=0 and $ks!=1 and $ks != count($list)-1){
						unset($list[$ks]);
					}   
				}
			}
			foreach ($list as $v){

				$fre    = round(($repayAmount*$rate['rate_rate'])+($v*$rate['rate_close_rate']),2);
				$data[] =[
					'sum'  => round(($repayAmount)/$v+$fre,2).'以预览计划为准',
					'muber'=> $v,
					'fee'  => $fre
					];
			}
			return json(['error'=>0,'msg'=>'请求成功','data'=>$data]);
		}
	}
	/**
	 * 预览计划 2.0
	 * @Author tw
	 * @return [type] [description]
	 */
	public function preview(){
		$post   = input('post.');
		if(empty($post))
		{
			return json(['error'=>1,'msg'=>'预览计划失败,请重试!']);
		}
		$uid = $this->uid;
	 	$pay_id = $post['pay_id'];
	 	$cid    = $post['cid'];
	 	$repayAmount    = $post['money'];//还款或者自定义金额
	 	$type    = intval($post['type']);//0是自定义金额 其他为笔数
	 	$version = $post['version'];
	 	$region = $post['region'];//地区
		$city_id = $post['city_id'];//城市id
        $start_time = $post['start_time'];//开始时间
	 	$end_time = $post['end_time'];//结束时间

		$cacheName = md5($pay_id.$cid.$repayAmount.$type);//缓存用
		if($version!='2.0')
		{
	 		return json(['error'=>1,'msg'=>'请联系客服或更新app']);

		}
		if(empty($repayAmount))
	 	{
	 		return json(['error'=>1,'msg'=>'还款金额必填填写']);
	 	}
	 	if(empty($start_time))
	 	{
	 		return json(['error'=>1,'msg'=>'开始日期必填填写']);
	 	}
	 	if(empty($end_time))
	 	{
	 		return json(['error'=>1,'msg'=>'结束日期必填填写']);
	 	}
		//开始时间不能小于当天时间
        if(strtotime(date('Y-m-d',time())) > strtotime($start_time)){
	 		return json(['error'=>1,'msg'=>'开始日期不能小于当天日期']);
        }
        //结束日期必须大于开始日期
        if(strtotime($start_time) > strtotime($end_time)){
	 		return json(['error'=>1,'msg'=>'结束日期必须大于开始日期']);
        }
        $start_time_mew = $start_time;
        if(date("H")>=18 && date("Y-m-d",strtotime("$start_time"))==date("Y-m-d"))
        {
	 		$start_time_mew = date("Y-m-d",strtotime("+1 day",strtotime("$start_time")));
        }
        //总天数
        $day_count = diffBetweenTwoDays($start_time_mew,$end_time);


		$payment =  Db::name('payment')
			->where('payment_id',$pay_id)
			->where('payment_type',2)
			->where('payment_use',1)
			->find();

		if(!$payment){
			return json(['error'=>1,'msg'=>'请选择支付通道']);
		}
		if(!in_array($payment['payment_mode'], [1,2]))
		{
			return json(['error'=>1,'msg'=>'请联系管理员,升级配置']);
		}
		//商户注册
	 	$payment_user = Db::name('payment_user')->where('user_pay_id',$pay_id)->where('user_uid',$uid)->where('user_type',1)->find();

	 	if(empty($payment_user) || $payment_user['user_state']==0 || $payment_user['user_state']==2)
	 	{
	 		$res = Controller('pay/'.$payment['payment_controller'])->register($pay_id,$uid);

	 		if($res['error']=='1')
	 		{
	 			return json($res);
	 		}
	 	}
	 	
		//后台自定义笔数
		if($payment['payment_pattern']==0)
		{
			$pattern=1;
		}
		else
		{
			$pattern = $payment['payment_pattern'];
		}

		//获取卡信息
		$userBank = Db::name('userCard')->where('card_id',$cid)->where('card_blocked',0)->where('card_uid',$this->uid)->find();
		if(!$userBank){
			return json(['error'=>1,'msg'=>'银行卡不存在']);
		}
		if($userBank['card_type']!=1){
			return json(['error'=>1,'msg'=>'额..只支持信用卡哦']);
		}

		$payment_bank = Db::name('bank')->where(['bank_bid'=>$userBank['card_bank_id'],'bank_pay_id'=>$pay_id])->find();
		if(empty($payment_bank))
		{
			return json(['error'=>1,'msg'=>'不支持此银行卡']);
		}
		//最高计划金额
		if($repayAmount>$payment['paymentst_money']){
            return json(['error'=>1,'msg'=>'最高计划金额为'.$payment['paymentst_money']]);
        }
		$rate = Db::name('userRate')->where('rate_uid',$this->uid)->where('rate_type',1)->find();
		if(!$rate){
			return json(['error'=>1,'msg'=>'会员费率不存在']);
		}
		//通道允许最小金额
		if(!empty($payment_bank['bank_min_money']) && $payment_bank['bank_min_money']!='0.00'  && $payment_bank['bank_min_money']>=$payment['payment_min_money'])
		{
			$min_money = $payment_bank['bank_min_money'];
		}
		else
		{
			$min_money = $payment['payment_min_money'];
		}
		//通道允许最大金额
		if(!empty($payment_bank['bank_max_money']) && $payment_bank['bank_max_money']!='0.00' && $payment_bank['bank_max_money']<=$payment['payment_max_money'])
		{
			$max_money = $payment_bank['bank_max_money'];
		}
		else
		{
			$max_money = $payment['payment_max_money'];
		}
		if($payment['payment_mode']==1)
		{
			$min_money = $min_money * $payment['payment_pattern'];
		}
		if($repayAmount<$min_money)
		{
			return json(['error'=>1,'msg'=>'金额不能低于'.$min_money]);
		}

		if($type==0){
			$rand_money = rand($min_money,$max_money);
        	$num = ceil($repayAmount/$rand_money);//随机生成笔数
		}
		else
		{
			if($payment['payment_max_money']!=0){
				
				if(($repayAmount/$type) > $max_money){
					return  json(['error'=>1,'msg'=>'此笔数低于最小值,最小笔数为'.ceil($repayAmount/$max_money)]);
				}
			}
			if($payment['payment_min_money']!=0){
				if($repayAmount/$type < $min_money){
					return  json(['error'=>1,'msg'=>'此笔数超过过最大笔数,最大笔数为'.floor($repayAmount/$min_money)]);
				}
			}
			$num = $type;
		}

		// if(($num/$payment['payment_day_num']) > $day_count){
		// 	$end_day = ceil($num/$payment['payment_day_num']);
		// 	$end_day = date('Y-m-d',strtotime("$start_time_mew + $end_day day"));
		// 	return  json(['error'=>1,'msg'=>'还款结束日期大约在'.$end_day.'完成']);
        // }
		if($num==1)
		{
			$lists[0]= $repayAmount;
		}
		else
		{
			if($min_money==($repayAmount/$num) || $max_money==($repayAmount/$num) )
			{
				$i = 1;
				while ( $i <= $num) {
					$lists[] = $repayAmount/$num;
					$i ++ ;
				}
			}
			else
			{
				$lists = randnum_new($repayAmount,$num,$min_money,$max_money,$payment['payment_risk_start'],$payment['payment_risk_end'],true);
			}
		}

		$reserved_money = max($lists);
		$interval_time = $payment['payment_interval_time'] * 60;//最低计划间隔时间

		$interval_time_end = $interval_time + 60 * rand(5,10);
		// $single_time = (($interval_time + 60 * 30)/60/60); //单个计划执行所需时间
		$single_time = (($interval_time + 60 * 10)/60/60); //单个计划执行所需时间
		// $single_time = ($payment['payment_pattern']+1)*(($interval_time + 60 * 30)/60/60); //单个计划执行所需时间
		$day_max_num = floor(9/$single_time);
		$count_plan_num = count($lists)*($pattern+1);
		// $count_plan_num = count($lists);
		$day_min = ceil($count_plan_num/$day_max_num);
		if($day_count>2)
		{
			$count_day = $day_count-1;
		}
		else
		{
			$count_day = $day_count;
		}
		if($day_min >= $count_day){
			// $day_min ++;
			$end_day = date('Y-m-d',strtotime("$start_time_mew + $day_min day"));
			return  json(['error'=>1,'msg'=>'建议设置计划结束日期'.$end_day.'或之后完成计划']);
		}
		$day_num = ceil($count_plan_num/$day_count);
		if($day_num > $payment['payment_day_num'])
		{
			$day_num = $payment['payment_day_num'];
		}
		$need_day = $count_plan_num/$day_num;
		if($need_day<$day_min)
		{
			$need_day = $day_min;
		}
		$list_day_num = repayment_day_num($count_plan_num,$day_count,$day_max_num);
		$list_time = get_time_scope($start_time_mew,$end_time);//,$need_day);//时间列表

		foreach ($list_day_num as $key => $value) {
			if($value!=0)
			{
				$list_day_num_arr[] = $value;
				$list_time_arr[] = $list_time[$key];
			}
		}
		$list_day_num = $list_day_num_arr;
		$list_time = $list_time_arr;
		$time = date("Y-m-d",strtotime($list_time[0]));
		if($time==date("Y-m-d") && date('H')>9 && date('H')<18)
		{
			
			$time = date("Y-m-d H:i:s",time()+ rand(100,600));
			// $time = date("Y-m-d H:i:s");
		}
		else
		{
			
			$time = date("Y-m-d H:i:s",strtotime(date("Y-m-d 09:00:00",strtotime($time))) + rand(0,$interval_time_end));
			// $time = date("Y-m-d 09:00:00",strtotime($start_time_mew));
		}

		$paytime = '';//计划首次支付时间
		$i = 0;
		if($payment['payment_mcc']==1 || $payment['payment_region']==1)
		{
			if(empty($region))
			{
				$user_card = Db::name('user_card')->where(['card_blocked'=>0,'card_uid'=>$uid,'card_id'=>$cid])->find();
				$region = trim($user_card['card_province']).'-'.trim($user_card['card_city']);
			}
			$region_arr = explode('-', $region);
			if ($city_id) {
				$mcc_data = Controller('pay/'.$payment['payment_controller'])->mcc($pay_id,$uid,$cid,$city_id,$region);
			}
			elseif($region_arr)
			{
				$mcc_data = Controller('pay/'.$payment['payment_controller'])->query_city_mcc($pay_id,$uid,$cid,$region_arr[0],$region_arr[1],$region);
			}
			if($mcc_data['error']=='0')
			{
				$mcc_data = $mcc_data['data'];
				$mcc_data_count = count($mcc_data) - 1;
			}
			
		}
		$mission_consume = 0;//消费笔数
		$mission_repayment = 0;//还款笔数
		if($payment['payment_mode']==1)
    	{
			$min_money = $min_money / $payment['payment_pattern'];
		}
		$interval_time_s = $interval_time;
		$interval_time_end_s = $interval_time_end;
		foreach ($lists as $key => $money){

			// if(date("Y-m-d",strtotime($time))!=date("Y-m-d"))
			// {
				$payment['payment_day_num'] = $list_day_num[array_search(date("Y-m-d",strtotime($time)), $list_time)];
				$interval_time_end = (9/(($interval_time_s/60/60)*$payment['payment_day_num'])*60*60);
				if($interval_time<$interval_time_s)
				{
					$interval_time = $interval_time_s;
				}
				if($interval_time_end<$interval_time_end_s)
				{
					$interval_time_end = $interval_time_end_s;
				}
			// }

		
			$more_money = 0;//多的金额
			$i ++ ;
			$repayment_time = repayment_time($time,$i,$key,$payment,$interval_time,$interval_time_end,'1',$list_time);

			$time = $repayment_time['time'];

			$i = $repayment_time['i'];
			if(date("Y",strtotime($time))=='1970')
			{
				return $this->preview();
			}
			if($key==0)
			{
				$paytime = $time;
			}
	        if(strtotime(date("Y-m-d",strtotime($time))) > strtotime($end_time))
	        {
				return json(['error'=>1,'msg'=>'请延长结束日期']);
	        }
			//还款模式
            if($payment['payment_mode']==1)
        	{
        		//多刷一还
				$moneys = randnum_new($money,$pattern,$min_money,$max_money,$payment['payment_risk_start'],$payment['payment_risk_end'],true);
				foreach ($moneys as $k => $v) {
					$i ++ ;
					if($mcc_data)
					{
						$mcc_arr = $mcc_data[rand(0,$mcc_data_count)];
						$mcc = $mcc_arr['mcc'];
						$mcc_name = $mcc_arr['name'];
					}
					//支付模式 0无余额 1有余额 
					if($payment['payment_pay_mode']==0)
					{
						$fee = $rate['rate_rate'] * $v + $rate['rate_close_rate']/$pattern;
					}
					elseif($payment['payment_pay_mode']==1)
					{
				        $fee = $rate['rate_rate'] * $v + $rate['rate_close_rate']/$pattern;
				        $fee = round($fee,2);
						$fee = repayment_fee($v,$fee,$rate['rate_rate'],$rate['rate_close_rate']/$pattern);
					}
					//扣款无小数手续费计算 不匹配所有通道
					$sum_money = $v+$fee;
					if($payment['payment_money_mode']==1)
					{
						
						$int_money = floor($sum_money);
						if($int_money!=$sum_money && ($sum_money - $int_money) <= $more_money)
						{
							$fee =$fee - ($sum_money - $int_money);
							$more_money = $more_money - ($int_money - $sum_money);
						}
						else
						{
							$int_money = ceil($sum_money);
							if($int_money!=$sum_money)
							{
								if(($int_money - $sum_money) < $more_money)
								{
									$fee =$fee - ($int_money - $sum_money);
									$more_money = $more_money - ($int_money - $sum_money);
								}
								else
								{
									$fee =$fee + ($int_money - $sum_money);
									$more_money = $more_money + ($int_money - $sum_money);
								}
							}
						}
					}

					$plan['money'] = (string)$v;
					$plan['time'] = (string)$time;
					$plan['type'] = '2';
					$plan['fee'] = (string)round($fee,2);
					$plan['sort'] = (string)($key+1);
					$plan['sum_money'] = (string)($plan['money']+$plan['fee']);
					$plan['mcc'] = $mcc;
					$plan['mcc_name'] = $mcc_name;
					$plan['i'] = $i;
					$list[$key][] = $plan;

					// $time = date("Y-m-d H:i:s",strtotime($time) + rand($interval_time,$interval_time_end)); //时间
					$repayment_time = repayment_time($time,$i,$key+1,$payment,$interval_time,$interval_time_end,2,$list_time);
					$time = $repayment_time['time'];
					$i = $repayment_time['i'];

					if(date("Y",strtotime($time))=='1970')
					{
						return $this->preview();
					}

					$free = $free + $plan['fee'];//总手续费
					$total_money = $total_money + $plan['sum_money'];//总消费金额
					unset($plan);
					$mission_consume ++;//消费笔数
				}
	        	$plan['money'] = (string)$money;
	        	$plan['time'] = (string)$time;
	        	$plan['type'] = '1';
		        $plan['fee'] = '0';
		        $plan['sort'] = (string)($key+1);
				$plan['i'] = $i;
	        	$list[$key][] = $plan;
				unset($plan);
				$mission_repayment ++;//还款笔数
				// $time = date("Y-m-d H:i:s",strtotime($time) + rand($interval_time,$interval_time_end)); //时间
        	}
            elseif($payment['payment_mode']==2)
        	{
	        	if($payment['payment_pay_mode']==0)
	        	{
	        		$fee = $rate['rate_rate'] * $money + $rate['rate_close_rate']/$pattern;
	        	}
	        	elseif($payment['payment_pay_mode']==1)
	        	{
			        $fee = $rate['rate_rate'] * $money + $rate['rate_close_rate']/$pattern;
			        $fee = round($fee,2);
	        		$fee = repayment_fee($money,$fee,$rate['rate_rate'],$rate['rate_close_rate']/$pattern);
	        	}

		        	$plan['money'] = (string)$money;
		        	$plan['time'] = (string)$time;
		        	$plan['type'] = '2';
		        	$plan['fee'] = (string)round($fee,2);
		        	$plan['sort'] = (string)($key+1);
		        	$plan['sum_money'] = (string)($plan['money']+$plan['fee']);
					$list[$key][] = $plan;
					
					// $time = date("Y-m-d H:i:s",strtotime($time) + rand($interval_time,$interval_time_end)); //时间
					$repayment_time = repayment_time($time,$i,$key+1,$payment,$interval_time,$interval_time_end,2,$list_time);
					$time = $repayment_time['time'];
					$i = $repayment_time['i'];
					if(date("Y",strtotime($time))=='1970')
					{
						return $this->preview();
					}

					$free = $free + $plan['fee'];//总手续费
					$total_money = $total_money + $plan['sum_money'];//总消费金额
					unset($plan);

					$mission_consume ++;//消费笔数
		        
				$moneys = randnum_new($money,$pattern,$min_money,$max_money,$payment['payment_risk_start'],$payment['payment_risk_end'],true);
		        foreach ($moneys as $k => $v) {
		        	$plan['money'] = (string)$v;
		        	$plan['time'] = (string)$time;
		        	$plan['type'] = '1';
			        $plan['fee'] = '0';
			        $plan['sort'] = (string)($key+1);
		        	$list[$key][] = $plan;
					unset($plan);
					$mission_repayment ++;//还款笔数
				}
				// $time = date("Y-m-d H:i:s",strtotime($time) + rand($interval_time,$interval_time_end)); //时间
        	}
		}
		if($region=='-')
		{
			$region = '';
		}
		$sumMoneyfree = ceil($reserved_money+$free);
		$cachedata = [
			'error'=>0,
			'msg'=>'成功',
			'sumMoneyfree'=>$sumMoneyfree,//预留金额
			'total_money'=>$total_money,//总消费金额
			'sumMoney'=>$repayAmount,//还款金额
			'free'=>$free,//总手续费
			'start_time'=>date("Y-m-d",strtotime($start_time)),//开始时间
			'end_time'=>date("Y-m-d",strtotime($end_time)),//结束时间
			'paytime'=>$paytime,//执行时间
			'region'=>$region,//地区
			'mission_consume'=>$mission_consume,//消费笔数
			'mission_repayment'=>$mission_repayment,//还款笔数
			'data'=>$list,
		];
 		Cache::set($cacheName,$cachedata,1200);
		return json($cachedata);
	}
	/**
	 * 编辑预览计划 行业信息
	 * @Author tw
	 * @return [type] [description]
	 */
	public function preview_edit(){
		$post   = input('post.');
		if(empty($post))
		{
			return json(['error'=>1,'msg'=>'预览计划失败,请重试!']);
		}
		$uid = $this->uid;
	 	$pay_id = $post['pay_id'];
	 	$cid    = $post['cid'];
	 	$repayAmount    = $post['money'];//还款或者自定义金额
	 	$type    = intval($post['type']);//0是自定义金额 其他为笔数
	 	// $version = $post['version'];
	 	// $region = $post['region'];//地区
		// $city_id = $post['city_id'];//城市id
        // $start_time = $post['start_time'];//开始时间
	 	// $end_time = $post['end_time'];//结束时间

	 	$cachedata = $post['data'];//json缓存数据
	 	if($cachedata['error']!=0)
	 	{
			return json(['error'=>1,'msg'=>'更改错误,请重试']);
	 	}
		$cacheName = md5($pay_id.$cid.$repayAmount.$type);//缓存用
		Cache::set($cacheName,$cachedata,1200);
	   	return json($cachedata);
	}
	/**
	 * 获取通道支持的省市
	 * @Author tw
	 * @return [type] [description]
	 */
	public function city()
	{
		$post   = input('post.');
		if(empty($post))
		{
			return json(['error'=>1,'msg'=>'获取地区失败']);
		}
		$id = $post['id'];//城市id 默认0
		$uid = $this->uid;//用户id
		$pay_id = $post['pay_id'];//支付通道id


        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }

        $result = Controller('pay/'.$payment_controller)->city($pay_id,$uid,$id);
        return josn($result);
	}
	/**
	 * 获取所有通道支持所有省市
	 * @Author tw
	 * @return [type] [description]
	 */
	public function city_all()
	{
		$post   = input('post.');
		if(empty($post))
		{
			return json(['error'=>1,'msg'=>'获取地区失败']);
		}
		$id = $post['id'];
		$uid = $this->uid;//用户id
		$pay_id = $post['pay_id'];//支付通道id


        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }

        $result = Controller('pay/'.$payment_controller)->city_all($pay_id,$uid,$id);
        return json($result);
	}
	
	/**
	 * 生成计划
	 * @Author tw
	 * @Date   2018-09-25
	 * @return [type]     [description]
	 */
	public function create(){
			$post   = input('post.');
			if(empty($post))
			{
				return json(['error'=>1,'msg'=>'请求参数错误']);
			}
			$uid = $this->uid;//用户id
		 	$pay_id = $post['pay_id'];//支付通道id
		 	$cid    = $post['cid'];//银行卡id
		 	$repayAmount    = $post['money'];//还款或者自定义金额
		 	$type    = intval($post['type']);//0是自定义金额 其他为笔数
			$version = $post['version'];//版本号
			$region = $post['region'];//地区
			
			if($region=='-')
			{
				$region = '';
			}
			$city_id = $post['city_id'];//城市id
			$start_time = $post['start_time'];//开始时间
			$end_time = $post['end_time'];//结束时间
			if($version!='2.0')
			{
				return json(['error'=>1,'msg'=>'请联系客服或更新app']);
			}
			$payment =  Db::name('payment')
				->where('payment_id',$pay_id)
				->where('payment_type',2)
				->where('payment_use',1)
				->find();
			if(!$payment){
				return json(['error'=>1,'msg'=>'请选择支付通道']);
			}

		 	//商户注册
		 	$payment_user = Db::name('payment_user')->where('user_pay_id',$pay_id)->where('user_uid',$uid)->where('user_type',1)->find();

		 	if(empty($payment_user) || $payment_user['user_state']==0 || $payment_user['user_state']==2)
		 	{
		 		$res = Controller('pay/'.$payment['payment_controller'])->register($pay_id,$uid);

		 		if($res['error']=='1')
		 		{
		 			return json($res);
		 		}
		 	}
			//通道是否需要绑卡
			if($payment['payment_bind']==1)
			{
				$payment_card = Db::name('payment_card')->where(['card_pay_id'=>$pay_id,'card_cid'=>$cid,'card_type'=>'1','card_state'=>'1'])->find();
				if(empty($payment_card) || $payment_card['card_state']!=1)
	            {
	                if($payment['payment_bind_way']=='web')
	                {
	                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/api/repayment/bind_web/?pay_id='.$pay_id.'&uid='.$this->uid.'&cid='.$cid.'&token='.$this->token;
	                }
	                elseif($payment['payment_bind_way']=='api')
	                {
	                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/api/repayment/bind_api';
	                }
	                else
	                {
	                	return json(['error'=>1,'msg'=>'绑卡参数错误']);
	                }
	                return json(['error'=>'10','msg'=>'请验证绑卡信息','type'=>$payment['payment_bind_way'],'title'=>'绑卡验证','url'=>$url]);
	            }
			}
			
			if(empty($repayAmount))
			{
				return json(['error'=>1,'msg'=>'还款金额必填填写']);
			}
			elseif(empty($start_time))
			{
				return json(['error'=>1,'msg'=>'开始日期必填填写']);
			}
			elseif(empty($end_time))
			{
				return json(['error'=>1,'msg'=>'结束日期必填填写']);
			}
			//开始时间不能小于当天时间
			if(strtotime(date('Y-m-d',time())) > strtotime($start_time)){
				return json(['error'=>1,'msg'=>'开始日期不能小于当天日期']);
			}
			//结束日期必须大于开始日期
			if(strtotime($start_time) > strtotime($end_time)){
				return json(['error'=>1,'msg'=>'结束日期必须大于开始日期']);
			}
			$cacheName = md5($pay_id.$cid.$repayAmount.$type);//缓存用
			$retCache  = Cache::get($cacheName);
			if(empty($retCache))
			{
				return json(['error'=>1,'msg'=>'请求超时,请重新生成计划,错误代码['.$pay_id.'-'.$uid.'-'.$cid.']']);
			}
			
			$state = Db::name('user_card')->where('card_id',$cid)->where('card_type',1)->find();
			if($state['card_state']==1)
			{
				if(Db::name('mission')->where('mission_cid',$cid)->where('mission_state',1)->where('mission_del',0)->find())
				{
					return json(['error'=>1,'msg'=>'已有计划,不能重复生成!']);
				}
				
			}
			$rate = Db::name('userRate')->where('rate_uid',$this->uid)->where('rate_type',1)->find();
			if(!$rate){
				return json(['error'=>1,'msg'=>'会员费率不存在']);
			}
		 	$list = $retCache['data'];//还款集合
			$totalfee = $retCache['free'];//手续费
			// $total_money = $retCache['total_money'];//总金额
			$mission_consume = $retCache['mission_consume'];//消费笔数
			$mission_repayment = $retCache['mission_repayment'];//还款笔数
			$paytime = $retCache['paytime'];//执行时间

			$this->payment_logs($post,$retCache,'test');
			$start_time = date("Y-m-d",strtotime($start_time));
			$end_time = date("Y-m-d",strtotime($end_time));
			if($retCache['start_time']!=$start_time)
			{
				return json(['error'=>1,'msg'=>'计划开始时间不匹配,请重新生成计划']);
			}
			elseif($retCache['end_time']!=$end_time)
			{
				return json(['error'=>1,'msg'=>'计划结束时间不匹配,请重新生成计划']);
			}
			elseif($retCache['region']!=$region)
			{
				$region = $retCache['region'];
				//return json(['error'=>1,'msg'=>'计划地址不匹配,请重新生成计划'.$retCache['region']]);
			}


			if($post['test']==1)
			{
				return json(['error'=>1,'msg'=>'测试计划']);
				exit();
			}
		 	$mission['mission_uid'] = $uid;
            $mission['mission_cid'] = $cid;
            $mission['mission_pay_id'] = $pay_id;
            $mission['mission_state'] = 1;
            $mission['mission_type'] = 0;
            $mission['mission_form_no'] = 'CU'.date('YmdHis',time()).$uid.rand(1000, 9999);
            $mission['mission_money'] = $repayAmount;
            $mission['mission_repayment'] = $mission_repayment;
            $mission['mission_consume'] = $mission_consume;
            $mission['mission_fee'] = $totalfee;
            $mission['mission_rate'] = $rate['rate_rate'];
            $mission['mission_close_rate'] = $rate['rate_close_rate'];
            $mission['mission_start_time'] = $start_time;
            $mission['mission_end_time'] = $end_time;
            $mission['mission_pay_time'] = $paytime;
            $mission['mission_time'] = date('Y-m-d H:i:s');
            $mission['mission_region_id'] = $region;
            $mission['mission_mcc'] = $city_id;
            $mission['mission_flag'] = $payment['payment_pattern'];
            $mission['mission_close'] = 1; //1允许用户删除
			$mission_id = Db::name('mission')->insertGetId($mission);
            if(empty($mission_id))
            {
                return json(['error'=>1,'msg'=>'计划生成失败']);
			}
            foreach ($list as $key => $plan_all) {
				$oids = array();
				foreach ($plan_all as $k => $v) {
					$data['plan_uid'] = $uid;
					$data['plan_mid'] = $mission_id;
					$data['plan_money'] = $v['money'];
					$data['plan_type'] = $v['type'];
					$data['plan_pay_time'] = $v['time'];
					$data['plan_fee'] = $v['fee'];
					$data['plan_sort'] = $v['sort'];
					$data['plan_pay_id'] = $pay_id;

					if($v['type']==2)
					{
						$data['plan_mcc'] = $v['mcc'];
						$data['plan_mcc_name'] = $v['mcc_name'];
						$data['plan_form_no'] = get_order_sn('P',$mission_id).$key.$k;
						$plan_id = Db::name('plan')->insertGetId($data);
						$oids[] = $plan_id;
					}
					elseif($v['type']==1)
					{
						$data['plan_form_no'] = get_order_sn('C',$mission_id).$key.$k;
						$data['plan_oids'] = implode(",",$oids);
						$plan_id = Db::name('plan')->insertGetId($data);
					}
					unset($data);
				}
			}
			Db::name('user_card')->where(['card_id'=>$cid])->update(['card_state'=>1]);
			Cache::rm($cacheName);
			if($payment['payment_paynow']=='1')
			{
				$url = 'http://'.$_SERVER['HTTP_HOST'].'/pay/'.$payment['payment_controller'].'/pay_web?id='.$mission_id;
				return json(['error'=>'10','msg'=>'计划生成成功','mid'=>$mission_id,'type'=>'pay_web','title'=>'支付验证','url'=>$url]);
			}

            return json(['error'=>0,'msg'=>'计划生成成功','mid'=>$mission_id]);
		
	}
	
	
	
	
	/**
	 * 获取卡对应的支付通道
	 * @Author tw
	 * @Date   2018-09-20
	 * @return [type]     [description]
	 */
	public function getbankpayment()
	{
		$post  = input('post.');
		if(empty($post))
		{
            return json(['error'=>1,'msg'=>'非法请求']);
		}

		$uid = $this->uid;//用户id
		$cid = $post['cid'];//用户卡id

		//获取银行卡信息
		$card = Db::name('user_card')->where(['card_id'=>$cid,'card_type'=>1])->find();
		if(empty($card))
		{
            return json(['error'=>1,'msg'=>'银行卡不存在']);
		}
			
		//获取可用支付渠道
		$channel_id = Db::name('payment_channel')->where(['channel_use'=>1])->order('channel_id')->column('channel_id');
		if(empty($channel_id))
		{
            return json(['error'=>1,'msg'=>'无可用通道']);
		}
		$payment = Db::name('payment')->field('payment_id,payment_name,payment_bind,payment_region,payment_mcc')->where('payment_use','eq','1')->where('payment_type','eq','2')->whereIn('payment_channel_id',$channel_id)->select();
		if(empty($payment))
		{
            return json(['error'=>1,'msg'=>'无可用通道']);
		}
		foreach ($payment as $k => $v) {
			//查询通道支持银行
			$bank = Db::name('bank')->where(['bank_bid'=>$card['card_bank_id'],'bank_pay_id'=>$v['payment_id']])->find();
			if(empty($bank))
			{
				unset($payment[$k]);
				continue;
			}

			if($v['payment_bind']==0)
			{
				$payment[$k]['state'] = 1;
			}
			elseif($v['payment_bind']==1)
			{
				$payment_card = Db::name('payment_card')
										->where('card_pay_id','eq',$v['payment_id'])
										->where('card_pay_uid','eq',$uid)
										->where('card_cid','eq',$cid)
										->where('card_type','eq','1')
										->where('card_state','eq','1')
										->find();
				if(empty($payment_card))
				{
					$payment[$k]['state'] = 0;
				}
				else
				{
					$payment[$k]['state'] = 1;
				}
			}
		}
        return json(['error'=>0,'msg'=>'成功','data'=>$payment]);

	}
	/*
	 * 根据笔数总金额生成计划
	 * $num 笔数
	 * $money 共计金额
	 * $min 最小
	 * $max 最大
	 * $riskStart 风控值
	 * $riskEnd 风控值
	 * $dengcha 平均数
	 * 2018年9月18日23:18:50
	 * 刘媛媛
	 */
	
	
	
	public function dispersed($num,$money,$min,$max,$riskStart,$riskEnd,$dengcha){
		
		/*
		980-242/3 = 246 
		980-242-246/2 = 246
		980-242-246-246 = 260
		*/
		$Newsrisk = intval($riskEnd/$num);
		$list  = array();
		$count = 0; 
		for($n=0;$n<$num;$n++){
			if($n==0){
				$list[] = round($dengcha+(rand(1,100)*0.01)-rand($riskStart,$Newsrisk),2);
			}else{
				$cha = 0;
				foreach($list as $value ){
					$cha += $value;
				}
				
				//echo $cha;
				//echo '<br/>';
				if(($num-1)==$n){
					$nodess = 0;
					$nodess = $money-$cha;
					$tpl    = $nodess;
					if($max!=0){
						
						if($nodess > $max){
							$nodess = $max-(rand(1,100)*0.01);
						}
						
						sort($list);
					
						$list[0] = round($tpl-$nodess+$list[0],2);
						
					}
					$list[] = round($nodess,2);
				}else{
					$nodes = intval(($money-$cha)/($num-$n))+(rand(1,100)*0.01)-rand($riskStart,$Newsrisk);
					if($min!=0){
						if($nodes  < $min){
							$nodes = $min+(rand(1,100)*0.01)+rand($riskStart,$Newsrisk);
						}
						
					}
					
					if($max!=0){
						if($nodes > $max){
							$nodes = $max+(rand(1,100)*0.01)-rand($riskStart,$Newsrisk);
						}
					}
					$list[] = round($nodes,2);
					unset($nodes);
					
				}
				
			}
			
		}
		
		$sum = 0;
		$isdg = false;
		foreach($list as $vsa){
			$sum +=$vsa;
			if($max>0){
				if($vsa>$max){
					$isdg =true;
				}
			}
			if($min>0){
				if($vsa<$min){
					$isdg =true;
				}
			}
			 
		}
		if($isdg){
			return $this->dispersed($num,$money,$min,$max,$riskStart,$riskEnd,$dengcha);
		}
		return $list;
	}
    //生成随机时间
    function rand_time($start_time,$end_time){
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        return date('Y-m-d H:i:s', mt_rand($start_time,$end_time));
    }
    /**
     * 通道查询
     * @return [type] [description]
     */
    public function getpayment(){
        if($this->request->isPost())
        {
            $post = input('post.');
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }
            $pay_id = $post['pay_id'];
            $type   = isset($post['type'])?$post['type']:1;
            //获取支付通道配置信息
            $payment =  Db::name('payment')
            ->field('payment_day_num,payment_num,payment_min_money,payment_max_money,payment_risk_start,payment_risk_end,paymentst_entime,paymentst_money,payment_pattern,payment_region,payment_mcc')
            ->where('payment_id',$pay_id)
            ->where('payment_type',$type)
            ->where('payment_use',1)
            ->find();
            
            if($payment){
                return json(['error'=>0,'msg'=>'查询成功','data'=>$payment]);
            }else{
                return json(['error'=>1,'msg'=>'渠道通道不存在']);
            }
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }

    }
    /*
     * 获取会员升级渠道
     * 2018年9月18日23:48:52
     * 刘媛媛
     */
    public function MembergetChannel(){
        if($this->request->isPost())
        {
        	$post = input('post.');
        	if(empty($post))
        	{
        		return json(['error'=>1,'msg'=>'参数错误']);
        	}
            $list = Db::name('paymentChannel')->where('channel_use',1)->select();
            $type = isset($post['type'])?$post['type']:4;
            foreach($list as $key=>$vs){
            	$payment = Db::name('payment')
            	->where('payment_channel_id',$vs['channel_id'])
            	->where('payment_type',$type)
            	->where('payment_use',1)->find();
            	if(empty($payment)){
            		unset($list[$key]);
            	}
            }
            $list = array_values($list);
            if(!$list){
                return json(['error'=>1,'msg'=>'此平台未配置渠道']);
            }else{
                return json(['error'=>0,'msg'=>'成功','data'=>$list]);
            }

        }else{
            return json(['error'=>1,'msg'=>'参数错误']);
        }
    }

    /**
     * 获取行业编码
     * @Author tw
     * @Date   2018-09-26
     * @return [type]     [description]
     */
    public function get_mcc()
    {
    	$post = input('post.');
		$pay_id = $post['pay_id'];
		$uid = $this->uid;
		$cid = $post['cid'];
		$region = $post['region'];
		$city_id = $post['city_id'];
		$payment =  Db::name('payment')
			->where('payment_id',$pay_id)
			->where('payment_type',2)
			->where('payment_use',1)
			->find();
		if(empty($payment))
		{
			return json(['error'=>1,'msg'=>'请选择支付通道']);
		}
		if($payment['payment_mcc']==1)
		{
			if ($city_id) {
				$list = Controller('pay/'.$payment['payment_controller'])->mcc($pay_id,$uid,$cid,$city_id);
			}
			else
			{
				$region_arr = explode('-', $region);
				$list = Controller('pay/'.$payment['payment_controller'])->query_city_mcc($pay_id,$uid,$cid,$region_arr[0],$region_arr[1]);
			}
		}
		else
		{
			$list = Db::name('payment_mcc')->field('mcc_mcc as mcc,mcc_title as name')->where('mcc_pay_id',$pay_id)->select();
			$list = ['error'=>0,'msg'=>'成功','data'=>$list];
		}
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }

    /**
     * 还款详情
     * @Author tw
     * @Date   2018-09-29
     * @return [type]     [description]
     */
    public function detail()
    {
    	$getdata = array();
    	$post = input('post.');
    	if(empty($post))
    	{
    		return json(['error'=>1,'msg'=>'参数错误']);
    	}
    	$uid = $this->uid;
    	$mid = $post['mid'];

    	$mission = Db::name('mission')->where('mission_id',$mid)->where('mission_uid',$uid)->find();
    	if(empty($mission))
    	{
    		return json(['error'=>1,'msg'=>'计划不存在']);
    	}
    	if($mission['mission_state']==1)
    	{
    		switch ($mission['mission_type']) {
    			case '1':
    			case '2':
    				$type_name = '失败';
    				break;
    			case '3':
    				$type_name = '还款中';
    				break;
    			default:
    				$type_name = '待还';
    				break;
    		}
    		$mission['type_name']=$type_name;
    	}
    	elseif($mission['mission_state']==2)
    	{
    		$mission['type_name']='已还完';
    	}
    	elseif($mission['mission_state']==3)
    	{
    		$mission['type_name']='失败';
    	}
    	elseif($mission['mission_state']==4)
    	{
    		$mission['type_name']='用户终止';
    	}
    	$card = Db::name('user_card')->alias('c')->join('bank_list uc','uc.list_id=c.card_bank_id','LEFT')->where('card_id',$mission['mission_cid'])->where('card_uid',$uid)->where('card_type',1)->find();
    	if(empty($card))
    	{
    		return json(['error'=>1,'msg'=>'银行卡不存在']);
    	}
        $plan = Db::name('plan')->field('plan_id,plan_uid,plan_mid,plan_form_no,plan_money,plan_type,plan_state,plan_pay_time,plan_oids,plan_fee,plan_mcc,plan_mcc_name')->where('plan_uid',$uid)->where('plan_mid',$mid)->where('plan_type',1)->order('plan_sort asc ,plan_pay_time asc')
                ->paginate('10',false,['query'=> $getdata]);
        if($plan->toArray())
        {
            //0未启动 1还款中 2已还完 3还款失败
            foreach($plan as $k=>$v){
                $data = array();
                // $data = $v;

                $kk = Db::name('plan')->field('plan_id,plan_uid,plan_mid,plan_form_no,plan_money,plan_type,plan_state,plan_pay_time,plan_oids,plan_fee,plan_mcc,plan_mcc_name')->where('plan_id','in',$v['plan_oids'])->select();
                array_push($kk,$v);

                foreach ($kk as $key => $value) {
                	$data[$key] = $value;
	                if($value['plan_type']==1)
	                {
	                    $type='还款';

	                    if($value['plan_state']==0)
		                {
		                    $status='待还';
		                }
		                else if($value['plan_state']==1)
		                {
		                    $status='已还';
		                }
		                else if($value['plan_state']==2)
		                {
		                    $status='失败';
		                }
		                else if($value['plan_state']==3)
		                {
		                    $status='还款中';
		                }
	                }
	                else if($value['plan_type']==2)
	                {
	                    $type='消费';
	                    if($value['plan_state']==0)
		                {
		                    $status='待消费';
		                }
		                else if($value['plan_state']==1)
		                {
		                    $status='消费';
		                }
		                else if($value['plan_state']==2)
		                {
		                    $status='失败';
		                }
		                else if($value['plan_state']==3)
		                {
		                    $status='支付中';
		                }
	                }
	                
	                $data[$key]['status'] = $status;
	                $data[$key]['type_name'] = $type;
	                $data[$key]['plan_money'] = (string)($value['plan_money']+$value['plan_fee']);
                }
                $plan->offsetSet($k,$data);
            }
        }
        $money = Db::name('plan')->where('plan_uid',$uid)->where('plan_mid',$mid)->where('plan_type',1)->where('plan_state',1)->sum('plan_money');

        $bank['name'] = $card['list_name'];//银行名称
        $bank['no'] = substr($card['card_no'],-4,4);//银行卡号
        $bank['logo'] = 'http://'.$_SERVER['HTTP_HOST'].$card['list_logo'];//银行卡号
        // $plan['error'] = 0;
        // return json($plan);
    	return json([
    		'error'=>0,
    		'msg'=>'成功',
    		'money'=>$money,
    		'bank'=>$bank,
    		'mission'=>$mission,
    		'data'=>$plan
    		]);
    }

    /**
     * 关闭/终止计划
     * @Author tw
     * @Date   2018-09-29
     * @return [type]     [description]
     */
    public function close()
    {
    	$post = input('post.');
    	$mid = $post['mid'];
    	$uid = $this->uid;

    	$mission = Db::name('mission')->where('mission_id',$mid)->where('mission_uid',$uid)->where('mission_del',0)->find();
    	if(empty($mission))
    	{
    		return json(['error'=>1,'msg'=>'计划不存在']);
    	}
    	elseif($mission['mission_state']=='4')
    	{
			return json(['error'=>0,'msg'=>'计划已终止']);
    	}
    	elseif($mission['mission_close']=='1')
    	{
			$up = Db::name('mission')->where('mission_id',$mid)->where('mission_uid',$uid)->where('mission_del',0)->update(['mission_state'=>4]);
    		if(empty($up))
			{
				return json(['error'=>1,'msg'=>'操作失败请重试']);
			}
			Db::name('user_card')->where('card_id',$mission['mission_cid'])->update(['card_state'=>0]);
			return json(['error'=>0,'msg'=>'终止计划成功']);
    	}
    	elseif($mission['mission_close']=='2')
    	{
    		return json(['error'=>0,'msg'=>'终止计划申请已提交，本次还款成功后将终止该计划']);
    	}
    	else
    	{
    		$up = Db::name('mission')->where('mission_id',$mid)->where('mission_uid',$uid)->where('mission_del',0)->update(['mission_close'=>2]);
    		if(empty($up))
    		{
    			return json(['error'=>1,'msg'=>'操作失败请重试']);
    		}
    		return json(['error'=>0,'msg'=>'提交终止计划申请成功,本次还款成功后将终止该计划']);
    	}
    }

    /**
     * 删除计划
     * @Author tw
     * @Date   2018-09-29
     * @return [type]     [description]
     */
    public function del()
    {
    	$post = input('post.');
    	$mid = $post['mid'];
    	$uid = $this->uid;

    	$mission = Db::name('mission')->where('mission_id',$mid)->where('mission_uid',$uid)->where('mission_del',0)->find();
    	if(empty($mission))
    	{
    		return json(['error'=>1,'msg'=>'计划不存在']);
    	}
    	elseif($mission['mission_del']=='1')
    	{
			return json(['error'=>0,'msg'=>'计划已成功删除']);
    	}
    	if($mission['mission_close']==1)
    	{
			$up = Db::name('mission')->where('mission_id',$mid)->where('mission_uid',$uid)->where('mission_del',0)->update(['mission_state'=>3,'mission_del'=>1]);
			if(empty($up))
			{
				return json(['error'=>1,'msg'=>'操作失败请重试']);
			}
			Db::name('user_card')->where('card_id',$mission['mission_cid'])->update(['card_state'=>0]);
			return json(['error'=>0,'msg'=>'计划已成功删除']);
    	}
    	else
    	{
    		$up = Db::name('mission')->where('mission_id',$mid)->where('mission_uid',$uid)->where('mission_del',0)->update(['mission_close'=>3]);
    		if(empty($up))
    		{
    			return json(['error'=>1,'msg'=>'操作失败请重试']);
    		}
    		return json(['error'=>0,'msg'=>'提交删除计划申请,本次还款成功后将删除该计划']);
    	}
    	
    }



    /**
     * 商户注册
     * @Author tw
     * @Date   2018-10-11
     * @return [type]     [description]
     */
    public function register()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $pay_id = $post['pay_id'];//支付通道id
        $uid = $this->uid;//用户id

        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return ['error'=>1,'msg'=>'通道不存在'];
        }
        $result = Controller('pay/'.$payment_controller)->register($pay_id,$uid);
        return json($result);
    }



    /**
     * 绑卡
     * @Author tw
     * @Date   2018-09-21
     */
    public function bind_web()
    {
        $get = input('get.');
        $pay_id = $get['pay_id'];
        $uid = $get['uid'];
        $cid = $get['cid'];
        $payment = Db::name('payment')->where('payment_id',$pay_id)->find();
        if(empty($payment))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $payment_card = Db::name('payment_card')
            ->where('card_uid','eq',$uid)
            ->where('card_pay_id','eq',$pay_id)
            ->where('card_cid','eq',$cid)
            ->where('card_type','eq','1')
            ->where('card_state','eq','1')
            ->find();
        if($payment_card)
        {
            return json(['error'=>1,'msg'=>'已绑卡']);
        }
        $result = Controller('pay/'.$payment['payment_controller'])->bind_card($pay_id,$uid,$cid,1);
        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        elseif($result['error']!=0)
        {
        	return json($result);
        }
        elseif($result['returnUrl'])
        {
        	echo $result['returnUrl'];
        }
        elseif($result['url'])
        {
          	$url = $result['url'];
          	echo "<script>window.location.href='".$url."'</script>";
        	//Header("location:".$url);
        }
    }

    /**
     * 绑卡
     * @Author tw
     * @Date   2018-09-21
     */
    public function bind_api()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $type = $post['type'];//1 绑卡提交 2提交绑卡验证 3重新获取验证码
        $pay_id = $post['pay_id'];//支付通道id
        $cid = $post['cid'];//银行卡id
        $uid = $this->uid;//用户id
        $smscode = $post['smscode'];//验证码紧type为2时有用

        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        if($type==1)
        {
            //绑卡提交
            $result = Controller('pay/'.$payment_controller)->bind_card($pay_id,$uid,$cid);
        }
        elseif($type==2)
        {
            if(empty($smscode))
            {
                return json(['error'=>1,'msg'=>'请填写验证码']);
            }
            //绑卡验证
            $result = Controller('pay/'.$payment_controller)->bind_smscode($pay_id,$uid,$cid,$smscode);
            
        }
        elseif($type==3)
        {
            //重发验证码
            $result = Controller('pay/'.$payment_controller)->bind_retry_smscode($pay_id,$uid,$cid);
            
        }

        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        return json($result);
    }
    /**
     * 查询状态
     * @Author tw
     * @Date   2018-10-20
     * @return [type]     [description]
     */
    public function state_query()
    {
        $post = input('post.');
        $id = $post['id'];//订单id
        $uid = $post['uid'];//用户id
        $pay_id = $post['pay_id'];//通道id
        $form_no = $post['form_no'];//订单编号
        $up_no = $post['up_no'];//上游返回编号
        $date = $post['date'];//交易日期

        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller('pay/'.$payment_controller)->state_query($id,$uid,$pay_id,$form_no,$up_no,$date);
        return json($result);
    }
}
 