<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Exception;
use think\Queue;
use think\facade\Hook;
use think\facade\Config;

class Task extends Controller{

	public function getkeys(){
       //连接
      $queue = config::pull('queue');
      dump($queue);
      $redis = new \Redis();
      $redis->connect($queue['host'], $queue['port']);
      $redis->auth($queue['password']); //密码验证
      $redis->select($queue['select']);//选择数据库2
      $sad  = $redis->lRange('queues: jhqueues', 0, -1);
      //queues:search:delayed
      //$sad  = $redis->keys('*');
   	  dump($sad);
    }
    /**
     * 定时任务执行入口
     * @Author tw
     * @Date   2018-09-06
     * @return [type]     [description]
     */
    public function index(){
		
            echo "暂停使用<br/>";
            exit();
        $get = input('param.');
        $hour = date('H:i');
        if($hour < '09:00' || $hour > '21:00'){
            echo "该时间段不允许计划<br/>";exit;
        }
        
        $where['mission_state'] = 1;
        $where['mission_type'] = 0;
        $where['mission_queues'] = 0;
        if($get['id'])
        {
            $where['mission_id'] = $get['id'];
        }
        if($get['kk'])
        {
          Db::name('mission')->where('mission_id',$get['id'])->update(['mission_queues'=>0]);
        }
        $missionlist = Db::name('mission')->where($where)->whereTime('mission_pay_time','<',date('Y-m-d H:i:s',time()))->order('mission_pay_time asc')->select();
        if (empty($missionlist)) {
            echo "没有可以执行的计划<br/>";
            exit();
        }
        unset($where);
        foreach ($missionlist as $key => $v) {
            $plan = Db::name('plan')
                ->where('plan_mid',$v['mission_id'])
                ->where('plan_state',0)
                ->where('plan_type',1)
                ->order('plan_pay_time asc')
                ->find();

            if(empty($plan))
            {
                echo '计划不存在 ['.$v['mission_id'].']<br/>';
                break;
            }

            $card = Db::name('user_card')->where('card_id',$v['mission_cid'])->where('card_type',1)->find();
            if(empty($card))
            {
                echo '计划 ['.$v['mission_id'].'] 银行卡不存在<br/>';
                break;
            }

            $payment = Db::name('payment')->where('payment_id',$v['mission_pay_id'])->find();
            if(empty($payment))
            {
                echo '计划 ['.$v['mission_id'].'] 通道不存在<br/>';
                break;
            }
            $planlist = Db::name('plan')->where('plan_id','in',$plan['plan_oids'])->where('plan_state',0)->select();
            $count_list =count($planlist);

            $res = $this->jhqueues($v,$plan,$planlist,$payment,$card,$v['mission_id'].'-'.$plan['plan_id'].'-'.$planlist[0]['plan_id']);
            return json($res);
	    }
	}
    
	/**
  	 * 进入队列排队
	 * 扣款还款项目队列
	 * @param  string     $mission  [计划]
	 * @param  string     $payment  [通道信息]
	 * @param  string     $infoId   [logid]
	 */
    protected function jhqueues($mission='',$plan='',$planlist='',$payment='',$card='',$infoId=''){

    		// 1.当前任务将由哪个类来负责处理。 
      		// 当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
			$jobHandlerClassName  = 'app\api\job\Kou';

			if(class_exists($jobHandlerClassName)){

				Db::name('mission')->where('mission_id',$mission['mission_id'])->update(['mission_queues'=>1]);
				// 2.当前任务归属的队列名称，如果为新队列，会自动创建
				$jobQueueName  	  = "jhqueues"; 
			    // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
			    //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
			    $jobData  = [ 'strat' => time(), 'mission' => $mission , 'plan' => $plan , 'planlist' => $planlist , 'payment' => $payment , 'card' => $card ,'logid'=>$infoId];

			    // 4.将该任务推送到消息队列，等待对应的消费者去执行
			    $isPushed = Queue::push($jobHandlerClassName , $jobData , $jobQueueName );

			    // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
			    if( $isPushed !== false ){

			    	$this->queueLog($payment['payment_controller'],$infoId,$plan['plan_form_no'].' MQ排队成功等待处理');
			    	return array('error'=>0,'msg'=>'MQ排队成功等待处理','hid'=>$infoId);
					//echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
			    }else{
			    	$this->queueLog($payment['payment_controller'],$infoId,'MQ排队失败请联系技术');
					return array('error'=>1,'msg'=>'MQ排队失败请联系技术','hid'=>$infoId);
			    }
		    }else{
		  		return array('error'=>1,'msg'=>'MQ排队失败请联系技术:'.$jobHandlerClassName.'不存在','hid'=>$infoId);
				//echo 'job类 '.$jobHandlerClassName.'不存在';
			}
    	
    }
  	

