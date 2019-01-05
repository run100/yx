<?php

namespace App\Http\Middleware;


use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectBinding
{
    const ENABLE_TIME_CHECK = false;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $proj = Project::matchByPath();
        if (!$proj) {
            abort(404);
        }

        if (static::ENABLE_TIME_CHECK) {
            $start = !$proj->start_at ? 0 : $proj->start_at->getTimestamp();
            $end = !$proj->start_at ? 0 : $proj->end_at->getTimestamp();

            if ($start && $start > time()) {
                abort(404);
            }

            if ($end && $end < time()) {
                //项目结束后，7天宽限期内，随机404
                //部分专题有长尾效应，服务器资源占用成本；必须设置项目截止日期

                $r = time() - $end;
                $width = 7 * 24 * 3600;
                $end += $width;

                if ($end < time()) {
                    return redirect_message('专题地址已失效, 即将跳转到万家热线', 'http://365jia.cn', 3);
                }

                $r = $r / $width;
                if (mt_rand(1, $width) / $width < $r) {
                    abort(404, "专题地址已失效, 宽限期内");
                }
            }
        }

        do {
            if (!$proj->conf_test_mode) {
                break;
            }

            //只对GET请求做外网限制; 这样不影响支付回调业务
            if ($request->method() !== Request::METHOD_GET) {
                break;
            }

            $ip_whitelist = [
                '127.0.0.1',
                '100.100.100.1',
                '60.173.236.97',
                '192.168.0.0/16',
                '172.16.10.0/16',
                '172.17.0.0/16',
                '61.132.221.56/29',
                '61.132.221.112/29',
                '61.132.221.200/29',
            ];

            //检查IP白名单
            if (in_iplist($request->ip(), $ip_whitelist)) {
                break;
            }

            $sign_token = $proj->conf_test_cookie_token;
            if (!$sign_token) {
                abort(403);
            }

            //之前注入过Cookie且在有效期内则放行
            $testing_token = $request->cookie('__testing');
            if ($testing_token && md5(substr($testing_token, 32) . $sign_token) === substr($testing_token, 0, 32)) {
                $formal = substr($testing_token, 32, 7);
                if ($formal === 'private') {
                    $expire = substr($testing_token, 55);
                    if ($expire > time()) {
                        break;
                    }
                }
            }

            //GET参数中有__testing参数，说明正在请求测试授权; 换取长效Token存入Cookie
            $testing_token = $request->get('__testing');
            if ($testing_token && md5(substr($testing_token, 32) . $sign_token) === substr($testing_token, 0, 32)) {
                $formal = substr($testing_token, 32, 7);
                if ($formal !== 'access_') {
                    abort(403);
                }

                $expire = substr($testing_token, 55);
                if ($expire > time()) {
                    $salt = Str::random();
                    $width = 60 * 60 * 24 * 7;  //7天有效期
                    $time = time() + $width;

                    $clearText = "private$salt$time";
                    $sign = md5($clearText . $sign_token);
                    \Cookie::queue('__testing', "$sign$clearText", $width / 60, $proj->path, null, false, true);
                    return redirect($this->getTargetUrl($request));
                }
            }

            abort(403);
        } while (false);

        $request->attributes->set('project', $proj);
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
        $queries = array_except($request->query(), ['__testing']);

        return $request->url().(empty($queries) ? '' : '?'.http_build_query($queries));
    }
}
