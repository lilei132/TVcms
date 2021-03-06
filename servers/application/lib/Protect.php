<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/2/21
 * Time: 10:46
 */

namespace app\lib;


use app\admin\service\Token;
use app\admin\service\User;
use think\Request;

class Protect
{

    protected static $expire = 86400;
    /*
     * 检查登录用户请求IP限制
     */
    public static function IpAndSidCount() {
        $request = Request::instance();
        $user = User::init();
        $id = $user['user_id'];
        $ip = $request->ip();
        $key = $id.DS.$ip;
        $count = Redis::init()->incr($key);
        if($count == 1) {
            Redis::init()->expire($key,self::$expire);
        }
        if($count>50) {
            $log = [
                'member_id' => $id,
                'ip' => $ip,
                'url' => $request->url(true)
            ];
            new WriteLog($log);
            return new Response(['msg'=>'最近24小时请求过多了哦']);
        }
    }

}