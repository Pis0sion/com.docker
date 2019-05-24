<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class Payhkllysmall extends Controller
{
    /**
     * 商户报件
     * @Author tw
     * @Date   2018-09-29
     * @return [type]     [description]
     */
    public function register()
    {
        $action=request()->action();
        $res = Controller('pay/'.request()->controller())->$action('20','4');
        dump($res);
    }

    public function bind_card()
    {
        $action=request()->action();
        $res = Controller('pay/'.request()->controller())->$action('20','37','75');
        dump($res);
    }
    public function bind_card_query()
    {
        $action=request()->action();
        $res = Controller('pay/'.request()->controller())->$action('20','37','75');
        dump($res);
    }
    public function city()
    {
        $action=request()->action();
        $res = Controller('pay/'.request()->controller())->$action('20','4','0');
        dump($res);
    }
    public function city_all()
    {
        $action=request()->action();
        $res = Controller('pay/'.request()->controller())->$action('20','4','0');
        dump($res);
    }
    public function mcc()
    {
        $action=request()->action();
        $res = Controller('pay/'.request()->controller())->$action('20','4','3','3022C0733998436B83CBA8CCF1691FC4');
        dump($res);
    }
    public function pay_notify()
    {
        $data = array (
  'sign' => '3cd595e204ae035c935865aaa588f1eb',
  'resmsg' => '成功',
  'orderNo' => 'PH20181116102419373288',
  'tradeNo' => '9AB745BA3FFB435C98E36C9C006D70F0',
  'rescode' => '00',
  'type' => 'YKXE',
  'tradeAmt' => '241.87',
);
        $res = api_post('pay/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }
}