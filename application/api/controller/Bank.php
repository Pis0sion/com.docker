<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\facade\Session;


class Bank extends Controller
{   
    public function index(){
       $s = $this->get_bank('6229023175015100');dump($s);
   }
      /**
     * 根据卡号获取对应银行
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function get_bank($card_no)
    {   
        if(empty($card_no)){
            return array('error'=>1,'msg'=>'请输入银行卡');
        }
        $cardType = array('贷记卡','准贷记卡','信用卡');
        $result = BankType($card_no);
        if($result['showapi_res_code']==0)
        {   
            if(!empty($result['showapi_res_body']['cardType']))
            {   
        
                //判断是贷记卡
                if(!in_array($result['showapi_res_body']['cardType'],$cardType)){
                    
					//为了保险执行下二次查询
					if($this->alibank($card_no)){
						return array('error'=>1,'msg'=>'banknull');
					}else{
						return array('error'=>1,'msg'=>'只支持贷记卡哦');
					}
                }
                
                $banklist = Db::name('BankList')->where('list_name',$result['showapi_res_body']['bankName'])->find();
                if(empty($banklist))
                {   
                    //查不到模糊查询一下
                    $banklistNew = Db::name('BankList')->select();
                    
                    foreach ($banklistNew as $bnk => $bnv){
                        $listmore = explode("|",$bnv['list_more']);
                        foreach($listmore as $k=>$v){
                            if($v && strpos($result['showapi_res_body']['bankName'],$v) !== false){
                                $results['id'] = $bnv['list_id'];
                                return array('error'=>0,'msg'=>'ok','bankid'=>$results);
                            }
                        }
                        
                        unset($listmore);
                    }
                   return array('error'=>1,'msg'=>'banknull');
                }
                
                $results['id'] = $banklist['list_id'];
               
                return array('error'=>0,'msg'=>'ok','bankid'=>$results);
            }else{
				
				//聚合接口查不到然后通过阿里的接口再次核对卡信息
				if($this->alibank($card_no)){
					return array('error'=>1,'msg'=>'banknull');
				}else{
					return array('error'=>1,'msg'=>'请检查银行卡是否正确');
				}
				
                //return array('error'=>1,'msg'=>$result['showapi_res_body']['remark']);//['showapi_res_body']['remark']
            }

        }else{
            return array('error'=>1,'msg'=>'接口请求异常');
        }
    }
	/**
     * 根据卡号获取对应银行阿里云
	 * 二次查询
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
	public function alibank($card){
		//ret_code
		$url = 'https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?cardNo='.$card.'&cardBinCheck=true';
		$str = file_get_contents($url);
		$arr = json_decode($str,true);
		//判断是否通过
		if($arr['validated']=='true'){
			//判断是否非储蓄卡
			if($arr['cardType']!='DC'){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
}
