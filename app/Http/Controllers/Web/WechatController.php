<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/26
 * Time: 下午1:45
 */

namespace App\Http\Controllers\Web;


use App\Http\Controllers\BaseController;
use App\Http\Response\RedirectMessageResponse;
use App\Lib\SiteUtils;
use App\Models\Merchant;
use App\Models\Project;
use App\Zhuanti\Redpacket\RedpacketVote;
use App\Zhuanti\WechatLogic;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Wanjia\Common\Exceptions\AppException;

class WechatController extends BaseController
{
    //公共微信专题服务
    protected static $global_services = [
        'vote'      => \App\Zhuanti\WechatLogics\Vote::class,
        'jizi'      => \App\Zhuanti\WechatLogics\JiZi::class,
        'prizes'      => \App\Zhuanti\WechatLogics\Prizes::class,
        'bargain'      => \App\Zhuanti\WechatLogics\Bargain::class,
        'material'      => \App\Zhuanti\WechatLogics\Material::class,
        'hongbao'      => RedpacketVote::class,
    ];

    //vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
    // 第三方平台授权相关

    /**
     * 微信帐号绑定
     * @param Request $request
     * @return RedirectMessageResponse
     */
    public function bind(Request $request)
    {
        $code = $request->route('code');



        $merchant = Merchant::repository()->findOneByPreAuthCode($code);
        if (!$merchant) {
            return redirect_message('Sorry, 未找到预授权信息.', '/', 3);
        }

        if ($merchant->pre_auth_code_expire_at->diffInSeconds(Carbon::now()) <= 0) {
            return redirect_message('Sorry, 授权链接已失效, 请联系客服重新获取.', '/', 3);
        }


        $platform = \EasyWeChat::open_platform();
        $response = $platform->pre_auth->redirect(route('wechat.bind_callback', ['code' => $code]));
        $url = $response->getTargetUrl();

        //下一个跳转微信需要一个 referer 头做验证; 不能直接用 header 头的方式跳转; 所以做了一个前端跳转的 Response
        return redirect_message('即将授权 【万家专题业务平台】 作为您微信专题业务支撑方.', $url, 3);
    }


    /**
     * 微信帐号绑定回调
     * @param Request $request
     * @return mixed
     */
    public function bindCallback(Request $request)
    {
        $code = $request->input('code');
        $authcode = $request->input('auth_code');

        $merchant = Merchant::repository()->findOneByPreAuthCode($code);
        if (!$merchant) {
            return redirect_message('Sorry, 未找到预授权信息.', '/', 3);
        }

        $platform = \EasyWeChat::open_platform();
        $info = $platform->getAuthorizationInfo($authcode)->get('authorization_info');

        if (!$info) {
            return redirect_message('Sorry, 授权失败, 请尝试重新授权.', $merchant->pre_auth_url, 3);
        }

        $merchant->appid = $info['authorizer_appid'];
        $merchant->refresh_token = $info['authorizer_refresh_token'];
        $merchant->refreshAuthorizerInfo(true);
        $merchant->pre_auth_code = null;

        $merchant->save();
        return redirect_message('授权成功, 感谢合作.', '/', 3);
    }


    public function onAuthEventAuthorized($msg)
    {
        //授权时信息更新走前端回调
    }


    public function onAuthEventUpdateauthorized($msg)
    {
        //更新授权也是走前端回调
    }


    public function onAuthEventUnauthorized($msg)
    {
        //取消授权
        $merchant = Merchant::repository()->findOneByAppid($msg->AuthorizerAppid);
        $merchant->appid = null;
        $merchant->refresh_token = null;
        $merchant->extras = null;
        $merchant->save();
    }

    public function onAuthEventComponentVerifyTicket($msg)
    {
        //更新 ticket, 由EasyWechat 自动处理
    }

    // End:
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^




