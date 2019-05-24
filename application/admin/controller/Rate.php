<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Rate extends Base{
	public function __construct(){
       parent::__construct();
    }
    public function index(){
    	
        $keywords = request()->param('keywords');
        $getdata = $where = array();
        $type_id = input('get.type_id',0);
        if($type_id)
        {
            $where[] = array('rate_type_id','eq',$type_id);
        }
        $list = Db::name('rate')->where($where)
            ->order('rate_type asc,rate_id desc')
            ->paginate(10,false,['query'=> $getdata]);

        $this->assign('list',$list);
        return $this->fetch();
    }
    public function add()
    {
        if($this->request->isPost()) {
            $id = input('get.id',0);
            $post = input('post.');
            if(!$id){
                return json(['error'=>1,'msg'=>'参数错误']);
            }
            if(empty($post['type']))
            {
                return json(['error'=>1,'msg'=>'请选择类型']);
            }
            $user_type = Db::name('user_type')->where(array('type_id'=>$id))->find();
            if(!$user_type){
                return json(['error'=>1,'msg'=>'会员等级不存在']);
            }
            if(Db::name('rate')->where(['rate_type_id'=>$post['type_id'],'rate_type'=>$post['type']])->find())
            {
                return json(['error'=>1,'msg'=>'费率已存在']);
            }
            
            $adData['rate_type_id'] = $post['type_id'];
            $adData['rate_rate'] = $post['rate'];
            $adData['rate_close_rate'] = $post['close_rate'];
            $adData['rate_type'] = $post['type'];
            $adData['rate_time'] = time();
            Db::name('rate')->insert($adData);
            return json(['error'=>0,'msg'=>'新增成功']);
            
        }
        $id = input('get.id',0);
       
        if(!$id){
            die('参数错误');
        }
        $user_type = Db::name('user_type')->where(array('type_id'=>$id))->find();
        if(!$user_type){
           die('会员等级不存在');
        }
        $this->assign('user_type',$user_type);
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

            $rate = Db::name('rate')->where(array('rate_id'=>$id))->find();
            if(!$rate){
                return json(['error'=>1,'msg'=>'费率不存在']);
            }
            $post= input('post.');
            $data['rate_rate']  = $post['rate'];
            $data['rate_close_rate'] = $post['close_rate'];
            $data['rate_type'] = $post['type'];
            Db::name('rate')->where(array('rate_id'=>$id))->update($data);
           //是否修改等级以后修改已生成的等级费率
           //true false
            $isuser = true;
            if($isuser){
            	$usetList = Db::name('user')->field('user_id,user_pid,user_type_id')->where('user_type_id', $rate['rate_type_id'])->select();
            	if(count($usetList)>0){
            		$arrId = array();
            		foreach ($usetList as $k=>$v){
                        Db::name('userRate')
                            ->where('rate_uid',$v['user_id'])
                            ->where('rate_type',$data['rate_type'])
                            ->update(['rate_rate'=>$data['rate_rate'],'rate_close_rate'=>$data['rate_close_rate']]);
                            
                            update_payment_fee($v['user_id'],$v['user_type_id']);
            			// $arrId[] = $v['user_id'];
            		}
            		// Db::name('userRate')
            		// ->where('rate_uid','in',$arrId)
            		// ->where('rate_type',$data['rate_type'])
            		// ->update(['rate_rate'=>$data['rate_rate'],'rate_close_rate'=>$data['rate_close_rate']]);

            	}
            }
            return json(['error'=>0,'msg'=>'修改成功']);   
        }

        $id = input('get.id',0);
        if(!$id){
            die('参数错误');
        }
        $rate = Db::name('rate')->where(array('rate_id'=>$id))->find();
        if(!$rate){
            die('费率不存在');
        }
        $this->assign('list',$rate);

        $user_type = Db::name('user_type')->where(array('type_id'=>$rate['rate_type_id']))->find();
        if(!$user_type){
           die('会员等级不存在');
        }
        $this->assign('user_type',$user_type);
        return $this->fetch();
    }
    public function delete()
    {

        $id = input('get.id',0);
        if(!$id){
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        if(!Db::name('rate')->where(array('rate_id'=>$id))->find()){
            return json(['error'=>1,'msg'=>'费率不存在']);
        }
        Db::name('rate')->where(array('rate_id'=>$id))->delete();
        return json(['error'=>0,'msg'=>'删除成功']);
    }
}
