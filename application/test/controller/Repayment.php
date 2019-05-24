<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class Repayment
{
    public function index()
    {
    	$data['uid'] = '1';//用户id
    	$data['state'] = '1';//计划状态
    	$res = api_post('api/repayment/index',$data);
    	echo ($res);
    	dump(json_decode($res,true));
    }
    public function card()
    {
    	$data['uid'] = '1';//用户id
    	$data['state'] = '1';//计划状态
    	$res = api_post('api/repayment/card',$data);
    	echo ($res);
    	dump(json_decode($res,true));
    }
    public function add()
    {
    	$data['uid'] = '1';//用户id

        $data['cid'] = '1';//卡id
        $data['pay_id'] = '1';//支付渠道id
        $data['type'] = '1';//预览/确认
        $data['money'] = '2000';//还款金额
        $data['start_time'] = '2018-09-20';//开始时间
        $data['end_time'] = '2018-10-08';//结束时间
        $data['repay_num'] = '1';//代还笔数
        $data['flag'] = '1';//代还模式
        $data['region_id'] = '1';//地区id 多个逗号分隔
        $data['mcc'] = '1';//自选行业id


    	$res = api_post('api/repayment/add',$data);
    	echo ($res);
    	dump(json_decode($res,true));
    }

    public function getChannel()
    {
        $data['token'] = '10ba5b43e0d35a3d6ae346229074d9f3';//用户id
        $data['bankid'] = '54';//卡id
        $res = api_post('api/repayment/getChannel',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function requests()
    {
        $data['token'] = gettoken('408');
        $data['pay_id'] = '2';//渠道id
        $data['cid'] = '465';//卡id
        $data['money'] = '8000';//金额
        $data['test'] = 1;
        
        $res = api_post('api/repayment/requests',$data);

        dump(json_decode($res,true));
        echo ($res);
    }
    public function getbankpayment()
    {
        $data['token'] = '10ba5b43e0d35a3d6ae346229074d9f3';//用户id
        $data['cid'] = '54';//卡id
        

        $res = api_post('api/repayment/getbankpayment',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function preview()
    {
        $data['token'] = gettoken('408');
        $data['pay_id'] = '2';//渠道id
        $data['cid'] = '465';//卡id
        $data['money'] = '8000';//金额
        $data['type'] = '10';//0是自定义金额 其他为笔数
        $data['version'] = '2.0';
        // $data['region'] = '山东省-济宁市';
        // $data['city_id'] = '3022C0733998436B83CBA8CCF1691FC4';
        $data['start_time'] = '2019-1-16';//开始日期
        $data['end_time'] = '2019-1-22';//结束日期
        $res = api_post('api/repayment/preview',$data);
        echo ($res);
        exit();
        dump(json_decode($res,true));
    }

    public function create()
    {
        $data['token'] = gettoken('404');
        $data['pay_id'] = '1';//渠道id
        $data['cid'] = '462';//卡id
        $data['money'] = '2100';//金额
        $data['type'] = '1';//0是自定义金额 其他为笔数
        $data['version'] = '2.0';
        // $data['region'] = '山东省-济宁市';
        // $data['city_id'] = '3022C0733998436B83CBA8CCF1691FC4';
        $data['start_time'] = '2018-12-29';//开始日期
        $data['end_time'] = '2019-12-30';//结束日期
        $res = api_post('api/repayment/create',$data);
        echo ($res);
        dump(json_decode($res,true));
    }

    public function preview_edit()
    {
        $data['token'] = gettoken('4');
        $data['pay_id'] = '20';//渠道id
        $data['cid'] = '3';//卡id
        $data['money'] = '1000';//金额
        $data['type'] = '2';//0是自定义金额 其他为笔数
        $data['data'] = '{"error":0,"msg":"成功","data":[[{"money":"255","time":"2018-11-19 17:01:25","type":"2","fee":"2.54","sort":"1","sum_money":"257.54","mcc":"MD0383948","mcc_name":"莱芜市农高区鑫聚婴母婴用品店"},{"money":"255","time":"2018-11-19 18:42:14","type":"2","fee":"2.54","sort":"1","sum_money":"257.54","mcc":"MD0336889","mcc_name":"茌平县石语珠宝店"},{"money":"510","time":"2018-11-19 20:15:18","type":"1","fee":"0","sort":"1"}],[{"money":"242","time":"2018-11-20 09:32:26","type":"2","fee":"2.44","sort":"2","sum_money":"244.44","mcc":"MD0383955","mcc_name":"莱芜市炫动体育设施"},{"money":"248","time":"2018-11-20 11:29:34","type":"2","fee":"2.49","sort":"2","sum_money":"250.49","mcc":null,"mcc_name":null},{"money":"490","time":"2018-11-20 13:08:01","type":"1","fee":"0","sort":"2"}]],"sumMoneyfree":311,"total_money":1010.01,"sumMoney":"1000","free":10.01,"start_time":"2018-11-19 17:01:25","end_time":"2018-11-20","paytime":"2018-11-19 17:01:25","region":"山东省-济宁市"}';
        $res = api_post('api/repayment/preview_edit',$data);
        echo ($res);
        dump(json_decode($res,true));
    }

    public function city_all()
    {
        $data['token'] = gettoken('4');
        $data['pay_id'] = '20';//渠道id
        $res = api_post('api/repayment/city_all',$data);
        echo ($res);
        dump(json_decode($res,true));
    }

    public function get_mcc()
    {
        $data['token'] = gettoken('4');
        $data['pay_id'] = '20';//渠道id
        $data['cid'] = '3';//渠道id
        $data['city_id'] = '5C054AEDD06240F6BB0895F26C17F7A7';
        $res = api_post('api/repayment/get_mcc',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function detail()
    {
        $data['token'] = gettoken('4');
        $data['mid'] = '8';
        $res = api_post('api/repayment/detail',$data);
        echo ($res);
        dump(json_decode($res,true));
        exit();

        $url='http://ka.lfbaohong.cn/api/repayment/detail';
        $res = curl($url,$data);
        echo ($res);
    }

    public function del()
    {
        $data['token'] = gettoken('35');
        $data['mid'] = '10';
        $res = api_post('api/repayment/del',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    
    /**
     * 商户报件
     * @Author tw
     * @Date   2018-09-29
     * @return [type]     [description]
     */
    public function register()
    {
        $data['token'] = gettoken('35');
        $data['pay_id'] = '17';
        $res = api_post('api/repayment/'.request()->action(),$data);
        echo ($res);
    }
    public function bind_card()
    {
        $data['token'] = gettoken('23');
        $data['pay_id'] = '16';
        $data['cid'] = '71';
        $res = api_post('api/repayment/'.request()->action(),$data);
        echo ($res);
    }
    public function bind_retry_smscode()
    {
        $data['token'] = gettoken('23');
        $data['pay_id'] = '17';
        $data['cid'] = '71';
        $res = api_post('api/repayment/'.request()->action(),$data);
        echo ($res);
    }
    public function bind_smscode()
    {
        $data['token'] = gettoken('35');
        $data['pay_id'] = '17';
        $data['cid'] = '73';
        $data['smscode'] = '111111';
        $res = api_post('api/repayment/'.request()->action(),$data);
        echo ($res);
    }
    public function unbind_card()
    {
        $data['token'] = gettoken('23');
        $data['pay_id'] = '17';
        $data['cid'] = '71';
        $res = api_post('api/repayment/'.request()->action(),$data);
        echo ($res);
    }
    public function pay()
    {
        $data['token'] = gettoken('23');
        $data['pay_id'] = '17';
        $data['cid'] = '71';
        $res = api_post('api/repayment/'.request()->action(),$data);
        echo ($res);
    }
    public function pay_confirm()
    {
        $data['token'] = gettoken('23');
        $data['pay_id'] = '17';
        $data['cid'] = '71';
        $res = api_post('api/repayment/'.request()->action(),$data);
        echo ($res);
    }
    public function query()
    {
        $data['token'] = gettoken('23');
        $data['pay_id'] = '17';
        $data['cid'] = '71';
        $res = api_post('api/repayment/'.request()->action(),$data);
        echo ($res);
    }


    public function bind_api()
    {
        $data['token'] = gettoken('23');
        $data['pay_id'] = '16';
        $data['cid'] = '71';
        $data['type'] = '1';
        $data['smscode'] = '111111';
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }
}
