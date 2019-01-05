<?php

namespace App\Http\Middleware;


use App\Models\Merchant;
use Closure;
use EasyWeChat\Foundation\Application;
use Event;
use Illuminate\Http\Request;
use Log;
use Overtrue\LaravelWechat\Events\WeChatUserAuthorized;
use Overtrue\Socialite\User;
use Overtrue\Socialite\User as SocialiteUser;


class WechatOAuth
{
    /**
     * Use Service Container would be much artisan.
     * @var Application
     */
    protected $wechat;

    protected $appid;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $scopes
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $scopes = null)
    {
        $this->setUpMockAuthUser();

        $isNewSession = false;
        $onlyRedirectInWeChatBrowser = config('wechat.oauth.only_wechat_browser', false);

        if ($onlyRedirectInWeChatBrowser && !$this->isWeChatBrowser($request)) {
            if (config('debug')) {
                Log::debug('[not wechat browser] skip wechat oauth redirect.');
            }

            return $next($request);
        }


        $project = $request->attributes->get('project');

        /** @var User $user */
        $user = session('wechat.oauth_user');
        if ($user && !$user->getAttribute('is_mock')) {
            if ($project) {
                if (session('wechat.proj_id') != $project->id) {
                    session()->forget('wechat.oauth_user');
                    session()->forget('wechat.proj_id');
                }
            } else {
                if (session('wechat.proj_id') != 0) {
                    session()->forget('wechat.oauth_user');
                    session()->forget('wechat.proj_id');
                }
            }
        }

        if ($scopes === 'check_only' && !wx_openid()) {
            if ($request->expectsJson()) {
                return wj_json_message('微信认证校验失败', 10014);
            } else {
                return response('微信认证校验失败');
            }
        }

        $this->injectApp($request);


        $scopes = $scopes ?: config('wechat.oauth.scopes', 'snsapi_base');
        $scopes = explode('|', $scopes);
        $scopes = array_values(array_filter(array_unique($scopes)));

        if (is_string($scopes)) {
            $scopes = array_map('trim', explode(',', $scopes));
        }


        if (!session('wechat.oauth_user') || $this->needReauth($scopes)) {
            if ($request->has('code') && $request->has('state')
                && (!$request->has('appid') || $request
                        ->get('appid') === $this->appid)) {
                session(['wechat.oauth_user' => $this->wechat->oauth->user()]);
                $isNewSession = true;
                Event::fire(new WeChatUserAuthorized(session('wechat.oauth_user'), $isNewSession));

                if ($project) {
                    session([
                        'wechat.proj_id' => $project->id
                    ]);
                } else {
                    session([
                        'wechat.proj_id' => 0
                    ]);
                }

                return redirect()->to($this->getTargetUrl($request));
            }

            session()->forget('wechat.oauth_user');

            return $this->wechat->oauth->scopes($scopes)->redirect($request->fullUrl());
        }

        Event::fire(new WeChatUserAuthorized(session('wechat.oauth_user'), $isNewSession));

        return $next($request);
    }

    /**
     * Build the target business url.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getTargetUrl($request)
    {
        $queries = array_except($request->query(), ['code', 'state', 'appid']);

        return $request->url().(empty($queries) ? '' : '?'.http_build_query($queries));
    }

    /**
     * Is different scopes.
     *
     * @param  array $scopes
     *
     * @return bool
     */
    protected function needReauth($scopes)
    {
        return session('wechat.oauth_user.original.scope') == 'snsapi_base' && in_array("snsapi_userinfo", $scopes);
    }

    /**
     * Detect current user agent type.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function isWeChatBrowser($request)
    {
        return strpos($request->header('user_agent'), 'MicroMessenger') !== false;
    }

    /**
     * @param Request $request
     */
    public function injectApp(Request $request)
    {
        $merchant = Merchant::matchByPath();
        $app = $merchant->wechat_app;
        $this->wechat = $app;
        $this->appid = $merchant->appid;
    }



    /**
     * 创建模拟登录.
     */
    protected function setUpMockAuthUser()
    {
        $user = config('wechat.mock_user');

        if (is_array($user) && !empty($user['openid']) && config('wechat.enable_mock')) {
            $user = new SocialiteUser([
                'id'       => array_get($user, 'openid'),
                'name'     => array_get($user, 'nickname'),
                'nickname' => array_get($user, 'nickname'),
                'avatar'   => array_get($user, 'headimgurl'),
                'email'    => null,
                'original' => array_merge($user, ['privilege' => []]),
                'is_mock'  => true
            ]);

            session(['wechat.oauth_user' => $user]);
        }
    }
}
