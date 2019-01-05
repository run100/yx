<?php

namespace App\Zhuanti\Prizes;


use App\Models\Prizes\PrizesLog;
use App\Models\Project;
use App\Zhuanti\Common\RedisOperator;
use App\Zhuanti\Prizes\Handler\CommonPrizesHandler;
use App\Zhuanti\Prizes\Handler\InterfacePrizesHandler;
use App\Zhuanti\Prizes\Handler\PrizesHandler;
use App\Zhuanti\Prizes\Handler\TextPrizesHandler;

class PrizesOperator extends RedisOperator
{

    protected $luaPath = __DIR__.'/prize.lua';

    public function givePlayerGift(Project $project, $playerInfo)
    {
        //抽奖
        $prizesArr = $this->givePrize($project);
        if ($prizesArr === null) {
            return false;
        }
        if ($prizesArr[0] === null) {
            $key = '';
            $total = 0;
        } else {
            $key = $prizesArr[0]->key;
            $total = $prizesArr[0]->total;
        }
        $res = $this->luaGivePrize($key, $total, $prizesArr[1]->key);
        //减少排队人数
        $key !== '' && $this->decrQueueCount($key);
        //抽奖记录
        return $this->createPrizeHandle($project, $playerInfo, $prizesArr[$res[0]])->handle();
    }

    /**
     * @param Project $project
     * @param $playerInfo
     * @param $prizes
     * @return PrizesHandler
     */
    private function createPrizeHandle(Project $project, $playerInfo, $prizes)
    {
        switch ($prizes->type) {
            case PrizesLog::TYPE_COMMON:
                return new CommonPrizesHandler($project, $playerInfo, $prizes);
            case PrizesLog::TYPE_TEXT:
                return new TextPrizesHandler($project, $playerInfo, $prizes);
            case PrizesLog::TYPE_INTERFACE:
                return new InterfacePrizesHandler($project, $playerInfo, $prizes);
        }
        return null;
    }

    /**
     * 赠送奖品 详情见 prize.lua
     * @param $key
     * @param $total
     * @return int
     */
    private function luaGivePrize($key, $total, $bKey)
    {
        return $this->getLuaRedisIns()->luaGivePrize($key, $total, $bKey);
    }


