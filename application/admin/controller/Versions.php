<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Versions extends Base{
	
	
	public function __construct(){
		parent::__construct();
		
	}
	
    public function index() {

		$list  = Db::name("appVersions")->order('app_id','desc')->paginate(10);
		$this->assign("list",$list);
		return $this->fetch();
	}
	
	public function addver(){
		
		if($this->request->isPost()) {
            $post = input('post.');
            if($post['app_verber']==''){
            	return json(['error'=>1,'msg'=>'请输入版本号']);
            }
            
            $ISIC = Db::name('appVersions')->where('app_verber',$post['app_verber'])->find();
            if($ISIC){
            	return json(['error'=>1,'msg'=>'版本已存在']);
            }
            if($post['apk_url']==''){
            	return json(['error'=>1,'msg'=>'请输入安卓地址']);
            }
            if($post['ipa_url']==''){
            	return json(['error'=>1,'msg'=>'请输入苹果地址']);
            }
         	$data = array();
         	$data['app_verber']		    =  $post['app_verber'];
         	$data['app_down_androlink'] =  $post['apk_url'];
         	$data['app_down_applelink'] =  $post['ipa_url'];
         	$data['app_androlink'] 		=  $post['app_androlink'];
         	$data['app_ioslink'] 		=  $post['app_ioslink'];
         	$data['app_time']			= time();
         	
         	Db::name('appVersions')->insert($data);
         	
         	return json(['error'=>0,'msg'=>'版本更新完成']);
        }
		
		
		
		return $this->fetch();
	}
	
	public function upload_apk() {
		
		$file = $this->request->file('file');
        
        if(!empty($file)){
            $path  = 'download/apk/'.date("Ymd",time()).'/'; //按日期上传路径
            $info  = $file->validate(['size'=>52428800,'ext'=>'apk'])->rule('uniqid')->move($path);
            $error = $file->getError();
            //验证文件后缀后大小
            if(!empty($error)){
                return ['code'=>1,'msg'=>$error];
            }
            if($info){
                $info->getExtension();
                $info->getSaveName();
                $photo = $info->getFilename();
                $img_path = '/'.$path.$photo;

            }else{
                // 上传失败获取错误信息
                return ['code'=>1,'msg'=>$file->getError()];
            }
        }else{
            $photo = '';
        }
        if($photo !== ''){
            return ['code'=>0,'msg'=>'成功','photo'=>$photo,'path'=>'http://'.$_SERVER['HTTP_HOST'].$img_path];
        }else{
            return ['code'=>1,'msg'=>'上传失败'];
        }
	}
	public function upload_ipa() {
		
		$file = $this->request->file('file');
        if(!empty($file)){
            $path  = 'download/ipa/'.date("Ymd",time()).'/'; //按日期上传路径
            $info  = $file->validate(['size'=>52428800,'ext'=>'ipa,deb,pxl'])->rule('uniqid')->move($path);
            $error = $file->getError();
            //验证文件后缀后大小
            if(!empty($error)){
                return ['code'=>1,'msg'=>$error];
            }
            if($info){
                $info->getExtension();
                $info->getSaveName();
                $photo = $info->getFilename();
                $img_path = '/'.$path.$photo;

            }else{
                // 上传失败获取错误信息
                return ['code'=>1,'msg'=>$file->getError()];
            }
        }else{
            $photo = '';
        }
        if($photo !== ''){
            return ['code'=>0,'msg'=>'成功','photo'=>$photo,'path'=>'http://'.$_SERVER['HTTP_HOST'].$img_path];
        }else{
            return ['code'=>1,'msg'=>'上传失败'];
        }
	}
	
	
}