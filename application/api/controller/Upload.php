<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;

class Upload extends Base{

    public function ajax_upload(){
        $file = $this->request->file('file');
        
        if(!empty($file)){
            $path='uploads/images/'.date("Ymd",time()).'/'; //按日期上传路径
            $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,gif'])->rule('uniqid')->move($path);
            $error = $file->getError();
            //验证文件后缀后大小
            if(!empty($error)){
                dump($error);exit;
            }
            if($info){
                $info->getExtension();
                $info->getSaveName();
                $photo = $info->getFilename();
                $img_path = '/'.$path.$photo;

            }else{
                // 上传失败获取错误信息
                $file->getError();
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

    /**
     * base64上传图片
     * @Author tw
     * @Date   2018-09-19
     * @return [type]     [description]
     */
    public function ajax_upload_img()
    {
        // header('Content-type:text/html;charset=utf-8');
        // file_put_contents('log/'.date("Ymd",time()).'.log', var_export($_POST, true), FILE_APPEND);
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid  = $this->uid;
        $img  = $post['img'];
        if(empty($img))
        {
            return json(['error'=>1,'msg'=>'请传图片']);
        }
        // $image = base64_decode($img);
        $dir = 'uploads/images/'.date("Ymd",time()).'/';
        if(!is_dir($dir)){
             mkdir($dir, 0777, true);
        } 
        

        $base64_image_content =$img;
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $new_file = $dir;
            $new_file = $new_file.time().".png";
            if (!file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                return json(['error'=>1,'msg'=>'上传失败']);
            }
        }
        return json(['error'=>0,'msg'=>'成功','path'=>'/'.$new_file]);
    }
    /**
     * base64上传图片
     * @Author tw
     * @Date   2018-09-19
     * @return [type]     [description]
     */
    public function ajax_upload_base()
    {
        // header('Content-type:text/html;charset=utf-8');
        // file_put_contents('log/'.date("Ymd",time()).'.log', var_export($_POST, true), FILE_APPEND);
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid  = $this->uid;
        $img  = $post['img'];
        $type1 = $post['type'];
        if(empty($img))
        {
            return json(['error'=>1,'msg'=>'请传图片']);
        }
        // $image = base64_decode($img);
        $dir = 'uploads/images/'.date("Ymd",time()).'/';
        if(!is_dir($dir)){
             mkdir($dir, 0777, true);
        } 
        

        $base64_image_content =$img;
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];
            $new_file = $dir;
            $new_file = $new_file.time().".png";
            if (!file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                return json(['error'=>1,'msg'=>'上传失败']);
            }
        }
        if($type1!=''){
            $result = $this->IdcardOcr(file_get_contents($new_file),$type1);
            $res    = json_decode($result,true);
            if($res['error']==1){
                return $result;
            }
        }
        return json(['error'=>0,'msg'=>'成功','path'=>'/'.$new_file]);
    }
/**
 * 身份证验证
 * @param [type] $img_path [description]
 * @param [type] $type     [description]
 */
    private function IdcardOcr($img_path,$type){
        $result = IdOcr($img_path,$type);

        if($result['image_status']=='normal')
        {
            if($type==1)
            {
                $list = Db::name('User')->where('user_id',$this->uid)->find();
                if(empty($list))
                {
                    return json_encode(['error'=>1,'msg'=>'会员不存在']);
                }
                //验证号码
                if($result['words_result']['姓名']['words']==$list['user_name']&&strtoupper($result['words_result']['公民身份号码']['words'])!=strtoupper($list['user_idcard']))
                {
                    return json_encode(['error'=>1,'msg'=>'身份证号码不一致']);
                }
                //验证姓名
                if($result['words_result']['姓名']['words']!=$list['user_name']&&strtoupper($result['words_result']['公民身份号码']['words'])==strtoupper($list['user_idcard']))
                {
                    return json_encode(['error'=>1,'msg'=>'身份证姓名不一致']);
                }
                //验证两者
                if($result['words_result']['姓名']['words']!=$list['user_name']&&strtoupper($result['words_result']['公民身份号码']['words'])!=strtoupper($list['user_idcard']))
                {
                    return json_encode(['error'=>1,'msg'=>'身份信息不一致']);
                }

                if($result['words_result']['姓名']['words']==$list['user_name']&&strtoupper($result['words_result']['公民身份号码']['words'])==strtoupper($list['user_idcard']))
                {
                    return json_encode(['error'=>0,'msg'=>'验证成功']);
                }
            }else{
                if($result['words_result']['失效日期']['words'] < date('Ymd',time()))
                {
                    return json_encode(['error'=>1,'msg'=>'身份证反面不正确']);
                }else{
                    return json_encode(['error'=>0,'msg'=>'验证成功']);
                }
            }

        }else{
            return json_encode(['error'=>1,'msg'=>'身份证识别失败']);
        } 
    }
/**
 * [将Base64图片转换为本地图片并保存]
 * @E-mial wuliqiang_aa@163.com
 * @TIME   2017-04-07
 * @WEB    http://blog.iinu.com.cn
 * @param  [Base64] $base64_image_content [要保存的Base64]
 * @param  [目录] $path [要保存的路径]
 */
    private function Base64($base64Str,$new_file){
        header('Content-type:text/html;charset=utf-8');

        $base64_image_content =$base64Str;
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $type = $result[2];

            $new_file = $new_file.time().".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                return true;
            }else{
                return false;
            }
        }
    }

}
