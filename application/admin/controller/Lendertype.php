<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

/**
 * 文章分类管理
 */
class Lendertype extends Base{
	public function __construct(){
       parent::__construct();
    }
    
    public function index()
    {
        $keywords = request()->param('keywords');
        $getdata = $where = $whereor=array();
        if(isset($keywords) && !empty($keywords)){
            $whereor[] = array('type_name','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('lender_type')
            ->where($where)->whereor($whereor)
            ->order('type_id desc')
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
            $post = $_POST;
            if(Db::name('lender_type')->insertGetId(['type_name'=>$post['type_name'],'type_time'=>time()]))
            {
                return json(['error'=>0,'msg'=>'添加成功']);
            }
            return json(['error'=>1,'msg'=>'添加错误']);
        }

        $lender_type = Db::name('lender_type')->select();
        $this->assign('lender_type',$lender_type);
        return $this->fetch();
    }

    /**
     * 编辑分类
     * @Author   tw
     * @DateTime 2018-09-03
     * @return   [type]     [description]
     */
    public function edit()
    {
        if($this->request->isPost())
        {
            $post = $_POST;
            $data['type_name'] = $post['type_name'];
            // $data['article_time'] = time();
            if(Db::name('lender_type')->where(['type_id'=>$post['id']])->update($data))
            {
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $lender_type = Db::name('lender_type')->where(['type_id'=>input('param.id')])->find();
        if(empty($lender_type))
        {
            echo "无分类";
            exit();
        }
        $this->assign($lender_type);
        return $this->fetch();
    }
}
