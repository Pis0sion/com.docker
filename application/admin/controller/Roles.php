<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Roles extends Base{
	
		
	public function roles_list(){
		
		$list = db::name("authGroup")->select();
		
		$this->assign("list", $list);
		return $this->fetch();
    }
    
    public function disable() {
    	$id = intval(input("get.id"));
    	if($id == 0) {
    		return json(array(
    			"error" => 1,
    			"msg" => "参数错误！"
    		));
    	}
    	$roles = db::name("authGroup")->where(array("id"=>$id))->find();
    	if(!$roles) {
    		return json(array(
    			"error" => 1,
    			"msg" => "参数错误！"
    		));
    	}
    	$newStatus = $roles["status"] == "0" ? "1":"0";
    	db::name("authGroup")->where(array("id"=>$id))->update(array("status"=>$newStatus));
    	return json(array(
	    	"error" => 0,
	    	"msg" => "操作成功！"
    	));
    }
    
    public function roles_add() {
		if($this->request->isPost()) {
			
			$post = input("post.");
			
			if($post["title"] == "") {
				return json(array(
					"error" => 1,
					"msg" => "请填写角色名字！"
				));
			}
			$data = array();
			$data["title"]  = $post["title"];
			$data["status"] = $post["status"];
			
			Db::name("authGroup")->insert($data);
			
		return json(array(
				"error" => 0,
				"msg" => "添加成功！"
			));
		}
		 return $this->fetch();
    }
    
    public function roles_edit() {
		$id = intval(input("get.id"));
		if($this->request->isPost()) {
			if($id == 0) {
    			return json(array(
	    			"error" => 1,
	    			"msg" => "参数错误！"
	    		));
	    	}
	    	$roles = db::name("authGroup")->where(array("id"=>$id))->find();
	    	if(!$roles) {
	    		return json(array(
	    			"error" => 1,
	    			"msg" => "参数错误！"
	    		));
	    	}
	    	
			$post = input("post.");
			
			if($post["title"] == "") {
				return json(array(
					"error" => 1,
					"msg" => "请填写角色名字！"
				));
			}
			$data = array();
			$data["title"]  = $post["title"];
			$data["status"] = $post["status"];
			
			M("authGroup")->where(array("id"=>$id))->update($data);
			
			return json(array(
				"error" => 0,
				"msg" => "修改成功！"
			));
		}
		if($id == 0) {
    		$this->adminError("参数错误！");
    	}
    	$roles = db::name("authGroup")->where(array("id"=>$id))->find();
    	if(!$roles) {
    		$this->adminError("参数错误！");
    	}
    	$this->assign("data", $roles);
		 return $this->fetch();
    }
    
    public function delete() {
    	$id = intval(input("get.id"));
		
    	if($id == 0) {
    		return json(array(
    			"error" => 1,
    			"msg" => "参数错误！"
    		));
    	}
    	$roles = db::name("authGroup")->where(array("id"=>$id))->find();
    	if(!$roles) {
    		return json(array(
    			"error" => 1,
    			"msg" => "参数错误！"
    		));
    	}
    	$roles = db::name("authGroup")->where(array("id"=>$id))->delete();
    	return json(array(
    		"error" => 0,
    		"msg" => "删除成功！"
    	));
    }
    
    public function set_auths() {
    	$id = intval(input('get.id'));
		if($this->request->isPost()) {
			if($id == 0) {
				return json(array(
					'msg'   => "您要设置的角色不存在！",
					'error' => 1
				));
			}
			$roles = db::name("authGroup")->where(array("id"=>$id))->find();
			if(!$roles) {
				return json(array(
					'msg'   => "您要设置的角色不存在！",
					'error' => 1
				));
			}
			$authStr = implode(",", input("post.auth"));
			Db::name("authGroup")->where(array("id"=>$id))->update(array("rules"=>$authStr));
			return json(array(
				'msg'   => "设置成功！",
				'error' => 0
			));
		}
		if($id == 0) {
			echo '您要设置的角色不存在1';exit;
		}
		$roles = db::name("authGroup")->where(array("id"=>$id))->find();
		if(!$roles) {
			echo '您要设置的角色不存在2';exit;
		}
		
		$authCate =  require CACHE_PATH.'/auth_class.php';
		$auths    = db::name("authRule")->select();
		
		$this->assign("data", $roles);
		$this->assign("classes", $authCate);
		$this->assign("list", $auths);
		return $this->fetch();
    }
     
}