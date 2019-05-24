<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Paymentcard extends Base{
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

        $list = Db::name('payment_card')->alias('pc')
                ->field('pc.*,u.user_name,b.list_name')
                ->join('user u','u.user_id=pc.card_uid','LEFT')
                ->join('user_card uc','uc.card_id=pc.card_cid','LEFT')
                ->join('bank_list b','b.list_id=uc.card_bank_id','LEFT')
                ->where('card_pay_id',$pay_id)->paginate(20,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('getdata',$getdata);
        $this->assign('pay_id',$pay_id);
        return $this->fetch();
    }
    

    /**
     * 解绑卡片
     * @Author tw
     * @Date   2018-09-19
     * @return [type]     [description]
     */
    public function del()
    {
        $cid = input('get.cid',0);
        $uid = input('get.uid',0);
        $pay_id = input('get.pay_id',0);
        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller('pay/'.$payment_controller)->unbind_card($pay_id,$uid,$cid);
        return json($result);
    }
}
