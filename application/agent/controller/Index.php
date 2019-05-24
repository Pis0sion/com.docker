<?php
namespace app\agent\controller;
use think\Controller;
use think\facade\Session;
use think\Db;
class Index extends Base
{
    public function index()
    {
        return view();
    }
    /**
     * 首页报表
     * @param  string $name [description]
     * @return [type]       [description]
     */
    public function console()
    {

        $time = time()-(86400*15);
        $list = Db::name('AgentProfit')
        ->where('profit_agent_id',session::get('agent_id'))
        ->where('profit_time','>',$time)
        ->select();
        $arr   = array();
        $suarr = array();
        $array = array();
        $times = strtotime(date('Y-m-d',time()));
        for ($i=0;$i<15;$i++){
          $timeArr = $times-(86400*$i);
          $arr[date('m-d',$timeArr)] = 0;
          $suarr[date('m-d',$timeArr)] = 0;
          foreach($list as $k=>$v){
            if( date('m-d',$timeArr) == date('m-d',$v['profit_time']) ){
              $arr[date('m-d',$timeArr)] += $v['profit_amount'];
              $suarr[date('m-d',$timeArr)] += $v['profit_money'];
            }
            
          }
          $key[] = date('m-d',$timeArr);
        }

        ksort($arr);
        ksort($suarr);
        foreach($arr as $ks=>$vs){
          $arrst[] =  $vs;
        } 
        foreach($suarr as $ks=>$vs){
          $suarrs[] =  $vs;
        } 
        sort($key);
        //今日消费金额
        $todymoney = Db::name('AgentProfit')->where('profit_time','>',strtotime(date("Y-m-d"),time()))->where('profit_agent_id',session::get('agent_id'))->sum('profit_amount');
        //今日分润
        $todyprofit = Db::name('AgentProfit')->where('profit_time','>',strtotime(date("Y-m-d"),time()))->where('profit_agent_id',session::get('agent_id'))->sum('profit_money');
        //总消费额
        $sunmoney = Db::name('AgentProfit')->where('profit_agent_id',session::get('agent_id'))->sum('profit_amount');
        //总分润
        $sunprofit = Db::name('AgentProfit')->where('profit_agent_id',session::get('agent_id'))->sum('profit_money');
        $this->assign('todymoney',$todymoney);
        $this->assign('todyprofit',$todyprofit);
        $this->assign('sunmoney',$sunmoney);
        $this->assign('sunprofit',$sunprofit);
        $this->assign('arrst',$arrst);
        $this->assign('suarrs',$suarrs);
        $this->assign('key',$key);
        return view();
    }
}
