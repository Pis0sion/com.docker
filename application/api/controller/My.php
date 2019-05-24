<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;

class My extends Base
{
    /**
     * 认证状态及信用卡数量
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function index()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $user = $this->user;//base 用户数据
        $user_card = Db::name('user_card')->where(['card_uid'=>$user['user_id'],'card_type'=>1,'card_blocked'=>0])->order('card_state desc')->count();
        return json(['error'=>0,'msg'=>'成功','real'=>$user['user_real'],'user_card'=>$user_card]);
    }
    /**
     * 修改密码
     * @Author tw
     * @Date   2018-09-11
     * @return [type]     [description]
     */
    public function password()
    {
        if($this->request->isPost()) {
            $post = input('post.');
            $user = $this->user;//base 用户数据
            $uid = $user['user_id'];

            $password = $post['keywords'];//旧密码
            $new_password = $post['new_keywords'];//新密码

            if(md5($password) != $user['user_password'])
            {
                return json(['error'=>1,'msg'=>'旧密码错误']);
            }

            $up = Db::name('user')->where(['user_id'=>$uid])->update(['user_password'=>md5($new_password)]);
            if(empty($up))
            {
                return json(['error'=>1,'msg'=>'修改失败']);
            }
            //发送短信
            ######
            ######
            return json(['error'=>0,'msg'=>'密码修改成功,请重新登录']);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }

    /**
     * 意见反馈
     * @Author tw
     * @Date   2018-09-11
     * @return [type]     [description]
     */
    public function feedback()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $data['feedback_uid'] = $this->uid;//用户id
        $data['feedback_title'] = $post['title'];//问题标题
        $data['feedback_body'] = $post['body'];//内容
        $data['feedback_name'] = $post['name'];//联系人姓名
        $data['feedback_phone'] = $post['phone'];//电话
        $data['feedback_state'] = 1;
        $data['feedback_time'] = time();

        if(empty(Db::name('feedback')->insert($data)))
        {
            return json(['error'=>1,'msg'=>'反馈失败']);
        }
        return json(['error'=>0,'msg'=>'反馈成功']);
    }
    
