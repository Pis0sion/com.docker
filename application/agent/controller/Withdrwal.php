<?php
namespace app\agent\controller;
use think\Controller;
use think\Db;
class Withdrwal extends Base
{

    /**
     * 提现记录
     * @param  string $name [description]
     * @return [type]       [description]
    */
    public function index()
    {
      
        $get = input('get.');
        // 过滤分页参数、路径参数、空参数
        unset( $get['page']);
        unset( $get['pagesize']);
        array_shift($get);
        $data = array_filter($get);

        $map  = [];
        $getdata = $where =$map=array();
        if(isset($data) && !empty($data)){
            $getdata = $data;
            $map  = $this->getwhere($data);
        }

        $map[] = ['benefit_agent_id','eq',$this->agent['agent_id']];
        $list = Db::name('agentBenefit')->where($map)->paginate(10, false, ['query'=> $getdata]);

        foreach($list as $k=>$v){
           $v['cardno'] = Db::name('agentCard')->where(['card_id'=>$v['benefit_cid']])->value('card_no');
           $list->offsetSet($k,$v);
        }
        $this->assign('getdata', $getdata);
        $this->assign('list', $list);

        return view();
    }

    // 提现详情
    public function wdetail(){
       $get = input('get.');
        // 过滤分页参数、路径参数、空参数
        $getdata = $where =$map=array();
        if(isset($get) && !empty($get)){
            $getdata = $get;
            $map  = [];
            $map  = $this->profitgetwheres($get);

        }
        $id = Db::name('agentBenefit')->where(['benefit_id'=>$get['id']])->value('benefit_ids');
        $list = Db::name('agentProfit')
        ->field('profit_id,profit_uid,profit_agent_id,profit_form_no,profit_amount,profit_money,profit_user_rate,profit_type,profit_time')
        ->where('profit_id','in',$id)
        ->where($map)
        ->paginate(10, false, ['query'=> $getdata]);
        
        foreach($list as $k=>$v){
            $account = Db::name('user')->where(['user_id'=>$v['profit_uid']])->value('user_account');
            $v['profit_uid'] = getUser($v['profit_uid'],'user_name').'['.$account.']';
            $v['profit_agent_id'] = getAgent($v['profit_agent_id'],'agent_account');
            $v['profit_user_rate'] = $v['profit_user_rate']*100;

            if($v['profit_type']==1){
              $v['profit_type'] = '还款';
            }else if($v['profit_type']==2){
              $v['profit_type'] = '收款分润';
            }else if($v['profit_type']==3){
              $v['profit_type'] = '普通用户激活';
            }else if($v['profit_type']==4){
              $v['profit_type'] = '升级';
            }
            $v['profit_time'] = date('Y-m-d H:i:s', $v['profit_time']);

           $list->offsetSet($k,$v);
        }
        
        $this->assign('getdata', $getdata);
        $this->assign('list', $list);
        return view();
    }
	
  	// 查看银行卡详情
    public function crdsdetail(){
       $id = input('get.id');

       if(!$id){
          return json(['error'=>1,'msg'=>'参数错误']);
       }

       $data = Db::name('agentCard')->where(array('card_id'=>$id))->find();
       $data['bankname'] = Db::name('bankList')->where(['list_id'=>$data['card_bank_id']])->value('list_name');
       $bank = Db::name('bankList')->select();
       $this->assign('data', $data);
       $this->assign('bank', $bank);

       return view();
    }
  
    // 过滤搜索查询信息
    public function getwhere($data){
        $where = array();

        // if(isset($data['order_sn'])){
        //   $where[] = ['order_sn', 'like', '%'.$data['order_sn'].'%'];//平台订单号
        // }
        
        if(isset($data['benefit_type'])){
            if($data['benefit_type'] == '1'){
               $bentype = '0';
            }else if($data['benefit_type'] == '2'){
               $bentype = '1';

            }else if($data['benefit_type'] == '3'){
               $bentype = '2';
            }
            $where[] =  ['benefit_type', 'eq', $bentype];    // 订单状态
        }
        
        if(isset($data['starttime'])){
            if(empty($data['endtime'])){
              $where[] = ['benefit_time', 'between time', [$data['starttime'], date('Y-m-d', time())]];
            }else{
              $where[] = ['benefit_time', 'between time', [$data['starttime'], $data['endtime']]];
            }
          
        }elseif(isset($data['endtime'])){
            if(empty($data['starttime'])){
              $where[] = ['benefit_time', 'between time', ['1970-10-1', $data['endtime']]];
            }else{
              
            }
        }

        return $where;
    }

