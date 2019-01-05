<?php

namespace App\Zhuanti\Bargain;


use App\Jobs\BargainJob;
use App\Models\Project;
use App\Zhuanti\Common\RedisOperator;

class BargainOperator extends RedisOperator
{

    protected $luaPath = __DIR__ . '/bargain.lua';

    /**
     * 添加选手信息
     * @param Project $proj
     * @param $openId
     * @param $wxInfo
     * @return boolean|string
     */
    public function addPlayer($proj, $openId, $wxInfo)
    {
        $redis = \RedisDB::connection();
        if ($proj->configs->baoming->ticket_mode == 'auto') {
            $wxInfo['ticket_no'] = $redis->incr("prj:{$proj->id}:auto_counter");
            if ($proj->configs->baoming->ticket_length) {
                $wxInfo['ticket_no'] = sprintf("%0{$proj->configs->baoming->ticket_length}d", $wxInfo['ticket_no']);
            }
        }
        if ($this->setPlayer($openId, $wxInfo)) {
            //保存openid和好友助力总数
            $goodsPrice = bcmul($proj->configs->bargain->goods_price, 100);
            $info = ['_openid' => $openId, 'price' => $goodsPrice];
            //保存信息
            if (isset($wxInfo['ticket_no'])) {
                $this->setPlayerPriceInfo($wxInfo['ticket_no'], $info);
                $this->luaAddSet($wxInfo['info_wx_nickname'], $wxInfo['info_wx_headimg'], $goodsPrice);
            }
            return isset($wxInfo['ticket_no']) ? $wxInfo['ticket_no'] : true;
        }
        return false;
    }

    public function bargain($proj, $playerInfo, $zhuli)
    {
        $min = bcmul($proj->configs->bargain->min, 100);
        $max = bcmul($proj->configs->bargain->max, 100);
        $bargainPrice = mt_rand($min, $max);
        $target = bcmul($proj->configs->bargain->bargain_price, 100);
        $goodsCount = $proj->configs->bargain->goods_count;
        $res = $this->luaBargain(
            $bargainPrice,
            $max,
            $playerInfo['ticket_no'],
            $goodsCount,
            $target,
            $playerInfo['info_wx_nickname'],
            $playerInfo['info_wx_headimg']
        );
        if ($res[0] < 0) {
            $this->delZhuli($zhuli['openid']);
            return $res[0];
        } else {
            //添加选手助力 set
            $zhuliPrice = bcdiv($res[1], 100, 2);
            $this->setPlayerPriceInfo($playerInfo['ticket_no'], [$zhuli['openid'] => $zhuliPrice]);
            $this->addPlayerZhuliSet(
                $proj->configs->bargain->etime,
                $playerInfo['ticket_no'],
                $zhuli['name'],
                $zhuliPrice
            );
            //更新选手排名
            $newPrice = bcdiv($res[2], 100, 2);
            //更新选手
            $bargainLog = ['created_at' => date('Y-m-d H:i:s'), 'openid' => $playerInfo['info_openid'],
                'name' => $playerInfo['info_wx_nickname'],
                'zhuli_openid' => $zhuli['openid'], 'zhuli_name' => $zhuli['name'],
                'price' => $zhuliPrice, 'project_id' => $proj->id];
            //添加助力记录，以及更新选手信息
            dispatch(new BargainJob('sync_log', $bargainLog, $playerInfo['info_openid'], $newPrice));
            return $zhuliPrice;
        }
    }

    /**
     * 砍价操作
     * @param $bargainPrice
     * @param $max
     * @param $ticketNo
     * @param $maxCount
     * @param $target
     * @return mixed
     */
    private function luaBargain($bargainPrice, $max, $ticketNo, $maxCount, $target, $wxName, $wxPoster)
    {
        return $this->getLuaRedisIns()->luaBargain($bargainPrice, $max, $ticketNo, $maxCount, $target, $wxName, $wxPoster);
    }

    /**添加set
     * @param $wxName
     * @param $wxPoster
     * @param $price
     * @return mixed
     */
    private function luaAddSet($wxName, $wxPoster, $price)
    {
        return $this->getLuaRedisIns()->luaAddSet($wxName, $wxPoster, $price);
    }

