<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;

class User extends Controller
{
    public function rate()
    {
    	if($this->request->isPost()) { 
	    	$post = input('post.');
	    	$uid = $post['uid'];
	    	$user = Db::name('user')->where(['user_id'=>$uid])->find();
	    	if(empty($user))
	    	{
	    		return json(['error'=>1,'msg'=>'会员不存在']);
	    	}
	    	$rate = Db::name('rate')->where(['rate_type_id'=>$user['user_type_id']])->order('rate_type asc')->select();
	    	if(empty($rate))
	    	{
	    		return json(['error'=>1,'msg'=>'费率模板不存在']);
	    	}
	    	foreach ($rate as $key => $value) {
	    		$user_rate = Db::name('user_rate')->where(['rate_uid'=>$uid,'rate_type'=>$value['rate_type']])->find();

				$data['rate_rate'] = $value['rate_rate'];
				$data['rate_close_rate'] = $value['rate_close_rate'];
				$data['rate_time'] = time();
	    		if($user_rate)
	    		{
	    			Db::name('user_rate')->where(['rate_id'=>$user_rate['rate_id']])->update($data);
	    			continue;
	    		}
				$data['rate_uid'] = $uid;
				$data['rate_type'] = $value['rate_type'];
				Db::name('user_rate')->insert($data);

	    	}
	    	return json(['error'=>0,'msg'=>'成功']);
	    }else{
			return json(['error'=>1,'msg'=>'非法访问']);
		}
    }
}
