<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class Payrecords
{
    public function index()
    {
        $data['token'] = gettoken('37');
        $data['state'] = '';//收款状态
        $res = api_post('api/payrecords/index',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function detail()
    {
        $data['uid'] = '1';//用户id
        $data['id'] = '1';//收款记录id
        $res = api_post('api/payrecords/detail',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function card()
    {
    	$data['uid'] = '3';//用户id
    	$data['state'] = '1';//收款状态
    	$res = api_post('api/payrecords/card',$data);
    	echo ($res);
    	dump(json_decode($res,true));
    }
    public function pay()
    {
        $data['token'] = gettoken('4');
        $data['money'] = '200';//金额
        $data['pay_cid'] = '3';//支付卡id
        $data['pay_id'] = '25';//通道id
        $data['type'] = '1';
        $data['id'] = '';
        $data['code'] = '111111';

        $res = api_post('api/payrecords/pay',$data);
        echo ($res);
        dump(json_decode($res,true));
    }

    public function bankinfo()
    {
        $data['token'] = gettoken('35');
        $data['cid'] = '73';//支付卡id
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }
    
    public function getpayment()
    {
        $data['token'] = gettoken('4');
        $data['cid'] = '3';//支付卡id
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }
    
    public function register()
    {
        $data['token'] = gettoken('35');
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }
    public function bind_api()
    {
        $data['token'] = gettoken('35');
        $data['pay_id'] = '181';//支付通道id
        $data['cid'] = '731';//银行卡id
        $data['type'] = '1';
        $data['smscode'] = '111111';
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }

    public function unbind_card()
    {
        $data['token'] = gettoken('35');
        $data['pay_id'] = '18';
        $data['cid'] = '73';
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }

    public function city_all()
    {
        $data['token'] = gettoken('4');
        $data['pay_id'] = '25';//渠道id
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
        dump(json_decode($res,true));
    }

    public function get_mcc()
    {
        $data['token'] = gettoken('4');
        $data['pay_id'] = '25';//渠道id
        $data['cid'] = '3';//卡id
        $data['city_id'] = '3022C0733998436B83CBA8CCF1691FC4';
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
        dump(json_decode($res,true));
    }
}
