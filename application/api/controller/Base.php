<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;

class Base extends Controller
{
    public function __construct(Request $request){
        
       	parent::__construct();
       	$token =  $request->param('token');
        if(empty($token) or $token ==null or  $token=='' or  $token=='null')
        {
            echo json_encode(['error'=>1,'msg'=>'token不能为空'],JSON_UNESCAPED_UNICODE);
            exit();
        }

        $User =$this->checkToken($token);
        if(!$User){
            echo json_encode(['error'=>1,'msg'=>'请重新登陆!'],JSON_UNESCAPED_UNICODE);
            exit();
        }
        
        if($User['user_state']!=0){
            echo json_encode(['error'=>1,'msg'=>'会员不存在或被冻结'],JSON_UNESCAPED_UNICODE);
            exit();
        }
        $this->user=$User;
        $this->uid=$User['user_id'];
        $this->token=$token;
    }
    
    protected function checkToken($token){
    	return Db::name('user')->where('user_token',$token)->find();
    }
	/**
     * 通道公用参数
     * @Author tw
     * @Date   2018-10-10
     * @param  [type]     $pay_id      [渠道id]
     * @param  [type]     $uid         [用户id]
     * @param  string     $user_prefix [user 前缀]
     * @param  string     $type        [1商户注册 2查询]
     * @param  string     $user_type   [description]
     * @return [type]                  [description]
     */
    protected function payment_public($pay_id,$uid,$user_prefix='',$type='',$user_type='1')
    {
        $payment = Db::name('payment')->where('payment_id',$pay_id)->find();
        if(empty($payment))
        {
            return ['error'=>1,'msg'=>'通道不存在'];
        }
        $payment_config = configJsonToArr($payment['payment_config']);

        $payment_user = Db::name('payment_user')->where('user_pay_id',$pay_id)->where('user_uid',$uid)->where('user_type',$user_type)->find();

        if($type==1)
        {
            if(empty($payment_user))
            {
                $payment_user['user_uid']= $uid;
                $payment_user['user_pay_id']= $pay_id;
                $payment_user['user_number']= $user_prefix.date('ymdHis') . rand(10,99) . sprintf("%04d", $uid);
                // $payment_user['user_name']= $card['card_city'].$card['card_district'].$user['user_name'];
                // $payment_user['user_shortname']= $card['card_district'].$user['user_name'];
                $payment_user['user_type']= $user_type;
                $payment_user['user_state']= '0';
                $payment_user['user_time']= time();
                $payment_user['user_id'] = Db::name('payment_user')->insertGetId($payment_user);
            }
            elseif($payment_user['user_state']==1)
            {
                return ['error'=>1,'msg'=>'商户已注册'];
            }
            elseif($payment_user['user_state']==3)
            {
                return ['error'=>1,'msg'=>'商户审核中'];
            }
        }
        elseif($type==2)
        {
            if(empty($payment_user) || $payment_user['user_state']==2)
            {
                return ['error'=>1,'msg'=>'商户未注册'];
            }
            elseif($payment_user['user_state']==3)
            {
                return ['error'=>1,'msg'=>'商户审核中'];
            }
            elseif($payment_user['user_state']==4)
            {
                return ['error'=>1,'msg'=>'商户冻结'];
            }
        }
        

        $user = Db::name('user')->where('user_id',$uid)->where('user_state',0)->find();
        if(empty($user))
        {
            return ['error'=>1,'msg'=>'用户不存在'];
        }
        $user_rate_hk = Db::name('user_rate')->where('rate_uid',$uid)->where('rate_type',1)->find();
        if(empty($user_rate_hk))
        {
            return ['error'=>1,'msg'=>'用户还款费率不存在'];
        }
        $user_rate_sk = Db::name('user_rate')->where('rate_uid',$uid)->where('rate_type',2)->find();
        if(empty($user_rate_sk))
        {
            return ['error'=>1,'msg'=>'用户收款费率不存在'];
        }
        
        $card = Db::name('user_card')->where('card_uid',$uid)->where('card_type',2)->find();
        if(empty($card))
        {
            return ['error'=>1,'msg'=>'结算卡不存在'];
        }

        
        // $pay_card = Db::name('user_card')->where('card_uid',$uid)->where('card_type',1)->find();
        // if(empty($card))
        // {
        //     return ['error'=>1,'msg'=>'支付卡不存在'];
        // }


        return [
                    'error'=>0,
                    'msg'=>'成功',
                    'payment'=>$payment,
                    'payment_config'=>$payment_config,
                    'payment_user'=>$payment_user,
                    // 'payment_card'=>$payment_card,
                    'user'=>$user,
                    'user_rate_hk'=>$user_rate_hk,
                    'user_rate_sk'=>$user_rate_sk,
                    'card'=>$card,
                    // 'pay_card'=>$pay_card
                ];
    }

