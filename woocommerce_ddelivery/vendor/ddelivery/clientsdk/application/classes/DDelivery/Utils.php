<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 4/10/15
 * Time: 11:03 PM
 */

namespace DDelivery;


class Utils {


    public static function generateToken(){
        $rand = rand( -1000, 1000 );
        $token = md5( self::getUserHost() . $rand . time() );
        return $token;
    }

    public static function urlOrigin($s, $use_forwarded_host=false)
    {
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }


    public static function fullUrl($s, $use_forwarded_host=false)
    {
        return self::urlOrigin($s, $use_forwarded_host) . $s['SCRIPT_NAME'];
    }

    public static function getUserHost(){
        if (!empty($_SERVER['HTTP_X_REAL_IP'])){
            $ip=$_SERVER['HTTP_X_REAL_IP'];
        }elseif (!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            if(isset($_SERVER['REMOTE_ADDR'])){
                $ip=$_SERVER['REMOTE_ADDR'];
            }else{
                $ip=rand( -1000, 1000 );
            }

        }
        return $ip;
    }
} 