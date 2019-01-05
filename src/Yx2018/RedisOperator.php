<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/3/12
 * Time: 下午6:16
 */

namespace App\Features\Yx2018;


use App\Features\Yx2018\Controllers\Controller;
use Wanjia\Common\LuaRedis;

/**
 * 因为逻辑比较复杂，以及性能要求较高; 我们对 LuaRedis 再做扩展
 *
 * 背景:
 * LUA脚本中操作Redis数据，适合具备瞬时性(排行榜数据上一秒和下一秒可能都不一样)、数据一致性(如报名过程需要写入很多Key)的场景
 * 但是LUA脚本的执行效率并不如单条Redis命令的执行效率，并且每次执行过程传入参数以及返回的数据都可能比普通Redis命令要多得多，所以高并发场景下对Redis数据库网络带宽占用比较厉害，并有可能形成瓶颈。
 *
 * 所以毅行项目里，数据一致性要求高的或具有瞬时性的场景用LUA来实现; 数据一致性要求不高的场景采用PHP中调用Redis命令的方式。
 *
 *
 * 以下方法来自operators.lua; 其中AndParse后缀会做一些返回值转换，如JSON/Map等
 * @method getVotelist(string $groupid, int $pos,  int $len)        取助力者列表
 * @method getVotelistAndParse(string $groupid, int $pos,  int $len)
 *
 * @method getGroup(string $openid)        取报名者信息(个人中心)
 * @method getGroupAndParse(string $openid)
 *
 * @method ranking(string $line, int $pos,  int $len, string $me, int $around)  取排行榜; $me可传递某人的openid 额外获取其前后$around人的排名信息
 * @method rankingAndParse(string $line, int $pos,  int $len, string $me = null, int $around = 0)
 *
 * @method vote(string $groupid, int $openid,  string $donate, string $timestamp)   助力
 *
 * @method savePlayer(string $info)   保存用户
 * @method removePlayer(string $phone)   删除用户
 *
 * @method regist(string $threadid, string $openid, string $players)   报名
 */
class RedisOperator extends LuaRedis
{
    public const REDIS_NS = Controller::REDIS_NS;
    public const kPlayers = self::REDIS_NS . ':players';               //选手表            {Hash}           Phone  => 选手信息JSON
    public const kGroups = self::REDIS_NS . ':groups';                 //参赛组            {Hash}           OpenID => Phone列表逗号分割，第一个为领队
    public const kGroupLines = self::REDIS_NS . ':group_lines';        //参赛租线路        {Hash}           OpenId => Line
    public const kWxMembers = self::REDIS_NS . ':wx_members';          //微信用户          {Hash}           OpenID => 微信用户信息JSON(头像、昵称)
    public const kPassports = self::REDIS_NS . ':passports';           //参赛证件          {Set}            {Passport_Type}:{Passport}
    public const pVoteLog = self::REDIS_NS . ':vote_log:';             //助力记录(多值前缀) {OpenId:List}    时间戳,OpenId,捐款数额
    public const pVoteLimit = self::REDIS_NS . ':vote_limit:';         //助力限制(多值前缀) {OpenId:Set}     助力者OpenId
    public const pRanking = self::REDIS_NS . ':rank:';                 //排行榜(多值前缀)   {线路:SortedSet} OpenID => {整数部分=总金额}.{小数部分=X-时间戳}
    public const kThreads = self::REDIS_NS . ':threads';               //thread列表       {Hash}           threadid => 报名者OpenID
    public const kTotalDonate = self::REDIS_NS . ':total_donate';      //总捐助金额        {String}
    public const kNicknamesNew = self::REDIS_NS . ':nicknamesnew';     //用于昵称搜索      {Hash}           nickname => threadid,headimgurl
    public const kWaterLine = self::REDIS_NS . ':water_line';          //名额预测          {Hash}           line => rank
    public const kSearchPlayer = self::REDIS_NS . ':search_player';    //用于选手查询       {Hash}          Hash(name,passport) => phone

