<?php
/**
 * Created by PhpStorm.
 * User: zhuzq
 * Date: 2018/11/23
 * Time: 08:54
 */

namespace App\Zhuanti\Redpacket;


use App\Jobs\RedpacketJob;
use App\Models\Project;
use App\Zhuanti\Common\RedisOperator;

class RedpacketOperator extends RedisOperator
{
    protected $luaPath = __DIR__.'/redpacket.lua';

    public function addPlayer($user, $proj)
    {
        $user['ticket_no']= \RedisDB::connection()->incr("prj:{$proj->id}:auto_counter");
        if ($proj->configs->baoming->ticket_length) {
            $user['ticket_no'] = sprintf("%0{$proj->configs->baoming->ticket_length}d", $user['ticket_no']);
        }
        $this->setPlayer($user['id'], $user);
        $identitys = [
            -1 => 'NOBIND',
            0 => 'COMMON',
            1 => 'CAPTAIN',
        ];
        $player = [
            'info_wx_nickname' => $user['name'],
            'project_id' => $proj->id,
            'merchant_id' => $proj->merchant_id,
            'checked' => 1,
            'info_openid' => $user['id'],
            'info_hb_count' => 0,
            'info_hb_win_count' => 0,
            'info_hb_money' => 0,
            'info_hb_identity' => $identitys[$user['identity']],
        ];
        dispatch(new RedpacketJob('add_player', $player));
        return $user['ticket_no'];
    }

    public function updatePlayer($projId,$playerInfo)
    {
        if (isset($playerInfo['md5key'])) {
            unset($playerInfo['md5key']);
        }
        \RedisDB::connection()->hset(
            $this->getRedisIndex('players'),
            $this->md5OpenId($playerInfo['id']),
            wj_json_encode($playerInfo)
        );
        //更新用户身份
        dispatch(new RedpacketJob('update_player_identity', $projId, $playerInfo['id'], $playerInfo['identity']));
    }

    public function sendCommonRedpacket(Project $proj, $playerMd5Key)
    {
        $res = $this->giveRedpacket($proj);
        $money = 0;
        if ($res !== 0) {
            //获取红包
            $totalMoney = bcmul($proj->configs->hongbao_setting->money, 100);
            $totalCount = (int)$proj->configs->hongbao_setting->total;
            $isYes = isset($proj->configs->hongbao_setting->is_yes) && $proj->configs->hongbao_setting->is_yes==1 ? 1 : 0;
            $status = $this->luaGiveRedpacket($res[0], $totalMoney, $totalCount, $isYes, $playerMd5Key, $proj->configs->hongbao->hb_count);
            //减少排队
            $res[1] && $this->decrQueue();
            $status == 1 && $money = $res[0];
        } else {
            //更新红包次数
            $status = $this->luaUpdatePlayerCount($playerMd5Key, $proj->configs->hongbao->hb_count);
        }
        /**
         * status 1 正常  (money 正常,其他情况都是0)
         *        -1 超红包金额 超红包数量
         *        -2 个人超量  特殊情况下
         */
        return [$status, $money];
    }

    public function sendTeamRedpacket(Project $proj, $playerMd5Key)
    {

        //获取红包
        $totalMoney = bcmul($proj->configs->hongbao_setting->money, 100);
        $totalCount = (int)$proj->configs->hongbao_setting->total;
        return $this->luaGiveRedpacket(0, $totalMoney, $totalCount, 0, $playerMd5Key, $proj->configs->hongbao->hb_count);
    }

    /**
     * @param $money
     * @param $totalMoney
     * @param $totalCount
     * @param $isYes
     * @param $playerMd5Key
     * @param $drawCount
     * @return int 1 正常  (money 正常,其他情况都是0)
     *             -1 超红包金额 超红包数量
     *             -2 个人超量  特殊情况下
     */
    private function luaGiveRedpacket($money, $totalMoney, $totalCount, $isYes, $playerMd5Key, $drawCount)
    {
        return $this->getLuaRedisIns()->luaGiveRedpacket($money, $totalMoney, $totalCount, $isYes, $playerMd5Key, $drawCount, time(), uniqid());
    }

    private function luaUpdatePlayerCount($playerMd5Key, $drawCount)
    {
        return $this->getLuaRedisIns()->luaUpdatePlayerCount($playerMd5Key, $drawCount);
    }

    public function luaZhuli($zlid, $playerId, $zdlimit, $drawCount, $poster)
    {
        return $this->getLuaRedisIns()->luaZhuli($zlid, $playerId, $zdlimit, $drawCount, $poster);
    }

    public function giveRedpacket(Project $proj)
    {
        $min = bcmul($proj->configs->hongbao_setting->min_money, 100);
        $min < 30 && $min = 30;
        $max = bcmul($proj->configs->hongbao_setting->max_money, 100);
        $max < $min && $max = $min;
        $isDecr = false;
        if (!isset($proj->configs->hongbao_setting->is_yes) || empty($proj->configs->hongbao_setting->is_yes)) {
            $win = $proj->configs->hongbao_setting->timeplan;
            $queueCount = $this->incrQueue();
            $K = $win->total;
            $N = $this->getHongbaoTotal();
            $Tn = time();
            if ($N>=$K) {
                $this->decrQueue();
                return 0;
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
                $T0 = strtotime($proj->configs->hongbao->stime);
                $TL1 = 0;
                $TL0 = strtotime($proj->configs->hongbao->etime) - $T0;
            }
            if ($T0<=0) {
                $this->decrQueue();
                return 0;
            }
            $R = ($Tn - $T0 + $TL1) / $TL0 * ($K + 1) / ($N + $queueCount) - 1;
            $RD = mt_rand() / mt_getrandmax();
            if ($RD >= $R) {
                $this->decrQueue();
                return 0;
            }
            $isDecr = true;
        }
        return [mt_rand($min, $max), $isDecr];

    }

    private function getHongbaoTotal()
    {
        return (int)\RedisDB::connection()->get($this->getRedisIndex('hbcount'));
    }

    public function initHongbao()
    {
        $redis = \RedisDB::connection();
        $ckey = $this->getRedisIndex('hbcount');
        $mkey = $this->getRedisIndex('hbmoney');
        if (!$redis->exists($ckey)) {
            $redis->set($ckey, 0);
        }
        if (!$redis->exists($mkey)) {
            $redis->set($mkey, 0);
        }
    }

    public function getLoginCode($code)
    {
        return (int)\RedisDB::connection()->hget($this->getRedisIndex('code'), $code);
    }

    public function makeLoginCode($code)
    {
        return \RedisDB::connection()->hset($this->getRedisIndex('code'), $code, 1);
    }

    public function closeLoginCode($code)
    {
        return \RedisDB::connection()->hset($this->getRedisIndex('code'), $code, 2);
    }

    public function getWins()
    {
        return \RedisDB::connection()->zrevrange($this->getRedisIndex('wins'), 0, 20);
    }

    public function addPlayerTickMap($tickNo, $openId)
    {
        return \RedisDB::connection()->hset($this->getRedisIndex('plyticks'), $tickNo, $openId);
    }

    public function getPlayerOpenId($tickNo)
    {
        return \RedisDB::connection()->hget($this->getRedisIndex('plyticks'), $tickNo);
    }

    public function isZhuli($openid)
    {
        return \RedisDB::connection()->sismember($this->getRedisIndex('hblogs'), $openid);
    }

    private function incrQueue()
    {
        return (int)\RedisDB::connection()->incr($this->getRedisIndex('queue'));
    }

    private function decrQueue()
    {
        return (int)\RedisDB::connection()->incr($this->getRedisIndex('queue'));
    }

}