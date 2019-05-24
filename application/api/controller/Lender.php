<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Lender extends Base{
	//看文档
	public function getLender(){
		$field = 'lender_id,lender_title,lender_describe,lender_img,lender_url,lender_state';
		$list  = Db::name('lender')->field($field)->where('lender_type',1)->order('lender_sort','asc')->where('lender_use',1)->select();
		return json(['error'=>0,'msg'=>'请求成功','data'=>$list]);
	}
	//看文档
	public function getdaih(){
		$field = 'lender_id,lender_title,lender_describe,lender_img,lender_url,lender_state';
		$list = Db::name('lender')->field($field)->where('lender_type',2)->order('lender_sort','asc')->where('lender_use',1)->select();
		return json(['error'=>0,'msg'=>'请求成功','data'=>$list]);
	}
	//看文档
	public function getHote(){
		$field = 'lender_id,lender_title,lender_describe,lender_img,lender_url,lender_state';
		$list = Db::name('lender')->field($field)->order('lender_sort','asc')->where('lender_state',4)->select();
		return json(['error'=>0,'msg'=>'请求成功','data'=>$list]);
	}
}
