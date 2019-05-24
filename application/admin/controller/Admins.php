<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Admins extends Base{
    public function initialize(){
        parent::initialize();
        $this->assign('menu','Admins');
    }
    
    
    public function index(){
    	
    	$list =Db::name('admin')
		->alias('a')
		->join('auth_group w','a.admin_role = w.id')
		->paginate(10);
		$this->assign('list',$list);
        return $this->fetch();
    }
	
	/*
	 * 重置密码
	 */
	public function rpass(){
		$id = input('get.id',0);
		if(intval($id)==0){
			return json(['error'=>1,'msg'=>'参数错误']);
		}
		
		Db::name('admin')->where('admin_id',$id)->update(['admin_password'=>md5('123456')]);
		
		$this->adminLog('重置后台管理员密码',$id);
		
		return json(['error'=>0,'msg'=>'操作成功']);
	}
	/*
	 * 删除管理员
	 */
	public function admindele(){
		
		$id = input('get.id',0);
		if(intval($id)==0){
			return json(['error'=>1,'msg'=>'参数错误']);
		}
		
		Db::name('admin')->where('admin_id',$id)->delete();
		
		$this->adminLog('删除管理员',$id);
		$this->setAccess($id,true);
		return json(['error'=>0,'msg'=>'删除成功']);
		
	}
	/*
	 * 记录
	 */
	public function infolog(){
		$id = input('get.id',0);
		// 查询状态为1的用户数据 
		if($id==0){
			$list = Db::name('adminLog')->order('log_time','desc')->paginate(10);
		}else{
			$list = Db::name('adminLog')->where('log_adminid',$id)->order('log_time','desc')->paginate(10);
		}
	
		// 把分页数据赋值给模板变量list
		$this->assign('list', $list);
		// 渲染模板输出
		return $this->fetch();
		
	}
	
	/*
	 * 修改自己的密码
	 */
	public function mypass(){
		if($this->request->isPost()) {
		 	$post = input('post.');
		 	
		 	if(md5($post['pass'])!=$this->admin['admin_password']){
		 		return json(array('error'=>1,'msg'=>'原密码错误!'));
		 	}
		 	if($post['passn']!=$post['passr']){
		 		return json(array('error'=>1,'msg'=>'两次输入的密码不一致!'));
		 	}
		 	
        	Db::name('admin')->where(array('admin_id'=>$this->admin['admin_id']))->update(['admin_password'=>md5($post['passn'])]);
		 	$this->adminLog('修改密码',json_encode($post));
		 	return json(array('error'=>0,'msg'=>'修改成功!','url'=>Url('Admin/Main/logout')));
		}
		
		return $this->fetch();
		
	}
	
	
	
	public function updeadmin(){
		if($this->request->isPost()) {
		 	$post = input('post.');
		 	
		 	$id   = intval($post['id']);
	        if($id == 0){
	            $this->error('参数错误');
	        }
        
		 	$retData  = Db::name('admin')->where(array('admin_id'=>$id))->find();
	        if(!$retData){
	            $this->error('管理员不存在');
	        }
	        $data['admin_name'] = $post['admin_name'];
	        $data['admin_user'] = $post['admin_user'];
	        $data['admin_role'] = intval($post['member_agent']);
        	Db::name('admin')->where(array('admin_id'=>$id))->update($data);
		 	$this->adminLog('编辑管理员',json_encode($data));
		 	//$post
		 	$this->setAccess($id);
		 	return json(array('error'=>0,'msg'=>'修改成功!'));//$this->error('管理员不存在');
		}
		
		$id   = intval(input('get.id'));
        if($id == 0){
            $this->error('参数错误');
        }
        $retData  = Db::name('admin')->where(array('admin_id'=>$id))->find();
        if(!$retData){
            $this->error('管理员不存在');
        }
        $listclass  = Db::name('authGroup')->where('status',1)->select();
        
        $this->assign('data',$retData);
        $this->assign('listclass',$listclass);
        return $this->fetch();
        
	}
    // 添加管理员
    public function addmin(){
        if($this->request->isPost()) {
            $post = input('post.');
           
            $data['admin_name']     = $post['admin_name'];
            $data['admin_user']     = $post['admin_user'];
            $data['admin_password'] = md5($post['admin_password']?:123456);
            $data['admin_role']     = $post['admin_role'];
            
            if($data['admin_name']==''){
                return json(array('error'=>1,'msg'=>'请输入登录账户！'));
            }

            $resDat  = Db::name('admin')->where(array('admin_user'=>$data['admin_user']))->find();
            if($resDat){
                return json(array('error'=>1,'msg'=>'账户已经存在'));
            }

            $resData = Db::name('admin')->insert($data);
            if($resData){
            	$this->setAccess($id);
            	$this->adminLog('添加商户',json_encode($post));
                return json(array('error'=>0,'msg'=>'添加成功！'));
            }else{
                return json(array('error'=>1,'msg'=>'添加失败！'));
            }
        }
		$listclass  = Db::name('authGroup')->where('status',1)->select();
 		$this->assign('listclass',$listclass);
        return $this->fetch();
    }
    
    
    private function setAccess($adminID, $isDelete = false) {
    	
    	if($isDelete) {
    		Db::name("authGroupAccess")->where(array("uid"=>$adminID))->delete();
    		return ;
    	}
    	
    	$admin = Db::name("admin")->where(array("admin_id"=>$adminID))->find();
    	$access = Db::name("authGroupAccess")->where(array("uid"=>$adminID))->find();
    	if($access) {
    		Db::name("authGroupAccess")->where(array("uid"=>$adminID))->update(array("group_id"=>$admin["admin_role"]));
    	} else {
    		Db::name("authGroupAccess")->add(array("uid"=>$adminID,"group_id"=>$admin["admin_role"]));
    	}
    }

}
