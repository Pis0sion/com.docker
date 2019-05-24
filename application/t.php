<?php
// +----------------------------------------------------------------------
// | DH [ PERFECT SURROGATE SYSTEM ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 All rights reserved.
// +----------------------------------------------------------------------
// | Author: grass <1251700162@qq.com>
// +----------------------------------------------------------------------

    /**
     * 检验用户token
     * @author tw  2018-05-21
     * @param  string $uid   [description]
     * @param  string $token [description]
     * @return [type]        [description]
     */
    function check_token($uid='',$token='')
    {
        if(empty($uid) || empty($token))
        {
            return echoarr('参数不正确');
            
        }
        $where['user_id']=$uid;
        $where['user_token']=$token;
        $token=Db::name('user')->where($where)->find();
        if(empty($token))
        {
            return json(['error'=>1,'msg'=>'验证失败']);
        }
        return json(['error'=>0,'msg'=>'验证成功']);
    }
    /**
     * 获取费率
     * @Author   tw
     * @DateTime 2018-09-05
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    function getTypeRate($id,$type)
    {
        if($id==0)return '无';
        $rate = Db::name('rate')->where(array('rate_type_id'=>$id,'rate_type'=>$type))->find();
        if(!$rate){
            return '无';
        }
        if($type==3){
            $result = $rate['rate_rate']*100 . '%';
        }else{
            $result = $rate['rate_rate']*100 . '% + ' . $rate['rate_close_rate'];
        }
        return $result;  
    }

    /**
     * 通道名称
     * @Author   tw
     * @DateTime 2018-08-30
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    function getPaymentChannel($id)
    {
        if($id==0)return '无';
        $field = Db::name('payment_channel')->cache(120)->where(array('channel_id'=>$id))->value('channel_name');
        if(!$field){
            return '无';
        }
        return $field;  
    }
    function api_post($url,$data)
    {

        $url='http://'.$_SERVER['HTTP_HOST'].'/'.$url;
        return curl($url,$data);
    }

    /**
     * post提交
     */
    function curl($url,$data){
        //启动一个CURL会话
        $ch = curl_init();
        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        //忽略证书
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL,$url);
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_HEADER,0);//是否需要头部信息（否）
        // 执行操作
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    /**
    * 模拟post进行url请求
    * @param string $url
    * @param array $post_data
    */
    function request_post_urlencode($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        $o = "";
        // ksort($post_data);
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);
        
        $postUrl = $url;
        $curlPost = $post_data;
      	//判断某字符串中是否包含https的方法
		if(strpos($url,'https://') !== false){
           $ishttps = true;
        }else{
        	$ishttps = false;
        }
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
      	if($ishttps){
        	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }


    /*
     * 两个日期之间相差的天数
    */
    function diffBetweenTwoDays ($day1, $day2){
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return (($second1 - $second2) / 86400)+1;
    }
    /*
     * $total 待划分的数字
     * $div   分成的份数
     * $area  各份数间允许的最大差值
    */
    function randnum($total,$div,$area = '100'){
        $average = round($total / $div);
        $sum = 0;
        $result = array_fill( 1, $div, 0 );
     
        for( $i = 1; $i < $div; $i++ ){
            //根据已产生的随机数情况，调整新随机数范围，以保证各份间差值在指定范围内
            if( $sum > 0 ){
                $max = 0;
                $min = 0 - round( $area / 2 );
            }elseif( $sum < 0 ){
                $min = 0;
                $max = round( $area / 2 );
            }else{
                $max = round( $area / 2 );
                $min = 0 - round( $area / 2 );
            }
     
            //产生各份的份额
            $random = rand( $min, $max );
            $sum += $random;
            $result[$i] = $average + $random;
        }
     
        //最后一份的份额由前面的结果决定，以保证各份的总和为指定值
        $result[$div] = $total - array_sum($result);
        foreach( $result as $temp ){
            $data[] = $temp;
        }
        return $data;
    }
    /**
     * [计划生成]
     * @Author tw
     * @param  [type] $money     [金额]
     * @param  [type] $num       [笔数]
     * @param  [type] $money_min [最小金额]
     * @param  [type] $money_max [最大金额]
     * @param  string $min       [description]
     * @param  string $max       [description]
     * @param  string $decimals  [是否有小数]
     * @return [type]            [description]
     */
    function randnum_new($money,$num,$min_money,$max_money,$min_risk,$max_risk, $decimals= 'false'){

        $average = round($money / $num);
        $risk_min_money = $average - $min_money;
        // $max_risk = $max_money - $average;

        $sum = 0;
        $result = array_fill( 1, $num, 0 );
        for( $i = 1; $i < $num; $i++ ){
            $risk = round( (rand( $min_risk, $max_risk )) / 2 );
            if($risk > $risk_min_money)
            {
                $risk = $risk_min_money/2;
            }
            if( $sum > 0 ){
                $max = 0;
                $min = 0 - $risk;
            }elseif( $sum < 0 ){
                $min = 0;
                $max = $risk;
            }else{
                $max = $risk;
                $min = 0 - $risk;
            }
            $random = rand( $min, $max );

            if($decimals)
            {
                $random = $random.".".rand(00,99);
            }
            $sum += $random;
            $result[$i] = $average + $random;
            if($result[$i] < $min_money)
            {
                $result[$i] = $min_money;
            }
            elseif($result[$i]>$max_money)
            {
                $result[$i] = $max_money;
            }
        }
     
        //最后一份的份额由前面的结果决定，以保证各份的总和为指定值
        $result[$num] = $money - array_sum($result);
        foreach( $result as $temp ){
            $data[] = $temp;
        }
        return $data;
    }


    /**
     * 往数组中某一个位置，插入一个元素
     *
     * @param 原数组 $array
     * @param 插入第几个元素后面，0为最前面，但不能大于原数组的最大元素数 $position
     * @param 要插入的值 $value
     * @return 新数组
     */
    function addvtorandp($array,$position,$value){
        $tmp = array();
        for($i = 0; $i <= count($array); $i++){
            if($i == $position){
                $tmp[$position] = $value;
            }else if($i < $position){
                $tmp[$i] = @$array[$i];
            }else{
                $tmp[$i] = $array[$i-1];
            }
        }
        return $tmp;
    }
    
    /**
     * 生成订单号
     * @author tw  2018-05-09
     * @param  string $sn [description]
     * @return [type]     [description]
     */
    function get_order_sn($sn='',$uid='')
    {
        mt_srand((double) microtime() * 1000000);
        if($uid)
        {
            $uid = sprintf("%04d", $uid);
        }
        $order_sn=$sn.date('ymdhis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT).$uid.rand(10,99);
        return $order_sn;
    }

    /**
     * 获取所有上级 代理id
     * @Author tw
     * @Date   2018-09-14
     * @param  [type]     $uid         [description]
     * @param  boolean    $includeSelf [description]
     * @return [type]                  [description]
     */
    function getagentSups($uid, $includeSelf = true,$ids=array()){
        if(empty($uid))
        {
            return array();
        }
        // static $ids = [];
        if($includeSelf) {
            if(!in_array($uid, $ids)) {
                array_push($ids, $uid);
            }
        }
        $where[] = ['agent_id','eq',$uid];
        $where[] = ['agent_state','eq',0];
        $where[] = ['agent_pid','gt',0];
        $subIds = Db::name('agent')->field('agent_pid')->where($where)->select();
        $subIds = array_column($subIds, 'agent_pid');
        $ids = array_unique(array_merge($ids, $subIds));
        
        foreach($subIds as $sub_id) {
            $ids = getagentSups($sub_id, $includeSelf,$ids);
        }
        return $ids;
    }
    /**
     * 获取所有上级 代理地区ID
     * @Author tw
     * @Date   2018-09-14
     * @param  [type]     $uid         [description]
     * @param  boolean    $includeSelf [description]
     * @return [type]                  [description]
     */
    function getagentregion($uid, $includeSelf = true,$ids=array()){
        if(empty($uid))
        {
            return array();
        }
        // static $ids = [];
        if($includeSelf) {
            if(!in_array($uid, $ids)) {
                array_push($ids, $uid);
            }
        }
        $where[] = ['region_id','eq',$uid];
        $where[] = ['region_pid','gt',0];
        $subIds = Db::name('region')->field('region_pid')->where($where)->select();
        $subIds = array_column($subIds, 'region_pid');
        $ids = array_unique(array_merge($ids, $subIds));

        foreach($subIds as $sub_id) {
            $ids = getagentregion($sub_id, $includeSelf,$ids);
        
        }
        return $ids;
    }
    /**
     * 代理分润处理
     * @param [type] $uid        [用户ID]
     * @param [type] $agent_id   [代理商ID]
     * @param string $orderid    [订单ID]
     * @param string $order_no   [订单号]
     * @param [type] $amount     [订单金额]
     * @param [type] $money      [分润金额]
     * @param [type] $rate       [分润费率]
     * @param [type] $user_rate  [用户费率]
     * @param [type] $agent_rate [代理费率]
     * @param [type] $type       [类型 1 还款分润 2收款分润  3普通用户激活 4升级 11还款补偿 12收款补偿]
     * @param [type] $time       [分润时间]
     * @param [type] $pay_type   [状态 0未打款 1已打款 2已拒绝 3处理中]
     */
    function InsertAgentProfit($uid,$agent_id,$orderid='',$order_no='',$amount,$money,$rate,$user_rate='',$agent_rate,$type,$time,$pay_type=0){
        //增加分润
        $data = array();
        $data['profit_uid'] = $uid;
        $data['profit_agent_id'] = $agent_id;
        $data['profit_orderid'] = $orderid;
        $data['profit_form_no'] = $order_no;
        $data['profit_amount'] = $amount;
        $data['profit_money'] = $money;
        $data['profit_rate'] = $rate;
        $data['profit_user_rate'] = $user_rate;
        $data['profit_agent_rate'] = $agent_rate;
        $data['profit_state'] = 1;
        $data['profit_type'] = $type;
        $data['profit_time'] = $time;
        $data['profit_pay'] = $pay_type;
        if(Db::name('agentProfit')->insert($data)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 会员上级三级
     * @Author tw
     * @Date   2018-09-27
     * @param  [type]     $uid         [description]
     * @param  boolean    $includeSelf [description]
     * @return [type]                  [description]
     */
    function getuserlevel($uid, $includeSelf = true,$up='3',$ids=array()){
        if(empty($uid))
        {
            return array();
        }
        // static $ids = [];
        if($includeSelf) {
            if(!in_array($uid, $ids)) {
                array_push($ids, $uid);
            }
        }
        $where[] = ['user_id','eq',$uid];
        $where[] = ['user_pid','gt',0];
        $subIds = Db::name('user')->field('user_pid')->where($where)->select();
        $subIds = array_column($subIds, 'user_pid');
        $ids = array_unique(array_merge($ids, $subIds));
        if(count($ids)>=$up)
        {
            return $ids;
        }
        foreach($subIds as $sub_id) {
            $ids = getuserlevel($sub_id, $includeSelf,$up,$ids);
        }
        return $ids;
    }
    /**
     * 查询所有上级用户
     * @Author tw
     * @param  [type]  $uid         [description]
     * @param  boolean $includeSelf [description]
     * @return [type]               [description]
     */
    function getuserSups($uid, $includeSelf = true,$ids=array()){
        if(empty($uid))
        {
            return array();
        }
        // static $ids = [];
        if($includeSelf) {
            if(!in_array($uid, $ids)) {
                array_push($ids, $uid);
            }
        }
        $where[] = ['user_id','eq',$uid];
        $where[] = ['user_pid','gt',0];
        $subIds = Db::name('user')->field('user_pid')->where($where)->select();
        $subIds = array_column($subIds, 'user_pid');
        $ids = array_unique(array_merge($ids, $subIds));
        foreach($subIds as $sub_id) {
            $ids = getuserSups($sub_id, $includeSelf,$ids);
        }
        return $ids;
    }
    /**
     * 获取上级用户
     * @Author tw
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    function getuserup($uid){
        $up_user = Db::name('user')->field('user_id,user_pid,user_type_id')->where('user_id',$uid)->find();
        return $up_user;
    }

    /**
     * 获取用户下级
     * @Author tw
     * @param  [type]  $uid         [description]
     * @param  boolean $includeSelf [description]
     * @param  string  $level          [description]
     * @return [type]               [description]
     */
    function getuserlower($uid, $includeSelf = true,$level='3',$show = false,$ids=array()){
        if(empty($uid))
        {
            return array();
        }
        // static $ids = [];
        static $lower_level = 1;
        if($includeSelf) {
            if(!in_array($uid, $ids)) {
                array_push($ids, $uid);
            }
        }
        $subIds = Db::name('user')->field('user_id')->whereIn('user_pid',$uid)->select();
        $subIds = array_column($subIds, 'user_id');
        $ids = array_unique(array_merge($ids, $subIds));
        if($show)
        {
            $ids = array_unique(array_merge($ids, $subIds));
        }
        else
        {
            $ids = $subIds;
        }
        if($lower_level>=$level)
        {
            return $ids;
        }
        $lower_level ++;
        $ids = getuserlower($subIds, $includeSelf,$level,$show,$ids);
        return $ids;
    }
    /**
     * 对银行卡号进行掩码处理
     * @Author tw
     * @Date   2018-09-15
     * @param  [type]     $bankCardNo [description]
     * @return [type]                 [description]
     */
    function formatBankCardNo($bankCardNo){
        //截取银行卡号后4位
        $suffix = substr($bankCardNo,-4,4);
        $maskBankCardNo = "**** **** **** ".$suffix;
        return $maskBankCardNo;
    }
    /**
     * 隐藏手机中间四位
     * @author tw  2018-05-02
     * @param  string $tel [description]
     * @return [type]      [description]
     */
    function hide_tel($tel='')
    {
        if(empty($tel))
        {
            return ;
        }
        return substr_replace($tel,'****',3,4);
    }
    /**
     * 将一个字符串部分字符 替代隐藏
     * @Author tw
     * @param string    $string   待处理的字符串
     * @param int       $start    规定在字符串的何处开始，
     *                            正数 - 在字符串的指定位置开始
     *                            负数 - 在从字符串结尾的指定位置开始
     *                            0 - 在字符串中的第一个字符处开始
     * @param int       $length   可选。规定要隐藏的字符串长度。默认是直到字符串的结尾。
     *                            正数 - 从 start 参数所在的位置隐藏
     *                            负数 - 从字符串末端隐藏
     * @param string    $re       替代符
     * @return string   处理后的字符串
     */
    function hide_str($string, $start = 0, $length = 0, $re = '*') {
        if (empty($string)) return false;
        $strarr = array();
        $mb_strlen = mb_strlen($string);
        while ($mb_strlen) {//循环把字符串变为数组
            $strarr[] = mb_substr($string, 0, 1, 'utf8');
            $string = mb_substr($string, 1, $mb_strlen, 'utf8');
            $mb_strlen = mb_strlen($string);
        }
        $strlen = count($strarr);
        if($strlen==2)
        {
            $length = 0;
        }
        $begin  = $start >= 0 ? $start : ($strlen - abs($start));
        $end    = $last   = $strlen - 1;
        if ($length > 0) {
            $end  = $begin + $length - 1;
        } elseif ($length < 0) {
            $end -= abs($length);
        }
        for ($i=$begin; $i<=$end; $i++) {
            $strarr[$i] = $re;
        }
        //if ($begin >= $end || $begin >= $last || $end > $last) return false;
        return implode('', $strarr);
    }
    /**
     * 格式化数字 默认保留两位不四舍五入
     * @Author tw
     * @Date   2018-09-21
     * @param  string     $num      [description]
     * @param  string     $decimals [description]
     * @return [type]               [description]
     */
    function substr_num($num='0',$decimals='2')
    { 
        $decimals = pow(10,$decimals);
        return floor($num*$decimals)/$decimals;
    }

    /**
     * 获取地理编码
     * @Author tw
     * @Date   2018-09-29
     * @param  [type]     $name [description]
     * @return [type]           [description]
     */
    function getadcode($name)
    {
        if(empty($name))return '';
        $field = Db::name('region')->where(array('region_name'=>$name))->value('region_adcode');
        if(!$field){
            return '';
        }
        return $field;  
    }
    function gettoken($uid)
    {
        $token = Db::name('user')->where('user_id',$uid)->value('user_token');
        if(empty($token))
        {
            $token = md5($uid.time());
            Db::name('user')->where('user_id',$uid)->update(['user_token'=>$token]);
        }
        return $token;
    }
    
    /**
     * 获取地区
     */
    function getregion($name='',$field=''){
        if(empty($field))
        {
            $field = 'region_name';
        }
        return Db::name('region')->where('region_name',$name)->value($field);
    }
    /**
     * 修改用户费率
     * @Author tw
     * @param  string $uid     [description]
     * @param  string $type_id [description]
     * @return [type]          [description]
     */
    function update_rate($uid='',$type_id='')
    {
        $rate = Db::name('rate')->where('rate_type_id',$type_id)->order('rate_type asc')->select();

        if(empty($rate))
        {
            return json(['error'=>1,'msg'=>'费率模板不存在']);
        }
        foreach ($rate as $key => $value) {
            $user_rate = Db::name('user_rate')->where(['rate_uid'=>$uid,'rate_type'=>$value['rate_type']])->find();
            $data['rate_rate'] = $value['rate_rate'];
            $data['rate_close_rate'] = $value['rate_close_rate'];
            $data['rate_time'] = time();
            if($user_rate)
            {
                Db::name('user_rate')->where(['rate_id'=>$user_rate['rate_id']])->update($data);
                if($value['rate_type']=='1' || $value['rate_type']=='2')
                {
                    update_payment_fee($uid,$value['rate_type']);
                }
                // continue;
            }

        }
    }
    /**
     * 更改通道费率
     * @Author tw
     * @param  string $uid  [description]
     * @param  string $type [description]
     * @return [type]       [description]
     */
    function update_payment_fee($uid='',$type='')
    {
        $payment_user = Db::name('payment_user')->where('user_uid',$uid)->where('user_type',$type)->where('user_state',1)->select();
        foreach ($payment_user as $key => $v) {
            $payment_controller = Db::name('payment')->where('payment_id',$v['user_pay_id'])->value('payment_controller');
            $result = Controller('pay/'.$payment_controller)->update_fee($v['user_pay_id'],$uid);
        }
    }
    /**
     * 创建用户费率
     * @Author tw
     * @param  string $uid     [description]
     * @param  string $type_id [description]
     * @return [type]          [description]
     */
    function user_rate($uid='',$type_id='')
    {
        $rate = Db::name('rate')->where(['rate_type_id'=>$type_id])->order('rate_type asc')->select();
        if(empty($rate))
        {
            return ['error'=>1,'msg'=>'费率模板不存在'];
        }
        foreach ($rate as $key => $value) {
            $user_rate = Db::name('user_rate')->where(['rate_uid'=>$uid,'rate_type'=>$value['rate_type']])->find();

            $data['rate_rate'] = $value['rate_rate'];
            $data['rate_close_rate'] = $value['rate_close_rate'];
            $data['rate_time'] = time();
            if($user_rate)
            {
                Db::name('user_rate')->where(['rate_id'=>$user_rate['rate_id']])->update($data);
                continue;
            }
            $data['rate_uid'] = $uid;
            $data['rate_type'] = $value['rate_type'];
            Db::name('user_rate')->insert($data);

        }
        return ['error'=>0,'msg'=>'成功'];
    }
    /**
     * 获取用户费率
     * @Author tw
     */
    function get_user_rate($uid='',$type='hk')
    {
        if($type=='sk')
        {
            $rate_type = '2';
        }
        else
        {
            $rate_type = '1';
        }
        $user_rate = Db::name('user_rate')->where(['rate_uid'=>$uid,'rate_type'=>$rate_type])->find();
        return $user_rate;
    }
    /**
     * 获取等级信息
     * @Author tw
     * @param  [type] $type_id [description]
     * @return [type]          [description]
     */
    function get_user_type($type_id)
    {
        $user_type = Db::name('user_type')->where('type_id',$type_id)->find();
        return $user_type;
    }

    /**
     * 还款费率计算
     * @Author tw
     * @param  string $money      [description]
     * @param  string $fee        [description]
     * @param  string $rate       [description]
     * @param  string $close_rate [description]
     * @return [type]             [description]
     */
    function repayment_fee($money='',$fee='',$rate='',$close_rate='')
    {
        $paymeny_money = $money + $fee - ($money+$fee) *$rate - $close_rate;
        $paymeny_money = (string)((floor($paymeny_money*100))/100);
        if($paymeny_money<$money)
        {
            // $fee = $fee + round($money - $paymeny_money,2);
            $fee = $fee + 0.01;
            $fee = (string)round($fee,2);
            return repayment_fee($money,$fee,$rate,$close_rate);
        }
        return $fee ;
    }

    /**
     * 支付时间计算
     * @Author tw
     * @param  string $time [description]
     * @param  string $i    [description]
     * @return [type]       [description]
     */
    function repayment_time($time='',$i='0',$key='0',$payment='',$interval_time='0',$interval_time_end='5000',$type='1',$list_time=array())
    {
        $hour = date("H",strtotime($time));
        // if($hour > 18 || $hour < 9 || ($i > $payment['payment_day_num'] && ($list_time[0]!=$list_time[array_search(date("Y-m-d",strtotime($time)), $list_time)] || ( $i > 2 && $list_time[0]==$list_time[array_search(date("Y-m-d",strtotime($time)), $list_time)]))))
        if($hour > 18 || $hour < 9 || ($i > $payment['payment_day_num'] ))
        {
            $i = 1;
            $time = date("Y-m-d",strtotime("$time"));
            $k = array_search($time, $list_time);
            $time = $list_time[$k+1];
            $time = date("Y-m-d H:i:s",strtotime(date("Y-m-d 09:00:00",strtotime($time))) + rand(0,$interval_time_end));
        }
        elseif($key==0)
        {
            if(date("Y-m-d",strtotime("$time"))==date("Y-m-d") && date('H')>9 && date('H')<18)
            {
                $time = date("Y-m-d H:i:s",time()+ rand(100,600));
            }
            else
            {
                $time = date("Y-m-d H:i:s",strtotime(date("Y-m-d 09:00:00",strtotime("$time"))) + rand(0,3600));
            }
            $paytime = $time;
        }
        else
        {
            $time = date("Y-m-d H:i:s",strtotime($time) + rand($interval_time,$interval_time_end)); //时间
        }

        if(date("H",strtotime($time))>19)
        {
            $time = date("Y-m-d 19:i:s",strtotime($time)); //时间
        }
        return ['time'=>$time,'i'=>$i];

    }
    function repayment_day_num($count_num='',$count_day='',$day_max_num='')
    {
        $i = 0;
        $stop = 0;
        $day = 0;
        while($i<$count_num) {
            if($day>=$count_day)
            {
                $day = 0;
            }
            if($i==0)
            {

                $data[$day] ++;
                $i++;
            }
            elseif(rand(0,1) && $day_max_num>$data[$day])
            {
                if(!($day==0 && ((date("H")>=9 && date("H")<12 && $data[$day]>=4) ||  (date("H")>12 && date("H")<15 && $data[$day]>=2) || (date("H")>15 && date("H")<18 && $data[$day]>=1))))
                {
                    $data[$day] ++;
                    $i++;
                }
            }
            else
            {
                $data[$day]=$data[$day]?$data[$day]:0;
                $stop ++;
            }
            if($stop>100)
            {
                $data[$count_day] = $count_num-array_sum($data);
                break;
            }
            $day ++;
        }
        return $data;
    }

    /**
     * 根据指定时间获取随机值
     */
    function get_time_scope($start_time='',$end_time='',$day='')
    {
        // $day = ceil($day);
        $time = range(strtotime($start_time), strtotime($end_time), 24*60*60);
        $time = array_map(create_function('$v', 'return date("Y-m-d", $v);'), $time);
        if($day && $day>1)
        {
            $rand_keys = array_rand($time,$day);
            foreach ($rand_keys as $key => $value) {
                $data[] = $time[$rand_keys[$key]];
            }
        }
        else
        {
            $data = $time;
        }
        return $data;
    }

	/*
	 * 根据支付笔数结束时间计算
	 * 执行时间
	 * 2018年12月5日15:25:46
	 * 刘媛媛
	 * $strDate 开始时间
	 * $endDate 结束时间
	 * $countNumber 总笔数 10 / 2  20 
	 * $payment 通道数据
	 * $intervalTime 间隔时间
	 * $mode  生成模式 1一笔生成2次时间 2是一笔生成2次时间 .... 
	 */
	function jisuanbishu($strDate,$endDate,$countNumber,$payment,$intervalTime,$mode=1){
		
		$day_count   = diffBetweenTwoDays($strDate,$endDate);//总天数
		
		$strDateTime =  strtotime($strDate);
	
		$endDateTime =  strtotime($endDate);
		$stime 		 = 32400;//凌晨到9点的时间戳差
		$etime 	     = 64800;//凌晨到18点的时间戳差
		//判断总笔数是超过当日笔数而且时间不够
		//这种情况就得逐天生成了结束时间作废 这儿没有计算 $mode
		$arr   = array();
		$spmsl = ceil($countNumber/$day_count);
		$spmslor = floor($countNumber/$day_count);
		$count = 0;
		
		//选择的时间段足够均分这些笔数
		//判断笔数是否超过
		
		//限制笔一天
		//$cpCut = ceil($countNumber%$payment['payment_day_num']);
		
		for($s=0;$s<$spmslor;$s++){
			$list[] = $day_count;
		}
		
		$list[] = $countNumber%$day_count;
		
		//如果笔数多时间短就得延长
		if(count($list)>$payment['payment_day_num']){
			
			$spmsl   = ceil($countNumber%$day_count);
			$day_count += count($list)+$spmsl;
			$spmsl   = ceil($countNumber%$day_count);
			
			$spmslor = floor($countNumber/$day_count);
			$endDate +=count($list)*86400;
			$endDateTime =  strtotime($endDate);
			unset($list);
			for($s=0;$s<$spmslor;$s++){
				$list[] = $day_count;
			}
			if($spmsl>0){
				$list[] = $countNumber%$day_count;
			}
		}
		
		//非正整数拆分多余笔数
		
		foreach($list as $ks=> $vs){
			
			for($i=0;$i<$vs;$i++){
					
				$arr[$i][] = $count++;
				
			}
			$count	     = 0;
			$strDateTime = strtotime($strDate);
		}
		
		//arsort($arr[$i]);
		$listNes = array();
		foreach ($arr as $k=>$v){
			
			foreach ($v as $ks=>$vs){
				$listNes[$k][] =  $strDateTime+ ($k*86400)+rand($stime,$etime);
			}
		}
			
		//dump($listNes);exit;
		return $listNes;
	}
	
?>