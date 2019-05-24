<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;

class Common extends Controller
{
	/**
	 * 获取版本信息
	 */
	public function version()
	{
		if($this->request->isPost()) {
			$post = input('post.');
			$version  = $post['version']; // 版本号
			$os		  = $post['os']; // 类型
			if(!$version || !$os){
	            return json(['error'=>1,'msg'=>'参数错误']);
			}

	        $versinfo = Db::name('appVersions')->order('app_id desc')->find();
	        // 是否最新版本
			if($versinfo['app_verber'] != $version){
				$data['code'] = '0';
			}else{
				$data['code'] = '1';
			}
			
			// 是否ios系统
			if($os == 'ios'){
				$data['url'] = $versinfo['app_down_applelink'];
			}else{
				$data['url'] = $versinfo['app_down_androlink'];
			}
			$data['version'] = $versinfo['app_verber'];

	        return json(['error'=>0, 'data'=>$data]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
	}
	/*
	 * ajax获取省份
	 * 2018年10月12日11:25:30
	 * 刘媛媛
	 */
	public function ajax_province(){
		return json($this->province(0));
	}
	/*
	 * ajax获取城市
	 * 2018年10月12日11:25:30
	 * 刘媛媛
	 */
	public function ajax_city(){
		
		$pid = input('get.id');
		if($pid == 0) return null;
		$city = Db::name('region')->cache(864000)->where('region_type',2)->where('region_pid',$pid)->select();
		$rs   = array();
		foreach($city as $c){
			$arr[$c['region_id']] = [
				'cityid' 	=>$c['region_id'],
				'cityname'  =>$c['region_name'],
				'zipcode'    =>$c['region_adcode'],
				'provinceid'  =>$pid,
			];
		}
		return json($arr);
	}
	/*
	 * ajax获取地区
	 * 2018年10月12日11:25:30
	 * 刘媛媛
	 */
	public function ajax_district(){
		$pid = input('get.id');
		if($pid == 0) return null;
		$city = Db::name('region')->cache(864000)->where('region_type',3)->where('region_pid',$pid)->select();
		$rs   = array();
		foreach($city as $c){
			$arr[$c['region_id']] = [
				'districtid' 	=>$c['region_id'],
				'districtname'  =>$c['region_name'],
				'zipcode'    =>$c['region_adcode'],
				'cityid'  =>$pid,
			];
		}
		return json($arr);
	}
	
	public function province($pid = 0) {
		
		
		if($pid == 0) {
			$list = Db::name('region')->cache(864000)->where('region_type',1)->select();
		} else {
			$list =  Db::name('region')->where('region_type',1)->where('region_id',$pid)->select();
		}
		
		$arr  = array();
		foreach ($list as $k=>$v){
			$arr[$v['region_id']] = [
				'provinceid' 	=>$v['region_id'],
				'provincename'  =>$v['region_name'],
			];
		}
		return $arr;
	}
	/*
	 * 获取现在已有银行
	 * 2018年11月16日19:14:12
	 * 刘媛媛
	 */
	public function getbank(){
		$list = Db::name('bankList')->order('list_id desc')->select();
		return json(['error'=>0,'msg'=>'请求成功','data'=>$list]);
	}
	
}
