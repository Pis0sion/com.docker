<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Img extends Controller{
	//优惠券功能
	public function index(){
		
		
		$id = input('get.type');
		
		$list = Db::name('img')->where('img_start_switch',1)->where('img_type',$id)->order('img_sort asc')->select();
		
		$news = array();
		foreach($list as $k=>$v){
			$news[]= [
				'img_id'=>$v['img_id'],
				'img_title'=>$v['img_title'],
				'img_type'=>$v['img_type'],
				'img_url'=>$v['img_url'],
				'img_img'=>'http://'.$_SERVER['HTTP_HOST'].$v['img_img'],
				'img_sort'=>$v['img_sort'],
				'img_time'=>$v['img_time'],
			];
		}
		return json(['error'=>0,'msg'=>'请求成功','data'=>$news]);
	}
}
