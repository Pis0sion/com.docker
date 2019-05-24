<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class Profit
{
    public function repayment()
    {
        $res = Controller('pay/Profit')->repayment('CC00029181120000115405');
        dump($res);
    }
    public function payrecords()
    {
        $data['form_no'] = 'KJ1810123998831003546';
        $res = api_post('api/Profit/payrecords',$data);
        echo ($res);
    }
    public function upgrade()
    {
        $data['form_no'] = '11111111111';
        $res = api_post('api/Profit/upgrade',$data);
        echo ($res);
    }
}