    /**
     * 抽奖
     * @param Project $project
     * @return array
     */
    private function givePrize(Project $project)
    {
        $allPrizes = $project->configs->base_form_prizes;
        $startAt = $project->configs->draw->stime;
        $endAt = $project->configs->draw->etime;
        $Tn = time();
        $max = count($allPrizes);
        if ($max <= 0) {
            \Log::error('PrizesError::ProjectId('.$project->id.')::prizes::do not have prizes');
            return null;
        }

        //不限量类奖品 和 100%的奖品
        $notWinPrizes = [];
        $isYesPrizes = [];
        foreach ($allPrizes as $prize) {
            $prize->is_limit == 0 && $notWinPrizes[] = $prize;
            $prize->is_yes == 1 && $isYesPrizes[] = $prize;
        }
        $notWinCount = count($notWinPrizes);
        $defaultIndex = $notWinCount == 1 ? 0 : mt_rand(0, $notWinCount-1);
        $default = $notWinPrizes[$defaultIndex];

        //优先抽取100%奖品 不走抽奖逻辑
        foreach ($isYesPrizes as $v) {
            foreach ($v->timeplan->plans as $plan) {
                if ($plan->start <= $Tn && $plan->end > $Tn && $this->getPrizeFontTotal($v->key) < $v->total) {
                    //返回当前100%奖品库存
                    return [$v, $default];
                }
            }
        }

        //抽奖算法
        $prizeIndex = $max == 1 ? 0 : mt_rand(0, $max - 1);
        $win = $allPrizes[$prizeIndex];
        if ($win->is_limit==0) {
            return [null, $win];
        }

        //增加队列
        $queueCount = $this->incrQueueCount($win->key);
        $K = $win->total;
        $N = (int)$this->getPrizeFontTotal($win->key);
        if ($N>=$K) {
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
                    break;
                }
            }
        } else {
            $T0 = strtotime($startAt);
            $TL1 = 0;
            $TL0 = strtotime($endAt) - $T0;
        }
        if ($T0<=0) {
            $this->decrQueueCount($win->key);
            return [null, $default];
        }
        $R = ($Tn - $T0 + $TL1) / $TL0 * ($K + 1) / ($N + $queueCount) - 1;
        $RD = mt_rand() / mt_getrandmax();
        if ($RD < $R) {
            return [$win, $default];
        } else {
            $this->decrQueueCount($win->key);
            return [null, $default];
        }
    }

    /**
     * 获取可抽奖的次数
     * @param Project $proj
     * @param array|null $jizi
     * @return int
     */
    public function getPlayerDrawCount(Project $proj, $jizi = null)
    {
        $drawCount = 0;
        if ($proj->can('jizi') && $jizi!=null) {
            $jiziCount = 0;
            foreach ($proj->configs->base_font_setting as $v) {
                if ($jizi[$v->key] == 0) {
                    $jiziCount = 0;
                    break;
                } elseif ($jiziCount==0 || $jiziCount>$jizi[$v->key]) {
                    $jiziCount = $jizi[$v->key];
                }
            }
            $drawCount += $jiziCount;
        }
        return $drawCount;
    }

    /**
     * 设置奖品 Hash 缓存
     * @param array $jizi
     * @return mixed
     */
    public function setPrizeHash($jizi)
    {
        return \RedisDB::connection()->hmset($this->getRedisIndex('prizes'), $jizi);
    }

    /**
     * 删除奖品 Hash 缓存
     * @param string $prizeField
     * @return mixed
     */
    public function delPrizeHash($prizeField)
    {
        return \RedisDB::connection()->hdel($this->getRedisIndex('prizes'), $prizeField);
    }

    /**
     * 获取奖品 Hash 缓存
     * @return array
     */
    public function getPrizeHash()
    {
        return \RedisDB::connection()->hgetall($this->getRedisIndex('prizes'));
    }

    /**
     * 判断奖品 Hash 缓存 是否存在
     * @return int
     */
    public function existPrizeHash()
    {
        return \RedisDB::connection()->exists($this->getRedisIndex('prizes'));
    }

    /**
     * 获取某个奖品已经发出去的数量
     * @param string $prizeField
     * @return int
     */
    public function getPrizeFontTotal($prizeField)
    {
        return \RedisDB::connection()->hget($this->getRedisIndex('prizes'), $prizeField);
    }

    /**
     * 添加中奖信息
     * @param $endDate
     * @param array $data
     * @return int
     */
    public function addWins($endDate, $data)
    {
        $k = strtotime($endDate) - time();
        $data[uniqid()] = "";
        $json = wj_json_encode($data);
        return \RedisDB::connection()->zadd($this->getRedisIndex('wins'), $k, $json);
    }

    /**
     * 获取中奖信息
     * @param int $page -1 最后一页
     * @return array
     */
    public function getWins($page = 1)
    {
        $redis = \RedisDB::connection();
        $totalCount = (int)$redis->zcard($this->getRedisIndex('wins'));
        $data = ['data'=>[], 'page'=>$page, 'total_count'=>$totalCount];
        if ($page < 1) {
            $page = ceil($totalCount/20);
            $data['page'] = $page;
        }
        $start = ($page-1)*20;
        if ($start>=0) {
            $data['data'] = $redis->zrange($this->getRedisIndex('wins'), $start, $start+19);
        }
        return $data;
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
        if (isset($proj->configs->baoming->ticket_mode) && $proj->configs->baoming->ticket_mode == 'auto') {
            $wxInfo['ticket_no']= \RedisDB::connection()->incr("prj:{$proj->id}:auto_counter");
            if ($proj->configs->baoming->ticket_length) {
                $wxInfo['ticket_no'] = sprintf("%0{$proj->configs->baoming->ticket_length}d", $wxInfo['ticket_no']);
            }
        } else {
            $wxInfo['ticket_no']= \RedisDB::connection()->incr("prj:{$proj->id}:auto_counter");
        }
        if ($this->setPlayer($openId, $wxInfo)) {
            return $wxInfo['ticket_no'];
        }
        return false;
    }

    /**
     * 获取选手的抽奖信息
     * @param $ticketNo
     * @param null $key
     * @return array|string
     */
    public function getPlayerPrizes($ticketNo, $key = null)
    {
        return $key===null ? \RedisDB::connection()->hgetall($this->getPlayerPrizesKey($ticketNo))
            : \RedisDB::connection()->hget($this->getPlayerPrizesKey($ticketNo), $key);
    }

    /**
     * 获取选手的抽奖信息的Key
     * @param $ticketNo
     * @return string
     */
    public function getPlayerPrizesKey($ticketNo)
    {
        return $this->getRedisIndex('ppz').':'.$ticketNo;
    }

    /**
     * 获取选手的获奖 key
     * @param $ticketNo
     * @return string
     */
    public function getPlayerWinsKey($ticketNo)
    {
        return $this->getRedisIndex('pwins').':'.$ticketNo;
    }

    /**
     * 获取选手的助力 key
     * @param $ticketNo
     * @return string
     */
    public function getPlayerZhuliKey($ticketNo)
    {
        return $this->getRedisIndex('pzhuli').':'.$ticketNo;
    }

    /**
     * 添加选手中奖记录
     * @param $ticketNo
     * @param $endDate
     * @param $data
     * @return int
     */
    public function addPlayWins($ticketNo, $endDate, $data)
    {
        $k = strtotime($endDate) - time();
        $data[uniqid()] = "";
        $json = wj_json_encode($data);
        return \RedisDB::connection()->zadd($this->getPlayerWinsKey($ticketNo), $k, $json);
    }

    /**
     * 获取用户中奖记录
     * @param $ticketNo
     * @return array
     */
    public function getPlayerWins($ticketNo)
    {
        return \RedisDB::connection()->zrange($this->getPlayerWinsKey($ticketNo), 0, 49);
    }

    /**
     * 获取用户中奖次数
     * @param $ticketNo
     * @return int
     */
    public function getPlayerWinsCount($ticketNo)
    {
        return \RedisDB::connection()->zcard($this->getPlayerWinsKey($ticketNo));
    }

    /**
     * 添加选手的助力记录
     * @param $ticketNo
     * @param $endDate
     * @param $name
     * @return int
     */
    public function addPlayerZhuli($ticketNo, $endDate, $name)
    {
        $k = strtotime($endDate) - time();
        $data['name'] = $name;
        $data[uniqid()] = "";
        $json = wj_json_encode($data);
        return \RedisDB::connection()->zadd($this->getPlayerZhuliKey($ticketNo), $k, $json);
    }

    /**
     * 获取用户 助力记录
     * @param $ticketNo
     * @param int $page
     * @return array
     */
    public function getPlayerZhulis($ticketNo, $page = 0)
    {
        return \RedisDB::connection()->zrange($this->getPlayerZhuliKey($ticketNo), $page*10, $page*10+9);
    }

}