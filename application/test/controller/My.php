<?php
namespace app\test\controller;
use think\Controller;
use think\Db;

class My
{

    public function index()
    {

        $data['token'] = '225798ff6ff703269d0d0c7d8c0cc448';
        $res = api_post('api/My/index',$data);
        echo ($res);
    }
    public function password()
    {
    	$data['uid'] = '3';
    	$data['password'] = '123456789';
    	$data['new_password'] = '123123';
    	$res = api_post('api/My/password',$data);
    	echo ($res);
    }

    public function feedback()
    {
    	$data['uid'] = '3';
    	$data['title'] = '标题';
    	$data['body'] = '内容';
    	$data['name'] = '姓名';
    	$data['phone'] = '13112341234';
    	$res = api_post('api/My/feedback',$data);
    	echo ($res);
    }
    public function out()
    {
    	$data['uid'] = '3';
    	$res = api_post('api/My/out',$data);
    	echo ($res);
    }
    public function article_type()
    {
    	$data['uid'] = '3';
    	$res = api_post('api/My/article_type',$data);
    	echo ($res);
    }
    public function article()
    {
    	$data['uid'] = '3';
    	$data['type'] = '1';
    	$data['page'] = '1';
    	$res = api_post('api/My/article',$data);
    	echo ($res);
    }
    public function article_detail()
    {
        $data['uid'] = '3';
        $data['id'] = '1';
        $res = api_post('api/My/article_detail',$data);
        echo ($res);
    }
    public function real()
    {
        $data['token'] = '';
        $data['token'] = '225798ff6ff703269d0d0c7d8c0cc448';
        $res = api_post('api/My/real',$data);
        echo ($res);
    }
    public function authentication()
    {
        $data['uid'] = '3';
        $data['user_name'] = '用户3';//用户姓名
        $data['user_idcard'] = '371215484';//身份证
        $data['user_idcard_z'] = '/uploads/images/20180904/5b8dd8daec929.png';//身份证正
        $data['user_idcard_f'] = '/uploads/4c95da0c0384b84cc0cbb1f53f6dde71.png';//身份证反
        $res = api_post('api/My/authentication',$data);
        echo ($res);
    }
    public function card()
    {
        $data['token'] = gettoken('88');

        $data['card_no'] = '6226580096073602';//银行卡号
        $data['phone'] = '15553983330';//银行预留手机号

        $data['bank_id'] = '22';//银行id
        $data['type'] = '1';//1信用卡 2储蓄卡
       
        $data['limit'] = '9000';//信用额度
        $data['cvn'] = '649';//CVN码
        $data['exp_date'] = '0823';//卡有效期（月/年）
        $data['account_day'] = '16';//帐单日
        $data['repayment_day'] = '050';//还款日

        // $data['region'] = '山东省-济宁市-任城区';//省
        $res = api_post('api/My/card',$data);
        echo ($res);
    }

    public function card_info()
    {

        $data['token'] = 'TwuW7Ln6080DWlC';
        $data['cid'] = '1';
        $res = api_post('api/My/card_info',$data);
        echo ($res);
    }
    public function card_edit()
    {

        $data['token'] = 'TwuW7Ln6080DWlC';
        $data['cid'] = '1';

        $data['phone'] = '15312311544';//银行预留手机号

        $data['type'] = '2';//1信用卡 2储蓄卡
       
        $data['limit'] = '10000';//信用额度
        $data['cvn'] = '123';//CVN码
        $data['exp_date'] = '1220';//卡有效期（月/年）
        $data['account_day'] = '1';//帐单日
        $data['repayment_day'] = '20';//还款日

        $data['branch'] = '支行';//省
        $data['province'] = '山东省';//省
        $data['city'] = '济宁市';//市

        $res = api_post('api/My/card_edit',$data);
        echo ($res);
    }
    public function integral()
    {
        $data['uid'] = '3';
        $res = api_post('api/My/integral',$data);
        echo ($res);
    }
    public function integral_list()
    {
        $data['uid'] = '3';
        $res = api_post('api/My/integral_list',$data);
        echo ($res);
        dump(json_decode($res,true));
    }

    public function card_list()
    {
        $data['token']      = 'TwuW7Ln6080DWlC';
        $res = api_post('api/My/card_list',$data);
        echo ($res);
        dump(json_decode($res,true));
    }

    public function agent()
    {
        $data['token']      = '225798ff6ff703269d0d0c7d8c0cc448';
        $res = api_post('api/My/agent',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function apply_agent()
    {
        $data['token']      = '225798ff6ff703269d0d0c7d8c0cc448';
        $data['name']      =  '代理';
        $data['phone']     = '15312312312';
        $data['city']     = '城市';
        $data['company']     = '公司';
        $res = api_post('api/My/apply_agent',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function agent_info()
    {
        $data['token']      = '225798ff6ff703269d0d0c7d8c0cc448';
        $res = api_post('api/My/agent_info',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function agent_edit()
    {
        $data['token']      = '225798ff6ff703269d0d0c7d8c0cc448';
        $data['agent_id']      = '34';
        $data['name']      =  '代理';
        $data['phone']     = '15312312312';
        $data['city']     = '城市';
        $data['company']     = '公司';
        $res = api_post('api/My/agent_edit',$data);
        echo ($res);
        dump(json_decode($res,true));
    }


    public function info()
    {
        $data['token']      = 'TwuW7Ln6080DWlC';
        $res = api_post('api/My/info',$data);
        echo ($res);
        dump(json_decode($res,true));
    }
    public function get_bank_id()
    {
        $data['token']      = 'TwuW7Ln6080DWlC';
        $res = api_post('api/My/get_bank_id',$data);
        echo ($res);
        dump(json_decode($res,true));
    }

    public function share()
    {
        $data['token'] = '225798ff6ff703269d0d0c7d8c0cc448';
        $res = api_post('api/My/share',$data);
        echo ($res);
    }
    public function trading()
    {
        $data['token'] = 'TwuW7Ln6080DWlC';
        $res = api_post('api/My/trading',$data);
        echo ($res);
    }
    
    public function card_del()
    {
        $data['token'] = gettoken('35');
        $data['cid'] = '73';//支付卡id
        $res = api_post('api/'.request()->controller().'/'.request()->action(),$data);
        echo ($res);
    }
    public function ddd()
    {
        $user_profit = Db::name('trading')
                    ->count();
                    if(empty($user_profit))
                    {
                        echo 1;
                    }
    }
}
