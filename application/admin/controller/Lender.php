<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

/**
 * 网申管理
 */
class Lender extends Base{
	public function __construct(){
       parent::__construct();
    }
    
    public function index()
    {
        $keywords = request()->param('keywords');
        $type = request()->param('type');
        $getdata = $where = $whereor=array();
        if($type)
        {
            $where[] = array('lender_type','eq',$type);
        }
        if(isset($keywords) && !empty($keywords)){
            $where[] = array('lender_title','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('lender')->alias('l')
            ->where($where)
            ->join('lender_type lt','lt.type_id=l.lender_type_id')
            ->order('lender_id desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('type',input('param.type',0));
        return $this->fetch();
    }

    /**
     * 添加文章
     * @Author   tw
     * @DateTime 2018-08-31
     */
    public function add()
    {
        if($this->request->isPost())
        {
            $post = $_POST;
            if(empty($post['type_id']))
            {
                return json(['error'=>1,'msg'=>'请选择平台']);
            }
            $data['lender_type_id'] = $post['type_id'];
            $data['lender_title'] = $post['title'];
            $data['lender_type'] = $post['type'];
            $data['lender_img'] = $post['img'];
            $data['lender_describe'] = $post['describe'];
            $data['lender_url'] = $post['url'];
            $data['lender_state'] = $post['state'];
            $data['lender_sort'] = $post['sort'];
            $data['lender_time'] = time();
            $data['lender_use'] = 1;

            if(Db::name('lender')->insertGetId($data))
            {
                return json(['error'=>0,'msg'=>'添加成功']);
            }
            return json(['error'=>1,'msg'=>'添加错误']);
        }
        $lender_type = Db::name('lender_type')->select();
        $this->assign('lender_type',$lender_type);
        $this->assign('type',input('param.type',0));
        $this->assign('type_id',input('param.type_id',0));
        return $this->fetch();
    }
    /**
     * 编辑文章
     * @Author   tw
     * @DateTime 2018-09-03
     * @return   [type]     [description]
     */
    public function edit()
    {
        if($this->request->isPost())
        {
            $post = $_POST;
            $data['lender_type_id'] = $post['type_id'];
            $data['lender_title'] = $post['title'];
            $data['lender_type'] = $post['type'];
            $data['lender_img'] = $post['img'];
            $data['lender_describe'] = $post['describe'];
            $data['lender_url'] = $post['url'];
            $data['lender_state'] = $post['state'];
            $data['lender_sort'] = $post['sort'];

            if(Db::name('lender')->where(['lender_id'=>$post['id']])->update($data))
            {
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $lender = Db::name('lender')->where(['lender_id'=>input('param.id')])->find();
        if(empty($lender))
        {
            echo "无内容";
            exit();
        }

        $lender_type = Db::name('lender_type')->select();
        $this->assign('lender_type',$lender_type);
        $this->assign('type',input('param.type',0));
        $this->assign('info',$lender);
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
