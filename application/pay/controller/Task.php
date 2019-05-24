<?php
namespace app\pay\controller;
use think\Controller;
use think\Db;

class Task extends Base
{
    public function index(){
        $get = input('param.');
        // if($get['t']!=1)
        // {

        //     echo "暂停";
        //     exit();
        // }
        $hour = date('H:i');
        if($hour < '09:00' || $hour > '21:00'){
            echo "该时间段不允许计划";exit;
        }
        $where['mission_state'] = 1;
        $where['mission_type'] = 0;
        $where['mission_queues'] = 0;
        if($get['id'])
        {
            $where['mission_id'] = $get['id'];
        }

        $missionlist = Db::name('mission')->where($where)->whereTime('mission_pay_time','<',date('Y-m-d H:i:s',time()))->select();
        if (empty($missionlist)) {
            echo "没有可以执行的计划";
            exit();
        }
        unset($where);
        foreach ($missionlist as $key => $v) {

            $plan = Db::name('plan')
                ->where('plan_mid',$v['mission_id'])
                ->where('plan_state',0)
                ->where('plan_type',1)
                ->order('plan_sort asc ,plan_pay_time asc ,plan_id')
                ->find();

            if(empty($plan))
            {
                echo '计划不存在 ['.$v['mission_id'].']<br/>';
                break;
            }

            $card = Db::name('user_card')->where('card_id',$v['mission_cid'])->where('card_type',1)->find();
            if(empty($card))
            {
                echo '计划 ['.$v['mission_id'].'] 银行卡不存在<br/>';
                break;
            }

            $payment = Db::name('payment')->where('payment_id',$v['mission_pay_id'])->find();
            if(empty($payment))
            {
                echo '计划 ['.$v['mission_id'].'] 通道不存在<br/>';
                break;
            }
            $planlist = Db::name('plan')->where('plan_sort',$plan['plan_sort'])->where('plan_type',2)->where('plan_mid',$mission['mission_id'])->where('plan_state',0)->find();

            $res = $this->pay($v,$plan,$planlist,$payment,$card);

            dump($res);
            // echo 1;

        }
    }

