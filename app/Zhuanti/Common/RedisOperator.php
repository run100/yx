<?php

namespace App\Zhuanti\Common;


use Wanjia\Common\LuaRedis;

class RedisOperator
{
    /**
     * @var LuaRedis
     */
    protected $lua;

    /**
     * @var int 专题项目ID
     */
    protected $projectId;

    private static $instances = [];

    /**
     * @var string lua 脚本路径
     */
    protected $luaPath;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @param $projectId
     * @return static
     */
    public static function instance($projectId)
    {
        if (!isset(self::$instances[static::class])) {
            self::$instances[static::class] = new static($projectId);
        }
        return self::$instances[static::class];
    }


    /**
     * 设置 lua 脚本 Path
     * @param string $path
     */
    public function setLuaPath($path)
    {
        $this->luaPath = $path;
    }

    /**
     * 获取 lua 脚本 Path
     */
    public function getLuaPath()
    {
        return $this->luaPath;
    }


    /**
     * 获取LuaRedis 操作实例
     * @return LuaRedis
     */
    public function getLuaRedisIns()
    {
        if ($this->lua == null) {
            $this->lua = new LuaRedis($this->luaPath, proj_redis_ns($this->projectId));
        }
        return $this->lua;
    }

    /**
     * 增加排队人数，保证概率
     * @param $key
     * @return int
     */
    protected function incrQueueCount($key)
    {
        return \RedisDB::connection()->hincrby($this->getRedisIndex('queue'), $key, 1);
    }

    /**
     * 减少排队人数
     * @param $key
     * @return int
     */
    protected function decrQueueCount($key)
    {
        return \RedisDB::connection()->hincrby($this->getRedisIndex('queue'), $key, -1);
    }

    /**
     * @param string $type queue        hash  排队（抽奖、集字） string 红包排队
     *                     jzlogs       set   集字助力记录（集字）
     *                     jizis        hash  集字库存（集字）
     *                     pjz:选手编号  hash  选手集字信息（集字）
     *                     ppz:选手编号  hash  选手抽奖信息（抽奖）
     *                     players      hash  选手基本信息（key为openid的md5计算值）
     *                     prizes       hash  奖品库存 （抽奖）
     *                     prizelog     hash  集字抽奖记录（key为openid）（集字）  废弃于20180625
     *                     wins         set   整个项目中奖记录（抽奖，红包）
     *                     winsmj       set   整个项目中奖马甲记录（抽奖）
     *                     pwins:选手编号        set   选手中奖记录（抽奖）
     *                     pzl:选手编号          set   选手助力记录（抽奖、砍价）
     *                      bglogs      hash   砍价助力记录（砍价）
     *                      bg:选手编号  hash   用户砍价信息（砍价）
     *                      pwins:选手编号      set    选手的砍价记录（砍价）
     *                     bargains            set    选手排名（砍价）
     *                     bgcount     string   总的发出去的商品数量（砍价）
     *                     ranking     set      选手砍价排名（砍价）
     *                     plyticks     hash    红包:tickno=>openid（红包）
     *                     hblogs       set    红包:助力者openid（红包）
     *                     hbcount      string    红包:消费的总数量（红包）
     *                     hbmoney      string    红包:消费的总金额（红包）
     * @return string
     */
    public function getRedisIndex($type)
    {
        return proj_redis_ns($this->projectId) . ':' . $type;
    }

    /**
     * 判断是否含有选手信息
     * @param $openId
     * @param boolean $isOpenId
     * @return int
     */
    public function hasPlayer($openId, $isOpenId = true)
    {
        $isOpenId && $openId = $this->md5OpenId($openId);
        return \RedisDB::connection()->hexists($this->getRedisIndex('players'), $openId);
    }

    /**
     * 获取选手信息
     * @param string $key
     * @param boolean $isOpenId
     * @param null|string $infoKey
     * @return null|array|string
     */
    public function getPlayer($key, $isOpenId = true, $infoKey = null)
    {
        $md5Key = $key;
        $isOpenId && $md5Key = $this->md5OpenId($key);
        $player = \RedisDB::connection()->hget($this->getRedisIndex('players'), $md5Key);
        if ($player) {
            $player = wj_json_decode($player);
            if ($infoKey !== null) {
                return isset($player[$infoKey]) ? $player[$infoKey] : null;
            }
            $player['md5key'] = $md5Key;
            return $player;
        }
        return null;
    }

    /**
     * 添加一个选手
     * @param $openId
     * @param $playerInfo
     * @return int
     */
    public function setPlayer($openId, $playerInfo)
    {
        if (isset($playerInfo['md5key'])) {
            unset($playerInfo['md5key']);
        }
        return \RedisDB::connection()->hset(
            $this->getRedisIndex('players'),
            $this->md5OpenId($openId),
            wj_json_encode($playerInfo)
        );
    }

    /**
     * 删除一个选手
     * @param $openId
     * @return int
     */
    public function removePlayer($openId)
    {
        return \RedisDB::connection()->hdel(
            $this->getRedisIndex('players'),
            $this->md5OpenId($openId)
        );
    }

    /**
     * @param $openId
     * @return string
     */
    public function md5OpenId($openId)
    {
        return md5(md5($openId) . ':jizi:' . $this->projectId);
    }


}