<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;
/**
 * 支付通道
 */
class Payment extends Base{
	public function __construct(){
       parent::__construct();
    }
    /**
     * 通道列表
     * @Author   tw
     * @DateTime 2018-08-29
     * @return   [type]     [description]
     */
    public function index()
    {
        $keywords = trim(request()->param('keywords'));
        $getdata = $where = $whereor=array();
        $pid = input('param.pid',0);
        if($pid)
        {
            $where[] = array('payment_channel_id','eq',$pid);
        }
        if(isset($keywords) && !empty($keywords)){
            $where[] = array('payment_name','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('payment')->where($where)
            ->order('payment_id desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('getdata',$getdata);
        return $this->fetch();
    }
    
    /**
     * 编辑文章
     * @Author   tw
     * @DateTime 2018-09-03
     * @return   [type]     [description]
     */
    public function edit()
    {
        if($this->request->isPost())
        {
            $post = $_POST;
            if($post['channel']==0)
            {
                return json(['error'=>1,'msg'=>'请选择通道']);
            }

            if($post['min_money']>$post['max_money']){
                return json(['error'=>1,'msg'=>'单笔金额值填写错误']);
            }
            // 风控开始、结束值不可超过总款限制；风控结束值不可超过单笔金额值差
            $moneydif = $post['max_money']-$post['min_money'];
            if($post['risk_start']>$post['money'] || $post['risk_end']>$post['money'] || $post['risk_end'] > $moneydif){
               return json(['error'=>1,'msg'=>'风控值填写错误']);
            }
            if(empty($post['mode']) && in_array($post['type'], [2]))
            {
                return json(['error'=>1,'msg'=>'请选择还款模式']);
            }
	    if($post['que']<5){
            	return json(['error'=>1,'msg'=>'查询列表时间不能小于5分钟']);
            }
	    if($post['interval_time']<10){
            	return json(['error'=>1,'msg'=>'最小计划间隔时间不能小于10分钟']);
            }
          

            $data['payment_channel_id']  = $post['channel'];
            $data['payment_name']        = $post['payment_name'];
            $data['payment_bind']        = $post['bind'];
            $data['payment_bind_d']        = $post['bind_d'];
            $data['payment_bind_way']        = $post['bind_way'];
            $data['payment_rate']        = $post['payment_rate'];
            $data['payment_close_fee']   = $post['payment_close_fee'];
            $data['payment_type']        = $post['type'];
            $data['payment_controller']  = $post['payment_controller'];
            $data['payment_use']         = $post['use'];
            $data['payment_day_num']     = $post['day_num'];
            $data['payment_num']       = $post['payment_num'];
            $data['payment_min_money']   = $post['min_money'];
            $data['payment_max_money']   = $post['max_money'];
            $data['payment_risk_start']  = $post['risk_start'];
            $data['payment_risk_end']    = $post['risk_end'];
            $data['paymentst_entime']    = $post['entime'];
            $data['paymentst_money']     = $post['money'];
            $data['payment_mode']        = $post['mode'];
            $data['payment_pattern']        = $post['pattern'];
            $data['payment_que']        = $post['que'];
            $data['payment_region']        = $post['region'];
            $data['payment_mcc']        = $post['mcc'];
            $data['payment_interval_time'] = $post['interval_time'];
            $data['payment_pay_mode'] = $post['pay_mode'];
            $data['payment_orders'] = $post['orders'];
            $data['payment_money_mode'] = $post['money_mode'];
            $data['payment_paynow'] = $post['paynow'];

            $config_jsonstr = $post['config'];
            if(isset($post['config']) && trim($post['config'])!=''){
                //当有设置时进行json化处理
                $config_jsonstr=$this->configToJson($post['config']);
            }
            $data['payment_config'] = $config_jsonstr;
            $up = Db::name('payment')->where(['payment_id'=>$post['id']])->update($data);
            if($up!==false)
            {
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $payment = Db::name('payment')->where(['payment_id'=>input('param.id')])->find();
        if(empty($payment))
        {
            echo "无内容";
            exit();
        }
        $payment['payment_config'] = $this->jsonTostr($payment['payment_config']);
        $payment_channel = Db::name('payment_channel')->select();

        $this->assign('payment_channel',$payment_channel);
        $this->assign('info',$payment);
        return $this->fetch();
    }
    
    /*
     * 增加渠道下的通道
     * 2018年9月4日15:24:14
     * 刘媛媛
     */
    public function addpayment(){
    	if($this->request->isPost()) {
    		$id = input('get.id',0);
            $post = input('post.');
            if(!$id){
        		return json(['error'=>1,'msg'=>'参数错误']);
        	}
        
	        $Channel = Db::name('paymentChannel')->where(array('channel_id'=>$id))->find();
	        if(!$Channel){
	           return json(['error'=>1,'msg'=>'渠道不存在']);
	        }

            $pment = Db::name('payment')->where(array('payment_channel_id'=>$id, 'payment_type'=>$post['type']))->find();
            if($pment){
                if($post['type']=='1'){
                    $tmsg = '收款';
                }else if($post['type']=='2'){
                    $tmsg = '还款';
                }else if($post['type']=='3'){
                    $tmsg = '代付';
                    return json(['error'=>1,'msg'=>'该渠道内'.$tmsg.'通道已存在']);
                }
            }
	        
            if($post['min_money']>$post['max_money']){
                return json(['error'=>1,'msg'=>'单笔金额值填写错误']);
            }

            // 风控开始、结束值不可超过总款限制；风控结束值不可超过单笔金额值差
            $moneydif = $post['max_money']-$post['min_money'];
            if($post['risk_start']>$post['money'] || $post['risk_end']>$post['money'] || $post['risk_end'] > $moneydif){
               return json(['error'=>1,'msg'=>'风控值填写错误']);
            }

            if(empty($post['mode']) && in_array($post['type'], [2]))
            {
                return json(['error'=>1,'msg'=>'请选择还款模式']);
            }
	    if($post['que']<5){
            	return json(['error'=>1,'msg'=>'查询列表时间不能小于5分钟']);
            }
	    if($post['interval_time']<10){
            	return json(['error'=>1,'msg'=>'最小计划间隔时间不能小于10分钟']);
            }
            //时间紧急不做验证了
            $adData = array();
            $adData['payment_channel_id']   = $Channel['channel_id'];
            $adData['payment_name'] 	    = $post['name'];
            $adData['payment_bind'] 	    = $post['bind'];
	    $adData['payment_bind_d']        = $post['bind_d'];
            $adData['payment_bind_way']        = $post['bind_way'];
            $adData['payment_rate'] 		= $post['rate'];
            $adData['payment_close_fee']	= $post['close_fee'];
            $adData['payment_type'] 		= $post['type'];
            $adData['payment_controller'] 	= $post['controller'];
            $adData['payment_use']			= $post['use'];
            $adData['payment_day_num'] 		= $post['day_num'];
            $adData['payment_num']          = $post['payment_num'];
            $adData['payment_min_money'] 	= $post['min_money'];
            $adData['payment_max_money'] 	= $post['max_money'];
            $adData['payment_time']			= time();
            $adData['payment_risk_start']   = $post['risk_start'];
            $adData['payment_risk_end']     = $post['risk_end'];
            $adData['paymentst_entime']     = $post['entime'];
            $adData['paymentst_money']      = $post['money'];
            $adData['payment_mode']        = $post['mode'];
            $adData['payment_pattern']        = $post['pattern'];
            $adData['payment_que']        = $post['que'];
            $adData['payment_region']        = $post['region'];
            $adData['payment_mcc']        = $post['mcc'];
            $adData['payment_interval_time'] = $post['interval_time'];
            $adData['payment_pay_mode'] = $post['pay_mode'];
            $adData['payment_orders'] = $post['orders'];
            $adData['payment_money_mode'] = $post['money_mode'];
            $adData['payment_paynow'] = $post['paynow'];

            $config_jsonstr = $post['config'];
            if(isset($post['config']) && trim($post['config'])!=''){
                //当有设置时进行json化处理
                $config_jsonstr=$this->configToJson($post['config']);
            }
            $adData['payment_config'] = $config_jsonstr;

            Db::name('payment')->insert($adData);
            return json(['error'=>0,'msg'=>'新增通道成功']);

        }
        $id = input('get.id',0);
       
        if(!$id){
        	die('参数错误');
        }
        
        $Channel = Db::name('paymentChannel')->where(array('channel_id'=>$id))->find();
        if(!$Channel){
           die('渠道不存在');
        }
        $this->assign('channel',$Channel);
    	return $this->fetch();
    }
    
    /**
     * config 序列化,字符串格式：name:张三|time:20180116
     * @author yan  2018-01-17
     * @return [type] [description]
     */
    protected function configToJson($configstr){
        $json_arr =array();
        $acc_config_arr =explode('|',$configstr);
        if(!empty($acc_config_arr)){
            foreach ($acc_config_arr as $value){
                if(trim($value)!=''){
                    $subarr=explode(':',$value,2);
                    if(!empty($subarr)){
                        $json_arr[]=$subarr;
                    }
                }

            }

        }
        //var_export($json_arr);
        //exit;
        return json_encode($json_arr,JSON_UNESCAPED_SLASHES);
    }
    /**
     * config json 转字符串[字符串格式：name:张三|time:20180116]
     * @author yan  2018-01-17
     * @return [str] [字符串格式：name:张三|time:20180116
     */
    protected function jsonTostr($configstr){

        $jsonarr =json_decode($configstr);
        $str2 ='';
        if($jsonarr){
            $json_main=array();
            foreach ($jsonarr as $arr1){
                $str =implode(':',$arr1);
                $json_main[]=$str;
            }
            $str2 =implode('|',$json_main);
        }
        return $str2;
    }
   /*
     * 删除通道
     * 2018年11月8日13:32:58
     * 刘媛媛
     */
    public function delepay(){
    	
    	$id = input('get.id',0);
       
        if(!$id){
        	return json(['error'=>1,'msg'=>'参数错误']);
        }
        
        $payment = Db::name('payment')->where(array('payment_id'=>$id))->find();
        if(!$payment){
        	return json(['error'=>1,'msg'=>'渠道不存在']);
        }
        if($payment['payment_use']==1){
        	return json(['error'=>1,'msg'=>'只能删除未启用的通道']);
        }
        //1收款 2还款 3代付  5混合 0其他异常
        if($payment['payment_type']==4){
        	$Upgrade = Db::name('payUpgrade')->where('upgrade_pay_id',$payment['payment_id']) ->whereTime('upgrade_time', 'yesterday')->find();
        	if($Upgrade){
        		return json(['error'=>1,'msg'=>'近2天内有此支付的订单请禁用后过2天在尝试']);
        	}
        	Db::name('payment')->where(array('payment_id'=>$id))->delete();
        	return json(['error'=>0,'msg'=>'删除成功']);
        }
        
        if($payment['payment_type']==3){
        	Db::name('payment')->where(array('payment_id'=>$id))->delete();
        	return json(['error'=>0,'msg'=>'删除成功']);
        }
        
        if($payment['payment_type']==1){
         	$Records = Db::name('payRecords')->where('records_pay_id',$id)->where('records_state',3)->find();
         	if($Records){
         		return json(['error'=>1,'msg'=>'还有未完成的收款不能删除']);
         	}
         	Db::name('payment')->where(array('payment_id'=>$id))->delete();
        	return json(['error'=>0,'msg'=>'删除成功']);
        }
        
        
        if($payment['payment_type']==2){
        	return json(['error'=>1,'msg'=>'抱歉还款接口删除会引起沉余数据,暂不支持删除']);
        }
        
        return json(['error'=>1,'msg'=>'未配置删除方式']); 
    }
}
