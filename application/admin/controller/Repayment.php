<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;
/**
 * 还款
 */
class Repayment extends Base{
	public function __construct(){
       parent::__construct();
    }
    public function index(){
        $state = request()->param('state');
        $type = request()->param('type');
        $keywords = request()->param('keywords');
        $form_no = request()->param('form_no');
        $getdata = $where = $whereor=array();
        //状态
        if(isset($state) && $state!='')
        {
            $where[] = array('mission_state','eq',$state);
            $getdata['state'] =$state;
        }
        if(isset($type) && $type!='')
        {
            $where[] = array('mission_type','eq',$type);
            $getdata['type'] =$type;
        }
        if($form_no)
        {
            $mission_id = Db::name('plan')->where(['plan_form_no'=>$form_no])->value('plan_mid');
            if(empty($mission_id))
            {
                $mission_id = 0;
            }
            $where[] = array('mission_id','eq',$mission_id);
            $getdata['form_no'] =$form_no;
        }
        if(isset($keywords) && !empty($keywords)){
            //$where[]   = array('user_name','=',$keywords);
            $where[] = array('user_name|user_phone|user_idcard|user_code|user_account|user_nickname|mission_form_no','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('mission')->alias('m')
            // ->field('m.*,u.*,bank.list_name as bank_name')
            ->where('mission_del',0)
            ->where($where)->whereor($whereor)
            ->join('user u','u.user_id = m.mission_uid','LEFT')
            ->join('user_card uc','uc.card_id=m.mission_cid','LEFT')
            ->join('bank_list bank','bank.list_id=uc.card_bank_id','LEFT')
            ->join('payment p','p.payment_id=m.mission_pay_id','LEFT')
            ->join('payment_channel pc','pc.channel_id=p.payment_channel_id','LEFT')
            ->order('mission_id desc')
            ->paginate(14,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('getdata',$getdata);


        $count['count1'] = Db::name('plan')->where('plan_type',1)->where('plan_state',1)->cache('3600')->sum('plan_money');
        $count['count2'] = Db::name('plan')->where('plan_type',2)->where('plan_state',1)->cache('3600')->sum('plan_money');
        $count['count_fee'] = Db::name('plan')->where('plan_state',1)->cache('3600')->sum('plan_fee');
        $count['today1'] = Db::name('plan')->where('plan_type',1)->where('plan_state',1)->whereTime('plan_time', 'today')->sum('plan_money');
        $count['today2'] = Db::name('plan')->where('plan_type',2)->where('plan_state',1)->whereTime('plan_time', 'today')->sum('plan_money');
        $count['today_fee'] = Db::name('plan')->where('plan_state',1)->whereTime('plan_time', 'today')->sum('plan_fee');
        $this->assign('count',$count);
        return $this->fetch();
    }
    /**
     * 计划明细
     * @Author   tw
     * @DateTime 2018-08-30
     * @return   [type]     [description]
     */
    public function detail()
    {
        $get = input('param.',0);
        $getdata = $where = array();
        $getdata['id'] = $get['id'];
        //搜索条件
        if(isset($get['id'])){
            $where[] = array('plan_mid','eq',$get['id']);
        }
        //搜索条件
        if(isset($get['state'])){
            $where[] = array('plan_state','eq',$get['state']);
        }
        //搜索条件
        if(isset($get['keywords'])){
            $where[] = array('plan_form_no','like','%'.trim($get['keywords'])."%");
            $getdata['keywords'] = $get['keywords'];
        }
        $list = Db::name('plan')->alias('p')
                // ->field('p.*,payment_controller,payment_config')
                ->join('mission m','m.mission_id=p.plan_mid','LEFT')
                // ->join('payment pay','pay.payment_id=m.mission_pay_id','LEFT')
                ->where($where)
                ->order('plan_sort asc ,plan_type desc,plan_id asc ,plan_pay_time asc')
                ->paginate('50',false,['query'=> $getdata]);
                // dump($list);
        if($list->toArray())
        {
            //0未启动 1还款中 2已还完 3还款失败
            foreach($list as $k=>$v){
                $data = array();
                $data = $v;
                if($v['plan_type']==1)
                {
                    $type='还款';
                    switch ($v['plan_state']) {
                        case '0':
                            $status='未还款';
                            break;
                        case '1':
                            $status='已还款';
                            break;
                        case '2':
                            $status='还款失败 - '.$v['plan_msg'];
                            break;
                        case '3':
                            $status='还款处理中';
                            break;
                        case '4':
                            $status='已退款';
                            break;
                        
                        default:
                            $status='未知状态';
                            break;
                    }
                }
                else if($v['plan_type']==2)
                {
                    $type='消费';
                    switch ($v['plan_state']) {
                        case '0':
                            $status='未扣款';
                            break;
                        case '1':
                            $status='已扣款';
                            break;
                        case '2':
                            $status='扣款失败 - '.$v['plan_msg'];
                            break;
                        case '3':
                            $status='扣款处理中';
                            break;
                        case '4':
                            $status='已退款';
                            break;
                        
                        default:
                            $status='未知状态';
                            break;
                    }
                }
               
                $data['status'] = $status;
                $data['type_name'] = $type;
                $data['plan_money'] = (string)($v['plan_money']+$v['plan_fee']);
                $list->offsetSet($k,$data);
            } 
        }
        $this->assign('list',$list);
        $this->assign($getdata);
        return $this->view->fetch();
    }
    /**
     * 订单查询
     * @Author tw
     * @return [type] [description]
     */
    public function state()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法操作']);
        }
        $form_no = $post['id'];
        $plan = Db::name('plan')->where(['plan_form_no'=>$form_no])->find();
        if(empty($plan))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        elseif($plan['plan_state']==0)
        {
            return json(['error'=>1,'msg'=>'请更改订单状态后查询']);
        }
        elseif($plan['plan_state']==4)
        {
            return json(['error'=>1,'msg'=>'订单已退款']);
        }
        $pay_id = $plan['plan_pay_id'];
        if(empty($pay_id))
        {
            $pay_id = Db::name('mission')->where('mission_id',$plan['plan_mid'])->value('mission_pay_id');
            Db::name('plan')->where('plan_id',$plan_id)->update(['plan_pay_id'=>$pay_id]);
            $plan['plan_pay_id'] = $pay_id;
        }
        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        
        $result = Controller('pay/'.$payment_controller)->query_order_status($plan);

        // if($plan['plan_type']==1)
        // {
        //     $result = Controller('pay/'.$payment_controller)->pay_state_df($plan);
        // }
        // elseif($plan['plan_type']==2)
        // {
        //     $result = Controller('pay/'.$payment_controller)->pay_state_kj($plan);
        // }

        //$result = Controller('pay/'.$payment_controller)->state_query($plan);
        return json($result);
    }
    /**
     * 关闭计划
     * @Author   tw
     * @DateTime 2018-08-30
     * @return   [type]     [description]
     */
    public function close()
    {
        $id = input('get.id',0);
        $type = input('get.type',0);
        if(empty($id))
        {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }
        $mission = Db::name('mission')->where(['mission_id'=>$id])->find();
        if(empty($mission))
        {
            return json(['error'=>1,'msg'=>'没有计划']);
        }
        if($type==1)
        {
            //关闭计划
            $up = Db::name('mission')->where(['mission_id'=>$id])->update(['mission_state'=>6]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            //更改信用卡还款状态
            Db::name('user_card')->where(['card_id'=>$mission['mission_cid']])->update(['card_state'=>0]);
            return json(['error'=>0,'msg'=>'成功']);
        }
        elseif($type==0)
        {
            if(Db::name('mission')->where(['mission_cid'=>$mission['mission_cid'],'mission_state'=>1])->find())
            {
                return json(['error'=>1,'msg'=>'操作失败,该卡有计划在执行']);
            }
            //启用计划
            $up = Db::name('mission')->where(['mission_id'=>$id])->update(['mission_state'=>1]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            //更改信用卡还款状态
            Db::name('user_card')->where(['card_id'=>$mission['mission_cid']])->update(['card_state'=>1]);
            return json(['error'=>0,'msg'=>'成功']);
        }
    }
    /**
     * 删除计划
     * @Author tw
     * @Date   2018-10-20
     * @return [type]     [description]
     */
    public function del()
    {
        $id = input('get.id',0);
        if(empty($id))
        {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }
        $mission = Db::name('mission')->where('mission_id',$id)->where('mission_del',0)->find();
        if(empty($mission))
        {
            return json(['error'=>1,'msg'=>'计划不存在']);
        }
        elseif($mission['mission_del']=='1')
        {
            return json(['error'=>0,'msg'=>'计划已成功删除']);
        }
        if($mission['mission_close']==1)
        {
            $up = Db::name('mission')->where('mission_id',$id)->where('mission_del',0)->update(['mission_state'=>3,'mission_del'=>1]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败请重试']);
            }
            return json(['error'=>0,'msg'=>'计划已成功删除']);
        }
        else
        {
            $up = Db::name('mission')->where('mission_id',$id)->where('mission_del',0)->update(['mission_close'=>3]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败请重试']);
            }
            return json(['error'=>0,'msg'=>'提交删除计划申请,本次还款成功后将删除该计划']);
        }
    }
    /**
     * 更改支付状态
     * @Author tw
     * @Date   2018-10-22
     * @return [type]     [description]
     */
    public function pay_state()
    {
        $id = input('param.id',0);
        $type = input('param.type',0);
        if(empty($id))
        {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }
        if(!in_array($type, [1,2,3,4]))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $plan = Db::name('plan')->where(['plan_id'=>$id])->find();
        if(empty($plan))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        //更改支付状态
        $up = Db::name('plan')->where(['plan_id'=>$id])->update(['plan_state'=>$type]);
        if(empty($up))
        {
            return json(['error'=>1,'msg'=>'操作失败,请重试']);
        }
        return json(['error'=>0,'msg'=>'成功']);
    }
    /**
     * 新增计划
     * @Author   tw
     * @DateTime 2018-08-31
     */
    public function add()
    {
        if($_POST)
        {
            /*$data['pay_id'] = '1';
            $data['type'] = '2';
            $data['money'] = '50000';
            $data['start_time'] = '2018-08-31';
            $data['end_time'] = '2018-10-08';
            $data['repay_num'] = '1';
            $data['flag'] = '1';
            $data['region_id'] = '1';
            $data['mcc'] = '1';*/
            $data = $_POST;
            echo api_post('Api/repayment/add',$data);
            return;
        }
        $data['uid']=input('param.uid',0);
        $data['cid']=input('param.cid',0);
        $this->assign('data',$data);
        return $this->fetch();
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
        $plan_id = $get['id'];
        $plan = Db::name('plan')->where(['plan_id'=>$plan_id])->find();
        if(empty($plan))
        {
            return json(['error'=>1,'msg'=>'订单不存在']);
        }

        $payment_controller = Db::name('payment')->where('payment_id',$plan['plan_pay_id'])->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller('pay/'.$payment_controller)->balance_query($plan);
        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'查询错误']);
        }

        return json($result);
    }
    /**
     * 更改计划当前执行状态
     * @Author tw
     * @Date   2018-10-22
     * @return [type]     [description]
     */
    public function current_state()
    {
        $id = input('get.id',0);
        $type = input('get.type',0);
        if(empty($id))
        {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }
        $mission = Db::name('mission')->where(['mission_id'=>$id])->find();
        if(empty($mission))
        {
            return json(['error'=>1,'msg'=>'没有计划']);
        }
        if($type==1 || $type==2)
        {
            //关闭计划
            $up = Db::name('mission')->where(['mission_id'=>$id])->update(['mission_current_state'=>$type]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            return json(['error'=>0,'msg'=>'成功']);
        }
    }

    /**
     * 修改
     * @Author   tw
     * @DateTime 2018-08-30
     * @return   [type]     [description]
     */
    public function edit()
    {
        $id = input('param.id',0);
        $plan = Db::name('plan')->where('plan_id',$id)->find();
        if($this->request->isPost()) {
            if(!$id){
                return json(['error'=>1,'msg'=>'计划id错误']);
            }

            if(!$plan){
                return json(['error'=>1,'msg'=>'计划不存在']);
            }
            $post= input('post.');
            $data['plan_form_no']  = $post['form_no'];
            $data['plan_money'] = $post['money'];
            $data['plan_fee'] = $post['fee'];
            $data['plan_msg'] = $post['msg'];
            $data['plan_state'] = $post['state'];
            $data['plan_pay_time'] = $post['pay_time'];
            Db::name('plan')->where(array('plan_id'=>$id))->update($data);
            return json(['error'=>0,'msg'=>'修改成功']);   
        }
        $this->assign($plan);
        return $this->view->fetch();
    }

    /**
     * 更改计划当前执行状态
     * @Author   tw
     * @DateTime 2018-08-30
     * @return   [type]     [description]
     */
    public function type()
    {
        $id = input('get.id',0);
        $type = input('get.type',0);
        if(empty($id))
        {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }
        $mission = Db::name('mission')->where(['mission_id'=>$id])->find();
        if(empty($mission))
        {
            return json(['error'=>1,'msg'=>'没有计划']);
        }
        if($type==99)
        {
            $up = Db::name('mission')->where(['mission_id'=>$id])->update(['mission_type'=>0]);
            return json(['error'=>0,'msg'=>'成功']);
        }
        else
        {
            return json(['error'=>0,'msg'=>'失败']);
        }
    }
}
