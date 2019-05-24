<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class Payhkhqe extends Controller
{
    public function e()
    {
        $payment_card = Db::name('payment_card')->alias('pc')

            ->field('pc.card_form_no,c.card_name,u.user_idcard,c.card_no,c.card_phone,b.list_name')
            ->join('user u','u.user_id = pc.card_uid','LEFT')
            ->join('user_card c','c.card_id = pc.card_cid','LEFT')
            ->join('bank_list b','b.list_id = c.card_bank_id','LEFT')
            ->where('card_pay_id',21)
            ->where('pc.card_state',1)
            ->select();
            dump($payment_card);
            foreach ($payment_card as $key => $value) {
                $data['bindid'] = $value['card_form_no'];
                $data['name'] = $value['card_name'];
                $data['idcard'] = $value['user_idcard'];
                $data['bankname'] = $value['list_name'];
                $data['card'] = $value['card_no'];
                $data['phone'] = $value['card_phone'];
                Db::name('huanqiue')->insert($data);
            }
        
    }
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
    public function bind_card_notify()
    {
        $data = array (
  'sign' => '555e3c89d262d2430e5cadfbc425c26d',
  'merchno' => '201808061452zj',
  'status' => '00',
  'bindId' => '2018112115340600100194852',
  'signType' => 'MD5',
  'dsorderid' => 'B1811211696210000472',
  'orderid' => '2018112115340600100194852',
);
        $res = api_post('pay/Payhkhqe/bind_card_notify',$data);
        echo ($res);
    }
    public function pay_notify()
    {
        $data = array (
  'sign' => 'a352df5b6e55b9f8e46562caa5dec81b',
  'amount' => '275.00',
  'transtype' => '81',
  'merchno' => '201808061452zj',
  'status' => '00',
  'signType' => 'MD5',
  'dsorderid' => 'CC00013181148000047815',
  'orderid' => '2018112117331000100103094',
  'paytime' => '20181121173321',
);
        $res = api_post('pay/Payhkhqe/pay_notify',$data);
        echo ($res);
    }

}