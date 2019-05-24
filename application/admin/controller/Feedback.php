<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Feedback extends Base{
	public function __construct(){
       parent::__construct();
    }
    public function index(){
    	
        $keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            $where[] = array('news_title','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('feedback')->alias('n')
            ->where($where)
            ->order('feedback_id desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('type',input('param.type',0));
        return $this->fetch();
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
