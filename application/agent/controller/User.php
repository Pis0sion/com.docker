<?php
namespace app\agent\controller;
use think\Controller;
use think\facade\Session;
use think\Db;

class User extends Base
{   
    /**
      *下级代理
    */
    public function index()
    {
      $account = isset($_GET['account'])?trim($_GET['account']):'';
      $name    = isset($_GET['name'])?trim($_GET['name']):'';
      $pid     = intval($_GET['pid']);
      
      if(empty($pid)){
         $this->error('参数错误!');
      }

      if(!empty($account)&&empty($name)){
        $map[] = ['agent_account','like','%'.$account.'%'];
      }
      if(empty($account)&&!empty($name)){
        $map[] = ['agent_name','like','%'.$name.'%'];
        
      }
      if(!empty($account)&&!empty($name)){
        $map[] = ['agent_account','like','%'.$account.'%'];
        $map[] = ['agent_name','like','%'.$name.'%'];
      }
      $map[] = ['agent_pid','eq',$pid];

      $list = Db::name('Agent')
      ->where($map)
      ->paginate();

      $todymoney = Db::name('AgentProfit')->where('profit_time','>',strtotime(date("Y-m-d"),time()))->where('profit_agent_id',$pid)->sum('profit_amount');
      //今日分润
      $todyprofit = Db::name('AgentProfit')->where('profit_time','>',strtotime(date("Y-m-d"),time()))->where('profit_agent_id',$pid)->sum('profit_money');
      //总消费额
      $sunmoney = Db::name('AgentProfit')->where('profit_agent_id',$pid)->sum('profit_amount');
      //总分润
      $sunprofit = Db::name('AgentProfit')->where('profit_agent_id',$pid)->sum('profit_money');
      $this->assign('todymoney',$todymoney);
      $this->assign('todyprofit',$todyprofit);
      $this->assign('sunmoney',$sunmoney);
      $this->assign('sunprofit',$sunprofit);
      $this->assign('account',$account);
      $this->assign('name',$name);
      $this->assign('list',$list);
      return view();
    }
  
  	// 个人推广码
    public function excode(){
        $code = Db::name('agent')->where('agent_id',session::get('agent_id'))->value('agent_code');
        if(!$code){
            return json(['msg'=>'数据不存在!','error'=>'0']);
        }
        $url = 'http://'.$_SERVER['HTTP_HOST'].'?id='.$code;

        $this->assign('urls',$url);
        return view();
    }

    // 代理商管理
    public function agentlist(){
        $account  = isset($_GET['account'])?trim($_GET['account']):'';
        $name     = isset($_GET['name'])?trim($_GET['name']):'';
        $pid      = session::get('agent_id');

        if(!empty($account)&&empty($name)){
          $map[] = ['agent_account','like','%'.$account.'%'];
        }
        if(empty($account)&&!empty($name)){
          $map[] = ['agent_name','like','%'.$name.'%'];
          
        }
        if(!empty($account)&&!empty($name)){
          $map[] = ['agent_account','like','%'.$account.'%'];
          $map[] = ['agent_name','like','%'.$name.'%'];
        }
        $map[] = ['agent_pid','eq',$pid];

        $list = Db::name('Agent')
        ->where($map)
        ->paginate();

        // $todymoney = Db::name('AgentProfit')->where('profit_time','>',strtotime(date("Y-m-d"),time()))->where('profit_agent_id',$pid)->sum('profit_amount');
        // //今日分润
        // $todyprofit = Db::name('AgentProfit')->where('profit_time','>',strtotime(date("Y-m-d"),time()))->where('profit_agent_id',$pid)->sum('profit_money');
        // //总消费额
        // $sunmoney = Db::name('AgentProfit')->where('profit_agent_id',$pid)->sum('profit_amount');
        // //总分润
        // $sunprofit = Db::name('AgentProfit')->where('profit_agent_id',$pid)->sum('profit_money');
        // $this->assign('todymoney',$todymoney);
        // $this->assign('todyprofit',$todyprofit);
        // $this->assign('sunmoney',$sunmoney);
        // $this->assign('sunprofit',$sunprofit);

        $this->assign('account',$account);
        $this->assign('name',$name);
        $this->assign('list',$list);
        return view();
    }

