<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

/**
 * 图片管理
 */
class Img extends Base{
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
            $where[] = array('img_type','eq',$type);
        }
        if(isset($keywords) && !empty($keywords)){
            $where[] = array('img_title','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('img')
            ->where($where)
            ->order('img_sort != 0,img_sort asc,img_id desc')
            ->paginate(10,false,['query'=> $getdata]);
        $this->assign('list',$list);
        $this->assign('type',input('param.type',0));
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
            $data['img_title'] = $post['title'];
            $data['img_type']  = $post['type'];
            $data['img_url']   = $post['url'];
            $data['img_img']   = $post['img'];
            $data['img_sort']  = $post['sort'];
            $data['img_time']  = time();
            $data['img_start_switch'] = 0;

            if(Db::name('img')->insertGetId($data))
            {
                return json(['error'=>0,'msg'=>'添加成功']);
            }
            return json(['error'=>1,'msg'=>'添加错误']);
        }
        $this->assign('type',input('param.type',0));
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
            $data['img_title'] = $post['title'];
            $data['img_type'] = $post['type'];
            $data['img_url'] = $post['url'];
            $data['img_img'] = $post['img'];
            $data['img_sort'] = $post['sort'];
            // $data['article_time'] = time();
            $data['img_start_switch'] = $post['start_switch']?:1;
            
            if(!Db::name('img')->where('img_id',$post['id'])->find()){
                return json(['error'=>1,'msg'=>'图片不存在']);
            }
            if(Db::name('img')->where(['img_id'=>$post['id']])->update($data))
            {
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $img = Db::name('img')->where(['img_id'=>input('param.id')])->find();
        if(empty($img))
        {
            echo "无内容";
            exit();
        }

        $this->assign('type',input('param.type',0));
        $this->assign($img);
        return $this->fetch();
    }

    public function close()
    {
        $id = input('get.id',0);
        $type = input('get.type',0);
        if(empty($id))
        {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }

        $article = Db::name('article')->where(['article_id'=>$id])->find();

        if(empty($article))
        {
            return json(['error'=>1,'msg'=>'文章不存在']);
        }
        if($type==0)
        {
            //不显示
            $up = Db::name('article')->where(['article_id'=>$id])->update(['article_use'=>0]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            return json(['error'=>0,'msg'=>'成功']);
        }
        elseif($type==1)
        {
            //启用计划
            $up = Db::name('article')->where(['article_id'=>$id])->update(['article_use'=>1]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'操作失败,请重试']);
            }
            return json(['error'=>0,'msg'=>'成功']);
        }
        
    }
  
  	public function imgsta()
    {
        $id = input('get.id',0);
        $type = input('get.type',0);
        if(empty($id)) {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }

        $imgs = Db::name('img')->where(['img_id'=>$id])->find();
        if(!$imgs) {
            return json(['error'=>1,'msg'=>'数据不存在']);
        }
       
        $up = Db::name('img')->where(['img_id'=>$id])->update(['img_start_switch'=>$type]);
        if(empty($up)) {
          return json(['error'=>1,'msg'=>'操作失败,请重试']);
        }
        return json(['error'=>0,'msg'=>'成功']);
        
        
    }
}
