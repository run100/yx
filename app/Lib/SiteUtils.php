<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 17/7/27
 * Time: 下午2:56
 */

namespace App\Lib;


use App\Http\Controllers\Web\AutoTemplateController;
use App\Zhuanti\Controllers\PvoteController;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

class SiteUtils
{

    private static $SIMPLE_FILTER_ARGS = array(
        'int' => array(
            'filter' => FILTER_SANITIZE_NUMBER_INT),
        'float' => array(
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION),
        'text' => array(
            'filter' => FILTER_CALLBACK,
            'options' => 'MainClass::filterText'),
        'base64' => array(
            'filter' => FILTER_CALLBACK,
            'options' => 'MainClass::filterBase64'),
        'unsafe' => array(
            'filter' => FILTER_UNSAFE_RAW),
        'id' => array(
            'filter' => FILTER_VALIDATE_INT,
            'options' => array('min_range' => 1, 'default' => 0)),
        'page' => array(
            'filter' => FILTER_VALIDATE_INT,
            'options' => array('min_range' => 1, 'max_range' => 50, 'default' => 1)),
        'username' => array(
            'filter' => FILTER_CALLBACK,
            'options' => 'MainClass::filterUsername'),
        'phone' => array(
            'filter' => FILTER_CALLBACK,
            'options' => 'MainClass::filterPhone'),
        'email' => array(
            'filter' => FILTER_CALLBACK,
            'options' => 'MainClass::filterEmail'),
        'ip' => array(
            'filter' => FILTER_CALLBACK,
            'options' => 'MainClass::filterIp'),
        'latlng' => array(
            'filter' => FILTER_CALLBACK,
            'options' => 'MainClass::filterLatlng')
    );

    public static function isPhone($phone)
    {
        $g = "/^1\d{10}$/";
        if (!preg_match($g, $phone)) {
            return false;
        }

        return true;
    }


