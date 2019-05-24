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
    class Querys extends Base {
		
	    /**
         * fire方法是消息队列默认调用的方法
         * @param Job            $job      当前的任务对象
         * @param array|mixed    $data     发布任务时自定义的数据
	    */
	    public function fire(Job $job,$data){
	      	
            $this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]队列接收请求成功开始执行');
            // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
           	
		    print("<warn>Querys begins to execute"."</warn>\n");
		
		    $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
         
            if(!$isJobStillNeedToBeDone){
             	print("<info>Task OK"."</info>\n");
            	$this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]可能已经不再需要执行了删除队列');
            	$job->delete();
            	return;
            }
			
	        $isJobDone = $this->doHelloJob($data);
	        
	        if ($isJobDone) {
	            //如果任务执行成功,记得删除任务
	            $this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]任务执行成功删除任务');
	            $job->delete();
	            print("<info>Task execution succeeds in deleting tasks"."</info>\n");
	        }else{
			    $execu = $job->attempts();
                if ($execu > 2) {
                  //通过这个方法可以检查这个任务已经重试了几次了
                  // print("<warn>Multiple deletion of task execution"."</warn>\n");
                  // queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]任务执行次数过多纳入删除个任务');
                  // $job->delete();
                  // 也可以重新发布这个任务
                  print("<info>Hello Job will be availabe again after 2s."."</info>\n");
                  $job->release(60); //$delay为延迟时间，表示该任务延迟2秒后再执行
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
      			$this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]retInfo不存在');
      			return false;
      		}
            if($retInfo['mission_state']==1)
            {
          		//申请状态 0 申请中 1复核成功 2复核审核失败 3提交上游等待处理 4 上游处理失败 5 上游处理成功
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
            }
            elseif($retInfo['mission_state']==2)
            {
                $jieGuo = false;
                $jieTxt = '计划失败|状态为:'.$retInfo['mission_queues'];
            }
            elseif($retInfo['mission_state']==3)
            {
                $jieGuo = false;
                $jieTxt = '计划完成|状态为:'.$retInfo['mission_queues'];
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
            //调用通道
            $this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]进行实际的业务处理（代付查询申请）');
            $mission= $data['mission'];
            $payment = $data['payment'];
            $infoId  = $data['infoId'];
            $event   = Controller('task/'.$payment['payment_controller']);
            // $retData = $event->pilitosearch($mission,$payment);

            $retData = $event->processed($mission,$plan,$planlist,$payment,$card);

            $ErrorMsgList = array('重复','重复订单','处理中','重复提交','存在','打款中','交易中','对账单','交易流水号重复','已经申请','不能重复');
            if(in_array($retData['msg'],$ErrorMsgList)){
                return false;
            }
    		if($retData['error']==0){
    			$this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]0'.$retData['msg']);
    			return true;
    			
    		}else if($retData['error']==1){
    			$this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]1'.$retData['msg']);
    			return true;
    			
    		}else if($retData['error']==2){
    			$this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]2'.$retData['msg']);
    			return false;
    			//16
    		}else{
    			$this->queueLog($data['payment']['payment_controller'],$data['logid'],'异常记录');
    			return false;
    		}
        	$this->queueLog($data['payment']['payment_controller'],$data['logid'],'[QUERY]异常记录retData=Error');
        }
        
  }