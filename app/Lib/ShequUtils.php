<?php
/**
 * Created by PhpStorm.
 * User: staff
 * Date: 2018/8/14
 * Time: 上午11:24
 */

namespace App\Lib;


class ShequUtils
{
    const KEY_LIGHT = 'shequ_light_2018_user_id';
    const KEY_SHEQUPUBLICWELFARE2018 = 'shequ_public_welfare_2018_user_id';

    const KEY_SHUSHANJIEDAO_VOTE2108 = "shequ_shushanjiedao_vote";

    const KEY_QCMBDSP_VOTE2018 = "shequ_mbdsp2018_vote";

    public static function getShequDomain()
    {
        return SiteUtils::getEnv() == 'local'?'http://majun2.sqdev.365jia.com':'http://365shequ.com';
    }

    public static function curlGet($url, $header)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $ret = json_decode(curl_exec($ch), true);

        curl_close($ch);

        return $ret;
    }

    /**
     * 返回用户信息['id' => 201889, 'avatar' => '', '']
     * @param $key
     * @return mixed|string
     */
    public static function getUserData($key)
    {
        if (!$key) {
            return '';
        }

//        return ['id' => 32868, 'nick_name' => '产品部测试4', 'avatar' => 'https://wx.qlogo.cn/mmopen/vi_32/9dWfsO0TlWqA5urxR8eFVzibjy4glQbBc6dYCavnlrpz62YKwqHXW1tUJe5EEZ8EibZd5JRkXHtk8gaQWhcpO3Yg/132'];

        if (session()->exists($key)) {
            return json_decode(session()->get($key), true);
        }

        $session = \Request::get('session');

        $url = self::getShequDomain()."/wechat/api/wj/get_user_data";

        $header = ["x-wechat-access-token: {$session}"];

        $ret = self::curlGet($url, $header);

        if (isset($ret['code']) && $ret['code'] == 0) {
            session()->put($key, json_encode($ret['data']));

            return json_decode(session()->get($key), true);
        }

        return [];

    }

    /**
     * 社区小程序授权时的地址
     * @param $url
     * @param $needLogin
     * @return string
     */
    public static function getMiniAuthUrl($url, $needLogin)
    {
        return "/pages/browser/browser?url=".urlencode($url)."&need_login=".(int)$needLogin;
    }

    public static function getDomain()
    {
        if (SiteUtils::getEnv() == 'production') {
            return 'https://zt.365jia.cn';
        }

        return 'http://zt.liqi.dev.365jia.com';
        //return 'http://zt.majun.dev.365jia.com';

    }

    //不使用 https
    public static function getDomainV2()
    {
        if (SiteUtils::getEnv() == 'production') {
            return 'http://zt.365jia.cn';
        }

        return 'http://zt.liqi.dev.365jia.com';
        //return 'http://zt.majun.dev.365jia.com';

    }

    /**
     * 获取分享连接的地址
     * @param int $needLogin 是否需要登录授权
     * @return string
     */
    public static function getShareUrl($needLogin = 0, $query = '')
    {
        $domain = self::getDomain();
        $pathInfo = $_SERVER['PATH_INFO'];
        $url = self::getMiniAuthUrl("{$domain}{$pathInfo}?{$query}", $needLogin);

        return $url;
    }

    public static function getQrCodeImage($path)
    {
        $path = base64_encode($path);
        $url = self::getShequDomain() . "/wechat/common/create_qr_image?path=" . $path;

        $session = \Request::get('session');
        $header = ["x-wechat-access-token: {$session}"];

        $ret = self::curlGet($url, $header);

        if (isset($ret['code']) && $ret['code'] == 0) {

            return $ret['data'];
        }

        return '';
    }
}