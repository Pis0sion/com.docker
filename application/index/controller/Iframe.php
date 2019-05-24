<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Iframe  extends Controller
{
    public function article(){
    
    	$id = input('get.id');
		$data = Db::name('article')->where(['article_id'=>$id])->field('article_body')->find();
		if(!$data){
			die('数据不存在');
		}
    	$this->assign('data', $data['article_body']);
    	$this->assign('title', '新闻详情');
    	return $this->fetch('info');
    }
	
}
