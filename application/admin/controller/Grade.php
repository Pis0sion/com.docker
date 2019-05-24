<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Grade extends Base{
	
	
	public function index(){
		
		if(getconfig('AGENT_GRADE')!=1){
			die('您未开通代理等级配置');
		}

		if($this->request->isPost()){
            $keywords = request()->param('keywords');
            $getdata = $where = $whereor=array();
            if(isset($keywords) && !empty($keywords)){
                //$where[]   = array('user_name','=',$keywords);
                $whereor[] = array('grade_name','like','%'.$keywords."%");
                $getdata['keywords'] =$keywords;
            }

            $list = Db::name('agentGrade')->where($whereor)
                ->order('grade_sort desc')
                ->paginate(10,false,['query'=> $getdata]);
            $this->assign('list',$list);
            return $this->fetch();

        }
          
        $list = Db::name('agentGrade')->order('grade_sort desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
	}
	/*
	 * 添加等级
	 * 2018年10月10日10:13:50
	 */
	public function gradd(){
		if($this->request->isPost()){
            $post = input('post.');
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            //数据添加
            $data['grade_name']       	= $post['grade_name'];
            $data['grade_sort']       	= $post['grade_sort'];
            $data['grade_state']      	= 1;
            $data['grade_rate']       	= $post['grade_rate'];
            $data['grade_rate_close']   = $post['grade_rate_close'];
            $data['grade_rate_upgrade'] = $post['grade_rate_upgrade'];
            $data['grade_capacity']     = $post['grade_capacity'];

            $res = Db::name('agentGrade')->insertGetId($data);
            if($res){
                return json(['error'=>0,'msg'=>'添加完毕']);
            }else{
                return json(['error'=>1,'msg'=>'添加失败']);
            }
        }

        return $this->fetch();
	}
	/*
	 * 修改等级
	 * 2018年10月10日10:14:06
	 * 
	 */
	public function grupde(){
		if($this->request->isPost()){
            $post = input('post.');
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            //数据修改
            $data['grade_id']       	= $post['grade_id'];
            $data['grade_name']       	= $post['grade_name'];
            $data['grade_sort']       	= $post['grade_sort'];
            $data['grade_rate']       	= $post['grade_rate'];
            $data['grade_rate_close']   = $post['grade_rate_close'];
            $data['grade_rate_upgrade'] = $post['grade_rate_upgrade'];
            $data['grade_capacity']     = $post['grade_capacity'];

            $res = Db::name('agentGrade')->update($data);
            if($res){
                return json(['error'=>0,'msg'=>'修改成功']);
            }else{
                return json(['error'=>1,'msg'=>'修改失败']);
            }
        }

        $id = input('get.id');
        if(!$id){
        	$this->error('参数错误！');
        }

        $info = Db::name('agentGrade')->where(['grade_id'=>$id])->find();
        $this->assign('info', $info);
        return $this->fetch();
	}
	
	/*
	 * 修改状态
	 * 2018年10月10日10:14:39
	 */
	public function grstate(){
		$id = intval(input("get.id"));

        if(empty($id)){
            return json(array(
                'msg'   => "参数错误!",
                'error' => 1
            ));
        }

        $auth = Db::name("agentGrade")->where(array('grade_id'=>$id))->find();
        if(!$auth) {    
            return json(array(
                'msg'   => "您要设置的权限不存在！",
                'error' => 1
            ));
        }

        if($auth['grade_state'] == 1) {
            Db::name("agentGrade")->where(array('grade_id'=>$id))->update(array('grade_state' => 0));
        } else {
            Db::name("agentGrade")->where(array('grade_id'=>$id))->update(array('grade_state' => 1));
        }
        return json(array(
            'msg'   => "设置成功！",
            'error' => 0
        ));
		
	}
	/*
	 * 删除等级
	 * 前提是没有代理商对应这个等级
	 */
	public function grdelete(){
		$id = intval(input("get.id"));

        if(empty($id)){
            return json(array(
                'msg'   => "参数错误!",
                'error' => 1
            ));
        }

        $auth = Db::name("agentGrade")->where(array('grade_id'=>$id))->find();
        if(!$auth) {    
            return json(array(
                'msg'   => "您要设置的权限不存在！",
                'error' => 1
            ));
        }

        $aginfo = Db::name('agent')->where(['agent_grade'=>$auth['grade_id']])->select();
        if($aginfo){
            return json(array(
                'msg'   => "请删除没有代理的等级~",
                'error' => 1
            ));
        }

        Db::name("agentGrade")->where(array('grade_id'=>$id))->delete();
        return json(array(
            'msg'   => "删除成功！",
            'error' => 0
        ));
	}
	/*
	 * 查看指定等级下的代理商
	 * 2018年10月10日10:15:37
	 */
	public function grinfo(){
        $gid = input('get.gid');
        if(!$gid){
        	$this->error('参数错误！');
        }

        $list = Db::name('agent')->where(['agent_grade'=>$gid])->select();
        $this->assign('list',$list);
        return $this->fetch();
	}
}
