<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class Payhklly extends Controller
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
        $res = Controller('pay/'.request()->controller())->$action('20','37');
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
        $res = Controller('pay/'.request()->controller())->$action('20','37','C59E260F57E443EBBDDB76FD9EA62DE5');
        dump($res);
    }
    public function mcc()
    {
        $action=request()->action();
        $res = Controller('pay/'.request()->controller())->$action('20','37','75','3022C0733998436B83CBA8CCF1691FC4');
        dump($res);
    }
    public function bind_card_notify()
    {
        $data = array (
  'sign' => '5e269653ba6b6da3f6dd8049f90b54de',
  'acqCode' => '8062',
  'resmsg' => '成功',
  'bindId' => '967AEBAA4D3B438A87CC06C2E0613E85',
  'rescode' => '00',
  'bankAccount' => '4033920034443145',
);
        $res = api_post('pay/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }
}