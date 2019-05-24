<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Usertype extends Base{
	public function __construct(){
       parent::__construct();
    }
    public function index(){
    	
        $keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $whereor[] = array('type_name','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('user_type')->where($where)->whereor($whereor)
            ->order('type_id desc')
            ->paginate(10,false,['query'=> $getdata]);

        $this->assign('list',$list);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()) {
            $post= input('post.');
            
            if(!$post){
                return json(['error'=>1,'msg'=>'参数错误!']);
            }
            $data['type_name']        = $post['type_name'];
            $data['type_fee']         = $post['type_fee'];
            $data['type_free_count']  = $post['type_free_count'];
            $data['type_free_amount'] = $post['type_free_amount'];
            $data['type_sort']        = $post['type_sort'];
            $data['type_profit']      = $post['type_profit'];

            Db::name('user_type')->insert($data);
            return json(['error'=>0,'msg'=>'添加完成']);
        }

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
            $user_type = Db::name('user_type')->where(array('type_id'=>$id))->find();
            if(!$user_type){
                return json(['error'=>1,'msg'=>'等级不存在']);
            }
            $post= input('post.');
            
            $data['type_name']        = $post['type_name'];
            $data['type_fee']         = $post['type_fee'];
            $data['type_free_count']  = $post['type_free_count'];
            $data['type_free_amount'] = $post['type_free_amount'];
            $data['type_sort']        = $post['type_sort'];
            $data['type_profit']        = $post['type_profit'];

            Db::name('user_type')->where(array('type_id'=>$id))->update($data);
            return json(['error'=>0,'msg'=>'修改成功']);
                
        }

        $id = input('get.id',0);
        if(!$id){
            die('参数错误');
        }
        $user_type = Db::name('user_type')->where(array('type_id'=>$id))->find();
        if(!$user_type){
            die('等级不存在');
        }
        $this->assign('list',$user_type);
        return $this->fetch();
    }
    public function disauser(){
    	
    	$keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        $where[]= array('user_state','eq',1);
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $where[] = array('user_name','like','%'.$keywords."%");
            $where[] = array('user_phone','like','%'.$keywords."%");
            $where[] = array('user_idcard','like','%'.$keywords."%");
            $where[] = array('user_tcode','like','%'.$keywords."%");
            $where[] = array('user_account','like','%'.$keywords."%");
            $where[] = array('user_nickname','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('user')->where($where)
            ->order('user_time desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        return $this->fetch('index');
    }
    /*
     * 修改会员状态
     * 2018年8月27日11:24:40
     * 刘媛媛
     */
    public function updestate(){
    	$id = input('get.id',0);
       
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        
        $User = Db::name('user')->where(array('user_id'=>$id))->find();
        if(!$User){
            return json(['error'=>1,'msg'=>'会员不存在']);
        }
        
        if($User['user_state']==1){
            Db::name('user')->where(array('user_id'=>$id))->update(['user_state'=>0]);
        }else{
        	Db::name('user')->where(array('user_id'=>$id))->update(['user_state'=>1]);
        }
        return json(['error'=>0,'msg'=>'操作成功']);
    }
    /*
     * 重置登陆密码
     * 2018年8月27日11:54:40
     * 刘媛媛
     */
    public function resets(){
    	$id = input('get.id',0);
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        $User = Db::name('user')->where(array('user_id'=>$id))->find();
        if(!$User){
            return json(['error'=>1,'msg'=>'会员不存在']);
        }
        
        Db::name('user')->where(array('user_id'=>$id))->update(['user_password'=>md5('123456789')]);
        return json(['error'=>0,'msg'=>'重置密码成功<br/>123456789']);
    }
    /*
     * 查看会员
     * 2018年8月30日09:20:43
     * 刘媛媛
     */
    public function info(){
    	$id = input('get.id',0);
        if(!$id){
        	die('参数错误');
        }
        $User = Db::name('user')->where(array('user_id'=>$id))->find();
        if(!$User){
            die('会员不存在');
        }
    	
    	$this->assign('user',$User);
        return $this->fetch();
    }
    /*
     * 删除会员等级
     * 2018年11月8日13:37:46
     * 刘媛媛
     */
    public function delerat(){
		$id = input('get.id',0);
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        $user_type = Db::name('user_type')->where(array('type_id'=>$id))->find();
        
        if(!$user_type){
            return json(['error'=>1,'msg'=>'等级不存在']);
        }
		
		$isUser = Db::name('user')->where('user_type_id',$id)->find();
		
		if($isUser){
			return json(['error'=>1,'msg'=>'此等级下有会员禁止删除']);
		}
		
		Db::name('user_type')->where(array('type_id'=>$id))->delete();
		
		Db::name('rate')->where(array('rate_type_id'=>$id))->delete();
		
		return json(['error'=>0,'msg'=>'删除成功']);
	}
    public function state(){
        $id = input('get.id',0);
        if(!$id){
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $user_type = Db::name('user_type')->where(array('type_id'=>$id))->find();
        if(!$user_type){
            return json(['error'=>1,'msg'=>'等级不存在']);
        }

        if($user_type['type_state']==1){
            Db::name('user_type')->where(array('type_id'=>$id))->update(['type_state'=>0]);
        }else{
            Db::name('user_type')->where(array('type_id'=>$id))->update(['type_state'=>1]);
        }
        return json(['error'=>0,'msg'=>'操作成功']);
    }
}
