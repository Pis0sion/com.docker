<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\captcha\Captcha;
use app\admin\model\Admin;

class Coupon extends Base{
	public function __construct(){
       parent::__construct();
    }

    // 优惠券列表
    public function coulist(){

        if($this->request->isPost()){
            $keywords = request()->param('keywords');
            $getdata = $where = $whereor=array();
            if(isset($keywords) && !empty($keywords)){
                //$where[]   = array('user_name','=',$keywords);
                $whereor[] = array('cou_name','like','%'.$keywords."%");
                $getdata['keywords'] =$keywords;
            }

            $list = Db::name('coupon')->where($whereor)
                ->order('cou_value desc')
                ->paginate(10,false,['query'=> $getdata]);
            $this->assign('list',$list);
            return $this->fetch();

        }
          
        $list = Db::name('coupon')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    // 添加优惠券
    public function addconp(){
        
        if($this->request->isPost()){
            $post = input('post.');
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            if($post['rule']=='3'){
                $userType = Db::name('coupon')->where(['cou_rule'=>'3','cou_user_type'=>$post['user_type']])->find();
                if($userType){
                    return json(['error'=>1,'msg'=>'会员升级类型ID重复']);
                }
                $data['cou_user_type'] = $post['user_type'];
            }else{
                $post['user_type']     = 0;
            }

            //数据添加
            $data['cou_name']       = $post['name'];
            $data['cou_type']       = $post['type'];
            $data['cou_value']      = $post['value'];
            $data['cou_time']       = $post['time'];
            $data['cou_rule']       = $post['rule'];
            $data['cou_state']      = 0;
            $res = Db::name('coupon')->insertGetId($data);
            if($res){
                return json(['error'=>0,'msg'=>'新增优惠券成功']);
            }else{
                return json(['error'=>1,'msg'=>'添加失败']);
            }
        }

        $utyp = Db::name('userType')->select();
        $this->assign('utyp', $utyp);
        return $this->fetch();
    }

    // 修改优惠券
    public function upconp(){
        
        if($this->request->isPost()){
            $post = input('post.');
            // dump($post);
            if(empty($post)){
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            if($post['rule']=='3'){
                $userType = Db::name('coupon')->where(['cou_rule'=>'3','cou_user_type'=>$post['user_type']])->find();
                if($userType){
                    return json(['error'=>1,'msg'=>'会员升级类型ID重复']);
                }
                $data['cou_user_type'] = $post['user_type'];
            }else{
                $post['user_type']     = 0;
            }

            //数据整理
            $data['cou_id']    = $post['cou_id'];
            $data['cou_name']  = $post['name'];
            $data['cou_type']  = $post['type'];
            $data['cou_value'] = $post['value'];
            $data['cou_time']  = $post['time'];
            $data['cou_rule']  = $post['rule']; 
            $res = Db::name('coupon')->update($data);
            if($res){
                return json(['error'=>0,'msg'=>'修改成功']);
            }else{
                return json(['error'=>1,'msg'=>'修改失败']);
            }
        }
        
        $id = input('get.id');
        $conp = Db::name('coupon')->where(['cou_id'=>$id])->find();
        $utyp = Db::name('userType')->select();
        $this->assign('utyp', $utyp);
        $this->assign('conp', $conp);
        return $this->fetch();
    }

    // 更改优惠券状态
    public function conpsta(){
        $id = intval(input("get.id"));

        if(empty($id)){
            return json(array(
                'msg'   => "参数错误!",
                'error' => 1
            ));
        }

        $auth = Db::name("coupon")->where(array('cou_id'=>$id))->find();
        if(!$auth) {    
            return json(array(
                'msg'   => "您要设置的权限不存在！",
                'error' => 1
            ));
        }

        if($auth['cou_state'] == 1) {
            Db::name("coupon")->where(array('cou_id'=>$id))->update(array('cou_state' => 0));
        } else {
            Db::name("coupon")->where(array('cou_id'=>$id))->update(array('cou_state' => 1));
        }
        return json(array(
            'msg'   => "设置成功！",
            'error' => 0
        ));
    }

    // 券使用记录列表
    public function usedcoulist(){
        if($this->request->isPost()){

            $keywords = request()->param('keywords');
            $getdata = $where = $whereor=array();
            if(isset($keywords) && !empty($keywords)){
                $uid = Db::name('user')->where(['user_account'=>$keywords])->value('user_id');
                $where[]   = array('coul_user','=',$uid);
                $getdata['keywords'] = $keywords;
            }

            $list = Db::name('couponLog')
                ->where($where)
                ->order('coul_id desc')
                ->paginate(10);
            // dump($list);
            $this->assign('list',$list);
            return $this->fetch();
        }
        
        
        $list = Db::name('couponLog')->order('coul_id desc')->select();
        foreach($list as $k=>$v){ 
            $list[$k]['coul_cou'] = Db::name('coupon')->where(['cou_id'=>$v['coul_cou']])->value('cou_name');
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    // 删除未使用 和已过期的代金券
    public function coudels(){
        $id = intval(input("get.id"));
        if($id == 0) {
            return json(array(
                'msg'   => "参数错误",
                'error' => 1
            ));
        }
        $auth = Db::name("couponLog")->where(array('coul_id'=>$id))->find();
        if(!$auth) {
            return json(array(
                'msg'   => "您要删除的数据不存在或已删除！",
                'error' => 1
            ));
        }
        $auth = Db::name("couponLog")->where(array('coul_id'=>$id))->delete();
        return json(array(
            'msg'   => "删除成功！",
            'error' => 0
        ));
    }
}