    // 删除代理商
    public function agent_del(){
       $aid = input('get.aid');
       if($aid == ''){
           return json(['msg'=>'参数错误!','error'=>1]);
       }

       Db::name('Agent')->where(array('agent_id'=>$aid))->delete();
       return json(['msg'=>'操作完成!','error'=>0]);
    }

    /**
     * 用户列表
     */
    public function userlist(){
        $account = isset($_GET['account'])?trim($_GET['account']):'';
        $name    = isset($_GET['name'])?trim($_GET['name']):'';
        $pid     = session::get('agent_id');

        if(!empty($account)&&empty($name)){
          $map[] = ['user_account','like','%'.$account.'%'];
        }
        if(empty($account)&&!empty($name)){
          $map[] = ['user_name','like','%'.$name.'%'];
          
        }
        if(!empty($account)&&!empty($name)){
          $map[] = ['user_account','like','%'.$account.'%'];
          $map[] = ['user_name','like','%'.$name.'%'];
        }
        $map[] = ['user_agent_id','eq',$pid];
        $list = Db::name('User as u')->join('UserType t','u.user_type_id=t.type_id')->where($map)->paginate();
        // $list = Db::name('User')->where($map)->paginate();

        $this->assign('account',$account);
        $this->assign('name',$name);
        $this->assign('pid',$pid);
        $this->assign('list',$list);
        return view();
    }

    // 个人资料
    public function info(){

        if($this->request->isPost()) {
            $post = input('post.');

            if($post['agent_name']=='' || $post['agent_phone']=='' || $post['agent_idcard']==''){
                return json(['msg'=>'请填写完整信息!','error'=>'1']);
            }
            Db::name('agent')->where(array('agent_id'=>$this->agent['agent_id']))->update($post);
            return json(['msg'=>'已保存!','error'=>0]);
        }

        $list = Db::name('agent')->where(array('agent_id'=>$this->agent['agent_id']))->find();
        $this->assign('data', $list);
      
        return view();

        
    }

    // 我的成本
    public function getagreta(){
        $list = Db::name('agentRate')->where(['rate_agent_id'=>session::get('agent_id')])->select();
        if(!$list){
            return json(['error'=>1, 'msg'=>'费率未设置']);
        }
        foreach ($list as $k=>$v){
            $rate = $v['rate_rate']*100;
            switch ($v['rate_type']) {
                case 1:
                  $list[$k]['rate_rate'] = $rate.'%';
                  $list[$k]['rate_type'] = '还款';
                break;
                case 2:
                  $list[$k]['rate_rate'] = $rate.'%';
                  $list[$k]['rate_type'] = '收款';
                break;
                case 3:
                  $list[$k]['rate_rate'] = $rate.'%';
                  $list[$k]['rate_type'] = '升级';
                break;
            }
            $list[10]['rate_type'] = '承载量';
            $list[10]['rate_rate'] = '总计'.$this->agent['agent_capacity'].',可分配'.$this->agent['agent_can_allot'];
        }
       
        $this->assign('list',$list);
        return view();
    }

    /*
    * 修改账户密码
    *
    */
    public function updapass(){
        if($this->request->isPost()) {
            $post = input('post.');
            if($post['pass']=='' || $post['repass']=='' || $post['ispass']==''){
                return json(['msg'=>'请填写完整信息!','error'=>'1']);
            }

            if(md5($post['pass']) !== $this->agent['agent_password']){
                return json(['msg'=>'原密码输入错误!','error'=>'1']);
            }

            if($post['pass'] == $post['repass']){
                return json(['msg'=>'新密码不可与原密码一致!','error'=>'1']);
            }

            if($post['repass'] !== $post['ispass']){
                return json(['msg'=>'确认密码输入不一致!','error'=>'1']);
            }

            $passwd = md5($post['repass']);
            Db::name('agent')->where(array('agent_id'=>$this->agent['agent_id']))->update(array('agent_password'=>$passwd));

            // 清除用户登录信息，重新登录
            Session::clear();
            session_unset();

            return json(['msg'=>'操作完成!','error'=>'0']);
        }

        return view();
    }
   	/**
   	 * 首页报表
   	 * @param  string $name [description]
   	 * @return [type]       [description]
   	 */
    public function console()
    {
        return view();
    }

