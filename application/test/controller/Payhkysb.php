<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class Payhkysb extends Controller
{
    /**
     * 商户报件
     * @Author tw
     * @Date   2018-09-29
     * @return [type]     [description]
     */
    public function register()
    {
        $action = request()->action();
        $res = Controller('pay/'.request()->controller())->$action('19','11');
        dump($res);
        exit();
    }
    public function bind_card()
    {
        $action = request()->action();
        $res = Controller('pay/'.request()->controller())->$action('19','43','77');
        dump($res);
        exit();
    }
    public function bind_card_query()
    {
        $action = request()->action();
        $res = Controller('pay/'.request()->controller())->$action('19','43','77');
        dump($res);
        exit();
    }
    public function unbind_card()
    {
        $action = request()->action();
        $res = Controller('pay/'.request()->controller())->$action('19','43','77');
        dump($res);
        exit();
    }

    public function pay_notify()
    {
        $data = array (
  'amount' => '12.59',
  'result_code' => '0000',
  'tailNo' => '2140',
  'orderNo' => 'PH20181106173605434811',
  'batchNo' => '2010000000000000000929',
  'result_msg' => '',
  'bankName' => '中国农业银行',
  'mac' => '34C3152F73DB7A2B29081B10FD9D8FA6',
  'memberId' => 'hkysb_1811060043',
);
        $res = api_post('pay/'.request()->controller().'/'.request()->action(),$data);
        dump($res);
    }

    public function bind_card_notify()
    {
        $data = array (
  'accountId' => '1120180626092155001',
  'mac' => '50F71FF8A69321B263253D4B50F1BCF8',
  'bankName' => '中信银行信用卡中心',
  'cardType' => '1',
  'bindMsg' => '绑卡成功',
  'result_code' => '0000',
  'cardNo' => '3145',
  'bindCode' => '1028',
  'result_msg' => '受理成功',
  'token' => 'B28EB9BF91A0E487F23C30D764C4EC5E',
  'name' => '鲍执政',
  'memberId' => 'hkysb_1811030035',
  'merchantNo' => '2110000000000000001812',
);
        $res = api_post('pay/'.request()->controller().'/'.request()->action(),$data);
        dump($res);
    }
}