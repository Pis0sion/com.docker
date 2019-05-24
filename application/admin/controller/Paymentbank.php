<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;
/**
 * 支付通道银行
 */
class Paymentbank extends Base{
	public function __construct(){
       parent::__construct();
    }
    /**
     * 列表
     * @Author   tw
     * @DateTime 2018-08-29
     * @return   [type]     [description]
     */
    public function index()
    {
        $keywords = trim(request()->param('keywords'));
        $getdata = $where = $whereor=array();
        $pay_id = input('param.pay_id',0);
        if(!$pay_id){
            return json(['error'=>1,'msg'=>'参数错误']);
        }

        $list = Db::name('bank')->alias('b')
                ->join('bank_list bl','bl.list_id=b.bank_bid')
                ->where('bank_pay_id','eq',$pay_id)
                ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('getdata',$getdata);
        $this->assign('pay_id',$pay_id);
        return $this->fetch();
    }
    
    /**
     * 添加
     * @Author   tw
     * @DateTime 2018-08-31
     */
    public function add()
    {
        if($this->request->isPost())
        {
            $post = $_POST;
            if(empty($post['bank_bid']))
            {
                return json(['error'=>1,'msg'=>'请选择银行']);
            }

            $data['bank_bid'] = $post['bank_bid'];//banklist id
            $data['bank_pay_id'] = $post['pay_id'];//支付通道id
            $data['bank_name'] = $post['name'];//银行名称
            $data['bank_code'] = $post['code'];//编码
            $data['bank_min_money'] = $post['min_money'];//最小金额
            $data['bank_max_money'] = $post['max_money'];//最大金额
            $data['bank_unionpay_no'] = $post['unionpay_no'];
            $data['bank_number_id']  = $post['number_id'];
            $data['bank_num1'] = $post['bank_num1'];
            $data['bank_num2']  = $post['bank_num2'];
            $data['bank_time'] = time();
            if(empty($data['bank_name']))
            {
                return json(['error'=>1,'msg'=>'银行名称必填']);
            }
            elseif(empty($data['bank_code']))
            {
                return json(['error'=>1,'msg'=>'银行编码必填']);
            }
            elseif(empty($data['bank_unionpay_no']))
            {
                return json(['error'=>1,'msg'=>'银联号必填']);
            }
            if(Db::name('bank')->insertGetId($data))
            {
                return json(['error'=>0,'msg'=>'添加成功']);
            }
            return json(['error'=>1,'msg'=>'添加错误']);
        }
        $pay_id = input('get.pay_id',0);
        if(!$pay_id){
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $bank_bid = Db::name('bank')->where('bank_pay_id','eq',$pay_id)->column('bank_bid');
        $payment = Db::name('payment')->where('payment_id','eq',$pay_id)->find();
        $bank_list = Db::name('bank_list')->whereNotIn('list_id',$bank_bid)->select();
        $this->assign('bank_list',$bank_list);
        $this->assign('payment',$payment);
        $this->assign('pay_id',$pay_id);
        return $this->fetch();
    }
    /**
     * 编辑
     * @Author   tw
     * @DateTime 2018-09-03
     * @return   [type]     [description]
     */
    public function edit()
    {
        if($this->request->isPost())
        {
            $post = $_POST;

            $data['bank_name'] = $post['name'];
            $data['bank_code'] = $post['code'];
            $data['bank_min_money'] = $post['min_money'];
            $data['bank_max_money'] = $post['max_money'];
            $data['bank_unionpay_no'] = $post['unionpay_no'];
            $data['bank_number_id']  = $post['number_id'];
            $data['bank_num1'] = $post['bank_num1'];
            $data['bank_num2']  = $post['bank_num2'];
            if(empty($data['bank_name']))
            {
                return json(['error'=>1,'msg'=>'银行名称必填']);
            }
            elseif(empty($data['bank_code']))
            {
                return json(['error'=>1,'msg'=>'银行编码必填']);
            }
            elseif(empty($data['bank_unionpay_no']))
            {
                return json(['error'=>1,'msg'=>'银联号必填']);
            }
            if(Db::name('bank')->where(['bank_id'=>$post['id']])->update($data))
            {
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $info = Db::name('bank')->alias('b')
                ->join('bank_list bl','bl.list_id=b.bank_bid')
                ->where(['bank_id'=>input('param.id')])
                ->find();
        if(empty($info))
        {
            echo "无内容";
            exit();
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 删除
     * @Author tw
     * @Date   2018-09-19
     * @return [type]     [description]
     */
    public function del()
    {
        $id = input('get.id',0);
        $del = Db::name('bank')->where(['bank_id'=>$id])->delete();
        if(empty($del))
        {
            return json(['error'=>1,'msg'=>'删除失败']);
        }
        return json(['error'=>0,'msg'=>'删除成功']);
    }

    public function change()
    {
        $id = input('post.id');
        $bank = Db::name('bank_list')->where('list_id',$id)->find();
        if(empty($bank))
        {
            return json(['error'=>1,'msg'=>'银行错误']);
        }
        return json(['error'=>0,'msg'=>'成功','bank'=>$bank]);

    }
}
