<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

/**
 * 文章管理
 */
class Article extends Base{
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
            $where[] = array('article_type','eq',$type);
        }
        if(isset($keywords) && !empty($keywords)){
            $where[] = array('news_title','like','%'.$keywords."%");
            $getdata['keywords'] =$keywords;
        }
        $list = Db::name('article')->alias('n')
            ->where($where)
            ->join('article_type nt','nt.type_id=n.article_type')
            ->order('article_id desc')
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
            $listimg = isset($post['listimg'])?$post['listimg']:[];
            $data['article_title'] = $post['title'];
            $data['article_body'] = $post['body'];
            $data['article_type'] = $post['type'];
            $data['article_img'] = $post['img'];
            $data['article_time'] = time();
            $data['article_uptime'] = time();
            $data['article_use'] = 1;
            $id = Db::name('article')->insertGetId($data);
            if($id)
            {
                foreach ($listimg as $key => $value) {
                    Db::name('article_img')->insert(['img_news_id'=>$id,'img_img'=>$value,'img_time'=>time()]);
                }
                return json(['error'=>0,'msg'=>'添加成功']);
            }
            return json(['error'=>1,'msg'=>'添加错误']);
        }
        $article_type = Db::name('article_type')->select();
        $this->assign('article_type',$article_type);
        $this->assign('type',input('param.type',0));
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
            $listimg = isset($post['listimg'])?$post['listimg']:[];
            $id = $post['id'];

            $data['article_title'] = $post['title'];
            $data['article_body'] = $post['body'];
            $data['article_type'] = $post['type'];
            $data['article_img'] = $post['img'];
            $data['article_uptime'] = time();
            if(Db::name('article')->where(['article_id'=>$id])->update($data))
            {
                Db::name('article_img')->where(['img_news_id'=>$id])->delete();
                foreach ($listimg as $key => $value) {
                    Db::name('article_img')->insert(['img_news_id'=>$id,'img_img'=>$value,'img_time'=>time()]);
                }
                return json(['error'=>0,'msg'=>'修改成功']);
            }
            return json(['error'=>1,'msg'=>'修改错误']);
        }
        $article = Db::name('article')->where(['article_id'=>input('param.id')])->find();
        if(empty($article))
        {
            echo "无内容";
            exit();
        }
        $listimg = Db::name('article_img')->where(['img_news_id'=>input('param.id')])->field('img_img as img')->select();
        $this->assign('listimg',$listimg);

        $article_type = Db::name('article_type')->select();
        $this->assign('article_type',$article_type);
        $this->assign('type',input('param.type',0));
        $this->assign('info',$article);
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
}
