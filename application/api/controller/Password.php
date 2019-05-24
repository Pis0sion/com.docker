<?php
namespace app\api\controller;
use think\Controller;
use think\Db;

class Password extends Base
{

    public function user_password()
    {
        if($this->request->isPost()) {
            $post = input('post.');
            $uid = $this->uid;//用户id
            $password = $post['password'];//旧密码
            $new_password = $post['new_password'];//新密码

            if(empty(Db::name('user')->where(['user_id'=>$uid,'user_password'=>md5($password)])->find()))
            {
                return json(['error'=>1,'msg'=>'旧密码错误']);
            }
            $up = Db::name('user')->where(['user_id'=>$uid])->update(['user_password'=>md5($new_password)]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'修改失败']);
            }
            return json(['error'=>0,'msg'=>'成功']);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
}
