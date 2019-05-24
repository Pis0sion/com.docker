<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class User extends Base{
	public function __construct(){
       parent::__construct();
    }
  
  	/*
     * 查看会员注册资料
     * 2019年1月27日15:52:13
     * 刘媛媛
     */
    public function datainfo(){
    	$id = input('get.id',0);
        if(!$id){
        	die('参数错误');
        }
        $User = Db::name('user')->where(array('user_id'=>$id))->find();
        if(!$User){
            die('会员不存在');
        }
        $pathnews =   $_SERVER['DOCUMENT_ROOT'].'/logs/site/register/'.$id.'.txt';
      
    	if(!file_exists($pathnews)){
        	die('注册数据不存在或功能之前数据');
        }
      
      	$data=file_get_contents($pathnews);
         echo '<pre>'.$data.'</pre>';
      	echo '数据说明：<br/>';
        echo 'https://www.kancloud.cn/liuyuao/hksm/931153';
    }
    public function index(){
        $keywords = request()->param('keywords');
        $pid = request()->param('pid');
        $getdata = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $whereor[] = array('user_name','like','%'.$keywords."%");
            $whereor[] = array('user_phone','like','%'.$keywords."%");
            $whereor[] = array('user_idcard','like','%'.$keywords."%");
            $whereor[] = array('user_code','like','%'.$keywords."%");
            $whereor[] = array('user_account','like','%'.$keywords."%");
            $whereor[] = array('user_nickname','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
      		
     	$reals    = request()->param('reals');
      	if(isset($reals) && !empty($reals)){
          	if($reals=='1'){
            	$whereor[]   = array('user_real','=',1);
            }else if($reals=='2'){
            	$whereor[]   = array('user_real','=',0);
            }
            $getdata['reals'] =$reals;
        }
      
      	$usertype = request()->param('user_type_id');
      	if(isset($usertype) && !empty($usertype)){
            $whereor[]   = array('user_type_id','=',$usertype);
            $getdata['user_type_id'] = $usertype;
        }
      	
        if(isset($pid) && !empty($pid)){
        	$where[]   = array('user_pid','=',$pid);
            $getdata['pid'] =$pid;
        }
        $list = Db::name('user')->where($where)->whereor($whereor)
            ->order('user_id desc')
            ->paginate(10,false,['query'=> $getdata]);
      	$userType = Db::name('userType')->cache(36400)->where('type_state',0)->select();
      	$userTypeList = array();
      	foreach ($userType as $k=>$v){
          	$userTypeList[]=[
              	'typeid'=>$v['type_id'],
            	'typeName'=> $v['type_name'],
                 'typeCount'=>  Db::name('user')->where($where)->whereor($whereor)->where('user_type_id',$v['type_id'])->count()
            ];
        }
      	
      	$this->assign('getdata',$getdata);
        $this->assign('list',$list);
        $this->assign('userTypelist',$userTypeList);
        return $this->fetch();
    }
    
  	// 会员资金变动记录
  	public function capchange(){
    	$id = input('get.id');	
      	if(empty($id)){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
		$getdata = ['id'=>$id];
        $list = Db::name('userPresentation')->where(['presentation_uid'=>$id])
            ->order('presentation_time desc')
            ->paginate(10,false,['query'=> $getdata]);
      
      	$this->assign('list',$list);
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
    public function add()
    {
        if($_POST)
        {
            $data = $_POST;
            echo api_post('Api/Main/regisrer',$data);
            return;
        }

        $user_type = Db::name('user_type')->select();
        $this->assign('user_type',$user_type);
        return $this->fetch();
    }


    /**
     * 编辑
     * @Author t░
     * @Date   2018-09-07
     * @return [type]     [description]
     */
    public function uedit(){
    	
    	if($this->request->isPost()) {
            $post= input('post.');
            $id = $post['user_id'];
	        if(!$id){
	        	return json(['error'=>1,'msg'=>'参数错误']);
	        }
	        $User = Db::name('user')->where(array('user_id'=>$id))->find();
	        if(!$User){
	            return json(['error'=>1,'msg'=>'会员不存在']);
	        }
    		if($post['user_password']){
                $data['user_password']      = $post['user_password'];
            }

            if($post['pacc'] != '无'){
                $accuser = Db::name('user')->where(array('user_account'=>$post['pacc']))->whereOr(['user_phone'=>$post['pacc']])->value('user_id');
                
                if(!$accuser){
                    return json(['error'=>1,'msg'=>'该上级推荐人不存在']);
                }else{
                    $data['user_pid']  = $accuser;
                }
            }else{
                $data['user_pid']  = 0;
            }
            $data['user_phone']    = $post['user_phone'];
            $data['user_account']  = $post['user_account'];
            $data['user_nickname'] = $post['user_nickname'];
            $data['user_uuid']     = $post['user_uuid'];
            // $data['user_type_id']  = $post['user_type_id'];
            $data['user_real']     = $post['user_real'];
            $data['user_name']     = $post['user_name'];
            $data['user_idcard']   = $post['user_idcard'];
            $data['user_img']      = $post['user_img'];
    		
    		Db::name('user')->where(array('user_id'=>$id))->update($data);
    		
    		return json(['error'=>0,'msg'=>'修改成功']);
    		 	
    	}
    	$id = input('get.id',0);
        if(!$id){
        	die('参数错误');
        }
        $User = Db::name('user')->where(array('user_id'=>$id))->find();
        
    	$this->assign('user',$User);

        $user_type = Db::name('user_type')->select();
        $this->assign('user_type',$user_type);
        return $this->fetch();
    }

    /**
     * 用户分润提现管理
     */
    public function pftwarll(){
        $keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $whereor[] = array('user_name','like','%'.$keywords."%");
            $whereor[] = array('user_phone','like','%'.$keywords."%");
            $whereor[] = array('user_idcard','like','%'.$keywords."%");
            $whereor[] = array('user_code','like','%'.$keywords."%");
            $whereor[] = array('user_account','like','%'.$keywords."%");
            $whereor[] = array('user_nickname','like','%'.$keywords."%");
            $getdata['keywords'] = $keywords;
        }
        $list = Db::name('UserProfit')->where($where)->whereor($whereor)
            ->order('profit_id desc')
            ->paginate(10,false,['query'=> $getdata]);
        $page = $list->render();
        $list = $list->toArray();

        foreach($list['data'] as $k=>$v){
            $usercard = Db::name('UserCard')->where(['card_id'=>$v['profit_card_id']])->find();
            if($v['profit_true_money'] < 0){
                $list['data'][$k]['profit_true_money'] = 0;
            }
            // $list['data'][$k]['profit_card_id'] = Db::name('UserCard')->where(['card_bank_id'=>$v['profit_card_id']])->value('card_no');
            $list['data'][$k]['profit_admin_id'] = Db::name('admin')->where(['admin_id'=>$v['profit_admin_id']])->value('admin_name');
            //$list['data'][$k]['user_name'] = $usercard['card_name'];
            //$list['data'][$k]['user_phone'] = $usercard['card_phone'];
            $list['data'][$k]['user_branch'] = $usercard['card_branch'];
            unset($usercard);
        }
        $this->assign('list',$list['data']);
        $this->assign('page',$page);
        return $this->fetch();
    }

   /**
     * 用户分润提现或拒绝
     */ 
    public function wallsta(){
        $get = input('get.');
        if(empty($get)){
            return json(['error'=>0,'msg'=>'参数错误']);
        }
        $id   = trim($get['id']);
        $type = $get['type'];
        
        if($type == '1'){
            DB::name('UserProfit')->where(['profit_id'=>$id])->update(['profit_paytime'=>time(), 'profit_admin_id'=>session('admin_id'),'profit_type'=>2]);
        }elseif($type == '2'){

            // 更改提现状态
            DB::name('UserProfit')->where(['profit_id'=>$id])->update(['profit_admin_id'=>session('admin_id'),'profit_type'=>3]);

            // 退回账户余额
            $profit = DB::name('UserProfit')->where(['profit_id'=>$id])->find();
            
            DB::name('User')->where(['user_id'=>$profit['profit_uid']])->setInc('user_moeny', $profit['profit_money']);

            // 增加余额变动记录
            $data['presentation_uid']     = $profit['profit_uid'];
            $data['presentation_type']    = 1;
            $data['presentation_point']   = $profit['profit_money'];
            $data['presentation_surplus'] = Db::name('user')->where(array('user_id'=>$profit['profit_uid']))->value('user_moeny');
            $data['presentation_time']    = time();
            $data['presentation_content'] = '提现失败金额退回';
            DB::name('UserPresentation')->insertGetId($data);
        }

        return json(['error'=>0,'msg'=>'操作成功']);
    }
  	
  /*修改会员余额
	 * 2018年11月9日13:25:34
	 */
	public function  upmoeny(){
		$post = input('post.');
		
		if(intval($post['id'])==0){
			return json(['error'=>1,'msg'=>'会员ID错误']);
		}
		if($post['money']==''){
			return json(['error'=>1,'msg'=>'请输入操作金额']);
		}
		
		$retUser = Db::name('user')->where('user_id',$post['id'])->find();
		
		if(!$retUser){
			return json(['error'=>1,'msg'=>'会员不存在']);
		}
		
		Db::name('user')->where('user_id',$post['id'])->setInc('user_moeny',$post['money']);
		
		return json(['error'=>0,'msg'=>'操作成功']);
		
	}
}