	/*
  	 * 进入队列排队
	 * 扣款项目队列
  	 */
    protected function queues($retApply,$newBank,$retPay,$infoId){
    		// 1.当前任务将由哪个类来负责处理。 
      		// 当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
			$jobHandlerClassName  = 'app\api\job\Kou'; 
			
			if(class_exists($jobHandlerClassName)){
				// 2.当前任务归属的队列名称，如果为新队列，会自动创建
			  $jobQueueName  	  = "kkqueues"; 
			    // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
			    //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
			    $jobData  = [ 'strat' => time(), 'helppay' => $retApply , 'Bank' => $newBank , 'retPay' => $retPay ,'logid'=>$infoId];
			    // 4.将该任务推送到消息队列，等待对应的消费者去执行
			    $isPushed = Queue::push($jobHandlerClassName , $jobData , $jobQueueName );	
			    // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
			    if( $isPushed !== false ){
			    	queueLog($retPay['hp_controller'],$infoId,'MQ排队成功等待处理');
			    	return array('error'=>0,'msg'=>'MQ排队成功等待处理','hid'=>$infoId);
					//echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
			    }else{
			    	queueLog($retPay['hp_controller'],$infoId,'MQ排队失败请联系技术');
					return array('error'=>1,'msg'=>'MQ排队失败请联系技术','hid'=>$infoId);
			    }
			  	
		    }else{
		  		return array('error'=>1,'msg'=>'MQ排队失败请联系技术:'.$jobHandlerClassName.'不存在','hid'=>$infoId);
				//echo 'job类 '.$jobHandlerClassName.'不存在';
			}
    	
    }
  	

  	

	
	/*
  	 * 进入队列排队
	 * 还款通道
  	 */
    protected function hkqueues($retApply,$newBank,$retPay,$infoId){
    		// 1.当前任务将由哪个类来负责处理。 
      		// 当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
			$jobHandlerClassName  = 'app\api\job\Huan'; 
			
			if(class_exists($jobHandlerClassName)){
				// 2.当前任务归属的队列名称，如果为新队列，会自动创建
			    $jobQueueName  	  = "hkqueues"; 
			    // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
			    //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
			    $jobData  = [ 'strat' => time(), 'helppay' => $retApply , 'Bank' => $newBank , 'retPay' => $retPay ,'logid'=>$infoId];
			    // 4.将该任务推送到消息队列，等待对应的消费者去执行
			    $isPushed = Queue::push($jobHandlerClassName , $jobData , $jobQueueName );	
			    // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
			    if( $isPushed !== false ){
			    	$this->queueLog($retPay['hp_controller'],$infoId,'MQ排队成功等待处理');
			    	return array('error'=>0,'msg'=>'MQ排队成功等待处理','hid'=>$infoId);
					//echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
			    }else{
			    	$this->queueLog($retPay['hp_controller'],$infoId,'MQ排队失败请联系技术');
					return array('error'=>1,'msg'=>'MQ排队失败请联系技术','hid'=>$infoId);
			    }
			  	
		    }else{
		    	
		    	$this->queueLog($retPay['hp_controller'],$infoId,'MQ排队失败请联系技术'.$jobHandlerClassName.'不存在');
		  		return array('error'=>1,'msg'=>'MQ排队失败请联系技术:'.$jobHandlerClassName.'不存在','hid'=>$infoId);
				//echo 'job类 '.$jobHandlerClassName.'不存在';
			}
    	
    }
	protected function queueLog($path,$logId,$msg){
        $pathnews =  'logs/';
        //创建类型
        if (! is_dir($pathnews)) {
            mkdir($pathnews,0777);
        }
        $pathnews =  'logs/queueLog/';
        //创建类型
        if (! is_dir($pathnews)) {
            mkdir($pathnews,0777);
        }
		$pathnews =  $pathnews.$path;
		//创建类型
		if (! is_dir($pathnews)) {
			mkdir($pathnews,0777);
		}
		$filename = $pathnews .'/' . $logId . '.txt';
		$content = date("Y-m-d H:i:s",time())."\r\n".$msg."\r\n \r\n \r\n ";
		file_put_contents($filename, $content, FILE_APPEND);
	}

    public function bd()
    {
        $form_no = input('get.form_no');
        if(empty($form_no))
        {
            return json(['error'=>1,'msg'=>'订单错误']);
        }

        $plan = Db::name('plan')->where('plan_form_no',$form_no)->where('plan_state',2)->find();
        if(empty($plan))
        {
            return json(['error'=>1,'msg'=>'该订单无需补单']);
        }

        Db::name('plan')->where('plan_form_no',$form_no)->update(['plan_state'=>0,'plan_form_no'=>$form_no.'BD']);
        Db::name('mission')->where('mission_id',$plan['plan_mid'])->update(['mission_type'=>0,'mission_queues'=>0]);
        return json(['error'=>0,'msg'=>'成功']);
    }
}