    public function onMsgText(Merchant $merchant, $msg)
    {
        $text = $msg->Content;

        //不处理自动回复设置的关键词
        $reply_settings = $merchant->extras->authorizer_info->reply_settings;
        if (@$reply_settings->keyword_autoreply_info && @$reply_settings->is_autoreply_open) {
            $keywords = collect($reply_settings->keyword_autoreply_info->list)->pluck('keyword_list_info')->all();
            $keywords = array_merge(... $keywords);
            foreach ($keywords as $k) {
                if ($k->match_mode == 'contain') {
                    if (strpos($text, $k->content) !== false) {
                        return false;
                    }
                } elseif ($text == $k->content) {
                    return false;
                }
            }
        }


        $target = null;

        foreach ($merchant->valid_auto_replys as $reply) {
            if ($reply->match_mode === 'contains') {
                if (strpos($text, $reply->keyword) !== false) {
                    $target = $reply;
                    break;
                }
            } elseif ($reply->match_mode === 'prefix') {
                if (strpos($text, $reply->keyword) === 0) {
                    $target = $reply;
                    break;
                }
            } elseif ($reply->match_mode === 'regexp') {
                if (preg_match("`{$reply->keyword}`u", $text, $m)) {
                    $target = $reply;
                    $msg->__wj_match_result = $m;
                    break;
                }
            } elseif ($reply->match_mode === 'text') {
                if ($reply->keyword === $text) {
                    $target = $reply;
                    break;
                }
            }
        }

        if ($target) {
            if ($target->reply_mode === 'text') {
                return $target->reply;
            } elseif ($target->reply_mode === 'project') {
                //Do some thing.
                return $this->processWechatLogic($target->reply, $merchant, $msg);
            }
        }

        return false;
    }

    public function onEventSubscribe(Merchant $merchant, $msg)
    {
        if ($merchant->appid == 'wx1a17f99395584cd3') {
            \Log::error('onEventSubscribe:wx1a17f99395584cd3:'.$msg->FromUserName);
        }

        if (@$merchant->conf_wechat_enable_subscribe_reply) {
            return @$merchant->conf_wechat_subscribe_reply;
        }

        //新用户关注
        return false;
    }

    public function onEventUnsubscribe(Merchant $merchant, $msg)
    {
        //取消关注, 不能回复消息
    }

    public function onEventView(Merchant $merchant, $msg)
    {
        //菜单点击(跳转类) 貌似除了统计访问量没啥卵用, 不能回复消息
    }

    public function onEventScancodePush(Merchant $merchant, $msg)
    {
        //扫码跳转, 不能回复消息
    }

    public function onEventClick(Merchant $merchant, $msg)
    {
        //点击事件
        foreach ($merchant->menus as $menu) {
            if ($menu->type === 'click' && $menu->uri === $msg->EventKey) {
                return $this->processWechatLogic($menu->target, $merchant, $msg);
            }
        }

        return false;
    }

    public function onEventScancodeWaitmsg(Merchant $merchant, $msg)
    {
        //扫码获取消息
        foreach ($merchant->menus as $menu) {
            if ($menu->type === 'scancode_waitmsg' && $menu->uri === $msg->EventKey) {
                return $this->processWechatLogic($menu->target, $merchant, $msg);
            }
        }

        return false;
    }


    /**
     * 微信专题业务逻辑转发
     * @param $logic
     * @param Merchant $merchant
     * @param $msg
     * @return mixed
     */
    public function processWechatLogic($logic, Merchant $merchant, $msg)
    {
        if (strpos($logic, '::') === false) {
            $logic = "global::$logic";
        }

        $logic = explode('::', $logic);
        $namespace = $logic[0];
        $logic = explode(':', $logic[1]);


        if (!$logic[0]) {
            return false;
        }

        $cls = array_shift($logic);
        $params = $logic;

        if ($namespace === 'global') {
            $cls = static::$global_services[$cls];
        } else {
            $cls = module_ns($namespace, $cls);
        }

        if ($cls && class_exists($cls)) {
            /** @var WechatLogic $cls */
            $cls = new $cls(... $params);
            $cls->setMerchant($merchant);
            return $cls->handle($msg);
        }

        return false;
    }


