<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Paymentuser extends Base{
	public function __construct(){
       parent::__construct();
    }
    /**
     * 列表
     * @Author   tw
     * @DateTime 2018-08-29
     * @return   [type]     [description]
     */
    public function index()
    {
        $keywords = trim(request()->param('keywords'));
        $getdata = $where = $whereor=array();
        $pay_id = input('param.pay_id',0);
        if(!$pay_id){
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        if($keywords)
        {
            $where['u.user_name|u.user_id'] = $keywords;
            $getdata['keywords'] = $keywords;
        }
        $list = Db::name('payment_user')->alias('pu')
                ->field('pu.*,u.user_id as uid,u.user_name')
                ->join('user u','u.user_id=pu.user_uid','LEFT')
                ->where($where)
                ->where('user_pay_id',$pay_id)->paginate(20,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('getdata',$getdata);
        $this->assign('pay_id',$pay_id);
        return $this->fetch();
    }
    /**
     * 修改费率
     * @Author tw
     * @return [type] [description]
     */
    public function edit()
    {
        $id = input('get.id',0);
        $pay_id = input('get.pay_id',0);
        $payment_user = Db::name('payment_user')->where(['user_id'=>$id])->where(['user_pay_id'=>$pay_id])->find();
        if(empty($payment_user))
        {
            return json(['error'=>1,'msg'=>'用户不存在']);
        }

        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller('pay/'.$payment_controller)->update_fee($payment_user['user_pay_id'],$payment_user['user_uid']);
        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'查询错误']);
        }

        return json($result);
    }
    /**
     * 解绑卡片
     * @Author tw
     * @Date   2018-09-19
     * @return [type]     [description]
     */
    public function del()
    {
       /* $cid = input('get.id',0);
        $uid = input('get.uid',0);
        $pay_id = input('get.pay_id',0);
        if (empty(Db::name('payment_card')->where('card_id',$cid)->where('card_state',1)->find())) {
            return json(['error'=>0,'msg'=>'解绑成功']);
        }
        $data['token'] = gettoken($uid);
        $data['cid'] = $cid;
        $data['pay_id'] = $pay_id;
        $result = api_post('api/my/unbind_card',$data);
        $result = json_decode($result,true);
        return json($result);*/
    }
}
