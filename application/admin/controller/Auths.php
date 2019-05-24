<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Auths extends Base{
	
		
	private $cacheAuthClass = array();
	
	public function __construct(){
		parent::__construct();
		$this->cacheAuthClass =  require CACHE_PATH.'auth_class.php';
		$this->assign("classes",$this->cacheAuthClass);
	}
    public function index() {

		$count = Db::name("authRule")->count();
		$list  = Db::name("authRule")->order('id','desc')->paginate(10);
		
		$this->assign("list",$list);
		return $this->fetch();
	}
	
	private function updateClass($data) {
		$tag = 0;
		$authClass = Db::name("authClass")->where(array("class_tag"=>$data['class_tag']))->find();
		if($authClass) {
			$tag = Db::name("authClass")->where('class_id',$authClass['class_id'])->update($data);
		} else {
			$tag = Db::name("authClass")->insert($data);
		}
		return $tag;
	}
	
	public function auths_class() {
		if($this->request->isPost()) {
			$name = input("post.name");
			$tag  = input("post.tag");
			
			if(is_array($tag)) {
				foreach($tag as $k => $v) {
					if($v == "") continue;
					$data = array();
					$data['class_name'] = $name[$k];
					$data['class_tag']  = $v;
					$this->updateClass($data);
				}
				
			} else {
				$data = array();
				$data['class_name'] = $name;
				$data['class_tag']  = $tag;
				$this->updateClass($data);
			}
			$this->cache_class(0);
			return json(array(
				'msg'   => "保存成功！",
				'error' => 0
			));
		}
		return $this->fetch();
	}
	
	public function auths_insert() {
		
		if($this->request->isPost()) {
			
			$name      = input("post.ag_name");
			$title     = input("post.ag_title");
			$type      = input("post.ag_type");
			$status    = input("post.ag_status");
			$condition = input("post.ag_condition");
			$classID   = input("post.ag_cate");
			
			if(!Db::name("authClass")->where(array('class_id'=>$classID))->find()) {
				return json(array(
					'msg'   => "您选择的分类不存在！",
					'error' => 1
				));
			}
			
			$data = array();
			$data['name']      = $name;
			$data['title']     = $title;
			$data['type']      = $type;
			$data['status']    = $status;
			$data['condition'] = $condition;
			$data['class_id']  = $classID;
			
			$auth = Db::name("authRule")->where(array("name"=>$data['name']))->find();
			if($auth) {
				$tag = Db::name("authRule")->where('id',$auth['id'])->update($data);
			} else {
				$tag = Db::name("authRule")->insert($data);
			}
			
			return json(array(
				'msg'   => "保存成功！",
				'error' => 0
			));
		}
		
		return $this->fetch();
	}
	
	public function auths_edit() {
		$id = intval(input("get.id"));
		if($this->request->isPost()) {
			
			if($id == 0) {
				return json(array(
					'msg'   => "您要修改的权限不存在！",
					'error' => 1
				));
			}
			$auth = Db::name("authRule")->where(array('id'=>$id))->find();
			if(!$auth) {
				return json(array(
					'msg'   => "您要修改的权限不存在！",
					'error' => 1
				));
			}
			
			$name      = input("post.ag_name");
			$title     = input("post.ag_title");
			$type      = input("post.ag_type");
			$status    = input("post.ag_status");
			$condition = input("post.ag_condition");
			$classID   = input("post.ag_cate");
			
			if(!Db::name("authClass")->where(array('class_id'=>$classID))->find()) {
				return json(array(
					'msg'   => "您选择的分类不存在！",
					'error' => 1
				));
			}
			
			$data = array();
			$data['name']      = $name;
			$data['title']     = $title;
			$data['type']      = $type;
			$data['status']    = $status;
			$data['condition'] = $condition;
			$data['class_id']  = $classID;
			
			$map['id'] = $auth['id'];
			
			$auth = Db::name("authRule")->where($map)->update($data);
			
			return json(array(
				'msg'   => "保存成功！",
				'error' => 0
			));
		}
		if($id == 0) {
			$this->adminError("您要修改的权限不存在！");
		}
		$auth = Db::name("authRule")->where(array('id'=>$id))->find();
		if(!$auth) {
			$this->adminError("您要修改的权限不存在！");
		}
		$this->assign("detail", $auth);
		return $this->fetch();
	}
	
	public function disable() {
		$id = intval(input("get.id"));
		if($id == 0) {
			return json(array(
				'msg'   => "您要设置的权限不存在！",
				'error' => 1
			));
		}
		$auth = Db::name("authRule")->where(array('id'=>$id))->find();
		if(!$auth) {
			return json(array(
				'msg'   => "您要设置的权限不存在！",
				'error' => 1
			));
		}
		if($auth['status'] == 1) {
			Db::name("authRule")->where(array('id'=>$id))->update(array('status' => 0));
		} else {
			Db::name("authRule")->where(array('id'=>$id))->update(array('status' => 1));
		}
		return json(array(
			'msg'   => "设置成功！",
			'error' => 0
		));
	}
	
	public function delete() {
		$id = intval(input("get.id"));
		if($id == 0) {
			return json(array(
				'msg'   => "您要删除的权限不存在或已删除！",
				'error' => 1
			));
		}
		$auth = Db::name("authRule")->where(array('id'=>$id))->find();
		if(!$auth) {
			return json(array(
				'msg'   => "您要删除的权限不存在或已删除！",
				'error' => 1
			));
		}
		$auth = Db::name("authRule")->where(array('id'=>$id))->delete();
		return json(array(
			'msg'   => "删除成功！",
			'error' => 0
		));
	}
	
	public function cache_class($isAjax = 1){
		$data = Db::name("authClass")->order('class_id')->select();
		$this->update_cache("auth_class", $data, "class_id");
		if($isAjax == 1) {
			return json(array(
				'msg'   => "缓存更新成功！",
				'error' => 0
			));
		}
	}
	public function auths_add(){
		return $this->fetch();
	}
	public function reg_auth(){
		return $this->fetch();
	}
	
	public function update_cache($cache_file, $list, $pk) {
		$cache_file = CACHE_PATH.'/'.$cache_file.'.php';
		if(!is_null($pk)) {
			$data = array();
			foreach($list as $v) {
				$data[$v[strtolower($pk)]] = $v;
			}
		} else {
			$data = $list;
		}
		file_put_contents($cache_file, "<?php \nreturn " . stripslashes(var_export($data, true)) . ";", LOCK_EX);
		@unlink(RUNTIME_FILE);
		return true;
	}
}