    /**
     * 转发微信通知事件
     * 注: 事件由 Lumen 处理非 Laravel
     *
     * @param null $appid
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Server\BadRequestException
     */
    public function events($appid = null)
    {

        if ($appid === null) {
            $app = \EasyWeChat::open_platform();
            $merchant = null;
        } else {
            $merchant = Merchant::repository()->findOneByAppid($appid);

            if (!$merchant) {
                abort(404);
            }

            $app = $merchant->wechat_app;
        }





        return $app->server->setMessageHandler(function ($msg) use ($appid, $merchant) {
            $args = [$msg];

            //全网测试
            if ($appid === 'wx570bc396a51b8ff8') {
                $this->testForPublish($msg);
            }

            if ($msg->MsgType) {
                if ($msg->MsgType == 'event') {
                    $method = 'onEvent' . ucfirst(Str::camel(strtolower($msg->Event)));
                } else {
                    $method = ucfirst(Str::camel($msg->MsgType));
                    $method = 'onMsg' . $method;
                }

                array_unshift($args, $merchant);
            } elseif ($msg->InfoType) {
                $method = ucfirst(Str::camel($msg->InfoType));
                $method = 'onAuthEvent' . $method;
            } else {
                $method = null;
                $merchant = null;
            }

            if (!$method) {
                return false;
            }

            if (!method_exists($this, $method)) {
                \Log::debug("Method:$method Data:$msg");
                return false;
            }

            return $this->$method(... $args);
        })->serve();
    }

    public function testForPublish($msg)
    {
        if ($msg->MsgType == 'event') {
            return $msg->Event . "from_callback";
        } elseif ($msg->Content == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
            return 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
        } elseif (preg_match('@^QUERY_AUTH_CODE:@', $msg->Content)) {
            $authcode = substr($msg->Content, 16);

            $openPlatform = \EasyWeChat::open_platform();
            $info = $openPlatform->getAuthorizationInfo($authcode);
            $info = $info->get('authorization_info');

            $app = $openPlatform->createAuthorizerApplication(
                $info['authorizer_appid'],
                $info['authorizer_refresh_token']
            );
            $app->staff->message($authcode . "_from_api")->to($msg->FromUserName)->send();
            return false;
        }

        return false;
    }


    public function jssdkConfig()
    {
        $url = \Request::header('referer');
        if (!$url) {
            throw new AppException('url is required', 10);
        }

        $pu = parse_url($url);
        if (@$pu['host'] && !SiteUtils::isCompanyDomain(@$pu['host'])) {
            throw new AppException('domain check fail', 11);
        }

        $project = Project::matchByPath($pu['path']);
        $merchant = $project->merchant;


        $signPackage = $merchant->wechat_app->js->signature($url);
        $base = [
            'debug' => false,
            'beta' => false,
        ];
        $config = array_merge($base, $signPackage, ['jsApiList' => [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone',
            'startRecord',
            'stopRecord',
            'onVoiceRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'onVoicePlayEnd',
            'uploadVoice',
            'downloadVoice',
            'translateVoice',
            'chooseImage',
            'getLocation',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'getLocalImgData',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'closeWindow',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'scanQRCode',
            'openAddress',
            'openProductSpecificView',
            'addCard',
            'chooseCard',
            'openCard',
            'consumeAndShareCard',
            'chooseWXPay',
            'openEnterpriseRedPacket',
            'startSearchBeacons',
            'stopSearchBeacons',
            'onSearchBeacons',
            'openEnterpriseChat',
            'launchMiniProgram',
            'miniProgram'
        ]]);

        return wj_json_message($config);
    }

    public function jssdkConfigV2()
    {
        $url = \Request::get('use_url');
        if (!$url) {
            throw new AppException('url is required', 10);
        }
        $pu = parse_url($url);
        if (@$pu['host'] && !SiteUtils::isCompanyDomain(@$pu['host'])) {
            throw new AppException('domain check fail', 11);
        }

        $project = Project::matchByPath($pu['path']);
        $merchant = $project->merchant;


        $signPackage = $merchant->wechat_app->js->signature($url);
        $base = [
            'debug' => false,
            'beta' => false,
        ];
        $config = array_merge($base, $signPackage, ['jsApiList' => [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'onMenuShareQZone',
            'startRecord',
            'stopRecord',
            'onVoiceRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'onVoicePlayEnd',
            'uploadVoice',
            'downloadVoice',
            'translateVoice',
            'chooseImage',
            'getLocation',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'getLocalImgData',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'closeWindow',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'scanQRCode',
            'openAddress',
            'openProductSpecificView',
            'addCard',
            'chooseCard',
            'openCard',
            'consumeAndShareCard',
            'chooseWXPay',
            'openEnterpriseRedPacket',
            'startSearchBeacons',
            'stopSearchBeacons',
            'onSearchBeacons',
            'openEnterpriseChat',
            'launchMiniProgram',
            'miniProgram'
        ]]);
        return wj_json_message($config);
    }

}