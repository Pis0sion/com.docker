<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;
/**
 * 收款
 */
class Payrecords extends Base{
	public function __construct(){
       parent::__construct();
    }
    public function index(){
    	
        $keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
      	$state    = request()->param('state');
      
        if(isset($keywords) && !empty($keywords)){
            $whereor[] = array('user_name','like','%'.$keywords."%");
            $whereor[] = array('user_phone','like','%'.$keywords."%");
            $whereor[] = array('user_account','like','%'.$keywords."%");
            $whereor[] = array('records_form_no','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
      	if(isset($state) && !empty($state)){
            //$where[] = array('records_state', $state);
          	$where[] = array('records_state','=', $state);
            $getdata['state'] = $state;
        }
		      
        $list = Db::name('pay_records')->alias('pr')
            ->field('pr.*,u.*,bank.list_id as back_id,bank.list_name as back_name,pc.channel_name,p.payment_id,p.payment_name,uc.card_id')
            ->where($where)->whereor($whereor)
            ->join('user u','u.user_id = pr.records_uid','LEFT')
            ->join('user_card uc','uc.card_id = pr.records_pay_cid','LEFT')
            ->join('bank_list bank','bank.list_id = uc.card_bank_id','LEFT')
            ->join('payment p','p.payment_id=pr.records_pay_id','LEFT')
            ->join('payment_channel pc','pc.channel_id=p.payment_channel_id','LEFT')
            ->order('records_id desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('getdata',$getdata);


        $count['count'] = Db::name('pay_records')->where('records_state',1)->cache('3600')->sum('records_money');
        $count['count_fee'] = Db::name('pay_records')->where('records_state',1)->cache('3601')->sum('records_fee');
        $count['today'] = Db::name('pay_records')->where('records_state',1)->whereTime('records_pay_time', 'today')->sum('records_money');
        $count['today_fee'] = Db::name('pay_records')->where('records_state',1)->whereTime('records_pay_time', 'today')->sum('records_fee');
        $this->assign('count',$count);
        return $this->fetch();
    }

    /**
     * 订单状态
     * @Author   tw
     * @DateTime 2018-09-03
     * @return   [type]     [description]
     */
    public function state()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法操作']);
        }
        $id = $post['id'];
        $pay_records = Db::name('pay_records')->where(['records_id'=>$id])->find();
        if(empty($pay_records))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }

        $payment_controller = Db::name('payment')->where('payment_id',$pay_records['records_pay_id'])->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        // $result = Controller('pay/'.$payment_controller)->state_query($id,$pay_records['records_uid'],$pay_records['records_pay_id'],$pay_records);
        $result = Controller('pay/'.$payment_controller)->query_order_status($pay_records);
        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'查询错误']);
        }

        return json($result);
    }
    /**
     * 收款补单
     * @Author tw
     * @return [type] [description]
     */
    public function bd()
    {
        $get = input('get.');
        if(empty($get))
        {
            return json(['error'=>1,'msg'=>'非法操作']);
        }
        $records_id = $get['records_id'];
        $form_no = $get['form_no'];
        $pay_records = Db::name('pay_records')->where(['records_id'=>$records_id])->where(['records_form_no'=>$form_no])->find();
        if(empty($pay_records))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }

        $payment_controller = Db::name('payment')->where('payment_id',$pay_records['records_pay_id'])->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller('pay/'.$payment_controller)->order_db($pay_records);
        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'查询错误']);
        }

        return json($result);
    }
    /**
     * 商户余额
     * @Author tw
     * @return [type] [description]
     */
    public function balance_query()
    {
        $get = input('get.');
        if(empty($get))
        {
            return json(['error'=>1,'msg'=>'非法操作']);
        }
        $form_no = $get['form_no'];
        $pay_records = Db::name('pay_records')->where(['records_form_no'=>$form_no])->find();
        if(empty($pay_records))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }

        $payment_controller = Db::name('payment')->where('payment_id',$pay_records['records_pay_id'])->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller('pay/'.$payment_controller)->balance_query($pay_records);
        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'查询错误']);
        }

        return json($result);
    }
}

