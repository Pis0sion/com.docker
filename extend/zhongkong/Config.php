<?php
namespace zhongkong;
/**
 * Created by PhpStorm.
 * User: Adi
 * Date: 2018/7/24
 * Time: 12:47
 */


class Config
{	

    public static $baseHost   = "https://lion-api.51ley.com/apis"; 
    public static $oemID      = 1007180566354722817;
    public static $merchant   = "15737708825";
    public static $publicKey  = "/www/wwwroot/huanka/extend/zhongkong/publicKey.pem";
    public static $privateKey = "/www/wwwroot/huanka/extend/zhongkong/privateKey.pem"; //file_get_contents
	
    static function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }
}