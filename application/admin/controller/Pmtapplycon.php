<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Pmtapplycon extends Base{
	
	public function __construct(){
       parent::__construct();
    }
    
    // 申请通道列表
    public function paclist(){
    	$keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        
        if(isset($keywords) && !empty($keywords)){
            $where[] = array('apply_name','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('bankApply')
        	->where($where)
            ->order('apply_time desc')
            ->paginate(20,false,['query'=> $getdata]);

        $this->assign('list',$list);
        return $this->fetch();
    }


    public function pacrcord(){
        $keywords = request()->param();
        $getdata = $where = $whereor=array();
        // dump($keywords);

        if(isset($keywords['startTime'])){
            if(empty($keywords['endTime'])){
                $where[] = ['log_time', 'between time', [$keywords['startTime'], time()]];
            }else{
                $where[] = ['log_time', 'between time', [$keywords['startTime'], $keywords['endTime']]];
            }
            
        }elseif(isset($keywords['endTime'])){
            if(empty($keywords['startTime'])){
                $where[] = ['log_time', 'between time', [0, $keywords['endTime']]];
            }else{
                
            }
        }
        $getdata =$keywords;
        $list = Db::name('bankApplyLog')
            ->where($where)
            ->order('log_time desc')
            ->paginate(20,false,['query'=> $getdata]);

        $this->assign('list',$list);
        $this->assign($getdata);
        return $this->fetch();
    }
  
  	public function dismon(){
    	 if($this->request->isPost()){
    	 	
    	 	$config = require CACHE_PATH.'system.php';
	        $post = input('post.');
         	if(!$post){
                return json(['error'=>1,'msg'=>'参数错误!']);
            }
           	
           	$find = Db::name('bankApplyLog')->where(['log_id'=>$post['id'], 'log_fre_type'=>1])->find();
			if(!$find){
            	return json(['error'=>1,'msg'=>'数据不存在!']);
            }
           
           	$data['log_id']       = $post['id'];
           	$data['log_fre_type'] = 2;           	
            $data['log_fre']      = $post['mon'];
			
           	$res = Db::name('bankApplyLog')->update($data);
           	if($res){
           		//申请类型 1信用卡申请 2 网贷申请 3 积分兑换
           		$list = getuserSups($find['log_user'],false,3);
           		$i = 0;
                if($find['log_type']==1 and $config['USER_CRARD_CZ']==1){
                	
                	foreach ($list as $k=>$v){
                		$i++;
                		$result = bonuslog($v, $post['mon']*$config['USER_CRARD_TYPE_CZ_'.$i], time(), 1, '下级会员信用卡申请升级'.$i.'级分润',($i-1), 0, 0);
                	}
                }elseif($find['log_type']==2 and $config['USER_LOAN_CZ']==1){
                  
                	foreach ($list as $k=>$v){
                		$i++;
                		$result = bonuslog($v, $post['mon']*$config['USER_LOAN_TYPE_CZ_'.$i], time(), 1, '下级网贷申请'.$i.'级分润',($i-1), 0, 0);
                	}
                }elseif($find['log_type']==3 and $config['USER_INTEGRAL_CZ']==1){
                 	
                	foreach ($list as $k=>$v){
                		$i++;
                		$result = bonuslog($v, $post['mon']*$config['USER_INTEGRAL_TYPE_CZ_'.$i], time(), 1, '下级积分兑换'.$i.'级分润',($i-1), 0, 0);
                	}
                }
                
            	return json(['error'=>0,'msg'=>'处理成功']);
            }else{
            	return json(['error'=>1,'msg'=>'处理失败，请重试。']);
            }
         }
    }

    public function pacadd(){
    	if($this->request->isPost()){
    		$post = input('post.');

            $data['apply_name']   		= $post['apply_name'];
            $data['apply_qdname'] 		= $post['apply_qdname'];
            $data['apply_rate']   		= $post['apply_rate'];
            $data['apply_type']   		= $post['apply_type'];
            $data['apply_time']   	    = time();
            $data['payment_controller'] = $post['payment_controller'];
            $data['apply_config'] 		= $post['apply_config'];
            $data['apply_use'] 			= 0;
            
            $id = Db::name('bankApply')->insertGetId($data);
            if($id){
                return json(['error'=>0,'msg'=>'添加成功']);
            }
            return json(['error'=>1,'msg'=>'添加错误']);
        }

    	return $this->fetch();
    }

    // 编辑
    public function pacedit(){
    	if($this->request->isPost()){
    		$post = input('post.');

    		$data['apply_id']           = $post['apply_id'];
            $data['apply_name']   		= $post['apply_name'];
            $data['apply_qdname'] 		= $post['apply_qdname'];
            $data['apply_rate']   		= $post['apply_rate'];
            $data['apply_type']   		= $post['apply_type'];
            $data['apply_time']   	    = time();
            $data['payment_controller'] = $post['payment_controller'];
            $data['apply_config'] 		= $post['apply_config'];
            $data['apply_use'] 			= 0;
            
            $id = Db::name('bankApply')->update($data);
            if($id){
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改失败，请重试。']);
        }

        $info = Db::name('bankApply')->where(['apply_id'=>input('param.id')])->find();
        $this->assign($info);
    	return $this->fetch();
    }

    // 删除
    public function pacdel(){
    	$id = input('get.id',0);
        if(empty($id)){
            return json(['error'=>1,'msg'=>'参数错误']);
        }

        $article = Db::name('bankApply')->where(['apply_id'=>$id])->find();
        if(empty($article)){
            return json(['error'=>1,'msg'=>'通道不存在']);
        }

        $up = Db::name('bankApply')->where(['apply_id'=>$id])->delete();
        return json(['error'=>0,'msg'=>'成功']);
       
    }

    // 更改通道状态
    public function pacsta(){
    	$id = input('get.id',0);
        $type = input('get.type',0);
        if(empty($id)){
            return json(['error'=>1,'msg'=>'参数错误']);
        }

        $article = Db::name('bankApply')->where(['apply_id'=>$id])->find();

        if(empty($article)){
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        if($type==0){
            //不显示
            $up = Db::name('bankApply')->where(['apply_id'=>$id])->update(['apply_use'=>0]);
            if(empty($up)){
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            return json(['error'=>0,'msg'=>'成功']);
        } elseif($type==1){
            //启用计划
            $up = Db::name('bankApply')->where(['apply_id'=>$id])->update(['apply_use'=>1]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            return json(['error'=>0,'msg'=>'成功']);
        }
    }
}