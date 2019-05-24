<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Userrate extends Base{
	public function __construct(){
       parent::__construct();
    }
    

    public function index()
    {
        $id = input('get.id',0);
        $list = Db::name('user_rate')->where(['rate_uid'=>$id])->select();
        $this->assign('list',$list);
        $this->assign('uid',$id);
        return $this->fetch();
    }

    /**
     * 编辑
     * @Author   tw
     * @DateTime 2018-09-05
     * @return   [type]     [description]
     */
    public function edit(){
        if($this->request->isPost()) {
            $id = input('get.id',0);
            if(!$id){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            $user_rate = Db::name('user_rate')->where(array('rate_id'=>$id))->find();
            if(!$user_rate){
                return json(['error'=>1,'msg'=>'费率不存在']);
            }
            $post= input('post.');
            $data['rate_rate']  = $post['rate'];
            $data['rate_close_rate'] = $post['close_rate'];
            $data['rate_type'] = $post['type'];
            $res = Db::name('user_rate')->where(array('rate_id'=>$id))->update($data);
            if(empty($res))
            {
                return json(['error'=>1,'msg'=>'修改失败']);
            }
            update_payment_fee($user_rate['rate_uid'],$post['type']);
            return json(['error'=>0,'msg'=>'修改成功']);
        }

        $id = input('get.id',0);
        if(!$id){
            die('参数错误');
        }
        $user_rate = Db::name('user_rate')->where(array('rate_id'=>$id))->find();
        if(!$user_rate){
            die('费率不存在');
        }
        $this->assign('list',$user_rate);
        return $this->fetch();
    }
}