    /**
     * 判断选手是否存在
     * @param $ticketNo
     * @return int
     */
    public function hasPlayerByTicket($ticketNo)
    {
        return \RedisDB::connection()->exists($this->getPlayerPriceKey($ticketNo));
    }


    /**
     * 添加用户至参与助力的微信用户集合
     * @param $openId
     * @return int
     */
    public function addZhuliSet($openId)
    {
        return \RedisDB::connection()->sadd($this->getRedisIndex('bglogs'), $openId);
    }


    /**
     * 添加用户至参与助力的微信用户集合
     * @param $openId
     * @return int
     */
    public function delZhuli($openId)
    {
        return \RedisDB::connection()->srem($this->getRedisIndex('bglogs'), $openId);
    }

    /**
     * 判断用户是否参与助力
     * @param $openId
     * @return int
     */
    public function isZhuli($openId)
    {
        return \RedisDB::connection()->sismember($this->getRedisIndex('bglogs'), $openId);
    }

    /**
     * 设置选手砍价信息
     * @param $ticketNo
     * @param $info
     * @return mixed
     */
    private function setPlayerPriceInfo($ticketNo, $info)
    {
        return \RedisDB::connection()->hmset($this->getPlayerPriceKey($ticketNo), $info);
    }

    /**
     * 获取选手好友砍价价格
     * @param $ticketNo
     * @param $openId
     * @return string
     */
    public function getZhuliPrice($ticketNo, $openId)
    {
        return \RedisDB::connection()->hget($this->getPlayerPriceKey($ticketNo), $openId);
    }

    /**
     * 获取选手商品价格
     * @param  string $ticketNo 选手的编号
     * @return string
     */
    public function getPlayerPrice($ticketNo)
    {
        return \RedisDB::connection()->hget($this->getPlayerPriceKey($ticketNo), 'price');
    }

    /**
     * 获取选手OPENID
     * @param  string $ticketNo 选手的编号
     * @return string
     */
    public function getPlayerOpenId($ticketNo)
    {
        return \RedisDB::connection()->hget($this->getPlayerPriceKey($ticketNo), '_openid');
    }

    private function getPlayerPriceKey($ticketNo)
    {
        return $this->getRedisIndex('bg:') . $ticketNo;
    }

    /**
     * 添加用户助力集合
     * @param $endDate
     * @param $ticketNo
     * @param $name
     * @param $price
     * @return int
     */
    public function addPlayerZhuliSet($endDate, $ticketNo, $name, $price)
    {
        $k = strtotime($endDate) - time();
        $data = ['name' => $name, 'price' => $price, 'date' => date('Y/m/d H:i')];
        $json = wj_json_encode($data);
        return \RedisDB::connection()->zadd($this->getPlayerZhuliKey($ticketNo), $k, $json);
    }

    /**
     * 获取选手的助力 key
     * @param $ticketNo
     * @return string
     */
    public function getPlayerZhuliKey($ticketNo)
    {
        return $this->getRedisIndex('pzhuli') . ':' . $ticketNo;
    }

    /**
     * 获取选手助力记录
     * @param $ticketNo
     * @param int $page
     * @return array
     */
    public function getPlayerZhulis($ticketNo, $page = 0)
    {
        return \RedisDB::connection()->zrange($this->getPlayerZhuliKey($ticketNo), $page * 10, $page * 10 + 9);
    }

    /**
     * 获取已发出去砍价商品的数量
     * @return int
     */
    public function getBargainCount()
    {
        return (int)\RedisDB::connection()->get($this->getRedisIndex('bgcount'));
    }

    /**
     * 获取排名
     */
    public function getRakings($page)
    {
        $redis = \RedisDB::connection();
        $data = ['data' => [], 'page' => $page];
        if ($page < 1) {
            $totalCount = (int)$redis->zcard($this->getRedisIndex('ranking'));
            $page = ceil($totalCount / 20);
            $data['page'] = $page;
        }
        $start = ($page - 1) * 20;
        if ($start >= 0) {
            $data['data'] = $redis->zrange($this->getRedisIndex('ranking'), $start, $start + 19);
        }
        return $data;
    }

}