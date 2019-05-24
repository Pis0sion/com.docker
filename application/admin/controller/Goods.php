<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Goods extends Base{
	public function __construct(){
       parent::__construct();
    }
    
    // public function index()
    // {
    //     $keywords = request()->param('keywords');
    //     $type = request()->param('type');
    //     $getdata = $where = $whereor=array();
    //     if($type)
    //     {
    //         $where[] = array('goods_type_id','eq',$type);
    //     }
    //     if(isset($keywords) && !empty($keywords)){
    //         $where[] = array('news_title','like','%'.$keywords."%");
    //         $getdata['keywords'] =$keywords;
    //     }
    //     $list = Db::name('goods')->alias('g')
    //         ->where($where)
    //         ->join('goods_type gt','gt.type_id=g.goods_type_id')
    //         ->order('goods_id desc')
    //         ->paginate(10,false,['query'=> $getdata]);
    //     $this->assign('list',$list);
    //     $this->assign('type',input('param.type',0));
    //     return $this->fetch();
    // }

    /**
     * 添加
     * @Author   tw
     * @DateTime 2018-08-31
     */
    // public function add()
    // {
    //     if($this->request->isPost()){
    //         $post = input('post.');
    //         if(!$post){
    //             return json(['error'=>0,'msg'=>'参数错误！']);
    //         }

    //         $img = array($post['Mulimg1'],$post['Mulimg2'],$post['Mulimg3'],$post['Mulimg4']);
    //         $imgs = array_filter($img);

    //         if($post['title']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['type']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['state']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['salenum']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['storage']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['commend']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['marketprice']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['goods_type']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['distribution']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['give_integral']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['distribution_type']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['goods_distribution_1']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['goods_distribution_2']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['goods_distribution_3']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['body']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['img']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if(!$imgs){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }

    //         $data['goods_title']             = $post['title'];
    //         $data['goods_body']              = $post['body'];
    //         $data['goods_type_id']           = $post['type'];
    //         $data['goods_img']               = $post['img'];
    //         $data['goods_integral']          = $post['integral'];
    //         $data['goods_time']              = time();
    //         $data['goods_state']             = $post['state'];
    //         $data['goods_jingle']            = $post['goods_jingle'];
    //         $data['goods_price']             = $post['goods_price'];
    //         $data['goods_salenum']           = $post['salenum'];
    //         $data['goods_storage']           = $post['storage'];
    //         $data['goods_commend']           = $post['commend'];
    //         $data['goods_marketprice']       = $post['marketprice'];
    //         $data['goods_type']              = $post['goods_type'];
    //         $data['goods_distribution']      = $post['distribution'];
    //         $data['goods_give_integral']     = $post['give_integral'];
    //         $data['goods_distribution_type'] = $post['distribution_type'];
    //         $data['goods_distribution_1']    = $post['goods_distribution_1'];
    //         $data['goods_distribution_2']    = $post['goods_distribution_2'];
    //         $data['goods_distribution_3']    = $post['goods_distribution_3'];
    //         $data['goods_imgmore']           = json_encode($imgs);
    //         $id = Db::name('goods')->insertGetId($data);
    //         if($id){
    //             return json(['error'=>0,'msg'=>'添加成功']);
    //         }
    //         return json(['error'=>1,'msg'=>'添加错误']);
    //     }
    //     $goods_type = Db::name('goods_type')->select();
    //     $this->assign('goods_type',$goods_type);
    //     $this->assign('type',input('param.type',0));
    //     return $this->fetch();
    // }
    /**
     * 编辑文章
     * @Author   tw
     * @DateTime 2018-09-03
     * @return   [type]     [description]
     */
    // public function edit()
    // {
    //     if($this->request->isPost()){
    //         $post = input('post.');
    //         // dump($post);
    //         // exit;
    //         if(!$post){
    //             return json(['error'=>0,'msg'=>'参数错误！']);
    //         }

    //         $id = $post['id'];
    //         $img = array($post['Mulimg1'],$post['Mulimg2'],$post['Mulimg3'],$post['Mulimg4']);
    //         $gimg = array_filter($img);

    //         if($post['title']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['type']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['state']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['salenum']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['storage']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['commend']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['marketprice']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['goods_type']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['distribution']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['give_integral']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['distribution_type']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['goods_distribution_1']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['goods_distribution_2']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['goods_distribution_3']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['body']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if($post['img']==''){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }
    //         if(!$gimg){
    //             return json(['error'=>1,'msg'=>'必填项不可为空']);
    //         }

    //         $data['goods_title']             = $post['title'];
    //         $data['goods_body']              = $post['body'];
    //         $data['goods_type_id']           = $post['type'];
    //         $data['goods_img']               = $post['img'];
    //         $data['goods_integral']          = $post['integral'];
    //         $data['goods_uptime']            = time();
    //         $data['goods_state']             = $post['state'];
    //         $data['goods_jingle']            = $post['goods_jingle'];
    //         $data['goods_price']             = $post['goods_price'];
    //         $data['goods_salenum']           = $post['salenum'];
    //         $data['goods_storage']           = $post['storage'];
    //         $data['goods_commend']           = $post['commend'];
    //         $data['goods_marketprice']       = $post['marketprice'];
    //         $data['goods_type']              = $post['goods_type'];
    //         $data['goods_distribution']      = $post['distribution'];
    //         $data['goods_give_integral']     = $post['give_integral'];
    //         $data['goods_distribution_type'] = $post['distribution_type'];
    //         $data['goods_distribution_1']    = $post['goods_distribution_1'];
    //         $data['goods_distribution_2']    = $post['goods_distribution_2'];
    //         $data['goods_distribution_3']    = $post['goods_distribution_3'];
    //         $data['goods_imgmore']           = json_encode($gimg);
    //         $id = Db::name('goods')->where(['goods_id'=>$id])->update($data);
    //         if($id){
    //             return json(['error'=>0,'msg'=>'修改成功']);
    //         }
    //         return json(['error'=>1,'msg'=>'修改错误']);
    //     }
    //     $goods = Db::name('goods')->where(['goods_id'=>input('param.id')])->find();
    //     $imgs = json_decode($goods['goods_imgmore'], true);
        
    //     if(empty($goods)){
    //         echo "商品错误！";
    //         exit();
    //     }
    //     $goods_type = Db::name('goods_type')->select();
    //     $this->assign('goods_type',$goods_type);
    //     $this->assign('type',input('param.type',0));
    //     $this->assign('info',$goods);
    //     $this->assign('imgs', $imgs);
    //     return $this->fetch();
    // }

    /**
     * 显示隐藏
     * @Author   tw
     * @DateTime 2018-09-04
     * @return   [type]     [description]
     */
    // public function close()
    // {
    //     $id = input('get.id',0);
    //     $type = input('get.type',0);
    //     if(empty($id))
    //     {
    //         return json(['error'=>1,'msg'=>'信息不完整']);
    //     }

    //     $goods = Db::name('goods')->where(['goods_id'=>$id])->find();

    //     if(empty($goods))
    //     {
    //         return json(['error'=>1,'msg'=>'文章不存在']);
    //     }
    //     if($type==0)
    //     {
    //         //不显示
    //         $up = Db::name('goods')->where(['goods_id'=>$id])->update(['goods_state'=>2]);
    //         if(empty($up))
    //         {
    //             return json(['error'=>1,'msg'=>'操作失败,请重试']);
    //         }
    //         return json(['error'=>0,'msg'=>'成功']);
    //     }
    //     elseif($type==1)
    //     {
    //         //启用计划
    //         $up = Db::name('goods')->where(['goods_id'=>$id])->update(['goods_use'=>1]);
    //         if(empty($up))
    //         {
    //             return json(['error'=>1,'msg'=>'操作失败,请重试']);
    //         }
    //         return json(['error'=>0,'msg'=>'成功']);
    //     }
        
    // }
}
