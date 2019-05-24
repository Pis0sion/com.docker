<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;


class Order extends Base{
	public function __construct(){
       parent::__construct();
    }
    
  //   public function index(){
    	
  //   	$get = input('get.');
    	
  //   	if(isset($get['type'])){
  //   		$where[] = ['order_goods_type','=',$get['type']];
  //   	}
    	
  //   	if(isset($get['time'])){
  //   		$where[] = ['order_goods_type','between time',['2015-1-1', '2016-1-1']];
  //   	}
    	
  //   	if(isset($get['keywords'])){
  //   		$where[] = ['order_goods_type','like','%'.$get['keywords'].'%'];
  //   	}
    	
  //   	$list = Db::name('order')
		// ->alias('a')
		// ->join('goods g','a.order_goods=g.goods_id')
		// ->join('ordercommon c','a.order_id=c.com_order')
		// ->paginate(10);
		// // 把分页数据赋值给模板变量list
		// $this->assign('list', $list);
		// // 渲染模板输出
		// return $this->fetch();
  //   }
	/*
	 * 商品发货
	 * 2018年10月9日11:27:09
	 * 刘媛媛
	 */
    public function state()
    {
        $id = input('get.id',0);
        $code = input('get.code','自提');
        if(empty($id)){
            return json(['error'=>1,'msg'=>'信息不完整']);
        }

        $order = Db::name('order')->where(['order_id'=>$id])->find();

        if(empty($order)){
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        
        if($order['order_order_state']!=20){
            return json(['error'=>1,'msg'=>'订单状态不允许发货']);
        }
        
        
        
        $up =  Db::name('order')->where(['order_id'=>$id])->update(['order_order_state'=>30,'order_code'=>$code]);
        if(empty($up)){
            return json(['error'=>1,'msg'=>'操作失败,请重试']);
        }
        
        //是否增加推送
        return json(['error'=>0,'msg'=>'成功']);
    }
	
	/*
	 * 订单删除
	 * 2018年10月9日11:33:24
	 * 刘媛媛
	 */
    public function delete(){
    
        $id = input('get.id',0);
        if(empty($id))
        {
            return json(['error'=>1,'msg'=>'信息不完整']);
        }

        $order = Db::name('order')->where(['order_id'=>$id])->find();

        if(empty($order)){
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        if($order['order_order_state']>10){
        	return json(['error'=>1,'msg'=>'订单状态不允许删除']);
        }
        
        if((time()-$order['order_time'])>(86400*7)){
        	Db::name('order')->where(['order_id'=>$id])->delete();
       		Db::name('ordercommon')->where(['com_order'=>$id])->delete();
        }else{
        	return json(['error'=>1,'msg'=>'订单超过7天允许删除']);
        }
       
    }
    /*
     * 强制收货完成
     * 2018年10月9日11:36:40
     * 刘媛媛
     */
    public function confirmg(){
    	
    	$id = input('get.id',0);
        if(empty($id)){
            return json(['error'=>1,'msg'=>'信息不完整']);
        }

        $order = Db::name('order')->where(['order_id'=>$id])->find();

        if(empty($order)){
            return json(['error'=>1,'msg'=>'订单不存在']);
        }
        if($order['order_order_state']!=30){
        	return json(['error'=>1,'msg'=>'订单状态不允许收货']);
        }
    	
    	if($order['order_order_state']!=30){
        	return json(['error'=>1,'msg'=>'订单状态不允许收货']);
        }
        //确认收货函数
        confirmOrder($id);
        return json(['error'=>0,'msg'=>'操作完成']);
    }
    
    
}
