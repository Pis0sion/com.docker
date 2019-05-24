<?php 
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Region extends Base{
	public function __construct(){
       parent::__construct();
    }

    public function addregion(){

    	
    	$date = Db::name('Area')->select();
    	$da = array();
    	$b = array();
    	$c = array();
    	$arr2 = array();
    	foreach ($date as $key => $va) {
    		$b = explode(',',$va['area_province']);

    		if($b[$key] == ''){
    			unset($b[$key]);
    		}
    		$c[] = $b;

    	}

		foreach($c as $k=>$v){
			foreach($v as $key=>$val){
				$arr2[$key]=$val;
			}
			// unset($arr2[0]);
			// unset($arr2[1]);
		}
		$list = array();
		foreach ($arr2 as $key => $va) {
			
			$list[] = Db::name('Region')->where('region_type',1)->where('region_id',$va)->find();
		
		}
dump($c);die;
		$area = Db::name('Region')->where('region_type',1)->select();
		$data = array();
		foreach ($area as $key => $value) {
			if($value['region_id'] == $list[$key]['region_id']){
				unset($area[$key]);
			}
		}

    	$this->assign('data',$area);
    	return $this->fetch();
    }

    public function addcode(){
    	if($this->request->isPost()){

    		$post = input('post.');
    		if(empty($post)){
    			return json(['error'=>1,'msg'=>'参数错误']);
    		}

    		// $arr = explode(',', $post['data']);
    		// $list = array();
    		// foreach ($arr as $key => $val) {
    		// 	if($arr[$key]==''){
    		// 		unset($val);
    		// 	}
    		// 	$list[$key] = Db::name('Region')->where(['region_id'=>$val,'region_type'=>1])->find();
    		// }
    		
    		// $a = '';
    		// foreach ($list as $k => $v) {
    		// 	$a .= $v['region_id'].'|'.$v['region_name'].',';
    		// }
    		$b = Db::name('Area')->where('area_name',trim($post['name']))->select();
    		if(!empty($b)){
    			return json(['error'=>1,'msg'=>'该区域名字已存在']);
    		}
    		$data = array();
    		$data['area_name'] = trim($post['name']);
    		$data['area_province'] = ',,'.trim($post['data']);
    		if(Db::name('Area')->insert($data)){

    			return json(['error'=>0,'msg'=>'添加成功']);
    		}else{

    			return json(['error'=>1,'msg'=>'添加失败']);
    		}
    	}else{

    		return json(['error'=>1,'msg'=>'非法请求']);
    	}
    }
}