<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;
use think\Queue;
use think\facade\Hook;
use think\facade\Config;
class Message extends Base{
	public function __construct(){
       parent::__construct();
    }
    public function addmsg(){
    	 
        return $this->fetch();
    }
	
	public function dosmg(){
		if($this->request->isPost()){
			$post = $_POST;
			$ok   = 0;
			$no   = 0;
			if($post['sms_title']==''){
				return json(['msg'=>'请输入标题','error'=>1]);
			}
			
			if($post['sms_text']==''){
				return json(['msg'=>'请输入发送内容','error'=>1]);
			}
			if(intval($post['sms_isqun'])==0){
				
				$list = explode(',',$post['sms_uid']);
				if(!$list){
					return json(['msg'=>'请输入会员ID','error'=>1]);
				}
				foreach ($list as $k=>$v){
					$ret = $this->msgqueues($v,$post['sms_title'],$post['sms_text']);
					if($ret['error']==0){
						$ok ++;
					}else{
						$no ++;
					}
				}
			}else{
				$list = Db::name('user')->cache(3600)->where('user_state',0)->select();
				foreach ($list as $k=>$v){
					$ret = $this->msgqueues($v['user_id'],$post['sms_title'],$post['sms_text']);
					if($ret['error']==0){
						$ok ++;
					}else{
						$no ++;
					}
				}
			}
			
			if($ok>0){
				
				$logs['log_title'] = $post['sms_title'];
				$logs['log_body']  = $post['sms_text'];
				$logs['log_time']  = time();
				$logs['log_type']  = $post['sms_isqun'];
				$logs['log_ok']    = $ok;
				$logs['log_no']    = $no;
				Db::name('message_log')->insert($logs);
			}
			return json(['error'=>0,'msg'=>'操作成功']);
		}
	}
	
	public function msglog(){
		
		$list = Db::name('messageLog')->order('log_time','desc')->paginate(20);
		$this->assign('list', $list);
		// 渲染模板输出
		return $this->fetch();
	}
	/**
  	 * 消息队列发送推送以及入库
	 * 2018年11月5日15:32:12
	 */
    protected function msgqueues($uid,$title,$text){
		return array('error'=>0,'msg'=>'MQ排队成功等待处理');
		// 1.当前任务将由哪个类来负责处理。 
		// 当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
		$jobHandlerClassName  = 'app\api\job\Msg';

		if(class_exists($jobHandlerClassName)){

			$isuser = Db::name('user')->cache(3600)->where('user_id',$uid)->find();
			if($isuser){
				// 2.当前任务归属的队列名称，如果为新队列，会自动创建
				$jobQueueName  	  = "msgqueues"; 
				// 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
				//   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
				$jobData  = [ 'strat' => time(), 'uid' => $uid , 'title' => $title , 'isqun' => $isqun , 'text' => $text,'user'=>$isuser];

				// 4.将该任务推送到消息队列，等待对应的消费者去执行
				$isPushed = Queue::push($jobHandlerClassName , $jobData , $jobQueueName );
				// database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
				if( $isPushed !== false ){
					return array('error'=>0,'msg'=>'MQ排队成功等待处理');
				}else{
					return array('error'=>1,'msg'=>'MQ排队失败请联系技术');
				}
			}else{
				return array('error'=>1,'msg'=>'MQ排队失败请联系技术:'.$jobHandlerClassName.'不存在');
			}
		
		}else{
			return array('error'=>1,'msg'=>'会员不存在');
		}
    	
    }
    /**
     * 回复
     * @Author   tw
     * @DateTime 2018-09-04
     * @return   [type]     [description]
     */
    public function reply(){

        if($this->request->isPost())
        {
            $post = $_POST;
            $data['feedback_admin_id'] = session('admin_id');
            $data['feedback_body'] = $post['body'];
            $data['feedback_reply'] = $post['reply'];
            $data['feedback_reply_time'] = time();
            $data['feedback_state'] = 2;
            if(Db::name('feedback')->where(['feedback_id'=>$post['id']])->update($data))
            {
                return json(['error'=>0,'msg'=>'回复成功']);
            }
            return json(['error'=>1,'msg'=>'失败']);
        }

        $feedback = Db::name('feedback')->where(['feedback_id'=>input('param.id')])->find();
        if(empty($feedback))
        {
            echo "无内容";
            exit();
        }
        $this->assign('info',$feedback);
        return $this->fetch();
    }
}
