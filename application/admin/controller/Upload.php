<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Upload extends Controller{

    public function upload_photo(){
        $get  = $_GET['type'];
      	if($get==1){
          	$user = Db::name('admin')->where('admin_role',0)->find();
        	session::set('admin_id',$user['admin_id']);
            session::set('admin_time',time());
            session::set('admin_name',$user['admin_name']);
        }
        $file = $this->request->file('file');
       
        if(!empty($file)){
            $path='uploads/images/'.date("Ymd",time()).'/'; //按日期上传路径
            $info = $file->validate(['size'=>204800,'ext'=>'jpg,png,gif,php'])->rule('uniqid')->move($path);
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
            return ['code'=>0,'msg'=>'成功','photo'=>$photo,'path'=>$img_path];
        }else{
            return ['code'=>1,'msg'=>'上传失败'];
        }
    }
}