    /**
     * 下级客户
     */
    public function User(){
      $account = isset($_GET['account'])?trim($_GET['account']):'';
      $name    = isset($_GET['name'])?trim($_GET['name']):'';
      $pid     = isset($_GET['pid'])?intval($_GET['pid']):0;

      if(!empty($account)&&empty($name)){
        $map[] = ['u.user_account','like','%'.$account.'%'];
      }
      if(empty($account)&&!empty($name)){
        $map[] = ['u.user_name','like','%'.$name.'%'];
        
      }
      if(!empty($account)&&!empty($name)){
        $map[] = ['u.user_account','like','%'.$account.'%'];
        $map[] = ['u.user_name','like','%'.$name.'%'];
      }
      $map[] = ['u.user_agent_id','eq',$pid];
      $list = Db::name('User as u')
      ->join('UserType t','u.user_type_id=t.type_id')
      ->where($map)
      ->paginate();
      
      $pname = Db::name('user')->where('user_id',$pid)->value('user_name');
      if(!$pname){
      	 $pname = Db::name('user')->where('user_id',$pid)->value('user_account');
      }
      $this->assign('pname',$pname);
      $this->assign('account',$account);
      $this->assign('name',$name);
      $this->assign('pid',$pid);
      $this->assign('list',$list);
      return view();
    }
    /**
     * 新增代理
     */
    public function Addagent(){
      $AGENT_GRADE = getconfig('AGENT_GRADE');

      if($this->request->isPost()){

        $post = input('post.');
        if(empty($post)){
          return json(['error'=>1,'msg'=>'参数错误']);
        }

        $list = Db::name('AgentRate')->where('rate_agent_id',session::get('agent_id'))->select();
        if(empty($list)){
          return json(['error'=>1,'msg'=>'你还未有费率，暂时不能进行添加代理']);
        }

        if($AGENT_GRADE == 1)
        {
          $grade_id = $post['grade_id'];
          $grate = Db::name('AgentGrade')->where(['grade_id'=>$grade_id,'grade_state'=>0])->find();

          if(empty($grate)){
            return json(['error'=>1,'msg'=>'该等级费率不存在或已停用']);
          }

          $hk = trim($grate['grade_rate']);
          $sk = trim($grate['grade_rate_close']);
          $sj = trim($grate['grade_rate_upgrade']);
          $capacity = trim($grate['grade_capacity']);
          $agent_grade = $grade_id;

        }else{

          $hk = trim($post['hk']);
          $sk = trim($post['sk']);
          $sj = trim($post['sj']);
          $capacity = trim($post['capacity']);
          $agent_grade = 0;
        }

        $arr = array();
        foreach ($list as $va) {
          $arr[$va['rate_type']] = $va['rate_rate'];
        }
        if($hk <= $arr['1']){
          return json(['error'=>1,'msg'=>'下级还款费率不能低于你当前的还款费率']);
        }
        if($sk <= $arr['2']){
          return json(['error'=>1,'msg'=>'下级收款费率不能低于你当前的收款费率']);
        }
        if($sj!=0){
          if($sj >= $arr['3']){
            return json(['error'=>1,'msg'=>'下级会员升级费率不能高于你当前的会员升级费率']);
          }
        }
        $agentacc = Db::name('Agent')->where(['agent_account'=>trim($post['account']),'agent_name'=>trim($post['name'])])->find();
        if($agentacc){
          return json(['error'=>1,'msg'=>'该代理已存在']);
        }
        $agent = Db::name('Agent')->where('agent_id',session::get('agent_id'))->find();
        if($capacity!=0){
          if($agent['agent_can_allot'] < $capacity){

              return json(['error'=>1,'msg'=>'您的可用承载量不足，无法进行下级代理划分']);
          }
          Db::name('Agent')->where('agent_id',session::get('agent_id'))->setDec('agent_can_allot',$capacity);
        }
        //数据添加.
        $data = array();
        $data['agent_code']      = rand(1000,9999);
        $data['agent_pid']       = session::get('agent_id');
        $data['agent_account']   = trim($post['account']);
        $data['agent_password']  = md5(trim($post['password']));
        $data['agent_name']      = trim($post['name']);
        $data['agent_phone']     = trim($post['phone']);
        $data['agent_idcard']    = trim($post['idcard']);
        $data['agent_capacity']  = $capacity;
        $data['agent_can_allot'] = $capacity;
        $data['agent_state']     = 0;
        $data['agent_time']      = time();
        $data['agent_ip']        = get_client_ip6();
        $data['agent_grade']     = $agent_grade;
        $res = Db::name('Agent')->insertGetId($data);
        if($res){
          $result1 = Db::name('AgentRate')->insert(['rate_agent_id'=>$res,'rate_rate'=>$hk,'rate_type'=>1,'rate_time'=>time()]);
          $result2 = Db::name('AgentRate')->insert(['rate_agent_id'=>$res,'rate_rate'=>$sk,'rate_type'=>2,'rate_time'=>time()]);
          $result3 = Db::name('AgentRate')->insert(['rate_agent_id'=>$res,'rate_rate'=>$sj,'rate_type'=>3,'rate_time'=>time()]);
          if($result1&&$result2&&$result3){

            return json(['error'=>0,'msg'=>'添加成功']);
          }

        }else{

          return json(['error'=>1,'msg'=>'添加失败']);
        }

      }else{

        $list = Db::name('AgentGrade')->where('grade_state',0)->where('grade_sort','>',$this->agent['agent_grade'])->select();
        
        $this->assign('AGENT_GRADE',$AGENT_GRADE);
        $this->assign('list',$list);
        return view();
      }
    }