    /**
     * 获取分类
     * @Author tw
     * @Date   2018-09-11
     * @return [type]     [description]
     */
    public function article_type()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $list = Db::name('article_type')->field('type_id,type_name')->select();
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'获取失败']);
        }
        foreach($list as $k=>$v){
            $list[$k]['typeico'] = '/uploads/images/articletype/articletype_'.$v['type_id'].'.png';
        }
        return json(['error'=>0,'msg'=>'获取成功','data'=>$list]);
    }
    /**
     * 文章列表
     * @Author tw
     * @Date   2018-09-11
     * @return [type]     [description]
     */
    public function article()
    {
        $getdata = array();
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $type = $post['type'];//分类id
        $page = $post['page'];//第几页
        $list = Db::name('article')->where(['article_type'=>$type,'article_use'=>1])->field('article_id,article_title,article_type,article_img,article_time')->paginate(10,false,['query'=> $getdata]);
        foreach($list as $k=>$v){
            $data = array();
            $data = $v;
            $data['article_img'] = 'http://'.$_SERVER['HTTP_HOST'].$data['article_img'];
            $list->offsetSet($k,$data);
        }
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'获取失败']);
        }
        return json(['error'=>0,'msg'=>'获取成功','data'=>$list]);
    }
    /**
     * 文章详情
     * @Author tw
     * @Date   2018-09-11
     * @return [type]     [description]
     */
    public function article_detail()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $id = $post['id'];//文章id
        $article = Db::name('article')->where(['article_id'=>$id])->field('article_id,article_title,article_body,article_type,article_img,article_time')->find();
        if(empty($article))
        {
            return json(['error'=>1,'msg'=>'获取失败']);
        }
        $article['article_img'] = 'http://'.$_SERVER['HTTP_HOST'].$article['article_img'];
        $article['article_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/Index/Iframe/article?id='.$article['article_id'];
        return json(['error'=>0,'msg'=>'获取成功','data'=>$article]);
    }

    /**
     * 热门问题
     * @Author tw
     * @Date   2018-09-11
     * @return [type]     [description]
     */
    public function hotproblems(){
        $getdata = array();
        $list = Db::name('article')->where(['article_use'=>1])->field('article_id,article_title')->order('article_click desc')->limit(10)->select();

        if(empty($list)){
            return json(['error'=>1,'msg'=>'获取失败']);
        }
        return json(['error'=>0,'msg'=>'获取成功','data'=>$list]);
        
    }

    /**
     * 实名认证
     * @Author tw
     * @Date   2018-09-12
     * @return [type]     [description]
     */
    public function real()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $user = $this->user;
        $uid = $user['user_id'];

        //身份证号码
        $idcard = '0';
        if($user['user_idcard'])
        {
            $idcard = '1';
        }
        //身份证照片
        $idcard_img = '0';
        if($user['user_idcard_z'] && $user['user_idcard_f'])
        {
            $idcard_img = '1';
        }

        //结算卡
        $card = '0';
        $user_card = Db::name('user_card')->where(['card_uid'=>$uid,'card_type'=>2,'card_blocked'=>0])->find();
        if($user_card)
        {
            $card = '1';
        }

        $real_sum = $idcard + $idcard_img + $card;
        if($user['user_real'] != 1)
        {
            return json(['error'=>0,'msg'=>'未认证','idcard'=>$idcard,'idcard_img'=>$idcard_img,'card'=>$card,'real'=>0,'real_sum'=>$real_sum]);
        }
        return json(['error'=>0,'msg'=>'已认证','idcard'=>$idcard,'idcard_img'=>$idcard_img,'card'=>$card,'real'=>1,'real_sum'=>$real_sum]);

    }

    /**
     * 身份认证
     * @Author tw
     * @Date   2018-09-12
     * @return [type]     [description]
     */
    public function authentication()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id

        $user_name   = $post['user_name'];//用户姓名
        $user_idcard = strtoupper($post['user_idcard']);//身份证
        // $user_idcard_z = $post['user_idcard_z'];//身份证正
        // $user_idcard_f = $post['user_idcard_f'];//身份证反
        //判断是否激活
        $ra = Db::name('User')->where('user_id',$this->uid)->value('user_isactivation');
        if($ra==1){
            return json(['error'=>1,'msg'=>'你还未激活，请先进行激活']);
        }
        //待写验证接口
        $result = UsernameIs($user_idcard,$user_name);
        if(!$result){
            return json(['error'=>1,'msg'=>'身份证与姓名不符']);
        }
        $iscard = Db::name('user')->where('user_idcard',$user_idcard)->where('user_state',0)->find();
        if($iscard){
            return json(['error'=>1,'msg'=>'此身份证已被注册使用']);
        }
        $data['user_name'] = $user_name;//用户姓名
        $data['user_idcard'] = $user_idcard;//身份证
        // $data['user_idcard_z'] = $user_idcard_z;//身份证正面
        // $data['user_idcard_f'] = $usr_idcard_f;//身份证反面
        $data['user_real'] = 0;

        $up = Db::name('user')->where(['user_id'=>$uid])->update($data);
        if(empty($up))
        {
            return json(['error'=>1,'msg'=>'认证失败']);
        }
        return json(['error'=>0,'msg'=>'认证成功']);
    }
    /**
     * 照片认证
     * @Author tw
     * @Date   2018-09-12
     * @return [type]     [description]
     */
    public function authephotoation()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id

        $user_idcard_z = $post['user_idcard_z'];//身份证正
        $user_idcard_f = $post['user_idcard_f'];//身份证反
        $user_hand_img = $post['user_hand_img'];//身份证手持照
        if(!isset($user_idcard_z) || !isset($user_idcard_f)){
            return json(['error'=>1,'msg'=>'请上传完整照片']);
        }
        //待写验证接口

        $data['user_idcard_z'] = $user_idcard_z;//身份证正面
        $data['user_idcard_f'] = $user_idcard_f;//身份证反面
        $data['user_hand_img'] = $user_hand_img;//身份证手持照
        $data['user_real'] = 1;

        $up = Db::name('user')->where(['user_id'=>$uid])->update($data);
        if(empty($up))
        {
            return json(['error'=>1,'msg'=>'认证失败']);
        }
        return json(['error'=>0,'msg'=>'认证成功']);
    }
    /**
     * 银行卡认证
     * @Author tw
     * @Date   2018-09-12
     * @return [type]     [description]
     */
    public function card()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        // $smscode = $post['smscode'];//验证码 取消验证码

        $phone = $post['phone'];//银行预留手机号
        $bank_id = $post['bank_id'];//银行id
        $card_no = myTrim($post['card_no']);//银行卡号
        $type = $post['type']?$post['type']:'2';//1信用卡 2储蓄卡
        $region = isset($post['region'])?$post['region']:'';//地区 region = 省-市-区

        if(empty($bank_id))
        {
            return json(['error'=>1,'msg'=>'请选择对应银行']);
        }
        // $province = isset($post['province'])?$post['province']:'';//省
        // $city = isset($post['city'])?$post['city']:'';//市
        // $district = isset($post['district'])?$post['district']:'';//区
        if($type==1)
        {
            $limit = $post['limit'];//信用额度
            $cvn = $post['cvn'];//CVN码
            $exp_date = $post['exp_date'];//卡有效期（月/年）
            $account_day = $post['account_day'];//帐单日
            $repayment_day = $post['repayment_day'];//还款日
            $user_card = Db::name('user_card')->where(['card_uid'=>$uid,'card_type'=>2,'card_blocked'=>0])->find();
            if(empty($user_card))
            {
                return json(['error'=>1,'msg'=>'请先添加结算卡']);
            }
            $BankType = BankType($user_card['card_no']);
            
            if($BankType['showapi_res_body']['showapi_res_error']!=0){

                return json(['error'=>1,'msg'=>'添加失败,请稍后重试,错误代码-'.$uid]);
            }
            if($BankType['showapi_res_body']['area'])
            {
                $region = $BankType['showapi_res_body']['area'];
                $region = explode('-', $region);
                $province = $region[0];//省
                $city = $region[1];//市
                $district = '';//区*/
            }
            else
            {
                $province = $user_card['card_province'];//省
                $city = $user_card['card_city'];//市
                $district = $user_card['card_district'];//区*/
            }
        }
        elseif ($type==2) {
            if(empty($this->user['user_hand_img']))
            {
                // return json(['error'=>1,'msg'=>'请补全身份证手持照']);
            }
            $card_img1 = $post['card_img1'];//卡正面
            $card_img2 = $post['card_img2'];//卡反面
            // if(empty($card_img1))
            // {
            //     return json(['error'=>1,'msg'=>'请上传卡正面照片']);
            // }
            // if(empty($card_img2))
            // {
            //     return json(['error'=>1,'msg'=>'请上传卡反面照片']);
            // }
            if(empty($region)){
                return json(['error'=>1,'msg'=>'请选择所在区域']);
            }
            $region   = explode('-', $region);
            $province = $region[0];//省
            $city     = $region[1];//市
            $district = '';//区
        }

        $branch = $post['branch'];//支行

        $user = Db::name('user')->where(['user_id'=>$uid])->find();
        if(empty($user) || $user['user_state']==1)
        {
            return json(['error'=>1,'msg'=>'用户不存在']);
        }
        if($user['user_real']<>1)
        {
            return json(['error'=>1,'msg'=>'用户未实名']);
        }

        $idcard = $user['user_idcard']; //用户身份证
        $name = $user['user_name']; //用户姓名

        if(Db::name('user_card')->where(['card_uid'=>$uid,'card_no'=>$card_no,'card_blocked'=>0])->find())
        {
            return json(['error'=>1,'msg'=>'重复绑定']);
        }

        //待写四要素验证接口
        $result = proving($card_no,$name,$idcard,$phone);
        if($result['showapi_res_body']['code']!=0)
        {
            return json(['error'=>1,'msg'=>'银行卡要素'.$result['showapi_res_body']['msg']]);
        }


        //首次注册用户赠送积分
        $config = require CACHE_PATH.'system.php';
        $userintegral = Db::name('UserCard')->where('card_uid',$this->uid)->select();
        if(empty($userintegral) && $config['CACHE_PATH']!=0){
            $upda = Db::name('User')->where('user_id',$this->uid)->setInc('user_integral',$config['CACHE_PATH']);
            $upde = Db::name('User')->where('user_id',$this->uid)->setInc('user_total_integral',$config['CACHE_PATH']);
            $va = array();
            $va['integral_uid']     = $this->uid;
            $va['integral_type']    = 1;
            $va['integral_point']   = $config['CACHE_PATH'];
            $va['integral_surplus'] = $user['user_integral']+$config['CACHE_PATH'];
            $va['integral_time']    = time();
            $addin = Db::name('UserIntegral')->insert($va);
            if(!$upda || !$upde || !$addin){
                return json(['error'=>1,'msg'=>'赠送积分失败！']);
            }
        }
        
        $data['card_uid'] = $uid;//用户id
        $data['card_no'] = $card_no;//银行卡号
        $data['card_name'] = $name; //用户名
        $data['card_phone'] = $phone;//银行预留手机号

        $data['card_bank_id'] = $bank_id;//银行id
        $data['card_type'] = $type;//1信用卡 2储蓄卡
        if($type==1)
        {
            $data['card_credit_limit'] = $limit;//信用额度
            $data['card_cvn'] = $cvn;//CVN码
            $data['card_exp_date'] = $exp_date;//卡有效期（月/年）
            $data['card_account_day'] = $account_day;//帐单日
            $data['card_repayment_day'] = $repayment_day;//还款日
        }

        $data['card_img1'] = $card_img1;//卡正面
        $data['card_img2'] = $card_img2;//卡反面
        $data['card_state'] = 0;
        $data['card_branch'] = $branch;//支行
        $data['card_province'] = $province;//省
        $data['card_city'] = $city;//市
        $data['card_district'] = $district;//市
        $data['card_time'] = time();//时间
        $isCard = Db::name('user_card')->where('card_no',$data['card_no'])->where('card_blocked',0)->find();
        if($isCard){
            return json(['error'=>1,'msg'=>'此银行卡已经存在']); 
        }
        $insert = Db::name('user_card')->insert($data);
        if(empty($insert))
        {
            sendsms($phone,11,'绑卡失败！');
            return json(['error'=>1,'msg'=>'绑卡失败']);
        }
        
        sendsms($phone,11,'绑卡成功！');
        return json(['error'=>0,'msg'=>'绑卡成功']);
    }

    /**
     * 卡详情
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function card_info()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $cid = $post['cid'];//银行卡id
        $type = isset($post['type'])?$post['type']:2;//1信用卡 2储蓄卡
        if($type==2 && empty($cid))
        {
            $cid = Db::name('user_card')->where(['card_blocked'=>0,'card_uid'=>$uid,'card_type'=>$type])->order('card_id desc')->value('card_id');
        }
        $card = Db::name('user_card')->alias('c')
                        ->join('bank_list b','b.list_id=c.card_bank_id','LEFT')
                        ->where(['card_id'=>$cid,'card_uid'=>$uid])
                        ->find();
        if(empty($card))
        {
            return json(['error'=>1,'msg'=>'没有卡信息']);
        }

        $card['list_logo'] = 'http://'.$_SERVER['HTTP_HOST'].$card['list_logo'];
        $card['card_img1'] = 'http://'.$_SERVER['HTTP_HOST'].$card['card_img1'];
        $card['card_img2'] = 'http://'.$_SERVER['HTTP_HOST'].$card['card_img2'];
        // $card['card_no']   = '**** **** ****'.substr($card['card_no'],strlen($card['card_no'])-4);
        
        return json(['error'=>0,'msg'=>'成功','data'=>$card]);
    }

    /**
     * 编辑 卡
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function card_edit()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $user = $this->user;//用户
        $uid = $this->uid;//用户id
        $cid = $post['cid'];//银行卡id
        $phone = $post['phone'];//银行预留手机号
        $type = isset($post['type'])?$post['type']:'2';//1信用卡 2储蓄卡
        $region = isset($post['region'])?$post['region']:'';//地区 region = 省-市-区


        $branch = isset($post['branch'])?$post['branch']:'';//支行
        //$province = $post['province'];//省
        //$city = $post['city'];//市

        $idcard = $user['user_idcard']; //用户身份证
        $name = $user['user_name']; //用户姓名
        if($type==1)
        {
            $limit = $post['limit'];//信用额度
            $cvn = $post['cvn'];//CVN码
            $exp_date = $post['exp_date'];//卡有效期（月/年）
            $account_day = $post['account_day'];//帐单日
            $repayment_day = $post['repayment_day'];//还款日
        }
        elseif($type==2)
        {
            $bank_id = $post['bank_id'];//银行id
            $card_no = myTrim($post['card_no']);//银行卡号

            $result = proving($card_no,$name,$idcard,$phone);
            if($result['showapi_res_body']['code']!=0)
            {
                return json(['error'=>1,'msg'=>'银行卡要素'.$result['showapi_res_body']['msg']]);
            }
            $card_img1 = $post['card_img1'];//卡正面
            $card_img2 = $post['card_img2'];//卡反面
            if(empty($card_img1))
            {
                // return json(['error'=>1,'msg'=>'请上传卡正面照片']);
            }
            if(empty($card_img2))
            {
                // return json(['error'=>1,'msg'=>'请上传卡反面照片']);
            }
        
        }

        $card = Db::name('user_card')->where(['card_id'=>$cid,'card_uid'=>$uid])->find();
        if(empty($card) || $card['card_type'] != $type)
        {
            return json(['error'=>1,'msg'=>'没有卡信息']);
        }


        $where['card_id'] = $cid;//银行卡id
        $where['card_uid'] = $uid;//用户id
        $data['card_phone'] = $phone;//银行预留手机号
        if($type==1)
        {
            $data['card_credit_limit'] = $limit;//信用额度
            $data['card_cvn'] = $cvn;//CVN码
            $data['card_exp_date'] = $exp_date;//卡有效期（月/年）
            $data['card_account_day'] = $account_day;//帐单日
            $data['card_repayment_day'] = $repayment_day;//还款日
        }
        elseif($type==2)
        {
            //拆分地区
            if(empty($region))
            {
                return json(['error'=>1,'msg'=>'请选择所在区域']);
            }
            $region = explode('-', $region);
            $province = $region[0];//省
            $city = $region[1];//市
            //$district = $region[2];//区
            $data['card_no'] = $card_no;//银行卡号
            $data['card_bank_id'] = $bank_id;//银行id
            $data['card_branch'] = $branch;//支行
            $data['card_province'] = $province;//省
            $data['card_city'] = $city;//市
        }
        $data['card_img1'] = $post['card_img1'];//卡正面
        $data['card_img2'] = $post['card_img2'];//卡反面

        $data['card_time'] = time();//时间

        $up = Db::name('user_card')->where($where)->update($data);
        if(empty($up))
        {
            return json(['error'=>1,'msg'=>'修改失败']);
        }
        $cards = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->select();
        if($cards)
        {
            foreach ($cards as $k => $v) {
                $payment_controller = Db::name('payment')->where('payment_id',$v['card_pay_id'])->value('payment_controller');
                $result = Controller('pay/'.$payment_controller)->update_fee($v['card_pay_id'],$uid);
                // if($result['error'] != 0)
                // {
                //     return json(['error'=>1,'msg'=>'修改失败,请联系管理']);
                // }
            }
        }
        return json(['error'=>0,'msg'=>'修改成功']);
    }
    /**
     * 删除银行卡
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function card_del()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $cid = $post['cid'];//卡id

        $where['card_id'] = $cid;
        $where['card_uid'] = $uid;
        $card = Db::name('user_card')->where($where)->find();
        if(empty($card))
        {
            return json(['error'=>1,'msg'=>'银行卡不存在']);
        }
        elseif($card['card_state'] != 0 && $card['card_type']==1)
        {
            return json(['error'=>1,'msg'=>'计划中执行中,停止后可删除']);
        }
        elseif($card['card_type']==2)
        {
            return json(['error'=>1,'msg'=>'结算卡不能解绑']);
        }
        $type = 1;
        $cards = Db::name('payment_card')->where('card_cid',$cid)->where('card_uid',$uid)->select();
        if($card)
        {
            foreach ($cards as $k => $v) {
                $payment_controller = Db::name('payment')->where('payment_id',$v['card_pay_id'])->value('payment_controller');
                $result = Controller('pay/'.$payment_controller)->unbind_card($v['card_pay_id'],$uid,$cid);
                if($result['error'] != 0)
                {
                    $type = 0;
                }
            }
        }

        if($type==1)
        {
            $del = Db::name('user_card')->where(['card_id'=>$cid])->delete();
        }
        else
        {
            $del = Db::name('user_card')->where(['card_id'=>$cid])->update(['card_blocked'=>1]);
        }
        
        if(empty($del))
        {
            return json(['error'=>1,'msg'=>'解绑失败']);
        }
        return json(['error'=>0,'msg'=>'解绑成功']);

    }
    /**
     * 我的积分
     * @Author tw
     * @Date   2018-09-13
     */
    public function integral()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $user = Db::name('user')->where(['user_id'=>$uid])->field('user_total_integral,user_integral')->find();
        if (empty($user)) {
            return json(['error'=>1,'msg'=>'获取积分错误']);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$user]);
    }


    /**
     * 积分明细
     * @Author tw
     * @Date   2018-09-13
     */
    public function integral_list()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $user_integral = Db::name('user_integral')->where(['integral_uid'=>$uid])->paginate(10,false);
        if (empty($user_integral)) {
            return json(['error'=>1,'msg'=>'获取积分错误']);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$user_integral]);
    }
    /**
     * 我的银行卡
     * @Author tw
     * @Date   2018-09-15
     * @return [type]     [description]
     */
    public function card_list()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $where['card_uid'] = $uid;
        $where['card_type'] = 1;
        $where['card_blocked'] = 0;
        // $where['mission_state'] = 0;
        $list = Db::name('user_card')->alias('c')
                ->field('card_id,card_uid,card_bank_id,card_no,card_repayment_day,card_state,list_name,list_code,list_logo')
                ->join('bank_list b','b.list_id=c.card_bank_id','LEFT')
                ->where($where)
                ->order('card_state desc')
                ->select();
        
        foreach ($list as $key => $value) {
            $list[$key]['card_no'] = formatBankCardNo($value['card_no']);
            $list[$key]['card_type'] = '贷记卡';
            $list[$key]['card_real'] = '已认证';
            $list[$key]['list_logo'] = 'http://'.$_SERVER['HTTP_HOST'].$value['list_logo'];
            $mission = Db::name('mission')->where('mission_cid',$value['card_id'])->where('mission_state',1)->order('mission_id desc')->find();
            $list[$key]['mission_id'] = $mission['mission_id']?:0;
            if($mission)
            {

                $list[$key]['card_state_name'] = '还款中';
                $list[$key]['mission_money'] = $mission['mission_money']?:0;
                $list[$key]['mission_end_time'] = date('m-d', strtotime($mission['mission_end_time']));//计划结束日期
            }
            else
            {
                $list[$key]['card_state_name'] = '未还款';
                $list[$key]['mission_money'] = 0;
                $list[$key]['mission_end_time'] = '';//计划结束日期
            }
            $list[$key]['mission_end_time_name'] = '计划结束日期';
            $list[$key]['mission_pay_time_name'] = '还款日';
            if($value['card_repayment_day'] < date("d",time())){
                $BeginDate=date('Y-m-01', strtotime(date("Y-m-d")));
                $is_yue = date('m',time());
                if($is_yue == '01'){
                    $list[$key]['mission_pay_time'] = date("m-d",strtotime(date("y-").date( "m-", strtotime("first day of next month" )).$value['card_repayment_day']));
                }else{
                    $list[$key]['mission_pay_time'] = date("m-d",strtotime(date("y-").date("m-",strtotime("+1 month")).$value['card_repayment_day']));
                }
                $list[$key]["remainingday"]     = date('d', strtotime("$BeginDate +1 month -1 day"))-date("d",time())+$value['card_repayment_day'];
            }else{
                $list[$key]['mission_pay_time'] = date("m-",time()).$value['card_repayment_day'];
                $list[$key]["remainingday"]     = $value['card_repayment_day']-date("d",time());
            }

        }
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'没有信用卡','count'=>count($list)]);
        }

        return json(['error'=>0,'msg'=>'成功','count'=>count($list),'data'=>$list]);
    }

    /**
     * 申请代理状态
     * @Author tw
     * @Date   2018-09-17
     * @return [type]     [description]
     */
    public function agent()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $agent = Db::name('agent')->where(['agent_uid'=>$uid])->find();
        if(empty($agent))
        {
            return json(['error'=>0,'msg'=>'可申请','apply'=>1]);
        }
        elseif($agent['agent_state']==0)
        {
            return json(['error'=>0,'msg'=>'已经是代理','apply'=>0]);
        }
        elseif($agent['agent_state']==1)
        {
            return json(['error'=>0,'msg'=>'代理已冻结','apply'=>0]);
        }
        elseif($agent['agent_state']==2)
        {
            return json(['error'=>0,'msg'=>'资料已提交,等待审核','apply'=>0]);
        }
        elseif($agent['agent_state']==3)
        {
            return json(['error'=>0,'msg'=>'代理申请,已被拒绝','apply'=>1]);
        }
    }
    /**
     * 申请代理
     * @Author tw
     * @Date   2018-09-15
     * @return [type]     [description]
     */
    public function apply_agent()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        if(Db::name('agent')->where(['agent_uid'=>$uid])->find())
        {
            return json(['error'=>1,'msg'=>'不能重复申请']);
        }

        $name = $post['name'];//姓名
        $company = $post['company'];//公司
        $city = $post['city'];//申请代理城市
        $phone = $post['phone'];//手机号

        //数据添加
        $data['agent_uid']      = $uid;
        $data['agent_name']      = $name;
        $data['agent_phone']     = $phone;
        $data['agent_city']     = $city;
        $data['agent_company']     = $company;
        $data['agent_time']      = time();
        $data['agent_ip']        = get_client_ip6();
        $data['agent_state'] = 2;
        $res = Db::name('agent')->insertGetId($data);
        if(empty($res)){
            return json(['error'=>1,'msg'=>'申请失败']);
        }else{
            return json(['error'=>0,'msg'=>'提交成功,等待审核']);
        }
    }

    /**
     * 代理信息
     * @Author tw
     * @Date   2018-09-17
     * @return [type]     [description]
     */
    public function agent_info()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id
        $agent = Db::name('agent')->field('agent_id,agent_uid,agent_name,agent_phone,agent_city,agent_company')->where(['agent_uid'=>$uid])->find();

        if(empty($agent)){
            return json(['error'=>1,'msg'=>'代理商不存在']);
        }else{
            return json(['error'=>0,'msg'=>'成功','data'=>$agent]);
        }

    }
    /**
     * 申请代理修改
     * @Author tw
     * @Date   2018-09-15
     * @return [type]     [description]
     */
    public function agent_edit()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;//用户id

        $agent_id = $post['agent_id'];

        $agent = Db::name('agent')->where(['agent_id'=>$agent_id])->find();
        if(empty($agent_id))
        {
            return json(['error'=>1,'msg'=>'申请代理信息不存在']);
        }
        elseif($agent['agent_state']==0)
        {
            return json(['error'=>1,'msg'=>'已通过申请,不能修改']);
        }
        elseif($agent['agent_state']==1)
        {
            return json(['error'=>1,'msg'=>'代理已冻结,不能修改']);
        }

        $name = $post['name'];//姓名
        $company = $post['company'];//公司
        $city = $post['city'];//申请代理城市
        $phone = $post['phone'];//手机号

        
        $data['agent_uid']      = $uid;
        $data['agent_name']      = $name;
        $data['agent_phone']     = $phone;
        $data['agent_city']     = $city;
        $data['agent_company']     = $company;
        $data['agent_time']      = time();
        $data['agent_ip']        = get_client_ip6();
        $data['agent_state'] = 2;
        $res = Db::name('agent')->where(['agent_id'=>$agent_id])->update($data);
        if(empty($res)){
            return json(['error'=>1,'msg'=>'修改失败']);
        }else{
            return json(['error'=>0,'msg'=>'修改成功,等待审核']);
        }
    }

    /**
     * 用户费率
     * @Author tw
     * @Date   2018-09-14
     * @return [type]     [description]
     */
    public function rate()
    {
        if($this->request->isPost()) {
            $post = input('post.');
            if(empty($post))
            {
                return json(['error'=>1,'msg'=>'参数错误']);
            }
            $uid = $this->uid;
            $user = Db::name('user')->where(['user_id'=>$uid])->find();
            if(empty($user))
            {
                return json(['error'=>1,'msg'=>'会员不存在']);
            }
            $rate = Db::name('rate')->where(['rate_type_id'=>$user['user_type_id']])->order('rate_type asc')->select();
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
                    continue;
                }
                $data['rate_uid'] = $uid;
                $data['rate_type'] = $value['rate_type'];
                Db::name('user_rate')->insert($data);

            }
            return json(['error'=>0,'msg'=>'成功']);
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }

    /**
     * 变更结算卡
     * [Rate description]
     */
    public function chagcard(){
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid  = $this->uid; //用户id
        $crdid   = $post['crdid']; // 用户银行卡ID
        $phone   = $post['phone']; //银行预留手机号
        $bank_id = $post['bank_id']; //银行ID
        $card_no = myTrim($post['card_no']); //银行卡号
        $type    = '2'; //储蓄卡
        
        $province = $post['province']; //省
        $city     = $post['city']; //市
        $branch   = $post['branch']; //支行

        $user = Db::name('user')->where(['user_id'=>$uid])->find();
        if(empty($user) || $user['user_state']==1){
            return json(['error'=>1,'msg'=>'用户不存在']);
        }
        if($user['user_real']<>1){
            return json(['error'=>1,'msg'=>'用户未实名']);
        }

        $idcard = $user['user_idcard']; //用户身份证

        if(Db::name('user_card')->where(['card_uid'=>$uid,'card_no'=>$card_no,'card_blocked'=>0])->find())
        {
            return json(['error'=>1,'msg'=>'重复绑定']);
        }

        //待写四要素验证接口（检测银行信息是否无误！）

        $data['card_id']       = $crdid;
        $data['card_uid']      = $uid; //用户id
        $data['card_no']       = $card_no; //银行卡号
        $data['card_name']     = $name; //用户名
        $data['card_phone']    = $phone; //银行预留手机号
        $data['card_bank_id']  = $bank_id; //银行id
        $data['card_type']     = '2'; //储蓄卡
        $data['card_state']    = 0; // 状态正常
        $data['card_province'] = $province;//省
        $data['card_city']     = $city;//市

        $updata = Db::name('user_card')->update($data);
        if(empty($updata))
        {
            return json(['error'=>1,'msg'=>'变更失败']);
        }
        return json(['error'=>0,'msg'=>'变更成功']);
    }

    /**
     * 个人资料
     * @Author tw
     * @Date   2018-09-17
     * @return [type]     [description]
     */
    public function info()
    {   
        $config = require CACHE_PATH.'system.php';
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;
        if(empty($uid))
        {
            return json(['error'=>1,'msg'=>'失败']);
        }

        $list = Db::name('User as u')
              ->join('UserType t','u.user_type_id=t.type_id')
              ->where('u.user_id',$uid)
              ->find();
        unset($list['user_password']);
        if(!$list['user_name']){
            $list['user_name'] = '未认证';
        }
        $list['config_activation'] = $config['USER_ACTIVATION'];
        $list['config_activmoney'] = $config['USER_ACTIVATION_TYPE'];
        
        $list['user_img']       = $list['user_img']?'http://'.$_SERVER['HTTP_HOST'].$list['user_img']:getconfig('SITE_LOGO');
        $list['user_idcard_z'] = $list['user_idcard_z']?'http://'.$_SERVER['HTTP_HOST'].$list['user_idcard_z']:'';
        $list['user_idcard_f'] = $list['user_idcard_f']?'http://'.$_SERVER['HTTP_HOST'].$list['user_idcard_f']:'';
        $list['user_hand_img'] = $list['user_hand_img']?'http://'.$_SERVER['HTTP_HOST'].$list['user_hand_img']:'';
        
        if(!empty($list))
        {
            return json(['error'=>0,'msg'=>'成功','data'=>$list]);
        }else{
            return json(['error'=>1,'msg'=>'失败']);
        }
    }

    /**
     * 查询会员是否是代理商及配置
     * @Author tw
     * @Date   2018-09-17
     * @return [type]     [description]
     */
    public function Agency()
    {
        if($this->request->isPost())
        {
            $config = require CACHE_PATH.'system.php';
            $data = Db::name('AgentRecord')
                ->where('agent_user_id',$this->uid)
                ->where('agent_recode_state','in',array('0','1'))
                ->find();

            $date = array();
            $user = $this->user;
            $date['tel'] = $config['SITE_TEL'];
            if(empty($data))
            {   
                $date['phone'] = $user['user_phone'];
                return json(['error'=>0,'msg'=>'请求成功','data'=>$date]);
            }else{
                $date['type'] = $data['agent_recode_state'];
                return json(['error'=>1,'msg'=>'您已是代理商','data'=>$date]);
            }
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }

    /**
     * 代理商申请入驻
     * @Author tw
     * @Date   2018-09-17
     * @return [type]     [description]
     */
    public function AgentAdmission()
    {
        if($this->request->isPost())
        {
            $post = input('post.');
            if(empty($post))
            {
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            $agent_recode_phone   = $post['phone'];
            $list = Db::name('AgentRecord')
                ->where('agent_user_id',$this->uid)
                ->where('agent_recode_phone',$agent_recode_phone)
                ->find();
            if(!empty($list))
            {
                return json(['error'=>1,'msg'=>'手机号已被注册']);
            }

            $data = array();
            $data['agent_user_id']        = $this->uid;
            $data['agent_recode_city']    = $post['city'];
            $data['agent_recode_name']    = $post['name'];
            $data['agent_recode_company'] = isset($post['company'])?$post['company']:'';
            $data['agent_recode_phone']   = $agent_recode_phone;
            $data['agent_recode_state']   = 0;
            $data['agent_recode_time']    = time();

            if(Db::name('AgentRecord')->insert($data))
            {
                return json(['error'=>0,'msg'=>'申请成功']);
            }else{
                return json(['error'=>1,'msg'=>'申请失败']);
            }

        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }


    /**
     * 帮助中心
     * @Author tw
     * @Date   2018-09-17
     * @return [type]     [description]
     */
    public function issue()
    {   
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $type = input('get.type',0);
        $hot = input('get.hot',0);
        $where = array();
        if($type)
        {
            $where['type'] = $type;
        }
        if($hot)
        {
            $where['hot'] = 1;
        }
        $list = Db::name('issue')->where($where)->paginate(10,false);
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'没有相关资料']);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }
    /**
     * 帮助中心 分类
     * @Author tw
     * @Date   2018-09-17
     */
    public function issue_type()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $where = array();
        $where['type_status'] = 0;
        $list = Db::name('issue_type')->where($where)->select();
        foreach ($list as $key => $value) {
            $list[$key]['type_icon'] = 'http://'.$_SERVER['HTTP_HOST'].$value['type_icon'];
        }
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'没有分类']);
        }
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }

    /**
     * 银行列表
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function bank_list()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $list = Db::name('bank_list')->select();
        return json(['error'=>0,'msg'=>'成功','data'=>$list]);

    }
    /**
     * 获取银行名称
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function get_bank_id()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $card_no = myTrim($post['card_no']);
        
       $result = Controller('Bank')->get_bank($card_no);
       if(empty($result) or $result['error']==1){
           return json(['error'=>1,'msg'=>$result['msg']]);
       }else{
          $result['id'] = $result['bankid'];
       }
        $bank = Db::name('bank_list')->where(['list_id'=>$result['id']])->find();
        if(empty($bank))
        {
            return json(['error'=>1,'msg'=>'暂时不支持该银行']);
        }
        $bank['list_logo'] = 'http://'.$_SERVER['HTTP_HOST'].$bank['list_logo'];
        return json(['error'=>0,'msg'=>'成功','data'=>$bank]);
    }

    /**
     * 用户头像修改
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function user_img()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        $uid = $this->uid;
        $img = $post['img'];
        if(empty($img))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }

        // file_put_contents('log/'.date("Ymd",time()).'.log', var_export($_POST, true), FILE_APPEND);
        // $image = base64_decode($img);
        // $img_path = 'uploads/images/'.date("Ymd",time()).'/'.uniqid().'.jpg';
        // if(empty(file_put_contents($img_path,$image)))
        // {
        //     return json(['error'=>1,'msg'=>'头像上传失败']);
        // }

        $up = Db::name('user')->where(['user_id'=>$uid])->update(['user_img'=>$img]);
        if(!$up)
        {
            return json(['error'=>1,'msg'=>'上传失败']);
        }
        return json(['error'=>0,'msg'=>'上传成功']);
    }

    /**
     * 分享
     * @Author tw
     * @Date   2018-09-18
     * @return [type]     [description]
     */
    public function share()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'非法请求']);
        }
        //去判断用户是否实名了
        $User = Db::name('User')->where('user_id',$this->uid)->find();
        if(empty($User))
        {
            return json(['error'=>1,'msg'=>'用户不存在']);
        }
        if($User['user_real']!=1)
        {
            return json(['error'=>1,'msg'=>'用户未实名，请先实名认证']);
        }
        $uid = $this->uid;
        $user = $this->user;
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/?id='.$user['user_code'];
        return json(['error'=>0,'msg'=>'成功','url'=>$url]);
    }
    
    
    public function sharecode(){

        //去判断用户是否实名了
        $User = Db::name('User')->where('user_id',$this->uid)->find();
        if(empty($User))
        {
            return json(['error'=>1,'msg'=>'用户不存在']);
        }
        if($User['user_real']!=1)
        {
            return json(['error'=>1,'msg'=>'用户未实名，请先实名认证']);
        }

        $img = Db::name('img')->where('img_type',1)->where('img_start_switch',1)->find();
        if(!$img){
            return json(['error'=>1,'msg'=>'推广素材不存在']);
        }
        
        $upload_path = 'uploads/qrcode/promoteuser';//存放二维码海报推广路径
        $create_path = $upload_path.'/'.$img['img_id'].'_'.$this->uid.'.png';
        //判断服务器文件是否存在已经生成
        if (file_exists($create_path)) {
            return json(['error'=>0,'msg'=>'请求成功','imgurl'=>'http://'.$_SERVER["SERVER_NAME"].'/'.$create_path]);
        }else{
            //生成的网址
            $urls = 'http://'.$_SERVER["SERVER_NAME"].'?id='.$this->uid;
            $thumb_qrcode = 'uploads/qrcode/promoteuser/thumb_template/'.$this->uid.'.png';//推广二维码的地址
    
            if (!file_exists($thumb_qrcode)) {
                //不存在的话直接生成二维码然后进行合成
                getQRcode($urls,'uploads/qrcode/promoteuser/thumb_template',$this->uid);
            }
            
            $user_name    = '12312312';
            $user_logo    = 'public/static/images/default_img_url/logocode.png';//平台logo地址
            $shop_logo    = '';//店铺logo 
            $user_headimg = $this->user['user_img'];//头像没有的话给个默认url
            $path =$thumb_qrcode;//存放位置 为空是直接显示图片
            //定位 根据 左上像素值   
            $data = array();
            $data['background']      = 'public'.$img['img_url'];
            $data['nick_font_color'] = '#00ffff';
            $data['nick_font_size']  = '30';
            $data['is_logo_show']    = '0';
            $data['header_left']     = '80';
            $data['header_top']      = '455';
            $data['name_left']       = '137';
            $data['name_top']        = '469';
            $data['logo_left']       = '100';
            $data['logo_top']        = '67';
            $data['code_left']       = '85';
            $data['code_top']        = '158';
            showUserQecode($upload_path, $path, $thumb_qrcode, $user_headimg, $shop_logo, $user_name, $data, $create_path);
            
            return json(['error'=>0,'msg'=>'请求成功','imgurl'=>'http://'.$_SERVER["SERVER_NAME"].'/'.$create_path]);
        }
        
        
    }
    
    
    
    /**
     * 我的费率
     * [Rate description]
     */
    public function UserRate(){
        if($this->request->isPost()){
            $post = input('post.');
            if(empty($post))
            {
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            $list = Db::name('UserRate')->where(['rate_uid'=>$this->uid])->select();
            if(empty($list)){
                return json(['error'=>1,'msg'=>'获取失败']);
            }else{
                return json(['error'=>0,'msg'=>'获取成功','data'=>$list]);
            }            
        }else{

            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }
    /**
     * 会员权益
     */
    public function Membership(){
        $config = require CACHE_PATH.'system.php';
        if($this->request->isPost())
        {
            $post = input('post.');
            if(empty($post))
            {
                return json(['error'=>1,'msg'=>'参数错误']);
            }

            $UserType = ClassRates();

            if(empty($UserType))
            {
                return json(['error'=>1,'msg'=>'查无会员权益']);
            }
            $h = array();
            $s = array();
            foreach ($UserType as $key => $val) {
                if($val['type_fee']==0 && $val['type_free_count']==0 && $val['type_free_amount']==0)
                {
                    unset($val);
                } 
                unset($val['type_free_count']);
                unset($val['type_free_amount']);
                //判断是百分比还是固定值返 1百分比  0固定值
                if($config['USER_DISTRI_TYPE_CZ']==1)
                {   
                    if($val['type_id'] == '2'){
                        $val['money_z'] = $val['type_fee'] * ($config['USER_DISTRI_TYPE_CZ_1']/100);
                        $val['money_j'] = $val['type_fee'] * ($config['USER_DISTRI_TYPE_CZ_2']/100);
                        if($val['type_profit'] == 1)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']).'元';
                                }   
                            }
                        }elseif($val['type_profit'] == 2)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']).'元';
                                }   
                            }
                        }else
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 %';
                                    $val['rate_h_j'] = '0 %';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 %';
                                    $val['rate_s_j'] = '0 %';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 元';
                                    $val['rate_h_j'] = '0 元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 元';
                                    $val['rate_s_j'] = '0 元';
                                }   
                            }
                        }


                    }elseif($val['type_id'] == '8'){
                        $val['money_z'] = $val['type_fee'] * ($config['USER_DISTRI_TYPE_CZ_1']/100);
                        $val['money_j'] = $val['type_fee'] * ($config['USER_DISTRI_TYPE_CZ_2']/100);
                        if($val['type_profit'] == 1)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']).'元';
                                }   
                            }                            
                        }elseif($val['type_profit'] == 2)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']).'元';
                                }   
                            } 
                        }else
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 %';
                                    $val['rate_h_j'] = '0 %';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 %';
                                    $val['rate_s_j'] = '0 %';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 元';
                                    $val['rate_h_j'] = '0 元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 元';
                                    $val['rate_s_j'] = '0 元';
                                }   
                            } 
                        }

                    }elseif($val['type_id'] == '1'){

                        $val['money_z'] = $val['type_fee'] * ($config['USER_DISTRI_TYPE_CZ_1']/100);
                        $val['money_j'] = $val['type_fee'] * ($config['USER_DISTRI_TYPE_CZ_2']/100);
                        if($val['type_profit'] == 1)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']).'元';
                                }   
                            }
                        }elseif($val['type_profit'] == 2)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']).'元';
                                }   
                            }
                        }else{
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 %';
                                    $val['rate_h_j'] = '0 %';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 %';
                                    $val['rate_s_j'] = '0 %';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 元';
                                    $val['rate_h_j'] = '0 元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 元';
                                    $val['rate_s_j'] = '0 元';
                                }   
                            }
                        }

                    }

                }else{
                   if($val['type_id'] == '2'){

                        $val['money_z'] = $config['USER_DISTRI_TYPE_CZ_1'];
                        $val['money_j'] = $config['USER_DISTRI_TYPE_CZ_2'];
                        if($val['type_profit'] == 1)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']).'元';
                                }   
                            }
                        }elseif($val['type_profit'] == 2)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']).'元';
                                }   
                            }
                        }else
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 %';
                                    $val['rate_h_j'] = '0 %';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 %';
                                    $val['rate_s_j'] = '0 %';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 元';
                                    $val['rate_h_j'] = '0 元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 元';
                                    $val['rate_s_j'] = '0 元';
                                }   
                            }
                        }

                    }elseif($val['type_id'] == '8'){

                        $val['money_z'] = $config['USER_DISTRI_TYPE_CZ_1'];
                        $val['money_j'] = $config['USER_DISTRI_TYPE_CZ_2'];
                        if($val['type_profit'] == 1)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']).'元';
                                }   
                            }
                        }elseif($val['type_profit'] == 2)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']).'元';
                                }   
                            }
                        }else
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 %';
                                    $val['rate_h_j'] = '0 %';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 %';
                                    $val['rate_s_j'] = '0 %';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 元';
                                    $val['rate_h_j'] = '0 元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 元';
                                    $val['rate_s_j'] = '0 元';
                                }   
                            }
                        }

                    }elseif($val['type_id'] == '1'){

                        $val['money_z'] = $config['USER_DISTRI_TYPE_CZ_1'];
                        $val['money_j'] = $config['USER_DISTRI_TYPE_CZ_2'];
                        if($val['type_profit'] == 1)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_2']).'元';
                                }   
                            }
                        }elseif($val['type_profit'] == 2)
                        {
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']/100).'%';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']/100).'%';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']/100).'%';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']/100).'%';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = ($config['USER_DISTRI_MODE_HK_V_1']).'元';
                                    $val['rate_h_j'] = ($config['USER_DISTRI_MODE_HK_V_2']).'元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = ($config['USER_DISTRI_MODE_SK_V_1']).'元';
                                    $val['rate_s_j'] = ($config['USER_DISTRI_MODE_SK_V_2']).'元';
                                }   
                            }
                        }else{
                            if($config['USER_DISTRI_TYPE'] ==1)
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 %';
                                    $val['rate_h_j'] = '0 %';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 %';
                                    $val['rate_s_j'] = '0 %';
                                }                            
                            }else
                            {
                                if($val['rate_type']==1)
                                {
                                    $val['rate_h_z'] = '0 元';
                                    $val['rate_h_j'] = '0 元';
                                }elseif($val['rate_type']==2)
                                {
                                    $val['rate_s_z'] = '0 元';
                                    $val['rate_s_j'] = '0 元';
                                }   
                            }
                        }

                    }
                }
                $val['own_s'] = $config['POINTS_RECEIVABLES'].'元';
                $val['own_h'] = $config['POINTS_REPAYMENT'].'元';
                $val['handle'] = $config['USER_CRARD_TYPE_CZ_1'].'%';
                $val['netloan'] = $config['USER_LOAN_TYPE_CZ_1'].'%';
                if($val['rate_type']==1)
                {
                    $h[] = $val;
                }
                if($val['rate_type']==2){
                    $s[] = $val;
                }
            }
            $Receivables = array();
            $Receivables['sk'] = $s;
            $Receivables['hk'] = $h;
            if(!empty($Receivables))
            {
                return json(['error'=>0,'msg'=>'成功','data'=>$Receivables]);
            }else{
                return json(['error'=>1,'msg'=>'查询失败']);
            }
        }else{
            return json(['error'=>1,'msg'=>'非法请求']);
        }
    }   
    /*
     * 获取分润
     * 2018年10月11日15:57:37
     * 刘媛媛
     */
    public function getbonus(){
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'获取分润错误']);
        }
        $uid = $this->uid;
        $count = array();
        $count['money'] = $this->user['user_moeny'];
        $userCount = Db::name('user')->where('user_pid',$uid)->select();
        $count['puser'] = count($userCount);
        /*$count['countuser'] = 0;
        if($userCount>0){
            
            foreach($userCount as $uk=>$uv){
                $count['countuser'] += count(getuserlevel($uv['user_id'],false));
            }
            $count['countuser']-=$count['puser'];
        }*/
        $count['countuser'] = count(getuserlower($uid,false,2));
        //分润状态 1升级分润 2收款分润 3还款分润 4 交易分润 5贷款分润 6 办信用卡分润 7其他分润
        
        $list = Db::name('userBonuslog')->where('blog_user',$this->uid)->where('blog_state',0)->select();
        $count['money_1']=0;
        $count['count_1']=0;
        $count['money_2']=0;
        $count['count_2']=0;
        $count['money_3']=0;
        $count['count_3']=0;
        $count['money_4']=0;
        $count['count_4']=0;
        $count['money_5']=0;
        $count['count_5']=0;
        $count['money_6']=0;
        $count['count_6']=0;
        $count['money_7']=0;
        $count['count_7']=0;
        $count['coutmoney']  =0;
        foreach ($list as $k=>$v){
            $count['coutmoney'] +=$v['blog_money'];
            switch ($v['blog_type'])
            {
                case 1:
                # 升级分润
                $count['money_1'] += $v['blog_money'];
                $count['count_1'] ++;
            break;
                case 2:
                # 收款分润
                $count['money_2'] += $v['blog_money'];
                $count['count_2'] ++;
            break;
                case 3:
                # 还款分润
                $count['money_3'] += $v['blog_money'];
                $count['count_3'] ++;
            break;
                case 4:
                # 交易分润
                $count['money_4'] += $v['blog_money'];
                $count['count_4'] ++;
            break;
                case 5:
                # 贷款分润
                $count['money_5'] += $v['blog_money'];
                $count['count_5'] ++;
            break;
                case 6:
                # 办信用卡分润
                $count['money_6'] += $v['blog_money'];
                $count['count_6'] ++;
            break;
                case 7:
                # 其他分润
                $count['money_7'] += $v['blog_money'];
                $count['count_7'] ++;
            break;
            }
        }
        
        return json(['error'=>0,'msg'=>'请求成功','data'=>$count]);
    }
     
    /**
     * 解绑卡片
     * @Author tw
     * @Date   2018-10-16
     * @return [type]     [description]
     */
    public function unbind_card()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $pay_id = $post['pay_id'];//支付通道id
        $cid = $post['cid'];//银行卡id
        $uid = $this->uid;//用户id

        $payment_controller = Db::name('payment')->where('payment_id',$pay_id)->value('payment_controller');
        if(empty($payment_controller))
        {
            return json(['error'=>1,'msg'=>'通道不存在']);
        }
        $result = Controller($payment_controller)->unbind_card($pay_id,$uid,$cid);
        return json($result);
    }
    /**
     * 获取用户下级用户
     * @Author tw
     * @return [type] [description]
     */
    public function user_lower()
    {
        $post = input('post.');
        if(empty($post))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }
        $type = $post['type'];//银行卡id
        if(empty($type))
        {
            $type = 3;
        }
        $uid = $this->uid;//用户id
        $user_id=getuserlower($uid,false,$type);
        $list = Db::name('user')->field('user_id,user_name,user_phone,user_time')->where('user_state',0)->whereIn('user_id',$user_id)->order('user_id desc')->paginate(15,false);
        if(empty($list))
        {
            return json(['error'=>1,'msg'=>'参数错误']);
        }

        if($list->toArray())
        {
            //0未启动 1还款中 2已还完 3还款失败
            foreach($list as $k=>$v){
                $data = array();
                $data = $v;
                // $data['user_name'] = hide_str($v['user_name'],1,-1);
                if(empty($data['user_name']))
                {
                    $data['user_name'] = '未实名';
                }
                // $data['user_phone'] = hide_tel($v['user_phone']);
                $data['user_time'] = date('Y-m-d',$v['user_time']);
                $list->offsetSet($k,$data);
            } 
        }

        return json(['error'=>0,'msg'=>'成功','data'=>$list]);
    }
}
