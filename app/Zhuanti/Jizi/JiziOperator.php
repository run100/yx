<?php
namespace App\Zhuanti\Jizi;


use App\Jobs\JiziJob;
use App\Lib\SiteUtils;
use App\Models\Project;
use App\Zhuanti\Common\RedisOperator;

class JiziOperator extends RedisOperator
{

    protected $luaPath = __DIR__.'/jizi.lua';


    /**
     * @param Project $proj
     * @param $jiziLog array {playerid, openid, note}
     * @param string $ticketNo 选手编号
     * @param boolean $isFirst 是否第一次集字
     * @return boolean
     */
    public function givePlayerGift($proj, $jiziLog, $ticketNo, $isFirst = false)
    {
        $totalCount = 0;
        $jizi = null;
        if (!$isFirst) {
            $jizi = $this->getPlayerJizi($ticketNo);
            $totalCount =$this->incrFriendCount($ticketNo);
        }
        $allGift = $proj->configs->base_font_setting;
        if (empty($allGift) || !is_array($allGift)) {
            \Log::error('JiziError::ProjectId('.$proj->id.')::givePlayerGift::do not have jizis');
            return false;
        }
        $gifts = $this->jizi(
            $allGift,
            $jizi,
            $totalCount,
            $proj->configs->jizi->jizi_give_diffcount,
            $proj->configs->jizi->stime,
            $proj->configs->jizi->etime
        );
        $jiziLog['project_id'] = $proj->id;
        $jiziLog['merchant_id'] = $proj->merchant_id;
        $jiziLog['ip'] = SiteUtils::getRemoteIp();
        if ($gifts[0] !== null) {
            $drawKey = $gifts[0]->key;
            $drawTotal = $gifts[0]->total;
        } else {
            $drawKey = '';
            $drawTotal = 0;
        }
        $res = $this->luaGiveFont($drawKey, $drawTotal, $gifts[1]->key, $ticketNo);
        $drawKey !== '' && $this->decrQueueCount($drawKey);
        $jiziLog['field'] = $gifts[$res[0]]->key;
        $jiziLog['content'] = $gifts[$res[0]]->name;
        $jiziLog['created_at'] = time();
        dispatch(new JiziJob('sync_jizi_log', $jiziLog, $ticketNo));
        //判断是否集满字
        if (!isset($jizi['_jiqi']) || $jizi['_jiqi']==0) {
            $jizi = $this->getPlayerJizi($ticketNo);
            $isJiqi = true;
            foreach ($proj->configs->base_font_setting as $v) {
                if ($jizi[$v->key] == 0) {
                    $isJiqi = false;
                    break;
                }
            }
            if ($isJiqi) {
                $this->setPlayerJizi($ticketNo, ['_jiqi' => 1]);
                dispatch(new JiziJob('sync_is_jiqi', $proj->id, $ticketNo));
            }
        }
        return true;
    }

    /**
     * 发字操作-见 jizi.lua 脚本
     * @param string $font 抽中字的field
     * @param int $total 抽中字的总数量
     * @param string $bFont 备选字的field
     * @param string $tickNo 选手编号
     * @return int
     */
    private function luaGiveFont($font, $total, $bFont, $tickNo)
    {
        return $this->getLuaRedisIns()->luaGiveFont($font, $total, $bFont, $tickNo);
    }

    /**
     * 设置集字 Hash 缓存
     * @param array $jizi
     * @return mixed
     */
    public function setJzHash($jizi)
    {
        return \RedisDB::connection()->hmset($this->getRedisIndex('jizis'), $jizi);
    }

    /**
     * 设置集字 Hash 缓存
     * @param $key
     * @return mixed
     */
    public function delJzHash($key)
    {
        return \RedisDB::connection()->hdel($this->getRedisIndex('jizis'), $key);
    }

    /**
     * 获取集字 Hash 缓存
     * @return array
     */
    public function getJzHash()
    {
        return \RedisDB::connection()->hgetall($this->getRedisIndex('jizis'));
    }

    /**
     * 判断集字 Hash 缓存 是否存在
     * @return int
     */
    public function existJzHash()
    {
        return \RedisDB::connection()->exists($this->getRedisIndex('jizis'));
    }

    /**
     * 获取某个字已经发出去的数量
     * @param $font
     * @return mixed
     */
    public function getJzFontTotal($font)
    {
        return \RedisDB::connection()->hget($this->getRedisIndex('jizis'), $font);
    }


    /**
     * 添加选手信息
     * @param Project $proj
     * @param $openId
     * @param $wxInfo
     * @return boolean|string
     */
    public function addPlayer($proj, $openId, $wxInfo)
    {
        if ($proj->configs->baoming->ticket_mode == 'auto') {
            $wxInfo['ticket_no']= \RedisDB::connection()->incr("prj:{$proj->id}:auto_counter");
            if ($proj->configs->baoming->ticket_length) {
                $wxInfo['ticket_no'] = sprintf("%0{$proj->configs->baoming->ticket_length}d", $wxInfo['ticket_no']);
            }
        } else {
            $wxInfo['ticket_no']= \RedisDB::connection()->incr("prj:{$proj->id}:auto_counter");
        }
        if ($this->setPlayer($openId, $wxInfo)) {
            //保存openid和好友助力总数
            $fonts = ['_openid'=>$openId, '_friends'=>0];
            //保存集字信息
            if (isset($wxInfo['ticket_no'])) {
                foreach ($proj->configs->base_font_setting as $v) {
                    $fonts[$v->key] = 0;
                }
                $this->setPlayerJizi($wxInfo['ticket_no'], $fonts);
            }
            return isset($wxInfo['ticket_no']) ? $wxInfo['ticket_no'] : true;
        }
        return false;
    }