    /**
     * 计算两点地理坐标之间的距离
     * @param  Decimal $longitude1 起点经度
     * @param  Decimal $latitude1 起点纬度
     * @param  Decimal $longitude2 终点经度
     * @param  Decimal $latitude2 终点纬度
     * @param  Int $unit 单位 1:米 2:公里
     * @param  Int $decimal 精度 保留小数位数
     * @return int
     */
    public static function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 1, $decimal = 0)
    {
        $EARTH_RADIUS = 6370.996; // 地球半径系数
        $PI = 3.1415926;

        $radLat1 = $latitude1 * $PI / 180.0;
        $radLat2 = $latitude2 * $PI / 180.0;

        $radLng1 = $longitude1 * $PI / 180.0;
        $radLng2 = $longitude2 * $PI / 180.0;

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $distance = $distance * $EARTH_RADIUS * 1000;

        if ($unit == 2) {
            $distance = $distance / 1000;
        }

        return (int)round($distance, $decimal);
    }

    public static function objectArray($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = self::objectArray($value);
            }
        }
        return $array;
    }


    /**
     * @param string $message
     * @param mixed $data
     *
     * @return array
     */
    public static function makeResponse($message, $data)
    {
        return [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];
    }

    /**
     * @param string $message
     * @param array $data
     *
     * @return array
     */
    public static function makeError($message, array $data = [])
    {
        $res = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $res['data'] = $data;
        }

        return $res;
    }

    /**
     * get remote ip
     * @return string
     **/
    public static function getRemoteIp()
    {
        /*
        1.REMOTE_ADDR:浏览当前页面的用户计算机的ip地址
        2.HTTP_X_FORWARDED_FOR: 浏览当前页面的用户计算机的网关
        3.HTTP_CLIENT_IP:客户端的ip
        */
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function exportApiCURDRoutes($name, $controller)
    {
        \Route::resource($name, $controller, [
            'only' => ['index', 'show', 'store', 'update', 'destroy'],
            'parameters' => [
                $name => 'model'
            ]
        ]);
    }

    public static function exportTemplateRoutes($path, $base_root = '')
    {
        $rootlen = strlen($path);

        ob_start();
        passthru("find $path -type f -name '[^_]*.html'");
        $ret = ob_get_clean();
        $ret = array_filter(explode(PHP_EOL, $ret));


        foreach ($ret as $template) {
            $uri = substr($template, $rootlen, -5);
            $base = dirname($uri);
            $uri = preg_replace('@/index$@', '', $uri);
            $uri = substr($uri, 1);

            if ($base_root) {
                $uri = "$base_root/$uri";
            }

            $uri = preg_replace('@/$@', '', $uri);
            $name = str_replace('_', '-', Str::snake($uri));
            $name = str_replace('/', '.', $name);
            $name = str_replace('.-', '.', $name);
            $name = preg_replace('@-([a-z])\b@', '\1', $name);

            $file_content = file_get_contents($template, false, null, 0, 1024);
            if (preg_match('@[<]meta *name="wanjia-data" *content="([^"]+)" */[>]@i', $file_content, $m)) {
                $module = $uri;
                if (($pos = strpos($module, '/')) !== false) {
                    $module = substr($module, 0, $pos);
                }

                $action = module_ns($module, "Controllers\\$m[1]");
            } else {
                $action = '';
            }
            $route = \Route::get($uri, [
                'uses' => '\\' . AutoTemplateController::class . '@templatePage',
                '__auto_template' => $template,
                '__auto_template_base' => (strpos($base, '/') === strlen($base) - 1) ? $base : "$base/",
                '__auto_template_data_action' => $action,
                'as' => $name
            ]);

            $route->middleware('project');
            if (preg_match('@[<]meta *name="wanjia-perms" *content="([^"]+)" */[>]@i', $file_content, $m)) {
                if ($m[1] != 'guest') {
                    $route->middleware(... explode(';', $m[1]));
                }
            }

        }
    }



    /**
     * 公版新闻路由
     * @param string $path
     */
    public static function exportPublicNewsRoutes($path = '')
    {
      $route = \Route::get($path, ['uses' => "\App\Zhuanti\News\NewsController@index", 'as' => 'pvote.index'])->where('id', '[0-9]+');
      $route->middleware(['project', 'web']);
    }


    /**
     * 公版投票路由
     * @param string $path
     */
    public static function exportPublicVoteRoutes($path = '')
    {
        $route = \Route::get($path.'/login_status', ['uses' => "\App\Zhuanti\Controllers\CommonController@loginStatus", 'as' => 'pvote.login_status'])->where('id', '[0-9]+');
        $route->middleware(['project', 'web', 'wechat.oauth:check_only']);

        $route = \Route::get($path.'/login_start', ['uses' => "\App\Zhuanti\Controllers\CommonController@loginStart", 'as' => 'pvote.login_start'])->where('id', '[0-9]+');
        $route->middleware(['project', 'web', 'wechat.oauth:snsapi_userinfo']);

        $route = \Route::get($path, ['uses' => "\App\Zhuanti\Controllers\PvoteController@index", 'as' => 'pvote.index'])->where('id', '[0-9]+');
        $route->middleware(['project', 'cache:E60']);

        $route = \Route::get($path . '/detail', ['uses' => "\App\Zhuanti\Controllers\PvoteController@detail", 'as' => 'pvote.detail'])->where('id', '[0-9]+');
        $route->middleware(['project', 'cache:E60']);

        $route = \Route::get($path . '/rank', ['uses' => "\App\Zhuanti\Controllers\PvoteController@rank", 'as' => 'pvote.rank'])->where('id', '[0-9]+');
        $route->middleware(['project', 'cache:E60']);

        $route = \Route::get($path . '/reg', ['uses' => "\App\Zhuanti\Controllers\PvoteController@reg", 'as' => 'pvote.reg'])->where('id', '[0-9]+');
        $route->middleware(['project', 'cache:E60']);

        $route = \Route::post($path . '/register', ['uses' => "\App\Zhuanti\Controllers\PvoteController@register", 'as' => 'pvote.register'])->where('id', '[0-9]+');
        $route->middleware(['project', 'web', 'wechat.oauth:check_only']);

        $route = \Route::get($path . '/rule', ['uses' => "\App\Zhuanti\Controllers\PvoteController@rule", 'as' => 'pvote.rule'])->where('id', '[0-9]+');
        $route->middleware(['project', 'cache:E60']);
    }

    /**
     * 公版集字路由
     * @param string $path
     */
    public static function exportPublicJiziRoutes($path)
    {
        $controller = '\App\Zhuanti\Jizi\JiziController';
        $routeAs = 'jizi';
        static::exportPrizesIntefaceRoutes($path, $routeAs, $controller);
        \Route::group([
            'prefix' => $path,
            'as' => "{$routeAs}.",
            'middleware' => ['project', 'web']
        ], function () use ($controller) {
            \Route::get('', ['uses' => "{$controller}@index", 'as' => 'index'])->where('id', '[0-9]+')
                ->middleware(['wechat.oauth:snsapi_userinfo']);
            \Route::get('/baoming', ['uses' => "{$controller}@baoming", 'as' => 'jizi.baoming'])->where('id', '[0-9]+')
                ->middleware(['wechat.oauth:snsapi_userinfo']);
            \Route::post('/reg', ['uses' => "{$controller}@reg", 'as' => 'reg'])->where('id', '[0-9]+')
                ->middleware(['wechat.oauth:check_only']);
            \Route::get('/{player_id}', ['uses' => "{$controller}@player", 'as' => 'player'])->where('id', '[0-9]+');
        });
    }

    /**
     * 公版抽奖路由
     * @param string $path
     */
    public static function exportPublicPrizesRoutes($path)
    {
        $controller = '\App\Zhuanti\Prizes\PrizesController';
        $routeAs = 'prizes';
        static::exportPrizesIntefaceRoutes($path, $routeAs, $controller, '');
        \Route::group([
            'prefix' => $path,
            'as' => "{$routeAs}.",
            'middleware' => ['project', 'web']
        ], function () use ($controller) {
            \Route::get('/zhulis', ['uses' => "{$controller}@zhulis", 'as' => 'zhulis'])->where('id', '[0-9]+')
                ->middleware(['wechat.oauth:snsapi_userinfo']);
            \Route::get('/{player_id}', ['uses' => "{$controller}@share", 'as' => 'share'])->where('id', '[0-9]+');
        });
    }

    public static function exportPrizesIntefaceRoutes($path, $routeAs, $controller, $prizeIndex = '/prize_index')
    {
        \Route::group([
            'prefix' => $path,
            'as' => "{$routeAs}.",
            'middleware' => ['project']
        ], function () use ($controller, $prizeIndex) {
            \Route::get($prizeIndex, ['uses' => "{$controller}@prizeIndex", 'as' => 'prize_index'])
                ->where('id', '[0-9]+')
                ->middleware(['web', 'wechat.oauth:snsapi_userinfo']);
            \Route::get($prizeIndex.'/rule', ['uses' => "{$controller}@rule", 'as' => 'rule'])
                ->where('id', '[0-9]+')
                ->middleware(['web', 'wechat.oauth:snsapi_userinfo']);
            \Route::get('/wins', ['uses' => "{$controller}@prizeWins", 'as' => 'prize_wins'])->where('id', '[0-9]+');
            \Route::post('/wsinfo', ['uses' => "{$controller}@prizeWsInfo", 'as' => 'prize_ws_info'])
                ->where('id', '[0-9]+')->middleware(['web', 'wechat.oauth:check_only']);
            \Route::post('/draw', ['uses' => "{$controller}@drawLottery", 'as' => 'draw_lottery'])
                ->where('id', '[0-9]+')->middleware(['web', 'wechat.oauth:check_only']);
        });
    }

    /**
     * 公版砍价
     * @param string $path
     */
    public static function exportPublicBargainRoutes($path = '')
    {
        \Route::group(['prefix' => $path, 'as' => 'bargain.', 'middleware' => ['project']], function () {
            \Route::get('', ['uses' => '\App\Zhuanti\Bargain\BargainController@index', 'as' => 'index'])
                ->middleware(['web', 'wechat.oauth:snsapi_userinfo'])->where('id', '[0-9]+');
            \Route::get('start', ['uses' => '\App\Zhuanti\Bargain\BargainController@start', 'as' => 'start'])
                ->middleware(['web', 'wechat.oauth:snsapi_userinfo'])->where('id', '[0-9]+');
            \Route::post('reg', ['uses' => '\App\Zhuanti\Bargain\BargainController@reg', 'as' => 'reg'])
                ->middleware(['web', 'wechat.oauth:check_only'])->where('id', '[0-9]+');
            \Route::post('commit_info', ['uses' => '\App\Zhuanti\Bargain\BargainController@commitInfo', 'as' => 'commit_info'])
                ->middleware(['web', 'wechat.oauth:check_only'])->where('id', '[0-9]+');
            \Route::get('zhulis', ['uses' => '\App\Zhuanti\Bargain\BargainController@zhulis', 'as' => 'zhulis'])
                ->where('id', '[0-9]+');
            \Route::get('rakings', ['uses' => '\App\Zhuanti\Bargain\BargainController@rakings', 'as' => 'rakings'])
                ->where('id', '[0-9]+');
            \Route::get('{player_id}', ['uses' => '\App\Zhuanti\Bargain\BargainController@player', 'as' => 'player'])
                ->middleware(['web', 'wechat.oauth:snsapi_userinfo'])->where('id', '[0-9]+');
            \Route::post('validate', ['uses' => '\App\Zhuanti\Bargain\BargainController@validate', 'as' => 'validate'])
                ->middleware(['web', 'wechat.oauth:check_only'])->where('id', '[0-9]+')->where('code', 'ft[0-9]+');
            \Route::post('exchange', ['uses' => '\App\Zhuanti\Bargain\BargainController@exchange', 'as' => 'exchange'])
                ->middleware(['web', 'wechat.oauth:check_only'])->where('id', '[0-9]+')->where('code', 'ft[0-9]+');
            \Route::post('exchange', ['uses' => '\App\Zhuanti\Bargain\BargainController@exchange', 'as' => 'exchange'])
                ->middleware(['web', 'wechat.oauth:check_only'])->where('id', '[0-9]+')->where('code', 'ft[0-9]+');
        });
    }

    /**
     * 公版红包路由
     * @param string $path
     */
    public static function exportPublicRedpaketRoutes($path = '')
    {
        \Route::group(['prefix' => $path, 'as' => 'redpacket.', 'middleware' => ['project', 'web']], function (Router $router) {
            $router->get('login_status', ['uses'=>'\App\Zhuanti\Controllers\CommonController@loginStatus', 'as'=>'login_status'])
                ->where('id', '[0-9]+')->middleware(['wechat.oauth:check_only']);
            $router->get('login_start', ['uses'=>'\App\Zhuanti\Redpacket\RedpacketController@loginStart', 'as'=>'login_start'])
                ->where('id', '[0-9]+')->middleware(['wechat.oauth:snsapi_userinfo']);
        });
        \Route::group(['prefix' => $path, 'as' => 'redpacket.', 'middleware' => ['project']], function (Router $router) {
            $router->get('', ['uses'=>'\App\Zhuanti\Redpacket\RedpacketController@index', 'as'=>'index'])->middleware(['cache:E120'])
                ->where('id', '[0-9]+');

            $router->get('user', ['uses'=>'\App\Zhuanti\Redpacket\RedpacketController@user', 'as'=>'user'])
                ->where('id', '[0-9]+')->middleware(['web', 'wechat.oauth:check_only']);
            $router->post('draw_redpacket', ['uses'=>'\App\Zhuanti\Redpacket\RedpacketController@drawRedpacket', 'as'=>'draw_redpacket'])
                ->where('id', '[0-9]+')->middleware(['web', 'wechat.oauth:check_only']);
            $router->post('reset_team', ['uses'=>'\App\Zhuanti\Redpacket\RedpacketController@resetTeam', 'as'=>'reset_team'])
                ->where('id', '[0-9]+')->middleware(['web', 'wechat.oauth:check_only']);

            $router->get('{player_id}', ['uses'=>'\App\Zhuanti\Redpacket\RedpacketController@player', 'as'=>'player'])
                ->where('id', '[0-9]+');
        });
    }

    /**
     * @param $ret
     * @param string $method
     * @return array|bool
     */
    public static function getRepApiDatas($datas, $method = "toApiValue")
    {
        if (!$datas || !count($datas)) {
            return array();
        }

        $ret = array();
        if (count($datas) > 0) {
            foreach ($datas as $val) {
                $ret[] = $val->$method();
            }
        }
        return array_values($ret);
    }

    public static function getEnv()
    {
        return Config::get("app.env");
    }

    public static function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::user()->id;
        } else {
            return false;
        }


    }

    /**
     * 配合 filter_var / filter_var_array 使用，对输入参数做安全过滤
     *
     * @param $name
     * @param array $options
     * @return mixed
     */
    public static function createSimpleFilter($name, $options = array())
    {
        switch (true) {
            case preg_match('#\[\]$#', $name):
                $name = substr($name, 0, -2);
                return array(
                    'filter' => FILTER_CALLBACK,
                    'options' => function ($v) use ($name) {
                        return SiteUtils::simpleFilter($name, $v);
                    }
                );
            default:
                return @static::$SIMPLE_FILTER_ARGS[$name];
        }
    }

    /**
     * 使用已定义的规则对单个参数进行安全过滤
     *
     * @param $name
     * @param $var
     * @return bool|mixed
     */
    public static function simpleFilter($name, $var)
    {
        if (!array_key_exists($name, static::$SIMPLE_FILTER_ARGS)) {
            return false;
        }

        $filter = static::$SIMPLE_FILTER_ARGS[$name];


        if (!array_key_exists('options', $filter)) {
            if (array_key_exists('flags', $filter)) {
                return filter_var($var, $filter['filter'], $filter['flags']);
            } else {
                return filter_var($var, $filter['filter']);
            }
        } else {
            return filter_var($var, $filter['filter'], array('options' => $filter['options']));
        }
    }

    public static function isCompanyDomain($domain)
    {
        if (preg_match('@(^|[.])365jia[.](cn|com)$@', $domain)) {
            return true;
        }

        if (preg_match('@(^|[.])(365jia|365lin)[.]lab$@', $domain)) {
            return true;
        }

        if (preg_match('@(^|[.])365shequ[.]com$@', $domain)) {
            return true;
        }

        return false;
    }

    public static function is_mobile_request()
    {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }
        if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
            $mobile_browser++;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            $mobile_browser++;
        }
        if (isset($_SERVER['HTTP_PROFILE'])) {
            $mobile_browser++;
        }
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
        );
        if (in_array($mobile_ua, $mobile_agents)) {
            $mobile_browser++;
        }
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
            $mobile_browser++;
        }
        // Pre-final check to reset everything if the user is on Windows
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) {
            $mobile_browser = 0;
        }
        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) {
            $mobile_browser++;
        }
        if ($mobile_browser > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function isPassport($sfz)
    {
        $passport = trim(strtoupper($sfz));
        if (!preg_match('/^[1-9][0-9]{16}[0-9X]$/', $passport)) {
            return false;
        }

        $checkcode = substr($passport, 17, 1);
        $sum = 0;
        $pns = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        for ($j = 0; $j < 17; $j++) {
            $sum += $passport[$j] * $pns[$j];
        }
        $sum = $sum % 11;
        $sum = substr('10X98765432', $sum, 1);
        if ($sum != $checkcode) {
            return false;
        }

        $today = date('Ymd');
        $ymd = substr($passport, 6, 8);
        $y = substr($passport, 6, 4);
        $m = substr($passport, 10, 2);
        $d = substr($passport, 12, 2);
        $md = substr($passport, 10, 4);
        $gender = (substr($passport, 16, 1) % 2) ? 'M' : 'W';

        if ($ymd > $today || $m > 12 || $m < 1 || $d > 31 || $d < 1) {
            return false;
        }

        $y0 = date('Y');
        $md0 = date('md');

        $age = $y0 - $y + 1;
        if ($md > $md0) {
            $age -= 1;
        }

        return true;
    }

    public static function passportBirthday($passport, $fmt = null)
    {
        $ymd = (int)substr($passport, 6, 8);
        $y = substr($ymd, 0, 4);
        $m = substr($ymd, 4, 2);
        $d = substr($ymd, 6, 2);

        $time = strtotime(sprintf('%04d-%02d-%02d 00:00:00', $y, $m, $d));
        if ($fmt === null) {
            return $time;
        } else {
            return date($fmt, $time);
        }
    }

    public static function passportAge($passport)
    {
        $d0 = new \DateTime('@' . static::passportBirthday($passport));
        $d1 = new \DateTime();
        return (int)$d1->diff($d0)->format('%y');
    }

    public static function passportGender($passport)
    {
        if (((int)substr($passport, 16, 1)) % 2) {
            return 'M';
        } else {
            return 'W';
        }
    }

}