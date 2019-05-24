<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;
use app\pay\controller\Weixinapp;

class Payrecords extends Base
{
    /**
     * 收款记录
     * @Author tw
     * @Date   2018-09-13
     */
    public function index()
    {
        if($this->request->isPost()) {
            $post = input('post.');
            $uid = $this->uid;//用户id
            $cid = $post['cid'];//信用卡id
            $state = isset($post['state'])?$post['state']:'';//收款状态
            $where['records_uid'] = $uid;
            if(!empty($state))
            {
                $where['records_state'] = $state;
            }
            
            $where['records_pay_cid'] = $cid;
            $list = Db::name('pay_records')->where($where)->order('records_id desc')->whereIn('records_state','1,2')->paginate(10,false);
            if(empty($list))
            {
                return json(['error'=>1,'msg'=>'没有记录']);
            }
            if($list->toArray())
            {
                //0未启动 1还款中 2已还完 3还款失败
                foreach($list as $k=>$v){
                    $data = array();
                    $data = $v;
                    switch ($v['records_state']) {
                        case '1':
                            $type_name = '支付成功';
                            break;
                        case '2':
                            $type_name = '支付失败';
                            break;
                        case '3':
                            $type_name = '支付中';
                            break;
                        case '4':
                            $type_name = '代付中';
                            break;
                        case '5':
                            $type_name = '代付失败';
                            break;
                        
                        default:
                            $type_name = '未提交支付';
                            break;
                    }
                    $data['type_name'] = $type_name;
                    $list->offsetSet($k,$data);
                } 
            }

            return json(['error'=>0,'msg'=>'成功','data'=>$list]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    /**
     * 收款记录详情
     * @Author tw
     * @Date   2018-09-13
     * @return [type]     [description]
     */
    public function detail()
    {
        if($this->request->isPost()) {
            $post = input('post.');
            $id = $post['id'];//记录id
            $uid = $this->uid;//用户id
            if(empty($id) || empty($id))
            {
                return json(['error'=>1,'msg'=>'参数错误']);
            }
            $where['records_id'] = $id;
            $where['records_uid'] = $uid;

            $list = Db::name('pay_records')->where($where)->find();
            if(empty($list))
            {
                return json(['error'=>1,'msg'=>'没有记录']);
            }

            return json(['error'=>0,'msg'=>'成功','data'=>$list]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    
    /*
     * 收银行卡信息
     * 2018年9月29日15:13:06
     * 刘媛媛
     */
    public function  bankinfo(){
        
        if($this->request->isPost()) {
            $post = input('post.');
            $where['card_uid']       = $this->uid;
            $where['card_blocked']   = 0;
            $where['card_type']      = 1;
            $where['card_id']        = intval($post['cid']);
            $field = 'card_id,card_uid,card_type,list_name,list_code,list_logo,card_no';
            $card = Db::name('user_card')->alias('c')
                    ->field($field)
                    ->join('bank_list b','b.list_id=c.card_bank_id','LEFT')
                    ->where($where)
                    ->find();
            if(!$card){
                return json(['error'=>1,'msg'=>'银行卡信息不存在']);
            }
            
            $card['list_logo'] = 'http://'.$_SERVER['HTTP_HOST'].$card['list_logo'];
            $card['card_no'] = substr($card['card_no'],-4);
                
            $dayMoney = Db::name('payRecords') ->whereTime('records_pay_time', 'd')->where('records_pay_cid',$post['cid'])->where('records_state',1)->sum('records_money');
            
            return json(['error'=>0,'msg'=>1,'data'=>$card,'dayMoney'=>$dayMoney]);
            
        }
    }
    /*
     * 获取收款通道
     * 2018年9月29日15:45:58
     * 刘媛媛
     */
    public function getpayment(){
        if($this->request->isPost()) {
            $post = input('post.');
            $type = isset($post['type'])?$post['type']:1;
            $bnakId = Db::name('userCard')->where('card_id',$post['cid'])->where('card_uid',$this->uid)->value('card_bank_id');
            if(!$bnakId){
                return json(['error'=>1,'msg'=>'银行卡不存在']);
            }
            $field = 'payment_id,payment_name,payment_bind,payment_bind_way,payment_day_num,payment_min_money,payment_max_money,paymentst_entime,paymentst_money,payment_region,payment_mcc';
            $list  = Db::name('payment')->where('payment_type',1)->field($field)->where('payment_use',1)->select();
            foreach($list as $k=>$v){
                //查询通道支持银行
                if(empty(Db::name('bank')->where('bank_bid',$bnakId)->where('bank_pay_id',$v['payment_id'])->find()))
                {
                    unset($list[$k]);
                    continue;
                }
                if($v['payment_bind']==0)
                {
                    $list[$k]['state'] = 1;//可用
                    $list[$k]['state_msg'] = '正常';
                }
                elseif($v['payment_bind']==1)
                {
                    $payment_card = Db::name('payment_card')
                                            ->where('card_pay_id','eq',$v['payment_id'])
                                            ->where('card_uid','eq',$this->uid)
                                            ->where('card_cid','eq',$post['cid'])
                                            ->where('card_type','eq','1')
                                            ->find();
                    if(empty($payment_card) || $payment_card['card_state']==2)
                    {
                        $list[$k]['state'] = 0;//需要绑卡
                        $list[$k]['state_msg'] = '需要绑卡';
                        if($v['payment_bind_way']=='web')
                        {
                            $list[$k]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/payrecords/bind_web/?pay_id='.$v['payment_id'].'&uid='.$this->uid.'&cid='.$bank['card_id'].'&token='.$this->token;
                        }
                        elseif($v['payment_bind_way']=='api')
                        {

                            $list[$k]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/api/payrecords/bind_api';
                        }
                        
                    }
                    elseif($payment_card['card_state']==3)
                    {
                        $list[$k]['state'] = 2;//绑卡中
                        $list[$k]['state_msg'] = '绑卡中';
                    }
                    else
                    {
                        $list[$k]['state'] = 1;//可用
                        $list[$k]['state_msg'] = '正常';
                    }
                }
                $list[$k]['mcc'] = Db::name('payment_mcc')->where('mcc_pay_id',$v['payment_id'])->field('mcc_mcc,mcc_title')->select();
            }
            $list = array_values($list);
            if(count($list)==0){
                return json(['error'=>1,'msg'=>'没有更多支持您银行卡的通道']);
            }
            
            $Rate = Db::name('UserRate')->where(['rate_uid'=>$this->uid])->where('rate_type',$type)->find();
            if(!$Rate){
                return json(['error'=>1,'msg'=>'获取费率失败']);
            }
            $Rate['rate_rate'] = $Rate['rate_rate']*100;
            return json(['error'=>0,'msg'=>'成功','data'=>$list,'rate'=>$Rate]);
        }
        
        
        
    }
    /**
     * 银行卡列表
     * @Author tw
     * @Date   2018-09-13
     */
    public function card()
    {
        if($this->request->isPost()) {
            $post = input('post.');
            $uid = $this->uid;//用户id
            $where['card_uid'] = $uid;
            $where['card_blocked'] = 0;
            $field = 'card_id,card_uid,card_type,list_name,list_code,list_logo,card_no';
            $card = Db::name('user_card')->alias('c')
                    ->field($field)
                    ->join('bank_list b','b.list_id=c.card_bank_id','LEFT')
                    ->where($where)
                    ->where(['card_type'=>2])
                    ->order('card_state desc')
                    ->select();
            $pay_card = Db::name('user_card')->alias('c')
                    ->field($field)
                    ->join('bank_list b','b.list_id=c.card_bank_id','LEFT')
                    ->where($where)
                    ->where(['card_type'=>1])
                    ->order('card_state desc')
                    ->select();
            foreach($card as $ck=>$cv){
                $list['card'][$ck] = $cv;
                $list['card'][$ck]['list_logo'] = 'http://'.$_SERVER['HTTP_HOST'].$cv['list_logo'];
                $list['card'][$ck]['card_no'] = substr($cv['card_no'],-4);
            }                    
            
            foreach($pay_card as $pk=>$pv){
                $list['pay_card'][$pk] = $pv;
                $list['pay_card'][$pk]['list_logo'] = 'http://'.$_SERVER['HTTP_HOST'].$pv['list_logo'];
                $list['pay_card'][$pk]['card_no']   = substr($pv['card_no'],-4);
            }
            return json(['error'=>0,'msg'=>'成功','data'=>$list]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }

    /**
     * 新增收款
     * @Author tw
     * @Date   2018-09-13
     */
    public function pay()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $type = $post['type'];//1 创建收款 2提交支付 3重新获取验证码
        $pay_id = $post['pay_id'];//支付通道id
        $pay_cid = $post['pay_cid'];//银行卡id
        $uid = $this->uid;//用户id
        $money = $post['money']; //收款金额
        $mcc = $post['mcc']; //行业编码
        $region = $post['region']; //地区

        $id = $post['id'];//订单id type 为 2 3 时用
        $smscode = $post['code'];//验证码紧type为2时有用

        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        if($type==1)
        {
            //商户注册
            $payment_user = Db::name('payment_user')->where('user_pay_id',$pay_id)->where('user_uid',$uid)->where('user_type',2)->find();
            
            if(empty($payment_user) || $payment_user['user_state']==0 || $payment_user['user_state']==2)
            {
                $res = Controller('pay/'.$payment_controller)->register($pay_id,$uid);
                if($res['error']=='1')
                {
                    return json($res);
                }
            }
            //创建收款
            $result = $this->add($uid,$money,$pay_cid,$pay_id,$mcc,$region);
        }
        elseif($type==2)
        {
            if(empty($smscode))
            {
                return json(['error'=>1,'msg'=>'请填写验证码']);
            }
            //支付确认
            $result = Controller('pay/'.$payment_controller)->pay_confirm($pay_id,$uid,$pay_cid,$id,$smscode);
            
        }
        elseif($type==3)
        {
            //重发验证码
            $result = Controller('pay/'.$payment_controller)->pay_sms($pay_id,$uid,$pay_cid,$id);
            
        }
        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        return json($result);
    }
    protected function add($uid='',$money='',$pay_cid='',$pay_id='',$mcc='',$region='')
    {
        //查询用户信息
        $user = Db::name('user')->where(['user_id'=>$uid,'user_state'=>0])->find();
        if(empty($user))
        {
            return ['error'=>1,'msg'=>'用户信息不存在'];
        }
        //收款卡信息
        $card = Db::name('user_card')->where(['card_uid'=>$uid,'card_blocked'=>0,'card_type'=>2])->find();
        if(empty($card))
        {
            return ['error'=>1,'msg'=>'收款卡不存在'];

        }
        $cid = $card['card_id'];
        //支付卡信息
        $pay_card = Db::name('user_card')->where(['card_uid'=>$uid,'card_id'=>$pay_cid,'card_blocked'=>0,'card_type'=>1])->find();
        if(empty($pay_card))
        {
            return ['error'=>1,'msg'=>'支付卡不存在'];
        }
        //查询通道是否支持该银行
        $bank = Db::name('bank')->where(['bank_bid'=>$pay_card['card_bank_id'],'bank_pay_id'=>$pay_id])->find();
        if(empty($bank))
        {
            $bank_list = Db::name('bank_list')->where('list_id',$pay_card['card_bank_id'])->find();
            return ['error'=>1,'msg'=>'通道不支持'.$bank_list['list_name'].',请更换通道'];
        }
        $payment = Db::name('payment')->where('payment_id',$pay_id)->where('payment_type',1)->find();
        if(empty($payment))
        {
            return ['error'=>1,'msg'=>'通道不存在'];
        }
        if($payment['payment_bind_d']==1)
        {
            $payment_card = Db::name('payment_card')->where('card_pay_id',$pay_id)->where('card_cid',$cid)->where('card_uid',$uid)->find();
            if(empty($payment_card) || $payment_card['card_state']!=1)
            {
                if($payment['payment_bind_way']=='web')
                {
                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/api/payrecords/bind_web/?pay_id='.$pay_id.'&uid='.$this->uid.'&cid='.$cid.'&token='.$this->token;
                }
                elseif($payment['payment_bind_way']=='api')
                {
                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/api/payrecords/bind_api';
                }
                return ['error'=>'10','msg'=>'绑卡验证','type'=>$payment['payment_bind_way'],'title'=>'储蓄卡绑卡验证','url'=>$url];
            }
        }
        if($payment['payment_bind']==1)
        {
            $payment_card = Db::name('payment_card')->where('card_pay_id',$pay_id)->where('card_cid',$pay_cid)->where('card_uid',$uid)->find();
            if(empty($payment_card) || $payment_card['card_state']!=1)
            {
                if($payment['payment_bind_way']=='web')
                {
                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/api/payrecords/bind_web/?pay_id='.$pay_id.'&uid='.$this->uid.'&cid='.$pay_cid.'&token='.$this->token;
                }
                elseif($payment['payment_bind_way']=='api')
                {
                    $url = 'http://'.$_SERVER['HTTP_HOST'].'/api/payrecords/bind_api';
                }
                return ['error'=>'10','msg'=>'绑卡验证','type'=>$payment['payment_bind_way'],'title'=>'绑卡验证','url'=>$url];
            }
        }
        //通道允许最小金额
        if(!empty($bank['bank_min_money']) && $bank['bank_min_money']>=$payment['payment_min_money'] && $bank['bank_min_money']!='0.00')
        {
            $min_money = $bank['bank_min_money'];
        }
        else
        {
            $min_money = $payment['payment_min_money'];
        }
        //通道允许最大金额
        if(!empty($bank['bank_max_money']) && $bank['bank_max_money']<=$payment['payment_max_money'] && $bank['bank_max_money']!='0.00')
        {
            $max_money = $bank['bank_max_money'];
        }
        else
        {
            $max_money = $payment['payment_max_money'];
        }
        //判断金额是否小于最低金额
        if($money < $min_money)
        {
            return ['error'=>1,'msg'=>'金额不能小于'.$min_money.'元'];
        }
        //判断金额是否大于最高金额
        if($money > $max_money)
        {
            return ['error'=>1,'msg'=>'金额不能大于'.$max_money.'元'];
        }
        $user_rate_sk = Db::name('user_rate')->where('rate_uid',$uid)->where('rate_type',2)->find();
        if(empty($user_rate_sk))
        {
            return ['error'=>1,'msg'=>'用户收款费率不存在'];
        }
        $rate = $user_rate_sk['rate_rate'];//费率
        $close_fee = $user_rate_sk['rate_close_rate'];//结算费率

        $fee = number_format($money * $rate,2);
        $amount = $money - $fee - $close_fee;

        //创建订单
        $form_no = get_order_sn('KJ',$uid); //收款单号
        $time = time();
        $data['records_uid'] = $uid;
        $data['records_cid'] = $cid;
        $data['records_pay_cid'] = $pay_cid;
        $data['records_state'] = 0;
        $data['records_form_no'] = $form_no;
        $data['records_money'] = $money;
        $data['records_amount'] = $amount;
        $data['records_rate'] = $rate;
        $data['records_fee'] = $fee;
        $data['records_close_rate'] = $close_fee;
        $data['records_time'] = $time;
        $data['records_pay_id'] = $pay_id;
        $data['records_mcc'] = $mcc;
        $data['records_region'] = $region;
        $id = Db::name('pay_records')->insertGetId($data);
        if(empty($id))
        {
            return ['error'=>1,'msg'=>'支付失败'];
        }
        //提交支付
        return Controller('pay/'.$payment['payment_controller'])->pay($pay_id,$uid,$pay_cid,$id);
        // return ['error'=>0,'msg'=>'支付提交成功','id'=>$id];
    
    }

    /**
     * 获取所有通道支持所有省市
     * @Author tw
     * @return [type] [description]
     */
    public function city_all()
    {
        $post   = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'获取地区失败']);
        }
        $id = $post['id'];
        $uid = $this->uid;//用户id
        $pay_id = $post['pay_id'];//支付通道id


        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }

        $result = Controller('pay/'.$payment_controller)->city_all($pay_id,$uid,$id);
        return json($result);
    }
    /**
     * 获取行业编码
     * @Author tw
     * @Date   2018-09-26
     * @return [type]     [description]
     */
    public function get_mcc()
    {
        $post = input('post.');
        $pay_id = $post['pay_id'];
        $uid = $this->uid;
        $cid = $post['cid'];
        $region = $post['region'];
        $city_id = $post['city_id'];
        $payment =  Db::name('payment')
            ->where('payment_id',$pay_id)
            ->where('payment_use',1)
            ->find();
        if(empty($payment))
        {
            return json(['error'=>1,'msg'=>'请选择支付通道']);
        }
        if($payment['payment_mcc']==1)
        {
            if ($city_id) {
                $list = Controller('pay/'.$payment['payment_controller'])->mcc($pay_id,$uid,$cid,$city_id);
            }
            else
            {
                $region_arr = explode('-', $region);
                $list = Controller('pay/'.$payment['payment_controller'])->query_city_mcc($pay_id,$uid,$cid,$region_arr[0],$region_arr[1]);
            }
        }
        else
        {
            $list = Db::name('payment_mcc')->where('mcc_pay_id',$pay_id)->field('mcc_mcc as mcc,mcc_title as mcc_name')->select();
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }

    /**
     * 会员等级升级
     */
    public function MemberUpgrade(){
        if($this->request->isPost()){
          	header("content-type:text/html;charset=utf-8"); 
            $post = input('post.');
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }
            
            Db::startTrans();
            try{
                $userType = Db::name('userType')->where('type_id',$post['type_id'])->find();
                if(!$userType){
                    return json(['error'=>1,'msg'=>'请选择升级会员']);
                }
                
                $Payment = Db::name('Payment')->where('payment_type',4)->where('payment_use',1)->where('payment_channel_id',$post['chennel_id'])->find();
                if(empty($Payment))
                {
                    return json(['error'=>1,'msg'=>'支付通道信息错误']);
                }
                $order_no = $this->uid.rand(1000,9999).time();
                $data = array();
                $data['upgrade_uid']     = $this->uid;
                $data['upgrade_pay_id']  = $Payment['payment_id'];
                $data['upgrade_form_no'] = $order_no;
                $data['upgrade_money']   = $userType['type_fee'];
                $data['upgrade_type_id'] = $post['type_id'];
                $data['upgrade_state']   = 0;
                $data['upgrade_time']    = time();

                $result = Db::name('PayUpgrade')->insert($data);
                if($result)
                {   
                   // 提交事务
                    Db::commit();
					if($Payment['payment_controller'] == 'Weixinapp'){
                    	$pay = new Weixinapp;
                      	$res = $pay->init($order_no);
                      	$isurl = 0;
						return json(['error'=>0,'msg'=>'提交成功','data'=>['res'=>$res,'isurl'=>$isurl]]);
                      	
                    }else{
                    	$url = 'http://'.$_SERVER['SERVER_NAME'].'/'.URL('Pay/'.$Payment['payment_controller'].'/init').'?order_no='.$order_no;
                       	$isurl = 1;
                      	return json(['error'=>0,'msg'=>'提交成功','data'=>['order_no'=>$order_no,'url'=>$url,'isurl'=>$isurl]]);
                    }
                }else{
                    return json(['error'=>1,'msg'=>'提交失败']);
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    
/**
 * [会员分润提现]
 */
    public function Presentation(){
        if($this->request->isPost())
        {
            $post = input('post.');
            if(empty($post))
            {
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            $type = $post['type'];
            if($type=='1')  //type 1   银行卡查询    2分润提现
            {
                $usercard = Db::name('UserCard as u')->field('card_id,card_uid,card_bank_id,card_type,list_id,list_name')
                    ->join('bank_list b','u.card_bank_id=b.list_id')
                    ->where('u.card_uid',$this->uid)
                    ->where('u.card_type',2)
                    ->select();
                $usermoney = Db::name('User')->field('user_id,user_moeny')->where('user_id',$this->uid)->find();
                if(empty($usercard))
                {
                    return json(['error'=>1,'msg'=>'暂未绑定银行卡']);
                }
                return json(['error'=>0,'msg'=>'成功','data'=>$usercard,'money'=>$usermoney['user_moeny']]);
            }else{
                $resa = Db::name('User')->where('user_id', $this->uid)->value('user_moeny');
                if($resa<$post['price']){
                    return json(['error'=>1,'msg'=>'提现金额超过可提现金额']);
                }
                if($post['price']-2 < 0 || $post['price']-2 == 0){
                    return json(['error'=>1,'msg'=>'提现金额低于提现手续费']);
                }
                $usercard = Db::name('UserCard as u')->field('card_id,card_uid,card_bank_id,card_type,list_id,list_name')
                    ->join('bank_list b','u.card_bank_id=b.list_id')
                    ->where('u.card_uid',$this->uid)
                    ->where('u.card_type',2)
                    ->select();
                if(empty($usercard))
                {
                    return json(['error'=>1,'msg'=>'暂未绑定银行卡']);
                }
                
                $data = array();
                $data['profit_uid'] = $this->uid;
                $data['profit_card_id'] = $post['bank_id'];
                $data['profit_form_no'] = $this->uid.rand().time();
                $data['profit_money']   = $post['price'];
                $data['profit_true_money'] = $post['price']-2;
                $data['profit_rate']    = 2;
                $data['profit_type']    = 1;
                $data['profit_time']    = time();
                Db::startTrans();
                try{

                    $result = Db::name('UserProfit')->insert($data);
                    $auot   = Db::name('User')->where('user_id', $this->uid)->setDec('user_moeny', $post['price']);
                    $usermoney = Db::name('User')->where('user_id',$this->uid)->value('user_moeny');
                    if($auot){
                        $da = array();
                        $da['presentation_uid'] = $this->uid;
                        // $da['presentation_profit_id'] = $result;
                        $da['presentation_type']      = 2;
                        $da['presentation_point']     = $post['price'];
                        $da['presentation_surplus']   = $usermoney;
                        $da['presentation_time']      = time();
                        $da['presentation_content']   = '提现';
                        $res = Db::name('UserPresentation')->insert($da);
                        if($res)
                        {
                            // 提交事务
                            Db::commit();
                            return json(['error'=>0,'msg'=>'提交成功，等待审核']);
                        }                        
                    }

                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return json(['error'=>1,'msg'=>'提交失败']);
                }
            }
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    /**
     * 会员提现记录
     */
    public function record(){
        if($this->request->isPost()){
            $post = input('post.');
            if(empty($post))
            {
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            $type = isset($post['type'])?$post['type']:0;
            if($type==0){
                $where = ['profit_uid'=>$this->uid];
            }else{
                $where = ['profit_uid'=>$this->uid,'profit_type'=>$type];
            }
            $list = Db::name('UserProfit')->field('profit_uid,profit_true_money,profit_money,profit_type,profit_time,profit_paytime')->where($where)->select();
            if(empty($list))
            {
                return json(['error'=>1,'msg'=>'暂无记录']);
            }

            foreach ($list as $key => $val) {
                if($val['profit_paytime']=='')
                {
                    $list[$key]['profit_paytime']='-年-月-日';
                }
                $list[$key]['profit_time']= date("Y-m-d",$val['profit_time']);
                $list[$key]['profit_paytime']= date("Y-m-d",$val['profit_paytime']);
            }
            return json(['error'=>0,'msg'=>'成功','data'=>$list]);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }

    /**
     * 商户注册
     * @Author tw
     * @Date   2018-10-11
     * @return [type]     [description]
     */
    public function register()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $uid = $this->uid;//用户id
        $payment = Db::name('payment')->where('payment_type',1)->where('payment_use',1)->select();
        foreach ($payment as $k => $v) {
            $result = Controller($v['payment_controller'])->register($v['payment_id'],$uid);
            // echo($v['payment_id']);
            // dump($result);
        }
    }

    /**
     * 绑卡
     * @Author tw
     * @Date   2018-09-21
     */
    public function bind_web()
    {
        $get = input('get.');
        $pay_id = $get['pay_id'];
        $uid = $get['uid'];
        $cid = $get['cid'];
        $payment = Db::name('payment')->where('payment_id',$pay_id)->find();
        if(empty($payment))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }

        $payment_card = Db::name('payment_card')
            ->where('card_uid','eq',$uid)
            ->where('card_pay_id','eq',$pay_id)
            ->where('card_cid','eq',$cid)
            ->where('card_type','eq','1')
            ->where('card_state','eq','1')
            ->find();
        if($payment_card)
        {
            return json(['error'=>1,'msg'=>'已绑卡']);
        }
        $result = Controller('pay/'.$payment['payment_controller'])->bind_card($pay_id,$uid,$cid,1);
        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        elseif($result['error']!=0)
        {
            return json($result);
        }
        elseif($result['url'])
        {
            $url = $result['url'];
            echo "<script>window.location.href='".$url."'</script>";
            //header("location:".$url);
        }
    }

    /**
     * 绑卡
     * @Author tw
     * @Date   2018-09-21
     */
    public function bind_api()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $type = $post['type'];//1 绑卡提交 2提交绑卡验证 3重新获取验证码
        $pay_id = $post['pay_id'];//支付通道id
        $cid = $post['cid'];//银行卡id
        $uid = $this->uid;//用户id
        $smscode = $post['smscode'];//验证码紧type为2时有用
        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        if($type==1)
        {
            //绑卡提交
            $result = Controller('pay/'.$payment_controller)->bind_card($pay_id,$uid,$cid);
        }
        elseif($type==2)
        {
            if(empty($smscode))
            {
                return json(['error'=>1,'msg'=>'请填写验证码']);
            }
            //绑卡验证
            $result = Controller('pay/'.$payment_controller)->bind_smscode($pay_id,$uid,$cid,$smscode);
            
        }
        elseif($type==3)
        {
            //重发验证码
            $result = Controller('pay/'.$payment_controller)->bind_retry_smscode($pay_id,$uid,$cid);
            
        }

        if(empty($result))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        return json($result);
    }
    /**
     * 解绑卡片
     * @Author tw
     * @Date   2018-10-16
     * @return [type]     [description]
     */
    public function unbind_card()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $pay_id = $post['pay_id'];//支付通道id
        $cid = $post['cid'];//银行卡id
        $uid = $this->uid;//用户id

        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller('pay/'.$payment_controller)->unbind_card($pay_id,$uid,$cid);
        return json($result);
    }
    
    /**
     * 身份激活
     */
    public function UserActivation(){
        if($this->request->isPost()){

            $post = input('post.');
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            Db::startTrans();
            try{

                $order_no = $this->uid.rand(1000,9999).time();
                $data = array();
                $data['act_uid']     = $this->uid;
                $data['act_pay_id']  = $post['chennel_id'];
                $data['act_form_no'] = $order_no;
                $data['act_money']   = $post['type_fee'];
                $data['cat_state'] = 0;
                $data['act_time']    = time();

                $result = Db::name('UserActivation')->insert($data);
                if($result)
                {   

                    $Payment = Db::name('Payment')->where('payment_id',$post['chennel_id'])->find();
                    if(empty($Payment))
                    {
                        return json(['error'=>1,'msg'=>'支付通道信息错误']);
                    }
                    $url = 'http://'.$_SERVER['SERVER_NAME'].'/'.URL('Pay/'.$Payment['payment_controller'].'/init').'?order_no='.$order_no.'&type=1';
                    // 提交事务
                    Db::commit(); 
                    return json(['error'=>0,'msg'=>'提交成功','data'=>['order_no'=>$order_no,'url'=>$url]]);
                }else{
                    return json(['error'=>1,'msg'=>'提交失败']);
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

        }else{

            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    /**
     * 查询状态
     * @Author tw
     * @Date   2018-10-20
     * @return [type]     [description]
     */
    public function state_query()
    {
        $post = input('post.');
        $id = $post['id'];//订单id
        $uid = $post['uid'];//用户id
        $pay_id = $post['pay_id'];//通道id
        $form_no = $post['form_no'];//订单编号
        $up_no = $post['up_no'];//上游返回编号
        $date = $post['date'];//交易日期

        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller('pay/'.$payment_controller)->state_query($id,$uid,$pay_id,$form_no,$up_no,$date);
        return json($result);

    }
}