    // 分润明细 查询条件过滤
  public function profitgetwheres($keywords){
    $where = [];
    if($keywords['profit_form_no']){
            $where[] = ['profit_form_no','=',$keywords['profit_form_no']];
      }
      if($keywords['profit_type']){
            $where[] = ['profit_type','=',$keywords['profit_type']];
      }
        if(isset($keywords['starttime'])){
            if(empty($keywords['endtime'])){
              $where[] = ['profit_time', 'between time', [$keywords['starttime'], date('Y-m-d', time())]];
            }else{
              $where[] = ['profit_time', 'between time', [$keywords['starttime'], $keywords['endtime']]];
            }
          
        }elseif(isset($keywords['endtime'])){
            if(empty($keywords['starttime'])){
              $where[] = ['profit_time', 'between time', ['1970-10-1', $keywords['endtime']]];
            }else{
              
            }
        }
        return $where;
  }

   	/**
   	 * 银行卡管理
   	 * @param  string $name [description]
   	 * @return [type]       [description]
   	 */
    public function cardmag()
    {

        $res = Db::name('agentCard')->order('card_id desc')->where(array('card_agent_id'=>$this->agent['agent_id']))->select();
        foreach($res as $k=>$v){
            $res[$k]['card_bank_id'] = Db::name('bankList')->where(array('list_id'=>$v['card_bank_id']))->value('list_name');
        }

        $this->assign('data', $res);
        return view();
    }

    public function crdsadd(){
       if($this->request->isPost()) {
          $post = input('post.');
          if($post['card_name']=='' || $post['card_bank_id']=='' || $post['card_no']=='' || $post['card_phone']=='' || $post['card_province']=='' || $post['card_city']==''){
              return json(['msg'=>'请填写完整银行信息!','error'=>'1']);
          }
          $post['card_agent_id'] = $this->agent['agent_id'];
          Db::name('agentCard')->insert($post);
          return json(['msg'=>'已保存!','error'=>0]);
       }

       $bank = Db::name('bankList')->select();
       $this->assign('bank', $bank);
       return view();
    }

    /**
     * 银行卡信息修改
     * @param  string $name [description]
     * @return [type]       [description]
     */
    public function crdcags(){

       if($this->request->isPost()) {

          $post = input('post.');
          if($post['card_name']=='' || $post['card_bank_id']=='' || $post['card_no']=='' || $post['card_phone']=='' || $post['card_province']=='' || $post['card_city']==''){
              return '';
          }

          Db::name('agentCard')->update($post);
          return json(['msg'=>'操作完成!','error'=>0]);
       }

       $cid = input('get.cid');
       if($cid == ''){
          echo '参数错误!';
          exit;
       }

       $wallrecord = Db::name('agentBenefit')->where(array('benefit_cid'=>$cid))->find();

       if($wallrecord){
          echo '已使用过的银行卡不可进行操作';
          exit;
       }
       $data = Db::name('agentCard')->where(array('card_id'=>$cid))->find();
       $bank = Db::name('bankList')->select();
       $this->assign('data', $data);
       $this->assign('bank', $bank);
       return view();
    }

    /**
     * 银行卡删除
     * @param  string $name [description]
     * @return [type]       [description]
     */
    public function crddel(){
        $cid = input('get.cid');
        if($cid == ''){
            return json(['msg'=>'参数错误!','error'=>1]);
        }
        $wrecord = Db::name('agentBenefit')->where(array('benefit_cid'=>$cid))->find();
        if($wrecord){
          return json(['msg'=>'已使用过的银行卡不可进行操作!','error'=>1]);
        }

       Db::name('agentCard')->where(array('card_id'=>$cid))->delete();
       return json(['msg'=>'操作完成!','error'=>0]);
    }
}