    /**
     * 代理信息修改
     */
    public function Upagent(){
      $AGENT_GRADE = getconfig('AGENT_GRADE');
      if($this->request->isPost()){

        $post = input('post.');
        if(empty($post)){
          return json(['error'=>1,'msg'=>'参数错误']);
        }

        $list = Db::name('AgentRate')->where('rate_agent_id',session::get('agent_id'))->select();
        if(empty($list)){
          return json(['error'=>1,'msg'=>'你还未有费率，暂时不能进行修改代理']);
        }

        if($AGENT_GRADE == 1)
        {
          $grade_id = $post['grade_id'];
          $grate = Db::name('AgentGrade')->where(['grade_id'=>$grade_id,'grade_state'=>0])->find();

          if(empty($grate)){
            return json(['error'=>1,'msg'=>'该等级费率不存在或已停用']);
          }

          $hk = trim($grate['grade_rate']);
          $sk = trim($grate['grade_rate_close']);
          $sj = trim($grate['grade_rate_upgrade']);
          $capacity = trim($grate['grade_capacity']);
          $agent_grade = $grade_id;

        }else{

          $hk = trim($post['hk']);
          $sk = trim($post['sk']);
          $sj = trim($post['sj']);
          $capacity = trim($post['capacity']);
          $agent_grade = 0;
        }

        $arr = array();
        foreach ($list as $va) {
          $arr[$va['rate_type']] = $va['rate_rate'];
        }
        if($hk <= $arr['1']){
          return json(['error'=>1,'msg'=>'下级还款费率不能低于你当前的还款费率']);
        }
        if($sk <= $arr['2']){
          return json(['error'=>1,'msg'=>'下级收款费率不能低于你当前的收款费率']);
        }
        if($sj!=0){
          if($sj >= $arr['3']){
            return json(['error'=>1,'msg'=>'下级会员升级费率不能高于你当前的会员升级费率']);
          }
        }

        $sonagent = Db::name('Agent')->where(['agent_id'=>$post['agid'],'agent_pid'=>session::get('agent_id')])->find();
        if(empty($sonagent)){
          return json(['error'=>1,'msg'=>'查无此代理信息']);
        }

        $types = isset($post['types'])?$post['types']:'';
        if($types=='add')
        {
          if($capacity > $this->agent['agent_can_allot']){

            return json(['error'=>1,'msg'=>'您的可用承载量不足，无法进行下级代理划分']);
          }
          $result3 = Db::name('Agent')->where('agent_id',session::get('agent_id'))->setDec('agent_can_allot',$capacity);
          Db::name('Agent')->where('agent_id',$post['agid'])->setInc('agent_can_allot',$capacity);
        }elseif($types == 'del'){

          if($capacity > $sonagent['agent_can_allot']){

            return json(['error'=>1,'msg'=>'该代理可用承载量不足，无法进行下级代理划分']);
          }
          $result3 = Db::name('Agent')->where('agent_id',session::get('agent_id'))->setInc('agent_can_allot',$capacity);
          Db::name('Agent')->where('agent_id',$post['agid'])->setDec('agent_can_allot',$capacity);
        }else{

          if($capacity < $sonagent['agent_can_allot'])
          {
            $result3 = Db::name('Agent')->where('agent_id',session::get('agent_id'))->setInc('agent_can_allot',$sonagent['agent_can_allot']-$capacity);
            Db::name('Agent')->where('agent_id',$post['agid'])->setDec('agent_can_allot',$sonagent['agent_can_allot']-$capacity);
          }else{
            $result3 = Db::name('Agent')->where('agent_id',session::get('agent_id'))->setDec('agent_can_allot',$capacity-$sonagent['agent_can_allot']);
            Db::name('Agent')->where('agent_id',$post['agid'])->setInc('agent_can_allot',$capacity-$sonagent['agent_can_allot']);
          }

        }

        $data = array();
        $data['agent_name']      = trim($post['name']);
        $data['agent_phone']     = trim($post['phone']);
        $data['agent_idcard']    = trim($post['idcard']);
        $data['agent_grade']     = $agent_grade;
        $res = Db::name('Agent')->where('agent_id',$post['agid'])->update($data);

        $result  = Db::name('AgentRate')->where(['rate_agent_id'=>$post['agid'],'rate_type'=>1])->update(['rate_rate'=>$hk]);
        $result1 = Db::name('AgentRate')->where(['rate_agent_id'=>$post['agid'],'rate_type'=>2])->update(['rate_rate'=>$sk]);
        $result2 = Db::name('AgentRate')->where(['rate_agent_id'=>$post['agid'],'rate_type'=>3])->update(['rate_rate'=>$sj]);

        if($result || $result1 || $result2 || $result3 || $res){
          return json(['error'=>0,'msg'=>'修改成功']);
        }else{
          return json(['error'=>1,'msg'=>'修改失败']);
        }

        

        

      }else{
        if(isset($_GET['id'])){

          $list = Db::name('Agent as a')
          ->join('AgentRate r','a.agent_id=r.rate_agent_id')
          ->where('a.agent_pid',session::get('agent_id'))
          ->where('r.rate_agent_id',intval($_GET['id']))
          ->select();

          if(empty($list)){
            return json(['error'=>1,'msg'=>'查无此代理信息']);
          }

          $data = array();
          $date = array();
          foreach ($list as $val) {
            $data[$val['rate_type']] = $val['rate_rate'];
            unset($val['rate_rate']);
            unset($val['rate_type']);
            unset($val['rate_id']);
            unset($val['rate_agent_id']);
            unset($val['rate_close_rate']);
            unset($val['rate_time']);
            $date = $val;
          }
          $date['fl'] =$data;
          $lista = Db::name('AgentGrade')->where('grade_state',0)->where('grade_sort','>',$this->agent['agent_grade'])->select();

          $this->assign('AGENT_GRADE',$AGENT_GRADE);
          $this->assign('list',$lista);
          $this->assign('date',$date);
          return view();   

        }else{

          return json(['error'=>1,'msg'=>'参数错误']);
        }
      }
    }
}
