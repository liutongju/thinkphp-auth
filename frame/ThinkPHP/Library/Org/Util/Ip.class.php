<?php
namespace Org\Util;

/**
 * IP相关帮助类
 * @author  Evan<tangzhaowen@hiservice.com.cn>
 * @since   2015-11-02
 */
class Ip {

    /**
     * 获取客户端IP地址
     * @param  boolean $proxy_override 是否代理优先 默认是 不过好像没啥作用了
     * @return [type] [description]
     */
    public static function get($proxy_override = true) {
        if ($proxy_override) {
            //优先从代理那获取地址或者 HTTP_CLIENT_IP 没有值
            $ip = empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_CLIENT_IP"] : $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            //取 HTTP_CLIENT_IP, 虽然这个值可以被伪造, 但被伪造之后 NS 会把客户端真实的 IP 附加在后面
            $ip = empty($_SERVER["HTTP_CLIENT_IP"]) ? NULL : $_SERVER["HTTP_CLIENT_IP"];
        }

        if (empty($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            if (self::isLocal($ip)) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }

        //真实的IP在以逗号分隔的最后一个, 当然如果没用代理, 没伪造IP, 就没有逗号分离的IP
        if ($p = strrpos($ip, ",")) {
            $ip = substr($ip, $p + 1);
        }

        return trim($ip);
    }

    /**
     * 是否是局域网保留IP段
     * @param  string  $ip 要判断的IP地址
     * @return boolean     是否是局域网IP
     */
    public static function isLocal($ip) {
        $l = ip2long($ip);
        if (($l > 167772160 && $l < 184549375) || ($l > 2886729728 && $l < 2887778303) || ($l > 3232235520 && $l < 3232301055)) {
            return true;
        }
        return false;
    }
}