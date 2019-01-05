<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/23
 * Time: 下午9:47
 */

namespace App\Features\Yx2018\Controllers;

use Admin;
use App\Exceptions\PaymentException;
use App\Features\Yx2018\Jobs\DataSyncJob;
use App\Features\Yx2018\RedisOperator;
use App\Features\Yx2018\ServiceProvider;
use App\Http\Controllers\BaseController;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\Player;
use App\Models\Project;
use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Payment\Order;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use ValidatesRequests;

    /**
     * 相关RedisKey的命名前缀
     */
    public const REDIS_NS = 'zt:yx2018:v1';

    /**
     * 因涉及团队报名需要填多人信息; 信息验证逻辑和信息存储逻辑是分开的两个请求。
     * 定义一个Token给验证成功的信息做签名;
     * 保存时将带有签名的信息再传递回来，就可以在保存阶段免去再验证环节。
     *
     * 此Token还用来为对账单生成加密地址; 避免uploads公开目录中存储隐私数据文件名被穷举造成泄密。
     */
    const VALIDATION_TOKEN = "569f8b5564df655563a84bb8b30e8e0f";

    /**
     * 控制金额小数点后保留几位; 测试阶段支付一分钱保留两位，上线时支付一元保留整数部分
     */
    const DOTLEN = 0;

    /**
     * 支付金额，单位:分
     */
    const DONATE = 100;

    /**
     * 部分微信用户无头像，这里给一个默认头像
     */
    const DEFAULT_AVATAR = '/yx2018/images/ico_default_avatar.jpg';

    public static $CAN_REGIST;       //是否开放注册
    public static $CAN_CHAXUN;       //是否开放查询
    public static $CAN_VOTE;         //是否开放捐助
    public static $CAN_CONFIRM;      //是否开放信息确认
    public static $REGIST_END;       //注册通道关闭
    public static $CONFIRM_END;      //报名确认通道关闭
    public static $VOTE_END;         //捐助通道关闭
    public static $CHAXUN_END;       //查询通道关闭
    public static $RANKING_END;      //排名是否结束

    /**
     * PHP中没有类的静态构造方法;
     * 在此定义一个initialize方法，并在文末class定义结束后立即执行。模拟静态构造器
     */
    public static function initialize()
    {
        //调试时间表
        //static::timeplanForDebug(false);
        //测试时间表
        //static::timeplanForTesting('2018-03-22 22:55:00', 300);
        //官方预设时间表
        static::timeplanForOfficial();
    }

    /**
     * 调试用时间表
     */
    public static function timeplanForDebug($ranking)
    {
        static::$CAN_REGIST = false;
        static::$CAN_CHAXUN = true;
        static::$CAN_VOTE = false;
        static::$CAN_CONFIRM = false;
        static::$REGIST_END = false;
        static::$CONFIRM_END = true;
        static::$VOTE_END = false;
        static::$CHAXUN_END = false;
        static::$RANKING_END = !$ranking;
    }

    /**
     * 测试用时间表
     */
    public static function timeplanForTesting($start_at, $timespan = 600)
    {
        //测试开始时间
        $testing_start_at = strtotime($start_at);

        $time = time();

        static::$CAN_REGIST     = $time > $testing_start_at;
        static::$REGIST_END     = $time > $testing_start_at + $timespan * 1;

        static::$CAN_VOTE       = $time > $testing_start_at;
        static::$VOTE_END       = $time > $testing_start_at + $timespan * 1;

        //TODO: 这两个时间中间分配毅行编号

        static::$RANKING_END    = $time > $testing_start_at + $timespan * 2;

        static::$CAN_CHAXUN     = $time > $testing_start_at + $timespan * 2;
        static::$CHAXUN_END     = $time > $testing_start_at + $timespan * 4;

        static::$CAN_CONFIRM    = $time > $testing_start_at + $timespan * 2;
        static::$CONFIRM_END    = $time > $testing_start_at + $timespan * 3;

        //TODO: 此日期后立刻释放未确认的毅行编号，并记录
    }

    /**
     * 官方正式的时间表
     */
    public static function timeplanForOfficial()
    {
        $time = time();

        static::$CAN_REGIST     = $time > strtotime('2019-01-01 10:00:00');
        static::$REGIST_END     = $time > strtotime('2019-04-19 23:59:59');

        static::$CAN_VOTE       = $time > strtotime('2019-01-01 10:00:00');
        static::$VOTE_END       = $time > strtotime('2019-04-19 23:59:59');

        //TODO: 这两个时间中间分配毅行编号

        static::$RANKING_END    = $time > strtotime('2019-04-20 10:00:00');

        static::$CAN_CHAXUN     = $time > strtotime('2019-04-20 10:00:00');
        static::$CHAXUN_END     = $time > strtotime('2019-05-31 23:59:59');

        static::$CAN_CONFIRM    = $time > strtotime('2019-04-20 10:00:00');
        static::$CONFIRM_END    = $time > strtotime('2019-04-23 14:00:00');

        //TODO: 此日期后立刻释放未确认的毅行编号，并记录
    }

    /**
     * 获取 LuaRedis 操作实例
     * @return RedisOperator
     */
    public static function getLuaRedis()
    {
        static $lua;

        if ($lua) {
            return $lua;
        }

        return $lua = new RedisOperator();
    }

    /**
     * 获取报名表单字段配置信息
     * @return array 字段配置信息
     */
    protected function getFieldConfigs()
    {
        static $fields;

        if ($fields) {
            return $fields;
        }

        $proj = $this->getProject();
        $ret = collect(wj_obj2arr($proj->conf_base_form_design))->pluck('options', 'field')
            ->map(function ($v, $k) {
                if ($k === 'passport') {
                    $types = $v['passport']['passport_type'];
                    $types = array_combine($types, $types);
                    return array_intersect_key(Player::model()->listPassportType(), $types);
                }
                return @$v['select']['options'];
            })
            ->filter()
            ->map(function ($v, $k) {
                if ($k === 'passport') {
                    return $v;
                }
                return collect($v)->pluck('name', 'key')->all();
            })
            ->all();

        $ret['gender'] = Player::model()->listGender();
        $ret['line_length'] = [
            'L1'    => '4KM',
            'L2'    => '15KM',
            'L3'    => '33KM'
        ];
        return $fields = $ret;
    }

    /**
     * 解析选手的选择项字段 size/line/gender 等; 转为可显示的值，以txt_前缀插入到传入的字段数组里
     *
     * @param $player
     * @return mixed
     */
    protected function extractPlayer(&$player)
    {
        $fields = $this->getFieldConfigs();
        $player['txt_gender'] = @$fields['gender'][@$player['gender']];
        $player['txt_line'] = @$fields['line'][@$player['line']];
        $player['txt_size'] = @$fields['size'][@$player['size']];
        $player['txt_supply_loc'] = @$fields['supply_loc'][@$player['supply_loc']];
        $passport = explode(':', $player['passport']);
        $player['txt_passport_type'] = @$fields['passport'][$passport[0]];
        $player['txt_city'] = !@$player['city'] ? '' : wj_city_fullname($player['city']);
        $player['passport'] = $passport[1];
        return $player;
    }

    /**
     * 专题着陆页; 负责微信授权流程，以及授权后向相应页面跳转。
     * 注: 只在着陆页做授权登录，其他界面需要用户资料时仅检查没有授权过。这样做的目的是只留一个授权入口保持，保持系统纯净，不做过多检查，并且相关页面还可以做缓存
     *  (如果是AJAX请求，未授权转跳到微信授权登录，实际上没办法完成授权流程)
     *
     * 因前端是单页面应用，所以这里采用Cookie控制前端页面跳转。如果不是单页面应用，可以直接用 redirect 控制跳转目标。
     * 单页面应用的优势是，所有页面共享JS全局变量、所有资源在页面请求结束后都已准备好、页面跳转全部由前端DIV显隐来操控几乎无感，网络环境差时也能展示页面框架或加载中避免白屏等待
     *
     * start 接受的参数:
     * from:    来源参数，本身不影响逻辑，会被记录在accesslog中，可用于统计哪些渠道带来多少用户
     * act:     为空则跳转到首页; 为vote则跳转到助力页; 并通过Cookie向前端提供参数;
     *          注: Laravel默认会对cookie进行加密，加密后的Cookie前端是取不到的，加js:前缀将忽略加密流程，具体参考App/Http/Middleware/EncryptCookies中间件
     * threadid:当act=vote时必填;表示给谁助力。这里的threadid是用被助力人的openid md5得来的，目的是数据脱敏，保护用户隐私
     */
    public function start(Request $request)
    {
        //从start进来的，我们在Cookie中放置一个login标记，表示已经登录了; 前端会检查这个cookie标记，如果没有登录则跳转到start先进行登录
        \Cookie::queue('js:yx2018:login', 1, 0, null, null, false, false);

        $act = $request->input('act');
        $openid = wx_openid();

        $lua = static::getLuaRedis();

        //如果是首次进入保存微信用户信息(昵称、头像等)
        if (!$lua->checkMember($openid)) {
            $lua->saveMember($openid, wx_info());
        }

        if ($act == 'vote') {
            $threadid = $request->input('threadid');

            if (!$lua->getThread($threadid)) {
                abort(404);
            }

            //单页面不建议用GET参数；所以下面的redirect不带参数。所有需要的参数通过Cookie传递回去
            \Cookie::queue('js:yx2018:threadid', $threadid, 0, null, null, false, false);

            $groupid = $member = $lua->getThread($threadid);
            if ($groupid) {
                $member = $lua->getMemberAndParse($groupid);
                if ($member) {
                    //同样这里是前端需要的助力者昵称参数
                    \Cookie::queue('js:yx2018:thread_nickname', $member['nickname'], 0, null, null, false, false);
                }
            }
        }

        if ($act) {
            //但页面前端通过判断Cookie中的act决定跳转到哪里
            \Cookie::queue('js:yx2018:act', $act, 0, null, null, false, false);
        }

        return redirect(route('yx2018'));
    }

    /**
     * 单页面应用的数据源; 真正意义上的首页在前端的home模板里
     */
    public function index()
    {
        $proj = $this->getProject();
        $rule_content = $proj->rules;           //取后台配置的活动规则
        $fields = $this->getFieldConfigs();     //取表单字段配置
        $fields['city'] = wj_city_data();       //省市区数据

        //其实四个分享参数(标题、摘要、链接、图片)也可以通过后台配置，这里带给前端，这里没做

        //compact 将变量名作为key，变量值作为value组成数组
        return compact('fields', 'rule_content');
    }

    /**
     * 各种状态的判断及相应的提示;
     * 这个方法既作为routing方法也作为可调用的函数，如: user方法中就有调用
     */
    public function status($registed = null)
    {
        $lua = static::getLuaRedis();

        $with_response = false;
        if ($registed === null) {
            $registed = $lua->checkRegist(wx_openid());
            $with_response = true;
        }

        $ret = [];

        if ($registed) {
            $ret['can_baoming'] = false;
            $ret['baoming_notice'] = '您已经报过名了，请前往个人中心了解更多详情';
        } else {
            $ret['can_baoming'] = static::$CAN_REGIST && !static::$REGIST_END;
            if (!$ret['can_baoming']) {
                if (!static::$CAN_REGIST) {
                    $ret['baoming_notice'] = '预报名通道于3月23日上午10:00开启，请稍后';
                    //用于临时关闭的提示
                    //$ret['baoming_notice'] = '预报名通道暂未开启';
                } elseif (static::$REGIST_END) {
                    $ret['baoming_notice'] = '预报名通道已关闭，感谢您对本活动的关注！';
                }
            }
        }

        $ret['can_chaxun'] = static::$CAN_CHAXUN && !static::$CHAXUN_END;
        if (!$ret['can_chaxun']) {
            if (!static::$CAN_CHAXUN) {
                $ret['chaxun_notice'] = '报名查询通道于4月20日上午10:00开启，请稍后';
                //用于临时关闭的提示
                //$ret['chaxun_notice'] = '查询通道暂未开启';
            } elseif (static::$CHAXUN_END) {
                $ret['chaxun_notice'] = '查询通道已关闭，感谢您对本活动的关注！';
            }
        }

        $ret['can_vote'] = static::$CAN_VOTE && !static::$VOTE_END;
        if (!$ret['can_vote']) {
            if (!static::$CAN_VOTE) {
                //用于临时关闭的提示
                $ret['vote_notice'] = '募捐通道暂未开启';
            } elseif (static::$VOTE_END) {
                $ret['vote_notice'] = '公益募捐已结束！感谢您对本活动的关注！';
            }
        }

        $ret['can_confirm'] = static::$CAN_CONFIRM && !static::$CONFIRM_END;
        if (!$ret['can_confirm']) {
            if (!static::$CAN_CONFIRM) {
                //用于临时关闭的提示
                $ret['confirm_notice'] = '报名信息确认通道暂未开启';
            } elseif (static::$CONFIRM_END) {
                $ret['confirm_notice'] = '很遗憾！报名确认时间已截止，您错失了本届毅行名额，感谢您对本次活动的关注';
            }
        }

        $ret['members'] = $lua->getMembersAmount();

        if ($with_response) {
            return wj_json_message($ret);
        } else {
            return $ret;
        }
    }

    public function regen(Request $request)
    {
        $lua = static::getLuaRedis();

        $openid = wx_openid();
        $info = wx_info();
        $threadid = md5($openid);

        if ($request->input('tid') !== $threadid) {
            abort(404);
        }

        //检查授权
        if (!$lua->checkWxUpdatePerm($openid)) {
            redirect_message("未授权或授权已失效", "/yx2018/start");
        }


        if (!$lua->checkMember($openid)) {
            redirect_message("二维码仅供已参与人员更新微信信息用", "/yx2018/start");
        }

        $oldInfo = $lua->getMemberAndParse($openid);
        $lua->saveMember($openid, $info);

        $redis = $lua->getRedis();
        $nickkey = $oldInfo['nickname'];
        $idlist = $redis->hGet($lua::kNicknamesNew, $nickkey);
        $idlist = explode(',', $idlist);
        $idlist = array_filter(array_unique(array_diff($idlist, [$openid])));
        if (!$idlist) {
            $redis->hDel($lua::kNicknamesNew, $nickkey);
        } else {
            $redis->hSet($lua::kNicknamesNew, $nickkey, implode(',', $idlist));
        }

        $nickkey = $info['nickname'];
        $idlist = $redis->hGet($lua::kNicknamesNew, $nickkey);
        $idlist = explode(',', $idlist);
        $idlist[] = $openid;
        $idlist = array_filter(array_unique($idlist));
        $redis->hSet($lua::kNicknamesNew, $nickkey, implode(',', $idlist));


        //后台制作海报
        $job = new DataSyncJob('make_cover', $threadid, $info['nickname'], $info['headimgurl']);
        dispatch_now($job);

        $last_donate = $lua->getDonate($openid);
        //第一次获得捐助时生成捐款证书
        if ($last_donate > 0) {
            $job = new DataSyncJob('make_cert', $threadid, @$info['nickname']);
            dispatch_now($job);
        }

        //注销授权
        $lua->permitUpdateWxInfo($openid, false);

        redirect_message("微信资料更新成功，部分页面可能有缓存，请10分钟后刷新确认", "/yx2018/start");
    }

    /**
     * 个人中心AJAX接口
     */
    public function user()
    {
        $lua = static::getLuaRedis();

        $openid = wx_openid();
        $ret = $lua->getGroupAndParse($openid);

        if ($ret['players']) {
            //涉及金额的计算用BC库，避免浮点数存储精度问题
            $ret['donate'] = bcdiv($ret['donate'], 100, static::DOTLEN);

            //选手列表
            foreach ($ret['players'] as &$player) {
                $this->extractPlayer($player);
            }
            unset($player);
            $ret['registed'] = true;

            //排行榜TOP3
            $ret['top3'] = $lua->rankingAndParse($ret['players'][0]['line'], 0, 3) ?: [];
            foreach ($ret['top3'] as &$item) {
                $item = [
                    'rank'      => $item['rank'],
                    'nickname'  => $item['info']['nickname'],
                    'headimgurl'=> $item['info']['headimgurl'] ?: static::DEFAULT_AVATAR
                ];
            }
            unset($item);

            //最新20条助力记录
            $ret['new_voters'] = $lua->getVotelistAndParse($openid, 0, 20);
            if (!$ret['new_voters']) {
                $ret['new_voters'] = [
                    'data'          => [],
                    'has_more'      => false
                ];
            }
            foreach ($ret['new_voters']['data'] as &$item) {
                $item = [
                    'time'      => date('m-d H:i:s', $item['time']),
                    'donate'    => bcdiv($item['donate'], 100, static::DOTLEN),
                    'nickname'  => $item['info']['nickname'],
                    'headimgurl'=> $item['info']['headimgurl'] ?: static::DEFAULT_AVATAR
                ];
            }
            unset($item);

            $player = $ret['players'][0];

            //个人中心头像下方的公告板，一共有五种状态
            //如果排名系统已结束
            if (static::$RANKING_END) {
                if (@$player['ticket_no']) {
                    if (@$player['supply_loc']) {
                        //已填写物资领取地 = 已确认
                        $ret['status'] = 'confirmed';
                    } else {
                        //有毅行编号但是没填物资领取地 = 有名额，待确认
                        $ret['status'] = 'rankok';
                    }
                } else {
                    if (!@$player['checked']) {
                        //没毅行编号且未通过审核 = 没拿到名额
                        $ret['status'] = 'norank';
                    } else {
                        //没毅行编号但审核通过了 = 之前拿到过名额但被释放了
                        $ret['status'] = 'released';
                    }
                }
            } else {
                //没编号且尚在排名中 = 显示水位线
                $ret['status'] = 'ranking';
            }

            //水位线: 即前多少名能获得毅行名额。因为总人数固定，而每组报名人数不定，所以水位线是实时变化的。
            //需求是统计前一日的水位做参考; 水位计算逻辑在Console中 wanjia:yx2018:water_line; 并被加入到了定时任务: App\Console\Kernel
            $ret['water_line'] = $lua->getWaterLine($ret['players'][0]['line']);
        } else {
            $ret['registed'] = false;
            $ret['water_line'] = 0;
        }

        //后面所述的threadid都可以认为是openid; 我们给openid和 threadid=md5(openid) 做了个映射用于数据脱敏
        $ret['threadid'] = md5($openid);
        $ret['info'] = $lua->getMemberAndParse(wx_openid());

        $ret = array_merge($ret, $this->status($ret['registed']));
        return wj_json_message($ret);
    }

    /**
     * 报名查询逻辑; 因不涉及数据写入，预测并发量不会太高，直接查数据库了
     */
    public function searchPlayer(Request $request)
    {
        $status = $this->status(false);
        if (!$status['can_chaxun']) {
            return wj_json_message($status['chaxun_notice'], 1);
        }


        $query = $request->all(['name', 'passport', 'passport_type']);
        $query['passport'] = "{$query['passport_type']}:{$query['passport']}";
        unset($query['passport_type']);



        $lua = static::getLuaRedis();

        //尝试从缓存中获取用户信息
        $player = $lua->searchPlayer($query['name'], $query['passport']);
        if ($player) {
            $player = wj_arr2obj($player);
        } else {
            //找不到则查询数据库
            $proj = $this->getProject();
            $player = Player::repository()->findOneByProjectId($proj->id, [
                ServiceProvider::getPlayerIndex('name')         => $query['name'],
                ServiceProvider::getPlayerIndex('passport')     => $query['passport']
            ]);

            if ($player) {
                $checked = $player->checked;
                $ticket_no = $player->ticket_no;
                $player = $player->info;
                $player->checked = $checked;
                $player->ticket_no = $ticket_no;
            }
        }

        if (!$player) {
            return wj_json_message('未查询到报名信息', 1);
        }

        //checked表示获得过名额, 要在下一步同步检查ticket_no才能确认有没有名额; 因运营方需要区分 未获得名额 和 名额被释放 的情况
        if (!@$player->checked) {
            return wj_json_message('很遗憾，您未能成功获取毅行名额！', 2);
        }

        if (!($ticket_no = @$player->ticket_no)) {
            return wj_json_message('很遗憾，您因逾期未补充报名信息，名额已被释放！', 3);
        }

        if ($player->is_master === 'Y' && $player->openid === wx_openid()) {
            $buttons = ['my'];
        } else {
            $buttons = [];
        }

        $player = wj_obj2arr($player);
        $player['ticket_no'] = $ticket_no;
        $this->extractPlayer($player);

        $player['buttons'] = $buttons;
        return wj_json_message($player);
    }


    public function searchName(Request $request)
    {
        $k = $request->input('k');
        $k = trim($k);
        $k = str_replace('*', '', $k);

        if (!$k) {
            return wj_json_message([]);
        }

        $lua = static::getLuaRedis();
        $ret = $lua->searchNickname("*$k*", 10);
        return wj_json_message($ret);
    }

    /**
     * 报名信息的验证逻辑
     */
    public function checkPlayerInfo(Request $request)
    {
        $status = $this->status(false);
        if (!$status['can_baoming']) {
            return wj_json_message($status['baoming_notice'], 1);
        }

        //如果是身份证号码且身份证号码格式正确，则解析出 age 和 gender 字段
        if ($request->input('passport_type') == 'SFZ') {
            $sfz = wj_parse_sfz($request->input('passport'));
            if ($sfz !== false) {
                $request->request->add(wj_mask($sfz, ['age', 'gender']));
            }
        } else {
            $sfz = null;
        }

        $fields = [
            'name'          => '姓名',
            'passport_type' => '证件类型',
            'passport'      => '证件号码',
            'age'           => '年龄',
            'gender'        => '性别',
            'phone'         => '手机号码',
            'line'          => '目标终点'
        ];

        $field_confs = $this->getFieldConfigs();
        $lua = static::getLuaRedis();

        //验证规则
        $this->validate($request, [
            'name'          => 'required|between:2,48',
            'passport_type' => [
                'required',
                Rule::in(array_keys($field_confs['passport']))
            ],
            'passport'      => [
                'required',
                function ($attr, $value, $fail) use ($request, $sfz, $lua) {
                    if ($request->input('passport_type') == 'SFZ') {
                        if ($sfz === false) {
                            $fail("身份证号码验证失败");
                            return;
                        }
                    } else {
                        if (!preg_match('/^[A-Za-z()\d-]{6,24}$/', $value)) {
                            $fail("证件号码验证失败");
                            return;
                        }
                    }

                    $passport = $request->input('passport_type') . ':' . $value;
                    if ($lua->checkUsedPassport($passport)) {
                        $fail("证件号码已被使用");
                        return;
                    }
                }
            ],
            'age'           => [
                'required',
                function ($attr, $value, $fail) {
                    if ($value < 1 || $value > 55) {
                        $fail("选手年龄不能超过55周岁");
                        return;
                    }
                }
            ],
            'gender'        => 'required|in:W,M',
            'phone'      => [
                'required',
                'regex:/^1[0-9]{10}$/',
                function ($attr, $value, $fail) use ($lua) {
                    if ($lua->checkUsedPhone($value)) {
                        $fail("手机号码已被使用");
                        return;
                    }
                }
            ],
            'line'          => [
                'required',
                Rule::in(array_keys($field_confs['line']))
            ]
        ], [
        ], $fields);


        $data = $request->all(array_keys($fields));

        //合并证件类型和证件号
        $data['passport'] = "{$data['passport_type']}:{$data['passport']}";

        if ($data['passport_type'] === 'SFZ') {
            $data['sfz'] = $sfz;
        }

        $data['hash'] = $lua->hashPlayer($data['name'], $data['passport']);

        $data = wj_json_encode($data);

        //验证后的数据加签名，防篡改；后续存储直接验证签名即可
        $encodedData = md5($data . static::VALIDATION_TOKEN) . base64_encode($data);
        return wj_json_message($encodedData);
    }


    public function test()
    {
        //后台制作海报
        $threadid = md5(wx_openid());
        $info = wx_info();
        $job = new DataSyncJob('make_cover', $threadid, $info['nickname'], $info['headimgurl']);
        $job->onQueue('yx2018_cover');
        dispatch($job);

        return 'ok';
    }

    /**
     * 报名的最后一步： 提交已验证的信息然后保存
     */
    public function savePlayers(Request $request)
    {
        $status = $this->status(false);
        if (!$status['can_baoming']) {
            return wj_json_message($status['baoming_notice'], 1);
        }

        $players = $request->input('players');
        $players = collect($players)->filter(function ($player) {
            //验证签名
            if (!is_string($player)) {
                return false;
            }

            if (strlen($player) < 32) {
                return false;
            }

            $sign = substr($player, 0, 32);
            $data = substr($player, 32);
            $data = @base64_decode($data) ?: '';
            return md5($data . static::VALIDATION_TOKEN) === $sign;
        })->map(function ($player) {
            //解出选手信息
            $data = substr($player, 32);
            $data = base64_decode($data);
            return wj_json_decode($data);
        })->values()->all();

        $players = array_slice($players, 0, 3);

        $phones = [];
        $passports = [];
        foreach ($players as $k => &$player) {
            $player['is_master'] = $k == 0 ? 'Y' : 'N';
            $player['is_union'] = count($players) > 1 ? 'Y' : 'N';
            $player['openid'] = wx_openid();
            $player['registed_at'] = time();
            $phones[] = $player['phone'];
            $passports[] = $player['passport'];
        }
        unset($player);

        if (!$phones) {
            return wj_json_message('请提交有效的报名信息', 1);
        }

        if (count(array_unique($phones)) != count($players)) {
            return wj_json_message('同组队员的手机号不能相同', 1);
        }

        if (count(array_unique($passports)) != count($players)) {
            return wj_json_message('同组队员的证件号码不能相同', 1);
        }

        $lua = static::getLuaRedis();
        $threadid = md5(wx_openid());

        $ret = $lua->regist(
            $threadid,
            wx_openid(),
            wj_json_encode($players)
        );

        if ($ret[0] !== 'SUCCESS') {
            return wj_json_message($ret[1], 1);
        }


        //助力数相同时，按时间倒序
        $timepos = '2019-04-03';
        $timestamp = sprintf('%013d', abs(strtotime($timepos) * 10000 - floor(microtime(true) * 10000)));
        $donate = "0.$timestamp";
        $openid = wx_openid();
        $lua->vote($openid, $openid, "$donate", (string)time());

        //数据队列插入
        $proj = $this->getProject();
        $job = new DataSyncJob('player_to_db', $phones, $proj->id, $proj->merchant_id);
        $job->onQueue('yx2018_data');
        dispatch($job);

        //后台制作海报
        $info = wx_info();
        $job = new DataSyncJob('make_cover', $threadid, $info['nickname'], $info['headimgurl']);
        $job->onQueue('yx2018_cover');
        dispatch($job);

        //后台制作称号
        $job = new DataSyncJob('make_honor', $threadid, $info['nickname'], $info['headimgurl']);
        $job->onQueue('yx2018_cover');
        dispatch($job);

        return wj_json_message([
            'threadid'      => $threadid
        ]);
    }

    /**
     * 报名信息确认(补充详细信息)
     */
    public function confirmPlayer(Request $request)
    {
        $status = $this->status(false);
        if (!$status['can_confirm']) {
            return wj_json_message($status['confirm_notice'], 1);
        }


        $field_confs = $this->getFieldConfigs();

        $data = $request->json()->all();

        $proj = $this->getProject();
        $players = Player::repository()->findByProjectId($proj->id, [
            ServiceProvider::getPlayerIndex('openid') => wx_openid()
        ]);

        $player = $players[0];

        if (!$player) {
            return wj_json_message('抱歉，未找到选手信息', 1);
        }

        if (!$player->ticket_no) {
            return wj_json_message('抱歉，现在还不能确认信息', 1);
        }

        if ($player->info_supply_loc) {
            return wj_json_message('抱歉，您已经确认过了', 1);
        }

        $phones = array_keys($data['update']);
        if (!$phones || (count($phones) != count($players))) {
            return wj_json_message('参数错误', 2);
        }

        $cities = array_keys(wj_city_data(true));
        //检查是否本组所有成员都有编号
        foreach ($players as $player) {
            if (!$player->ticket_no) {
                return wj_json_message('抱歉，系统错误请联系主办单位解决; 服务热线: 400-8484-365 转 5610', 1);
            }

            if (!in_array($player->info_phone, $phones)) {
                return wj_json_message('参数错误', 1);
            }

            if (!in_array($data['update'][$player->info_phone]['size'], array_keys($field_confs['size']))) {
                return wj_json_message('参数错误', 1);
            }

            if (!in_array($data['update'][$player->info_phone]['city'], $cities)) {
                return wj_json_message('参数错误', 1);
            }
        }

        if (!in_array($data['supply_loc'], array_keys($field_confs['supply_loc']))) {
            return wj_json_message('参数错误', 1);
        }

        foreach ($players as $player) {
            $player->info_size = $data['update'][$player->info_phone]['size'];
            $player->info_city = $data['update'][$player->info_phone]['city'];
            $player->info_supply_loc = $data['supply_loc'];
            $player->info_confirmed_at = time();

            //可能会纳闷，这里直接操作数据库修改; Redis里的信息怎么办，可以看一下本模块下的ServiceProvider 有对Player对象数据库存储事件做拦截处理
            $player->save();
        }

        return wj_json_message('恭喜，您已成功完善毅行报名资料!');
    }

    /**
     * 助力界面生成支付订单
     */
    public function makeOrder(Request $request)
    {
        $status = $this->status(false);
        if (!$status['can_vote']) {
            return wj_json_message($status['vote_notice'], 1);
        }

        $threadid = $request->cookie('js:yx2018:threadid');

        $lua = static::getLuaRedis();
        if (!$threadid) {
            $groupid = wx_openid();
            $threadid = md5($groupid);
        } else {
            $groupid = $lua->getThread($threadid);
            if (!$groupid) {
                return wj_json_message('该微信号未完成报名', 1);
            }
        }

        $proj = $this->getProject();

        $order = Payment::repository()->findOneByProjectId($proj->id, [
            'openid'    => wx_openid(),
            'type'      => $threadid,       //type在本项目中用来存储threadid
            'is_valid'  => true
        ], [
            'orderby'   => 'updated_at desc'
        ]);

        //生成订单或拿到之前未完成的订单，因为只允许助力一次
        if ($order) {
            if ($order->payment) {
                return wj_json_message('您已捐过不能再捐了', 1);
            } elseif (abs($order->updated_at->getTimestamp() - time()) >= 7200) {
                //prepay_id有效期为俩小时；过期后要重新调接口获取
                $order->prepay_id = null;
            }
        } else {
            $order = new Payment();
            //其实也可以写 $order->project_id = $proj->id; 只不过写associate更面向对象一些，根据个人爱好吧
            $order->project()->associate($proj);
            $order->merchant()->associate($proj->merchant);
            $order->order_no = make_order_no();
            $order->openid = wx_openid();
            $order->type = $threadid;
            $order->is_valid = true;
            $order->data = wj_arr2obj([
                'groupid'   => $groupid,
                'donate'    => static::DONATE
            ]);

            try {
                $order->save();
            } catch (\Throwable $e) {
                \Log::error($e);
                return wj_json_message('生成订单失败', 1);
            }
        }

        $app = $proj->merchant->wechat_app;

        //从微信业务端获取prepare_id
        if (!$order->prepay_id) {
            $pOrder = new Order([
                'trade_type'       => 'JSAPI',
                'body'             => '毅行-活动捐款',
                'detail'           => wj_json_encode($order->data),
                'out_trade_no'     => $order->trade_no,
                'total_fee'        => $order->data->donate,
                'notify_url'       => route('yx2018.pay_callback'),     //设置支付完成后的回调地址, 此处对应 Controller@payCallback
                'openid'           => wx_openid()
            ]);
            $ret = $app->payment->prepare($pOrder);
            
            if ($ret->get('return_code') != 'SUCCESS' || $ret->get('result_code') != 'SUCCESS') {
                \Log::error($ret->get('return_msg'));
                return wj_json_message('生成订单失败', 1);
            }

            $order->prepay_id = $ret->get('prepay_id');
            $order->order = $pOrder->all();
            $order->save();
        }

        //对prepare_id做签名
        $signed_params = $app->payment->configForPayment($order->prepay_id, false);
        return wj_json_message($signed_params);
    }

    /**
     * 支付完成后的回调
     */
    public function payCallback()
    {
        $proj = $this->getProject();
        try {
            $response = $proj->merchant->wechat_app->payment->handleNotify(function ($notify, $successful) {
                //handleNotify内部已经做了签名验证，证明了本次调用来源自可信调用端;
                //什么是非可信端? CURL 或 浏览器中直接请求这个地址

                //这里验证$successful 表示这次请求是一次成功的支付
                if (!$successful) {
                    \Log::error("PAY_NO_SUCCESS:" . $notify);
                    return '支付验证失败';
                }

                try {
                    //取到交易订单，保存支付信息
                    $pay = \DB::transaction(function () use ($notify) {
                        /**
                         * 几个业务单号的概念:
                         * order_no     订单号 订单对应于一次购买行为
                         * trade_no     交易号 比如淘宝下单后，到支付页面发现价格接受不了，跟小二讨价还价让改价格; 一般情况下，订单号不改，但是支付系统要求改价格必须用新的单号，交易号就用来解决这个问题
                         * billing_no   流水号 在微信端对应一笔支付到账流水
                         * prepare_id   预支付订单号 这是微信的概念，因为交易过程是一个三方交互过程。微信作为交易中介，不能完全信任另外两方，比如付款小窗口中显示的交易价格
                         *              如果没有预支付订单的概念，付款方和收款放都可以在付款的任意环节修改交易价格，造成账务混乱。
                         */
                        $pay = Payment::repository()->findOneByTradeNo($notify->out_trade_no);
                        if (!$pay) {
                            throw new PaymentException(
                                '未找到交易信息',
                                PaymentException::CODE_NO_TRADE,
                                $notify
                            );
                        }

                        if ($pay->payment) {
                            throw new PaymentException(
                                '交易已处理',
                                PaymentException::CODE_TRADE_PROCESSED,
                                $notify
                            );
                        }

                        //对比支付金额和订单金额是否匹配
                        if ($pay->order->total_fee != $notify->total_fee) {
                            throw new PaymentException(
                                '订单金额校验失败',
                                PaymentException::CODE_ERR_FEE,
                                $notify
                            );
                        }

                        $pay->payment = $notify;
                        $pay->transaction_id = $notify->transaction_id;
                        $pay->save();

                        return $pay;
                    });
                } catch (PaymentException $ex) {
                    if ($ex->getCode() === PaymentException::CODE_TRADE_PROCESSED) {
                        \Log::warning("yx2018微信支付重复回调: " . $notify->out_trade_no);
                        return true;
                    }

                    \Log::error($ex->getMessage() . ': ' . wj_json_encode($notify));
                    return $ex->getMessage();
                }

                //记录助力数据
                $lua = static::getLuaRedis();
                $groupid = $pay->data->groupid;
                $last_donate = $lua->getDonate($groupid);
                $lua->vote($pay->data->groupid, $pay->openid, "{$pay->data->donate}", (string)time());

                //第一次获得捐助时生成捐款证书
                if ($last_donate <= 0) {
                    $info = $lua->getMemberAndParse($groupid) ?: [];
                    $job = new DataSyncJob('make_cert', md5($groupid), @$info['nickname']);

                    //此处应用了命名队列; 队列伺服进程可以只执行某种队列任务。
                    //因为有的任务可以并发执行，有的任务必须按顺序执行，有的任务执行时间长，有的任务执行时间短
                    //如果全部放到一个伺服进程里执行，耗时任务势必影响即时性要求高的任务；顺序型任务也牵制并发任务不能多队列执行
                    //队列的命名就是对任务进行分类，可以更好的对后台任务处理过程做治理
                    // docker/crontabs中定义了几个队列伺服容器的启动过程
                    $job->onQueue('yx2018_cover');

                    //dispatch就是投放到队列; 调用dispatch_now可以在当前进程中立即执行，可用于调试。
                    //Console中的wanjia:yx2018:fix_cover就是这么用的
                    dispatch($job);
                }

                return true;
            });
        } catch (FaultException $e) {
            return response('数据校验失败');
        }

        //更改Content-Type, 避免调试过程中被Debugbar注入
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * 排行榜头部显示的募捐总金额;
     * 需要微信授权登录，得到当前已报名用户选择的线路
     */
    public function rankingHead()
    {
        $lua = static::getLuaRedis();
        $line = $lua->getLine(wx_openid());

        if (!$line) {
            $line = 'L3';
        }

        $donate = $lua->getTotalDonate();

        return wj_json_message([
            'line'  => $line,
            'threadid' => md5(wx_openid()),
            'donate'=> number_format($donate, static::DOTLEN)
        ]);
    }

    /**
     * 排行榜头部显示的募捐总金额; PC端用，不需要微信认证
     */
    public function rankingHeadPc()
    {
        $lua = static::getLuaRedis();
        $donate = $lua->getTotalDonate();
        return wj_json_message([
            'donate'=> number_format($donate, static::DOTLEN)
        ]);
    }

    /**
     * 排行榜; routing中加了缓存中间件
     */
    public function ranking(Request $request)
    {
        $fields = $this->getFieldConfigs();

        try {
            $this->validate($request, [
                'line'          => [
                    'required',
                    Rule::in(array_keys($fields['line']))
                ]
            ], [
            ], [
                'line'  => '目标终点'
            ]);
        } catch (ValidationException $ex) {
            abort(404);
        }

        $line = $request->input('line');
        $threadid = $request->input('threadid');

        $lua = static::getLuaRedis();
        $groupid = $lua->getThread($threadid);

        $ranking = $lua->rankingAndParse($line, 0, 100, $groupid, 5);

        //数据脱敏
        foreach ($ranking['ranking'] as $k => &$v) {
            $this->maskRankingData($v);
        }
        unset($v);

        foreach ($ranking['around'] as $k => &$v) {
            $this->maskRankingData($v);
        }
        unset($v);

        $this->maskRankingData($ranking['me']);

        $ret = array_slice($ranking, 0, 100);

        return wj_json_message($ret);
    }

    /**
     * 排行榜数据脱敏
     */
    protected function maskRankingData(&$v)
    {
        if (!$v) {
            return;
        }

        $reg_time = $v['players'][0]['registed_at'];
        unset($v['players']);
        $v['info'] = wj_mask($v['info'], ['nickname', 'headimgurl']);
        $v['info']['headimgurl'] = $v['info']['headimgurl'] ?: static::DEFAULT_AVATAR;
        $v['donate'] = bcdiv($v['score'], 100, static::DOTLEN);
        $v['time'] = date('m-d H:i:s', $reg_time);
        $v['threadid'] = md5($v['openid']);

        unset($v['openid'], $v['score']);
    }

    /**
     * 助力者列表
     */
    public function votelist(Request $request)
    {
        $threadid = $request->get('threadid');
        $lua = static::getLuaRedis();
        $groupid = $lua->getThread($threadid);

        if (!$groupid) {
            abort(404);
        }

        if ($groupid != wx_openid()) {
            return wj_json_message('权限验证出错', 1);
        }

        $pos = (int)$request->get('pos', 0);
        $len = 100;

        $ret = $lua->getVotelistAndParse($groupid, $pos, $len);
        if (!$ret) {
            $ret = [
                'data'      => [],
                'has_more'  => false
            ];
        }

        foreach ($ret['data'] as &$item) {
            $item['info'] = wj_mask($item['info'], ['nickname', 'headimgurl']);
            $item['info']['headimgurl'] = $item['info']['headimgurl'] ?: static::DEFAULT_AVATAR;
            $item['time'] = date('m-d H:i:s', $item['time']);
            $item['donate'] = bcdiv($item['donate'], 100, static::DOTLEN);
            unset($item['openid']);
        }
        unset($item);

        if ($ret['has_more']) {
            $ret['next_pos'] = $pos + $len;
        }

        return wj_json_message($ret);
    }

    public function wxUpdatePerms(Request $request)
    {
        $lua = static::getLuaRedis();
        $proj = Project::matchByPath('/yx2018');

        if ($request->method() === 'POST') {
            do {
                $phone = $request->input('phone');

                $player = Player::repository()->findOneByProjectId($proj->id, [
                    ServiceProvider::getPlayerIndex('phone')        => $phone,
                    ServiceProvider::getPlayerIndex('is_master')    => 'Y'
                ]);

                if (!$player) {
                    $toastr = new MessageBag([
                        'message'   => '未找到队长信息',
                        'type'      => 'error'
                    ]);
                    break;
                }

                $lua->permitUpdateWxInfo($player->info_openid);

                $toastr = new MessageBag([
                    'message'   => '授权成功',
                    'type'      => 'success'
                ]);
            } while (false);

            return back()->with(compact('toastr'));
        }

        if ($request->input('act') === 'cancel') {
            $tid = $request->input('tid');

            $openid = $lua->getThread($tid);
            $lua->permitUpdateWxInfo($openid, false);

            $toastr = new MessageBag([
                'message'   => '取消授权成功',
                'type'      => 'success'
            ]);
            return back()->with(compact('toastr'));
        }

        return Admin::content(function (Content $content) use ($proj, $lua) {
            $content->header('授权更新微信资料');
            $content->description('注意: 频繁改变微信昵称有损排行榜公信力，请慎用此功能');

            $form = new \Encore\Admin\Widgets\Form();
            $form->action(route('yx2018.admin.wx_update_perms'));
            $form->method('POST');
            $form->mobile('phone', '队长手机号');
            $content->row((new Box('增加授权', $form))->style('success'));

            $list = [];

            $redis = $lua->getRedis();
            $perms = $redis->sMembers(RedisOperator::kWxUpdatePermit);
            if ($perms) {
                $openids = $redis->hMGet(RedisOperator::kThreads, $perms);
                $wx_infos = $redis->hMGet(RedisOperator::kWxMembers, $openids);
                $phones = $redis->hMGet(RedisOperator::kGroups, $openids);
                $phones = collect($phones)->map(function ($phone) {
                    return substr($phone, 0, 11);
                })->all();
                $players = $redis->hMGet(RedisOperator::kPlayers, $phones);

                foreach ($perms as $tid) {
                    $list[$tid] = [
                        'id'        => $tid,
                        'openid'    => $openid = @$openids[$tid],
                        'wx_info'   => @wj_json_decode(@$wx_infos[$openid]) ?: [],
                        'info'      => @wj_json_decode(@$players[$phones[$openid]]) ?: [],
                        'ticket'    => 'data:image/png;base64,' . base64_encode(
                                \QrCode::format('png')
                                    ->size(80)
                                    ->margin(0)
                                    ->generate(route('yx2018.regen', ['tid'   => $tid]))
                            )
                    ];
                }
            }

            $content->row(view('FT.yx2018::admin_wx_update_perms', compact(
                'list'
            )));
        });
    }

    /**
     * 数据统计后台
     */
    public function stats(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {

            set_time_limit(0);
            ini_set('memory_limit', '102400M');

            $content->header('毅行数据统计');
            $content->description('');

            $time = date('Y-m-d H:i:s');
            $proj = Project::matchByPath('/yx2018');
            $request->attributes->set('project', $proj);

            $lua = static::getLuaRedis();

            //专题的UV，即多少微信用户授权进来过
            $uv = $lua->getMembersAmount();


            //独立捐赠者数量, 因为一个人有可能给多个队捐款
            $voters = \DB::select(<<<SQL
                select 
                  count(distinct openid) total
                from `zt_payments` 
                where `project_id` = :proj_id 
                  and `payment` IS NOT NULL
SQL
                , ['proj_id' => $proj->id])[0]->total;


            //按线路分捐款额
            $line_donates = \DB::select(<<<SQL
                select 
                  str8 line, count(1) total
                from `zt_payments` m
                  inner join `zt_player` p on m.`type` = md5(p.str1)
                where m.`project_id` = :proj_id 
                  and `payment` IS NOT NULL
                  and p.str9='Y'
                group by str8
SQL
                , ['proj_id' => $proj->id]);
            $line_donates = collect($line_donates)->pluck('total', 'line')->all();

            //按线路分报名人数
            $line_players = \DB::select(<<<SQL
                select 
                  str8 line, count(*) total 
                from `zt_player` 
                where `project_id` = :proj_id 
                group by str8
SQL
                , ['proj_id' => $proj->id]);
            $line_players = collect($line_players)->pluck('total', 'line')->all();

            //按线路分报名团队数(含单人报名)
            $line_groups = \DB::select(<<<SQL
                select 
                  str8 line, count(*) total 
                from `zt_player` 
                where `project_id` = :proj_id 
                  and str9 = 'Y'
                group by str8
SQL
                , ['proj_id' => $proj->id]);
            $line_groups = collect($line_groups)->pluck('total', 'line')->all();

            //按线路分未募得捐款的团队数 “0元组”
            $line_zero_groups = [
                'L1'    => count($lua->getRedis()->zRangeByScore(RedisOperator::pRanking . 'L1', 0, 0.999)),
                'L2'    => count($lua->getRedis()->zRangeByScore(RedisOperator::pRanking . 'L2', 0, 0.999)),
                'L3'    => count($lua->getRedis()->zRangeByScore(RedisOperator::pRanking . 'L3', 0, 0.999)),
            ];

            //按线路分未募得捐款的团队数 “1元组”
            $line_one_groups = [
                'L1'    => count($lua->getRedis()->zRangeByScore(RedisOperator::pRanking . 'L1', 100, 100.999)),
                'L2'    => count($lua->getRedis()->zRangeByScore(RedisOperator::pRanking . 'L2', 100, 100.999)),
                'L3'    => count($lua->getRedis()->zRangeByScore(RedisOperator::pRanking . 'L3', 100, 100.999)),
            ];
            
            $lines = $this->getFieldConfigs()['line'];

            //按线路统计的数据再汇总
            $zero_groups = array_sum($line_zero_groups);
            $one_groups = array_sum($line_one_groups);
            $players = array_sum($line_players);
            $groups = array_sum($line_groups);
            $donates = array_sum($line_donates);

            $provinces = @\DB::select(<<<SQL
                select count(distinct info->>'$.sfz.location.province') total 
                from `zt_player` 
                where `project_id` = :proj_id 
                  && str3 like 'SFZ:%'
SQL
                , ['proj_id' => $proj->id])[0]->total;

            $cities = @\DB::select(<<<SQL
                select count(distinct info->>'$.sfz.location.city') total 
                from `zt_player` 
                where `project_id` = :proj_id 
                  && str3 like 'SFZ:%'
SQL
                , ['proj_id' => $proj->id])[0]->total;


            $time_10000 = @\DB::select(<<<SQL
              select `created_at`
              from zt_payments 
              where project_id = :proj_id  
                && payment > '' 
              order by `created_at` 
              limit 9999,1
SQL
                , ['proj_id' => $proj->id])[0]->created_at;

            $time_50000 = @\DB::select(<<<SQL
              select `created_at`
              from zt_payments 
              where project_id = :proj_id  
                && payment > '' 
              order by `created_at` 
              limit 49999,1
SQL
                , ['proj_id' => $proj->id])[0]->created_at;

            $time_100000 = @\DB::select(<<<SQL
              select `created_at`
              from zt_payments 
              where project_id = :proj_id  
                && payment > '' 
              order by `created_at` 
              limit 99999,1
SQL
                , ['proj_id' => $proj->id])[0]->created_at;

            $top10_donate = @\DB::select(<<<SQL
              select openid, count(1) total 
              from zt_payments 
              where project_id = :proj_id 
                && payment > '' 
              group by openid 
              having total > 1 
              order by total desc 
              limit 10
SQL
                , ['proj_id' => $proj->id]);

            foreach ($top10_donate as $item) {
                $item->wxinfo = $lua->getMemberAndParse($item->openid);
            }

            $rep_donaters = @\DB::select(<<<SQL
              select count(1) total
              from (
                select openid, count(1) n 
                from zt_payments 
                where project_id = :proj_id 
                  && payment > '' 
                group by openid
                having n > 1
              ) a
SQL
                , ['proj_id' => $proj->id])[0]->total;

            $donaters = @\DB::select(<<<SQL
              select count(1) total
              from (
                select openid, count(1) n 
                from zt_payments 
                where project_id = :proj_id 
                  && payment > '' 
                group by openid
              ) a
SQL
                , ['proj_id' => $proj->id])[0]->total;


            $stats = [];


            $stats[] = '年龄';
            $stats = array_merge($stats, @\DB::select(<<<SQL
                select
                  (
                  case 
                    when `int1` <=12 then '12及以下'
                    when `int1` >=13 && `int1` <=24 then '13~24'
                    when `int1` >=25 && `int1` <=40 then '25~40'
                    when `int1` >=41 then '41及以上'
                    else '未知'
                  end
                  ) name,
                  count(1) total
                from `zt_player` 
                where `project_id` = :proj_id 
                group by name
                order by name
SQL
                , ['proj_id' => $proj->id]));


            $stats[] = '小组人数';
            $stats = array_merge($stats, @\DB::select(<<<SQL
                select
                  (
                    case
                    when a.total = 1 then '1人组'
                    when a.total = 2 then '2人组'
                    when a.total = 3 then '3人组'
                    else '未知'
                    end
                  ) name,
                  sum(a.total) total
                from (
                  select
                    str1,
                    count(1) total
                  from zt_player
                  where `project_id` = :proj_id 
                  group by str1
                ) a
                group by name
                order by name
SQL
                , ['proj_id' => $proj->id]));

            $cnt = @\DB::select(<<<SQL
                select count(1) total
                from 
                zt_player m 
                where `project_id` = ?
                  && `int1` <= 15 
                  && (
                    select 
                    count(1) 
                    from zt_player s 
                    where `project_id` = ?
                      && s.str1 = m.str1
                  ) in (2, 3)
SQL
                , [$proj->id, $proj->id])[0]->total;
            $stats[] = (object)[
                'name'  => '2~3人组中15岁以下儿童',
                'total' => $cnt
            ];



            $stats[] = '性别';
            $stats = array_merge($stats, @\DB::select(<<<SQL
                select
                  (
                  case 
                    when str6 = 'W' then '女'
                    when str6 = 'M' then '男'
                    else '未知'
                  end
                  ) name,
                  count(1) total
                from `zt_player` 
                where `project_id` = :proj_id 
                group by name
                order by name
SQL
                , ['proj_id' => $proj->id]));


            $stats[] = '星座';
            $stats = array_merge($stats, @\DB::select(<<<SQL
                select
                  info->>'$.sfz.xingzuo' name,
                  count(1) total
                from `zt_player` 
                where `project_id` = :proj_id 
                  && str3 like 'SFZ:%'
                group by name
                order by name
SQL
                , ['proj_id' => $proj->id]));



            $stats[] = '队长星座';
            $stats = array_merge($stats, @\DB::select(<<<SQL
                select
                  info->>'$.sfz.xingzuo' name,
                  count(1) total
                from `zt_player` 
                where `project_id` = :proj_id 
                  && str3 like 'SFZ:%'
                  && str9 like 'Y'
                group by name
                order by name
SQL
                , ['proj_id' => $proj->id]));



            $stats[] = '地市';
            $stats = array_merge($stats, @\DB::select(<<<SQL
                select
                  if(info->>'$.sfz.location.province_name' = '安徽省', info->>'$.sfz.location.city_name', '[外省]') name,
                  count(1) total
                from `zt_player` 
                where `project_id` = :proj_id 
                  && str3 like 'SFZ:%'
                group by name
                order by name
SQL
                , ['proj_id' => $proj->id]));


            $stats = wj_obj2arr($stats);

            $content->row(view('FT.yx2018::admin_stats', compact(
                'time',
                'lines',
                'uv',
                'voters',
                'players',
                'groups',
                'donates',
                'zero_groups',
                'one_groups',
                'line_donates',
                'line_groups',
                'line_players',
                'line_zero_groups',
                'line_one_groups',
                'provinces',
                'cities',
                'stats',
                'top10_donate',
                'time_10000',
                'time_50000',
                'time_100000',
                'rep_donaters',
                'donaters'
            )));
        });
    }


    /**
     * 对账后台
     * 因各方面原因(逻辑错误、网络问题); 可能会存在，用户实际支付了，但系统中的订单没有打成支付状态，或者没有执行支付后的相关逻辑
     * 这里通过下载微信方的流水单，与本地订单数据和业务数据对比分析; 人工核对发现问题
     * 微信每日9点之后开始为商户生成前一日的对账数据(生成时间可能较长，9:00之后隔一段时间再下载)
     *
     * App\Console\Kernnel 中定义了每日下载对账单和对账的定时任务
     *  wanjia:yx2018:download_bill 下载对账单
     *  wanjia:yx2018:bill_check    核对账单
     */
    public function billing()
    {
        return Admin::content(function (Content $content) {

            $content->header('毅行捐款微信对账单');
            $content->description('');


            $proj = Project::matchByPath('/yx2018');

            //data字段比较大(业务量较多的时候可能达到几十M)，这里用不到，所以不用取出来; 加快SQL执行速度
            $billings = Billing::repository()->findByProjectId($proj->id, [], [
                'columns' => ['dateline', 'billings', 'payed', 'refund', 'hongbao_refund', 'tax']
            ]);

            foreach ($billings as $billing) {
                $filename = md5(static::VALIDATION_TOKEN . 'billing' . $billing->dateline);
                $url = uploads_url("yx2018/data/$filename.csv") . '?' . Str::random(4);
                $billing->link = $url;
            }

            $content->row(view('FT.yx2018::admin_billing', compact(
                'billings'
            )));
        });
    }

    /**
     *  显示各线路5000人以后200组捐赠二维码
     */
    public function sp200qrcode(Request $request)
    {
        return Admin::content(function (Content $content) use ($request) {
            $content->header('各线路排行榜溢出200二维码');
            $content->description('');

            $lua = static::getLuaRedis();
            $redis = $lua->getRedis();
            $kNoQualificationSp200 = $lua::REDIS_NS . ":noqualification:sp200";

            $perpage = 20;
            $total = $redis->lLen($kNoQualificationSp200);
            $page = $request->input('page') ?: 1;
            $groups = $redis->lRange($kNoQualificationSp200, (($page-1)*$perpage), ($page*$perpage)) ?: [];

            $lines = [ 'L1' => 'MINI线', 'L2' => '半程线', 'L3' => '全程线' ];

            $data['rows'] = [];
            foreach ($groups as $openid) {
                $member = $lua->getMemberAndParse($openid);
                $lineIndex = $redis->hGet($lua::kGroupLines, $openid);
                $data['rows'][] = [
                    'openid' => $openid,
                    'line' => $lines[$lineIndex],
                    'nickname' => $member['nickname'],
                    'qrcode' => uploads_url("yx2018/phpqrcodes/".md5($openid).".png")
                ];
            }
            if ($page > 1) {
                $data['pre_url'] = url()->current() . "?page=" . ($page-1);
            } else {
                $data['pre_url'] = '';
            }
            if ($page >= ceil($total/$perpage)) {
                $data['next_url'] = '';
            } else {
                $data['next_url'] = url()->current() . "?page=" . ($page+1);
            }
            $content->row(view('FT.yx2018::admin_sp200qrcode', $data));
        });
    }

}

Controller::initialize();
