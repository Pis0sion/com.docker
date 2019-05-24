<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Index  extends Controller
{
    public function index()
    {
       $id = input('get.id');

       $this->assign('id', $id);
       return view('register');
    }
	
	public function download(){
		
		$banben = Db::name('appVersions')->order('app_time','desc')->find();
		
		$this->assign('data', $banben);
		 return $this->fetch();
	}
}
