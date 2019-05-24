<?php
namespace app\api\job;
use think\Controller;
use think\Db;
use think\facade\Hook;
use think\queue\Job;
use think\Exception;
use think\Queue;
   /**
   * 文件路径： \application\index\job\Hello.php
   * 这是一个消费者类，用于处理 helloJobQueue 队列中的任务
   */
    class Kou extends Base {
		
	    /**
         * fire方法是消息队列默认调用的方法
         * @param Job            $job      当前的任务对象
         * @param array|mixed    $data     发布任务时自定义的数据
	    */
	    public function fire(Job $job,$data){

	      	print("<warn>Hello Job has been retried more than 3 times!1111"."</warn>\n");
      		$this->queueLog($data['payment']['payment_controller'],$data['logid'],'队列接收请求成功开始执行');
            // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
           	
		    print("<warn>Hello Job has been retried more than 3 times!222"."</warn>\n");
			
		    $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
            if(!$isJobStillNeedToBeDone){
            	$this->queueLog($data['payment']['payment_controller'],$data['logid'],'MQ可能已经不再需要执行了删除队列');
            	$job->delete();
            	return;
            }
            // echo 1;
            // exit();
	        $isJobDone = $this->doHelloJob($data);
	       // dump($isJobDone );
	       // exit();
	        if ($isJobDone) {
	            //如果任务执行成功， 记得删除任务
	            
	            $this->queueLog($data['payment']['payment_controller'],$data['logid'],'MQ任务执行成功删除任务');
	            $job->delete();
	            print("<info>Hello Job has been done and deleted"."</info>\n");
	        }else{
			    $execu = $job->attempts();
                if ($execu > 2) {
                	//通过这个方法可以检查这个任务已经重试了几次了
                	print("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");
                	$this->queueLog($data['payment']['payment_controller'],$data['logid'],'MQ任务执行次数过多纳入删除个任务');
                	
  					$job->delete();
                	// 也可以重新发布这个任务
                	//print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                	//$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
                }
	        }
			
	    }
      
        /**
        * 有些消息在到达消费者时,可能已经不再需要执行了
        * @param array|mixed    $data     发布任务时自定义的数据
        * @return boolean                 任务执行的结果 false 不需要以下执行了 true需要执行
        */
        private function checkDatabaseToSeeIfJobNeedToBeDone($data){
        	
			$payment  = $data['payment'];//通道
			$mission  = $data['mission'];//任务
			
      		$retInfo = Db::name('mission')->where('mission_id',$mission['mission_id'])->find();
      		if(!$retInfo){
      			$this->queueLog($payment['payment_controller'],$mission['mission_id'],'mission_id不存在');
      			return false;
      		}
      		
      		$jieGuo = false;
      		//申请状态 0 申请中 1复核成功 2复核审核失败 3提交上游等待处理 4 上游处理失败 5 上游处理成功
			//只有等于1的成功
			switch ($retInfo['mission_queues']){
				case '0':
					$jieGuo = false;
					$jieTxt = 'MQ未加入列队|状态为:'.$retInfo['mission_queues'];
					break;
				case '1':
					$jieGuo = true;
					$jieTxt = 'MQ列队成功|状态为:'.$retInfo['mission_queues'];
					break;
				case '2':
					$jieGuo = false;
					$jieTxt = 'MQ提交列队失败|状态为:'.$retInfo['mission_queues'];
					break;
				case '3':
					$jieGuo = false;
					$jieTxt = 'MQ列队处理成功|状态为:'.$retInfo['mission_queues'];
					break;
				case '4':
					$jieGuo = false;
					$jieTxt = 'MQ列队处理失败|状态为:'.$retInfo['mission_queues'];
					break;
				default:
					$jieGuo = false;
					$jieTxt = 'MQ异常订单订单状态为:'.$retInfo['mission_queues'];
			}
			$this->queueLog($payment['payment_controller'],$data['logid'],$jieTxt);
      		return $jieGuo;
      		
      		 
        }

      /**
       * 根据消息中的数据进行实际的业务处理
       * @param array|mixed    $data     发布任务时自定义的数据
       * @return boolean                 任务执行的结果
       */
        private function doHelloJob($data) {

            //echo $data['logid'];
            //调用通道
            $this->queueLog($data['payment']['payment_controller'],$data['logid'],'MQ进行实际的业务处理（代付发起申请）');

            $mission= $data['mission'];
            $plan = $data['plan'];
            $planlist = $data['planlist'];
            $payment = $data['payment'];
            $card = $data['card'];
            $infoId  = $data['logid'];
            // $event   = Controller('task/'.$payment['payment_controller']);
            $retData = Controller('Pay/task')->pay($mission,$plan,$planlist,$payment,$card);
    		if($retData['error']==0){

    			Db::name('mission')->where(['mission_id'=>$mission['mission_id'],'mission_queues'=>1])->update(['mission_queues'=>3]);

    			$this->queueLog($data['payment']['payment_controller'],$data['logid'],$retData['msg']);
    			//提交申请成功纳入查询队列
    			$this->actionQueryJob($mission,$plan,$planlist,$payment,$card,$data['logid']);
    			return true;
    			
    		}else if($retData['error']==1){

    			Db::name('mission')->where(['mission_id'=>$mission['mission_id'],'mission_queues'=>1])->update(['mission_queues'=>4]);
    			$this->queueLog($data['payment']['payment_controller'],$data['logid'],$retData['msg']);
    			return true;
    			
    		}else if($retData['error']==2){
    			Db::name('mission')->where(['mission_id'=>$mission['mission_id'],'mission_queues'=>1])->update(['mission_queues'=>4]);
    			$this->queueLog($data['payment']['payment_controller'],$data['logid'],$retData['msg']);
    			return false;
    			//16
    		}else{
    			Db::name('mission')->where(['mission_id'=>$mission['mission_id'],'mission_queues'=>1])->update(['mission_queues'=>4]);
    			$this->queueLog($data['payment']['payment_controller'],$data['logid'],'MQ异常记录');
    			return false;
    		}
        	$this->queueLog($data['payment']['payment_controller'],$data['logid'],'MQ异常记录retData=Error');
        	
        }
        
        
    public function actionQueryJob($mission,$plan,$planlist,$payment,$card,$infoId){
        	
    		// 1.当前任务将由哪个类来负责处理。 
      		// 当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
			$jobHandlerClassName  = 'app\api\job\Querys';
			
			if(class_exists($jobHandlerClassName)){
				// 2.当前任务归属的队列名称，如果为新队列，会自动创建
			    $jobQueueName  	  = "search"; 
			    // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
			    //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
			    $jobData  = [ 'strat' => time(), 'mission' => $mission , 'plan' => $plan , 'planlist' => $planlist , 'payment' => $payment , 'card' => $card ,'logid'=>$infoId];
			    // 4.将该任务推送到消息队列，等待对应的消费者去执行
                $queTime = strtotime($payment['payment_que'])*60;
			    $isPushed = Queue::later($queTime,$jobHandlerClassName , $jobData , $jobQueueName );	
			    // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
			    if( $isPushed !== false ){

			    	//Db::name('helppayInfo')->where('hi_id','eq',$infoId)->update(['hi_state'=>3,'hi_queue'=>2]);
			    	$this->queueLog($payment['payment_controller'],$infoId,'[QUERY]MQ查询代付状态排队成功等待处理');
			    	return array('error'=>0,'msg'=>'[QUERY]MQ查询代付状态排队成功等待处理','hid'=>$infoId);
					//echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
			    }else{

                    $this->queueLog($payment['payment_controller'],$infoId,'[QUERY]MQ查询代付状态排队失败请联系技术');
			    	// Db::name('helppayInfo')->where('hi_id','eq',$infoId)->update(['hi_state'=>3,'hi_queue'=>1]);
					return array('error'=>1,'msg'=>'[QUERY]MQ查询代付状态排队失败请联系技术','hid'=>$infoId);
			    }
			  	
		    }else{

		    	$this->queueLog($payment['payment_controller'],$infoId,'[QUERY]MQ查询代付状态排队失败请联系技术'.$jobHandlerClassName.'不存在');
		  		return array('error'=>1,'msg'=>'[QUERY]MQ查询代付状态排队失败请联系技术:'.$jobHandlerClassName.'不存在','hid'=>$infoId);
				//echo 'job类 '.$jobHandlerClassName.'不存在';
			}
    }
	  
  }