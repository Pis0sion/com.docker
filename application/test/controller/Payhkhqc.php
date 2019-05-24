<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class Payhkhqc extends Controller
{
    
    /**
     * 商户报件
     * @Author tw
     * @Date   2018-09-29
     * @return [type]     [description]
     */
    public function register()
    {
        $res = Controller('pay/Payskhqkj')->register('22','37');
        dump($res);
    }
    public function network_query()
    {
        $data['token'] = gettoken('35');
        $data['pay_id'] = '16';
        $res = api_post('api/Payhqc/network_query',$data);
        echo ($res);
    }
    public function balance_query()
    {
        $data['token'] = gettoken('35');
        $data['pay_id'] = '16';
        $res = api_post('api/Payhqc/balance_query',$data);
        echo ($res);
    }
    public function open_card()
    {
        $data['token'] = gettoken('35');
        $data['pay_id'] = '16';
        $data['cid'] = '73';
        $res = api_post('api/Payhqc/open_card',$data);
        echo ($res);
    }
    public function bind_card()
    {
        $data['token'] = gettoken('23');
        $data['pay_id'] = '16';
        $data['cid'] = '71';
        $res = api_post('api/Payhqc/bind_card',$data);
        echo ($res);
    }
    public function pay_notify()
    {
        $data = array (
  'sign' => '64a3f32c3a006d36d2262197ff44f0b9',
  'message' => '',
  'amount' => '96.78',
  'dsorderid' => 'CC20181018104310239558BD',
  'code' => 'SUCCESS',
);
        $res = api_post('pay/Payhqc/pay_notify',$data);
        echo ($res);
    }
}