    public const kWxUpdatePermit = self::REDIS_NS . ':wx_update_permit'; //微信资料更新授权 {Set}            threadid

    public const DOTLEN = 0;


    public function __construct($connection = null)
    {
        parent::__construct(__DIR__ . '/operators.lua', static::REDIS_NS, $connection);
    }

    /**
     * 获取threadid对应的openid
     * threadid向前端屏蔽了openid，做到数据脱敏，保护了用户隐私
     * openid对应的threadid可以通过md5(openid)得到，反向则没办法解密，所以需要这个东西
     */
    public function getThread($threadid)
    {
        $redis = $this->getRedis();
        return $redis->hGet(static::kThreads, $threadid);
    }

    /**
     * 获取募捐总额 (单位:元)
     */
    public function getTotalDonate()
    {
        $redis = $this->getRedis();
        $donate = $redis->get(static::kTotalDonate);
        return bcdiv($donate, 100, static::DOTLEN);
    }

    /**
     * 检查用户是否报过名
     */
    public function checkRegist($openid)
    {
        $redis = $this->getRedis();
        return $redis->hExists(static::kGroups, $openid);
    }

    /**
     * 检查手机号是否被报过名
     */
    public function checkUsedPhone($phone)
    {
        $redis = $this->getRedis();
        return $redis->hExists(static::kPlayers, $phone);
    }

    /**
     * 检查证件号是否被报过名
     */
    public function checkUsedPassport($passport)
    {
        $redis = $this->getRedis();
        return $redis->sIsMember(static::kPassports, $passport);
    }

    /**
     * 检查用户是否授权过
     */
    public function checkMember($openid)
    {
        $redis = $this->getRedis();
        return $redis->hExists(static::kWxMembers, $openid);
    }

    /**
     * 微信UV，即所有微信授权用户数
     */
    public function getMembersAmount()
    {
        $redis = $this->getRedis();
        return $redis->hLen(static::kWxMembers);
    }

    /**
     * 获取微信用户信息(昵称,头像等)
     */
    public function getMemberAndParse($openid)
    {
        $redis = $this->getRedis();
        $info = $redis->hGet(static::kWxMembers, $openid);

        if (!$info) {
            return $info;
        }

        return wj_json_decode($info);
    }

    /**
     * 保存微信用户信息
     */
    public function saveMember($openid, $info)
    {
        $redis = $this->getRedis();
        $redis->hSet(static::kWxMembers, $openid, wj_json_encode($info));
    }

    /**
     * 获取选手报名信息
     */
    public function getPlayerAndParse($phone)
    {
        $redis = $this->getRedis();
        $info = $redis->hGet(static::kPlayers, $phone);

        if (!$info) {
            return $info;
        }

        return wj_json_decode($info);
    }

    /**
     * 获取团队募捐了多少钱(单位:元)
     */
    public function getDonate($groupid)
    {
        $redis = $this->getRedis();

        $line = $redis->hGet(static::kGroupLines, $groupid);
        if (!$line) {
            return 0;
        }

        $kRanking = static::pRanking . $line;
        $score = $redis->zScore($kRanking, $groupid);
        return bcdiv($score, 100, static::DOTLEN);
    }


    /**
     * 获取团队对应的报名线路
     */
    public function getLine($groupid)
    {
        $redis = $this->getRedis();
        return $redis->hGet(static::kGroupLines, $groupid);
    }

    /**
     * 通过昵称搜索已报名用户
     *
     * @param     string $pattern
     * @param     int $count
     *
     * @return array
     */
    public function searchNickname($pattern, $count = 10)
    {
        $pattern = strtolower($pattern);

        $redis = $this->getRedis();
        $redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);

        $it = null;
        $ret = [];

        while (count($ret) < $count && $it !== 0) {
            $_ret = $redis->hScan(static::kNicknamesNew, $it, $pattern, 1000);
            $ret = array_merge($ret, $_ret);
        }
        $ret = array_slice($ret, 0, $count);