    /**
     * 商户报件
     * @Author tw
     * @Date   2018-10-10
     * @param  [type]     $code          [description]
     * @param  [type]     $user_msg      [description]
     * @param  [type]     $user_merchant [description]
     * @return [type]                    [description]
     */
    protected function payment_register($payment_user='',$user_state='',$user_msg='',$user_merchant='',$data=array())
    {
        
        $data['user_state'] = $user_state;
        if($user_msg)
        {
            $data['user_msg'] = $user_msg;
        }
        if($user_merchant)
        {
            $data['user_merchant'] = $user_merchant;
        }
        Db::name('payment_user')->where('user_id',$payment_user['user_id'])->where('user_pay_id',$payment_user['user_pay_id'])->update($data);
        
    }
    /**
     * 绑定支付卡
     * @Author tw
     */
    protected function payment_bind_card($payment_card='',$card_state='',$card_msg='',$card_pay_cid='',$card_form_no='')
    {
        $data['card_state'] = $card_state;
        $data['card_time'] = time();
        if($card_msg)
        {
            $data['card_msg'] = $card_msg;
        }
        if($card_pay_cid)
        {
            $data['card_pay_cid'] = $card_pay_cid;
        }
        if($card_form_no)
        {
            $data['card_form_no'] = $card_form_no;
        }
        Db::name('payment_card')->where('card_id',$payment_card['card_id'])->update($data);
    }
    /**
     * 解绑卡
     * @Author tw
     */
    protected function payment_unbind_card($payment_card='')
    {
        if(empty($payment_card))
        {
            return ['error'=>1,'msg'=>'卡不存在'];
        }
        Db::name('payment_card')->where('card_id',$payment_card['card_id'])->delete();
    }
    /**
     * 收款订单
     * @Author tw
     * @Date   2018-10-12
     * @param  [type]     $id     [订单id]
     * @param  [type]     $uid    [用户id]
     * @param  [type]     $type   [订单状态]
     * @param  [type]     $up_no  [返回单号]
     * @param  [type]     $number [付款单号]
     */
    protected function payment_kj($id='',$uid='',$type='',$up_no='',$number='',$msg='',$info='',$pay_time='')
    {
        if(empty($id) || empty($uid) || empty($type))
        {
            return ['error'=>1,'msg'=>'参数错误'];
        }
        if($up_no)
        {
            $data['records_form_up_no'] = $up_no;
        }
        if($number)
        {
            $data['records_form_number'] = $number;
        }
        if($msg)
        {
            $data['records_msg'] = $msg;
        }
        if($info)
        {
            $data['records_info'] = $info;
        }
        if($pay_time)
        {
            $data['records_pay_time'] = $pay_time;
        }
        $data['records_state'] = $type;
        Db::name('pay_records')->where('records_id',$id)->where('records_uid',$uid)->update($data);
    }

    protected function payment_logs($data='',$obj='',$controller='')
    {
        $path =  'logs/';
        if($controller)
        {
            $path =  $path.$controller.'/';
        }
        else
        {
            $path =  $path.request()->controller().'/';
        }
        //创建类型
        if (! is_dir($path)) {
            mkdir($path,0777);
        }
        $path =  $path.request()->action().'/';
        //创建类型
        if (! is_dir($path)) {
            mkdir($path,0777);
        }
        //支付渠道回调
        file_put_contents($path.date("Ymd",time()).'.log', PHP_EOL.date('Y-m-d H:i:s').PHP_EOL.'发送'.PHP_EOL.var_export($data, true).PHP_EOL, FILE_APPEND);
        file_put_contents($path.date("Ymd",time()).'.log', '返回'.PHP_EOL.var_export($obj, true).PHP_EOL, FILE_APPEND);

    }
}