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
class Paymentmcc extends Base{
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

        $list = Db::name('payment_mcc')->where('mcc_pay_id',$pay_id)->paginate(20,false,['query'=> $getdata]);
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
            $post = input('post.');
            if(empty($post['title']))
            {
                return json(['error'=>1,'msg'=>'Mcc名称必填']);
            }
            if(empty($post['mcc']))
            {
                return json(['error'=>1,'msg'=>'Mcc编码必填']);
            }

            $data['mcc_pay_id'] = $post['pay_id'];//支付通道id
            $data['mcc_title'] = $post['title'];//银行名称
            $data['mcc_mcc'] = $post['mcc'];//编码
            $data['mcc_time'] = time();
            $data['mcc_use'] = 1;
            if(Db::name('payment_mcc')->insertGetId($data))
            {
                return json(['error'=>0,'msg'=>'添加成功']);
            }
            return json(['error'=>1,'msg'=>'添加错误']);
        }
        $pay_id = input('get.pay_id',0);
        if(!$pay_id){
            return json(['error'=>1,'msg'=>'参数错误']);
        }
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
            $post = input('post.');
            if(empty($post['title']))
            {
                return json(['error'=>1,'msg'=>'Mcc名称必填']);
            }
            if(empty($post['mcc']))
            {
                return json(['error'=>1,'msg'=>'Mcc编码必填']);
            }
            $data['mcc_title'] = $post['title'];//银行名称
            $data['mcc_mcc'] = $post['mcc'];//编码

            if(Db::name('payment_mcc')->where(['mcc_id'=>$post['id']])->update($data))
            {
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $info = Db::name('payment_mcc')->where(['mcc_id'=>input('param.id')])->find();
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
        $del = Db::name('payment_mcc')->where(['mcc_id'=>$id])->delete();
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