    public function test(){
        $get = input('param.');
        $where['mission_id'] = $get['id'];

        $where['mission_state'] = 1;
        $where['mission_type'] = 0;
        $missionlist = Db::name('mission')->where($where)
                                        // ->whereTime('mission_pay_time','<',date('Y-m-d H:i:s',time()))
                                        ->select();
        if (empty($missionlist)) {
            echo "没有可以执行的计划";
            exit();
        }
        unset($where);
        foreach ($missionlist as $key => $v) {

            $plan = Db::name('plan')
                ->where('plan_mid',$v['mission_id'])
                ->where('plan_state',0)
                ->where('plan_type',1)
                ->order('plan_pay_time asc')
                ->find();
            if(empty($plan))
            {
                echo '计划不存在 ['.$v['mission_id'].']<br/>';
                break;
            }

            $card = Db::name('user_card')->where('card_id',$v['mission_cid'])->where('card_type',1)->find();
            if(empty($card))
            {
                echo '计划 ['.$v['mission_id'].'] 银行卡不存在<br/>';
                break;
            }

            $payment = Db::name('payment')->where('payment_id',$v['mission_pay_id'])->find();
            if(empty($payment))
            {
                echo '计划 ['.$v['mission_id'].'] 通道不存在<br/>';
                break;
            }
            $planlist = Db::name('plan')->where('plan_sort',$plan['plan_sort'])->where('plan_type',2)->where('plan_mid',$mission['mission_id'])->where('plan_state',0)->find();

            $res = $this->pay($v,$plan,$planlist,$payment,$card,$plan['plan_id']);
            dump($res);
            // echo 1;

        }
    }
    public function pay($mission='',$plan='',$planlist='',$payment='',$card='')
    {
        $mission = Db::name('mission')->where('mission_id',$plan['plan_mid'])->find();
        $planlist = Db::name('plan')->where('plan_sort',$plan['plan_sort'])->where('plan_type',2)->where('plan_mid',$mission['mission_id'])->whereIn('plan_state','0,2')->find();
        if($mission['mission_current_state']==1)
        {
            //执行代还
            if($planlist && $payment['payment_orders']==0)
            {
                Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->update(['mission_current_state'=>2]);
                return ['error'=>1,'msg'=>'有未支付的订单,已更改状态等待支付'];
            }
            // $plan = Db::name('plan')->where('plan_id','in',$plan['plan_id'])->where('plan_state',0)->find();
            $plan = Db::name('plan')->where('plan_sort',$plan['plan_sort'])->where('plan_type',1)->where('plan_mid',$mission['mission_id'])->where('plan_state',0)->find();
            if(empty($plan))
            {
                // Db::name('mission')->where(['mission_id'=>$plan['plan_mid']])->update(['mission_current_state'=>2]);
                return ['error'=>1,'msg'=>'订单已支付'];
            }
            return Controller($payment['payment_controller'])->pay_df($mission,$plan);
        }
        elseif($mission['mission_current_state']==2)
        {
            //执行支付
            if(empty($planlist))
            {
                return ['error'=>1,'msg'=>'没有可支付的订单'];
            }
            return Controller($payment['payment_controller'])->pay_kj($mission,$planlist);
        }
        else
        {
            return ['error'=>1,'msg'=>'错误'];
        }
    }
    /**
     * 还款补单
     * @Author tw
     * @return [type] [description]
     */
    public function repayment_bd()
    {
        $plan_id = input('get.id');
        $form_no = input('get.form_no');
        if(empty($plan_id))
        {
            return json(['error'=>1,'msg'=>'订单错误']);
        }

        $plan = Db::name('plan')->where('plan_id',$plan_id)->where('plan_form_no',$form_no)->where('plan_state',2)->find();

        if(empty($plan))
        {
            return json(['error'=>1,'msg'=>'该订单无需补单']);
        }
        $mission = Db::name('mission')->where('mission_id',$plan['plan_mid'])->find();
        $payment = Db::name('payment')->where('payment_id',$mission['mission_pay_id'])->find();

        $order_status = Controller($payment['payment_controller'])->query_order_status($plan);
        if($order_status['error']==0)
        {
            return json($order_status);
        }

        if($payment['payment_orders']=='1')
        {
            if($plan['plan_type']==1)
            {
                return json(['error'=>1,'msg'=>'还款不支持补单']);
            }
            $planlist = Db::name('plan')->where('plan_mid',$plan['plan_mid'])->whereIn('plan_state',[0,2])->where('plan_sort',$plan['plan_sort'])->select();
            foreach ($planlist as $key => $value) {
                Db::name('plan')->where('plan_id',$value['plan_id'])->update(['plan_state'=>0,'plan_form_no'=>$value['plan_form_no'].'BD']);
            }
            $plan['plan_form_no'] = $value['plan_form_no'].'BD';
        }
        else
        {
            Db::name('plan')->where('plan_id',$plan_id)->update(['plan_state'=>0,'plan_form_no'=>$form_no.'BD']);
        }
        Db::name('mission')->where('mission_id',$plan['plan_mid'])->update(['mission_type'=>0,'mission_queues'=>0]);

        return json(['error'=>0,'msg'=>'成功,等待支付!']);
        $res = $this->pay($mission,$plan,'',$payment,'');
        if($res['error']==0)
        {
            return json(['error'=>0,'msg'=>'成功']);
        }
        return json($res);
    }
    /**
     * 查询待处理订单
     * @Author tw
     * @Date   2018-10-23
     * @return [type]     [description]
     */
    public function processed()
    {
        $get = input('param.');
        /*if($get['admin']!='kk')
        {
            $hour = date('H:i');
            if($hour < '09:00' || $hour > '22:00'){
                echo "该时间段不允许处理订单";exit;
            }

        }*/
        $where['plan_state'] = 3;
        if($get['id'])
        {
            $where['plan_id'] = $get['id'];
        }

        $plan = Db::name('plan')
                ->where($where)
                ->order('plan_time asc,plan_type desc')
                // ->limit(1)
                ->select();
        if(empty($plan))
        {
            echo "没有待处理订单";
            exit();
        }
        foreach ($plan as $k => $v) {
            $result = array();
            if(empty($v['plan_pay_id']))
            {
                $mission = Db::name('mission')->where('mission_id',$v['plan_mid'])->find();
                $v['plan_pay_id'] = $mission['mission_pay_id'];
                Db::name('plan')->where('plan_id',$v['plan_id'])->update(['plan_pay_id'=>$v['plan_pay_id']]);

            }
            $payment = Db::name('payment')->where('payment_id',$v['plan_pay_id'])->find();

            if(empty($payment))
            {
                echo 'plan_id ['.$v['plan_id'].'] 通道不存在<br/>';
                break;
            }
            
            $result = Controller($payment['payment_controller'])->query_order_status($v);
            /*if($v['plan_type']==1)
            {
                $result = Controller($payment['payment_controller'])->pay_state_df($v);
            }
            elseif($v['plan_type']==2)
            {
                $result = Controller($payment['payment_controller'])->pay_state_kj($v);
            }*/
            dump($result);
        }
    }

    /**
     * 查询待处理订单
     * @Author tw
     * @Date   2018-10-23
     * @return [type]     [description]
     */
    public function processed_sk()
    {
        $get = input('param.');
        
        /*$hour = date('H:i');
        if($hour < '09:00' || $hour > '22:00'){
            echo "该时间段不允许处理订单";exit;
        }
*/
        if($get['id'])
        {
            $where['records_id'] = $get['id'];
        }
        $order = Db::name('pay_records')
                ->where($where)
                ->whereIn('records_state','3,4,5')
                // ->where('records_form_number','<>','')
                ->order('records_pay_time asc')
                // ->limit(1)
                ->select();
        if(empty($order))
        {
            echo "没有待处理订单";
            exit();
        }
        foreach ($order as $k => $v) {
            $payment = Db::name('payment')->where('payment_id',$v['records_pay_id'])->find();
            if(empty($payment))
            {

                $this->payment_kj($v['records_id'],$v['records_uid'],2,'','','通道不存在','','');
                echo 'records_id ['.$v['records_id'].'] 通道不存在-已终止订单<br/>';
                break;
            }
            $result = Controller($payment['payment_controller'])->query_order_status($v);

            /*if($v['records_form_number'])
            {
                $result = Controller($payment['payment_controller'])->pay_state_df($v);
            }
            else
            {
                $result = Controller($payment['payment_controller'])->pay_state_kj($v);
            }*/
            dump($result);
        }
    }
}