        $arr = [];
        foreach ($ret as $item) {
            $openid_list = explode(',', $item);
            foreach ($openid_list as $key => $value) {
                $playinfo = $redis->hGet(static::kWxMembers, $value);
                if ($playinfo && $playinfo = wj_json_decode($playinfo)) {
                    $arr[] = [
                        'nickname'  => $playinfo['nickname'],
                        'headimgurl'=> $playinfo['headimgurl'] ?: '/yx2018/images/ico_default_avatar.jpg',
                        'threadid'  => md5($value)
                    ];
                }
            }
        }
        return $arr;
    }





    /**
     * 计算报名线路水位线
     *
     * @param $line string 线路
     * @param $nums int 多少人
     * @return int  前多少名能得到名额
     */
    public function makeWaterLine($line, $nums)
    {
        $kRanking = static::pRanking . $line;

        $redis = $this->getRedis();
        $members = $redis->zRevRange($kRanking, 0, $nums) ?: [];
        $groups = $redis->hMGet(static::kGroups, $members) ?: [];

        $rank = 0;
        $players = 0;
        foreach ($members as $openid) {
            $phones = $groups[$openid];
            $players += substr_count($phones, ',') + 1;

            $rank++;        //TODO: 若执行严格不超策略，则移动到break之后
            if ($players >= $nums) {
                break;
            }
        }

        if ($players < $nums) {
            $rank = 0;
        }

        $redis->hSet(static::kWaterLine, $line, time() . ',' . $rank);

        return $rank;
    }

    /**
     * 选择线路下钱N名的选手
     *
     * @param $line
     * @param $nums
     * @return array    Phone列表
     */
    public function selectPlayers($line, $nums)
    {
        $kRanking = static::pRanking . $line;

        $redis = $this->getRedis();
        $members = $redis->zRevRange($kRanking, 0, $nums) ?: [];
        $groups = $redis->hMGet(static::kGroups, $members) ?: [];

        $rank = 0;
        $players = 0;
        $ret = [];
        foreach ($members as $openid) {
            $phones = $groups[$openid];
            $phones = explode(',', $phones);
            $phones = array_values(array_filter($phones));
            $players += count($phones);

            //手机号排序
            $master_phone = array_shift($phones);
            sort($phones);
            array_unshift($phones, $master_phone);

            $ret = array_merge($ret, $phones);

            $rank++;        //TODO: 若执行严格不超策略，则移动到break之后
            if ($players >= $nums) {
                break;
            }
        }

        return $ret;
    }

    /**
     * 获取线路的水位线
     */
    public function getWaterLine($line)
    {
        $redis = $this->getRedis();
        $ret = $redis->hGet(static::kWaterLine, $line);
        if (!$ret) {
            return false;
        }

        $ret = explode(',', $ret);
        if (!$ret[1]) {
            return false;
        }

        return [
            //记录的时间戳是第二日00:00以后的时间，前端展示要求显示前一天的日期，不带时间
            'time'      => date('n月j日', $ret[0] - 3600),
            'rank'      => $ret[1]
        ];
    }


    public function permitUpdateWxInfo($openid, $permit = true)
    {
        $threadid = md5($openid);

        $redis = $this->getRedis();
        if ($permit) {
            $redis->sAdd(static::kWxUpdatePermit, $threadid);
        } else {
            $redis->sRem(static::kWxUpdatePermit, $threadid);
        }
    }

    public function checkWxUpdatePerm($openid)
    {
        $threadid = md5($openid);

        $redis = $this->getRedis();
        return $redis->sIsMember(static::kWxUpdatePermit, $threadid);
    }

    public function hashPlayer($name, $passport)
    {
        $name = trim($name);
        $passport = trim($passport);
        return sha1("({$name},{$passport})");
    }

    public function searchPlayer($name, $passport)
    {
        $hash = $this->hashPlayer($name, $passport);

        $redis = $this->getRedis();
        $phone = $redis->hGet(static::kSearchPlayer, $hash);

        if (!$phone) {
            return false;
        }

        $info = $redis->hGet(static::kPlayers, $phone);

        if (!$info) {
            return false;
        }

        return wj_json_decode($info);
    }

}