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
    class Msg extends Base {
		
	    /**
         * fire方法是消息队列默认调用的方法
         * @param Job            $job      当前的任务对象
         * @param array|mixed    $data     发布任务时自定义的数据
	    */
	    public function fire(Job $job,$data){

	      	print("<warn>Hello Job has been retried more than 3 times!1111"."</warn>\n");
      		$this->queueLog('msg',$data['uid'],'队列接收请求成功开始执行');
            // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
           	
		    print("<warn>Hello Job has been retried more than 3 times!222"."</warn>\n");
			
		    $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
            if($isJobStillNeedToBeDone){
            	$this->queueLog('msg',$data['uid'],'MQ可能已经不再需要执行了删除队列');
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
	            
	            $this->queueLog('msg',$data['uid'],'MQ任务执行成功删除任务');
	            $job->delete();
	            print("<info>Hello Job has been done and deleted"."</info>\n");
	        }else{
			    $execu = $job->attempts();
                if ($execu > 2) {
                	//通过这个方法可以检查这个任务已经重试了几次了
                	print("<warn>Hello Job has been retried more than 3 times!"."</warn>\n");
                	$this->queueLog('msg',$data['uid'],'MQ任务执行次数过多纳入删除个任务');
                	
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
        	return Db::name('message')->where('message_uid',$data['uid'])->where('message_title',$data['title'])->find();
        }
      /**
       * 根据消息中的数据进行实际的业务处理
       * @param array|mixed    $data     发布任务时自定义的数据
       * @return boolean                 任务执行的结果
       */
        private function doHelloJob($data) {

            //echo $data['logid'];
            //调用通道
           $this->queueLog('msg',$data['uid'],'MQ进行实际的业务处理（代付发起申请）');
			$logs['message_uid']   =  $data['uid'];
			$logs['message_title'] =  $data['title'];
			$logs['message_body']  =  $data['text'];
			$logs['message_uid']   =  $data['uid'];
			$logs['message_time']  =  $data['strat'];
			$logs['message_read']  = 0;
			Db::name('message')->insert($logs);
			$this->queueLog('msg',$data['uid'],'记录创建成功');
			//执行推送方法
			$this->queueLog('msg',$data['uid'],'推送成功');
        	return true;
        }
	  
  }