<?php

namespace App\Zhuanti\Vote;


use App\Zhuanti\Common\RedisOperator;

class VoteOperator extends RedisOperator
{

    protected $luaPath = __DIR__.'/vote.lua';

    /**
     * 检测投票权限
     * @param int $total 投票者整个活动限制
     * @param int $limit 投票者每天限制
     * @param int $plyLimit 选手每日限制
     * @param string $openid 投票者OPENID
     * @param string $plyNum 选手编号
     * @param int $time 过期时间
     * @return int
     */
    public function luaCheckLimit($total, $limit, $plyLimit, $openid, $plyNum, $time)
    {
        if ($total<=0 && $limit<=0 && $plyLimit<=0) {
            return 0;
        }
        return $this->getLuaRedisIns()->checkLimit($total, $limit, $plyLimit, date('d'), $openid, $plyNum, $time);
    }

}