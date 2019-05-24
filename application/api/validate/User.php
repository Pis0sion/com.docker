<?php
namespace app\api\validate;

use think\Validate;

class User extends Validate
{	
        
    protected $rule =   [
        'phone'		 =>  'require|mobile',
        'keywords'   => 'require|length:6,16',
        'rekeywords' => 'require|confirm:keywords',
        'smscode'    => 'require|number|length:6',
    ];
    
    protected $message  =   [
        'smscode.require'    => '请填写验证码',
        'smscode.number'     => '验证码输入错误',
        'smscode.length'     => '验证码长度错误',
        'keywords.require'   => '请填写密码',
        'keywords.length'    => '密码长度错误',    
        'rekeywords.require' => '请填写确认密码',    
        'rekeywords.confirm' => '确认密码不一致！',    
        'phone.require'      => '手机号码必须填写',    
        'phone.mobile'  	 => '手机号码错误！',    
    ];
    
    protected $scene = [
        'reg'  =>  ['phone','keywords','rekeywords','smscode'],
    ];
    
}