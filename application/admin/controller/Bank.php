<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

/**
 * 银行列表
 */
class Bank extends Base{
	public function __construct(){
       parent::__construct();
    }
    
    public function index()
    {
        $keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();

        if(isset($keywords) && !empty($keywords)){
            $where[] = array('list_name','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('bank_list')->alias('i')
            ->where($where)
            ->order('list_id asc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
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

            $data['list_name'] = $post['name'];
            $data['list_code'] = $post['code'];
            $data['list_logo'] = $post['img'];
            $data['list_time'] = time();
            $data['list_more'] = $post['more'];
            $data['list_tel']  = $post['tel'];
	    $data['list_queryurl']  = $post['queryurl'];
            $data['list_unionpay_no'] = $post['unionpay_no'];
            $data['list_number_id']  = $post['number_id'];
            if(empty($data['list_name']))
            {
                return json(['error'=>1,'msg'=>'银行名称必填']);
            }
            elseif(empty($data['list_code']))
            {
                return json(['error'=>1,'msg'=>'银行编码必填']);
            }
            elseif(empty($data['list_unionpay_no']))
            {
                return json(['error'=>1,'msg'=>'银联号必填']);
            }
            if(Db::name('bank_list')->insertGetId($data))
            {
                return json(['error'=>0,'msg'=>'添加成功']);
            }
            return json(['error'=>1,'msg'=>'添加错误']);
        }
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

            $data['list_name'] = $post['name'];
            $data['list_code'] = $post['code'];
            $data['list_logo'] = $post['img'];
            $data['list_more'] = $post['more'];
            $data['list_tel']  = $post['tel'];
	    $data['list_queryurl']  = $post['queryurl'];
            $data['list_unionpay_no'] = $post['unionpay_no'];
            $data['list_number_id']  = $post['number_id'];
            if(empty($data['list_name']))
            {
                return json(['error'=>1,'msg'=>'银行名称必填']);
            }
            elseif(empty($data['list_code']))
            {
                return json(['error'=>1,'msg'=>'银行编码必填']);
            }
            elseif(empty($data['list_unionpay_no']))
            {
                return json(['error'=>1,'msg'=>'银联号必填']);
            }
            if(Db::name('bank_list')->where(['list_id'=>$post['id']])->update($data))
            {
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $info = Db::name('bank_list')->where(['list_id'=>input('param.id')])->find();
        if(empty($info))
        {
            echo "无内容";
            exit();
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 显示隐藏
     * @Author   tw
     * @DateTime 2018-09-04
     * @return   [type]     [description]
     */
    public function close()
    {
        $id = input('get.id',0);
        $type = input('get.type',0);
        if(empty($id))
        {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }

        $lender = Db::name('lender')->where(['lender_id'=>$id])->find();

        if(empty($lender))
        {
            return json(['error'=>1,'msg'=>'文章不存在']);
        }
        if($type==0)
        {
            //不显示
            $up = Db::name('lender')->where(['lender_id'=>$id])->update(['lender_use'=>0]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            return json(['error'=>0,'msg'=>'成功']);
        }
        elseif($type==1)
        {
            //启用计划
            $up = Db::name('lender')->where(['lender_id'=>$id])->update(['lender_use'=>1]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            return json(['error'=>0,'msg'=>'成功']);
        }
        
    }
}
