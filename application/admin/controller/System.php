<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class System extends Base{
    public function initialize() {
        parent::initialize();
        $this->assign('menu','system');
    }
    public function index()
    {	
    	
        $config_file = CACHE_PATH.'/system.php';

        if($this->request->isGet()) {
            $usertype = Db::name('UserType')->where('type_state',0)->where('type_fee','neq','0')->select();
            $agentlist = Db::name('agent')->select();
            $this->assign('usertype',$usertype);
            $this->assign('agentlist',$agentlist);
            $this->assign('config',require $config_file);
            $this->assign('method','indexsys');
            $this->assign('other',file_get_contents(CACHE_PATH.'keywords.txt'));
            return $this->fetch();
        }
		
        if($this->request->isPost()) {
            $post   = input('post.');
            //判断表中有无参数，有则更新，无则添加
            foreach($post as $key=>$value) {
                $dbKey = strtoupper($key);
                if(db::name('system')->where("key",$dbKey)->find()) {
                    db::name('system')->where("key",$dbKey)->update(["value"=>$value]);
                } else {
                    db::name('system')->insert(["key"=>$dbKey,"value"=>$value]);
                }
            }

            $new_config = array();
            $siteConfig = db::name('system')->order('key','ASC')->select();
            foreach($siteConfig as $params){
                $new_config[$params['key']] = $params['value'];
            }
           
            $this->update_config($new_config, $config_file);
            return json(['error'=>0,'msg'=>'修改成功']);
        }
    }
	public function other(){
		
		$txt   = input('post.keywords');
		if($txt==''){
			return json(['error'=>1,'msg'=>'请输入违规词']);
		}
		$txtUrl = CACHE_PATH.'keywords.txt';
		$myfile = fopen($txtUrl, "w");
		if(!$myfile){
			return json(['error'=>1,'msg'=>'文件不存在']);
		}
		fwrite($myfile, $txt);
		fclose($myfile);
		return json(['error'=>0,'msg'=>'设置成功']);
	}
	
	public function smslog(){
		
		$list = Db::name('UserVerify')->order('send_time desc')->paginate(10);
        $this->assign('list', $list);
        return $this->fetch();
	}
	
    private function update_config($new_config, $config_file = '') {
        !is_file($config_file) && $config_file = CACHE_PATH . '/#.php';
        if(is_writable($config_file)) {

            $config = require $config_file;

            $config = array_merge($config, $new_config);
            file_put_contents($config_file, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";", LOCK_EX);
            @unlink(RUNTIME_FILE);
            return true;
        } else {
            return false;
        }
    }
}