    /**
     * 判断选手是否存在
     * @param $ticketNo
     * @return int
     */
    public function hasPlayerByTicket($ticketNo)
    {
        return \RedisDB::connection()->exists($this->getPlayerJiziKey($ticketNo));
    }

    /**
     * 获取选手集字信息
     * @param $ticketNo
     * @param string $font
     * @return array|string
     */
    public function getPlayerJizi($ticketNo, $font = '')
    {
        if ($font) {
            return \RedisDB::connection()->hget($this->getPlayerJiziKey($ticketNo), $font);
        }
        return \RedisDB::connection()->hgetall($this->getPlayerJiziKey($ticketNo));
    }


    /**
     * 添加用户至参与助力的微信用户集合
     * @param $openId
     * @return int
     */
    public function addZhuliSet($openId)
    {
        return \RedisDB::connection()->sadd($this->getRedisIndex('jzlogs'), $openId);
    }

    /**
     * 判断用户是否参与助力
     * @param $openId
     * @return int
     */
    public function isZhuli($openId)
    {
        return \RedisDB::connection()->sismember($this->getRedisIndex('jzlogs'), $openId);
    }

    /**
     * 集字算法
     * @param $gifts array 所有的字
     * @param array|null $gaGift 已经获得的字
     * @param int $totalCount 已集到所有的字的数量
     * @param int $diffCount 指定次数发送不同的字
     * @param string $startAt 活动开始时间
     * @param string $endAt 活动结束时间
     * @return array  [抽中的字(可能为 null,不为 null 要减少排队), 备选的字（不可能为 null） ]
     * @throws \Exception
     */
    private function jizi($gifts, $gaGift, $totalCount, $diffCount, $startAt, $endAt)
    {
        $notGifts = [];
        $defaultGifts = [];
        foreach ($gifts as $k => $v) {
            if ($v->is_limit_count == 'N') {
                $defaultGifts[] = $v;
            }
            if ($gaGift && (!isset($gaGift[$v->key]) || $gaGift[$v->key]  == 0)) {
                $notGifts[] = $v;
            }
        }
        if (empty($defaultGifts)) {
            throw new \Exception('WinUtils::choujiangV1::not set defualt gift');
        }
        //设置默认值
        $defaultIndex = mt_rand(0, count($defaultGifts)-1);
        $default = $defaultGifts[$defaultIndex];
        $notGiftCount = count($notGifts);
        if ($totalCount>0 && $diffCount>0 && $totalCount%$diffCount == 0 &&  $notGiftCount> 0) {
            //给该选手发 系统已经放出来的自身没获得到的字
            $max = $notGiftCount;
            $giftPrizes =  $notGifts;
        } else {
            $max = count($gifts);
            $giftPrizes = $gifts;
        }
        if ($max <= 0) {
            return [null, $default];
        }
        $prizeIndex = $max == 1 ? 0 : mt_rand(0, $max - 1);
        $win = $giftPrizes[$prizeIndex];
        if ($win->is_limit_count != 'N') {
            $this->incrQueueCount($win->key);
            $Tn = time();
            $K = $win->total;
            $N = (int)$this->getJzFontTotal($win->key);
            if ($N >= $K) {
                $this->decrQueueCount($win->key);
                return [null, $default];
            }
            if (isset($win->timeplan->plans) && !empty($win->timeplan->plans) && isset($win->timeplan->time_total)) {
                $T0 = 0;
                $TL1 = 0;
                $TL0 = $win->timeplan->time_total;
                foreach ($win->timeplan->plans as $v) {
                    if ($v->start <= $Tn && $v->end > $Tn) {
                        $T0 = $v->start;
                        $TL1 = $v->len;
                    }
                }
            } else {
                $T0 = strtotime($startAt);
                $TL1 = 0;
                $TL0 = strtotime($endAt) - $T0;
            }
            if ($T0 <= 0) {
                $this->decrQueueCount($win->key);
                return [null, $default];
            }
            $R = ($Tn - $T0 + $TL1) / $TL0 * ($K + 1) / ($N + 1) - 1;
            $RD = mt_rand() / mt_getrandmax();
            if ($RD < $R) {
                return [$win, $default];
            }
            $this->decrQueueCount($win->key);
            return [null, $default];
        }
        return [null, $win];
    }

    /**
     * 设置选手集字信息
     * @param $ticketNo
     * @param $fonts
     * @return mixed
     */
    private function setPlayerJizi($ticketNo, $fonts)
    {
        return \RedisDB::connection()->hmset($this->getPlayerJiziKey($ticketNo), $fonts);
    }

    /**
     * 获取选手集字信息 KEY
     * @param  string $ticketNo 选手的编号
     * @return string
     */
    private function getPlayerJiziKey($ticketNo)
    {
        return $this->getRedisIndex('pjz:').$ticketNo;
    }

    /**
     * 获得当前好友助力数
     * @param $ticketNo
     * @return int
     */
    private function incrFriendCount($ticketNo)
    {
        return \RedisDB::connection()->hincrby(static::getPlayerJiziKey($ticketNo), '_friends', 1);
    }
}