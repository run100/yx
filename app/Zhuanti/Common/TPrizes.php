<?php
namespace App\Zhuanti\Common;


use App\Jobs\PrizesJob;
use App\Lib\SiteUtils;
use App\Zhuanti\Prizes\Handler\PrizesHandler;
use App\Zhuanti\Prizes\PrizesOperator;

trait TPrizes
{

    private $mPrizesInfo;

    /**
     * 检查抽奖资格
     * @param array ...$params
     * @return bool
     */
    protected function checkDrawPermission(... $params)
    {
        return true;
    }

    /**
     * 抽奖首页
     */
    public function prizeIndex()
    {
        $wxInfo = wx_user()->getOriginal();
        $proj = $this->getProject();
        $prizesOperator = PrizesOperator::instance($proj->id);
        if (!$prizesOperator->hasPlayer($wxInfo['openid'])) {
            //添加选手
            $result = [
                'info_wx_nickname'=>$wxInfo['nickname'],
                'info_wx_headimg'=>$wxInfo['headimgurl'],
                'info_draw_count' => 0,
                'info_win_count' => 0,
                'project_id' => $proj->id,
                'merchant_id' => $proj->merchant_id,
                'checked' => 1,
                'info_openid' => $wxInfo['openid'],
            ];
            $player = [
                'info_wx_nickname'=>$wxInfo['nickname'],
                'info_wx_headimg'=>$wxInfo['headimgurl'],
                'info_openid' => $wxInfo['openid'],
            ];
            //判断是否是好友助力进来的
            $friendid = \Request::instance()->get('friendid') ?? \Session::get('friendid');
            if ($friendid && $prizesOperator->hasPlayer($friendid, false)) {
                $player['friendid'] = $friendid;
            }
            $ticketNo = $prizesOperator->addPlayer($proj, $wxInfo['openid'], $player);
            $result['ticket_no'] = $ticketNo;
            dispatch(new PrizesJob('sync_player_info', $result));
            $this->assign['drawCount'] = $this->getDrawCount();
            $this->assign['player'] = $player;
            $this->assign['zjRecords'] = [];
            $this->assign['zlRecords'] = [];
        } else {
            $player = $prizesOperator->getPlayer($wxInfo['openid']);
            $this->assign['drawCount'] = $this->getDrawCount($player['ticket_no']);
            $this->assign['player'] = $player;
            $this->assign['zjRecords'] = $prizesOperator->getPlayerWins($player['ticket_no']);
            $this->assign['zlRecords'] = $proj->configs->draw->is_zhuli == 'Y' ?
                $prizesOperator->getPlayerZhulis($player['ticket_no']) : [];
        }
        $this->assign['playerKey'] = $prizesOperator->md5OpenId($wxInfo['openid']);
        return $this->render('zhuanti::public_prizes/index');
    }

    /**
     * 抽奖
     */
    public function drawLottery()
    {
        $proj = $this->getProject();
        //判断抽奖时间
        $cTime = time();
        if (strtotime($proj->configs->draw->stime) > $cTime) {
            return wj_json_message('抽奖还未开始', 1);
        }
        if (strtotime($proj->configs->draw->etime) < $cTime) {
            return wj_json_message('抽奖已结束', 1);
        }
        $openId = wx_openid();
        //判断选手是否报过名
        $prizesOperator = PrizesOperator::instance($proj->id);
        if (!$prizesOperator->hasPlayer($openId)) {
            return wj_json_message('用户信息失效，请刷新再试', 1);
        }
        $resData = ['status'=>-2, 'name'=>'', 'content'=>'', 'point'=>0, 'draw_count'=>0];
        $playerInfo = $prizesOperator->getPlayer($openId);
        if (!$this->checkDrawPermission($playerInfo)) {
            return wj_json_message('没有抽奖资格', 1);
        }
        $drawCount = $this->getDrawCount($playerInfo['ticket_no']);
        if ($drawCount>0) {
            //抽奖
            $resData = $prizesOperator->givePlayerGift($proj, $playerInfo);
            //更新 redis信息
            $resData['draw_count'] = $this->updateRedis($playerInfo['ticket_no']);
            //判断是否为第一次抽奖 添加助力记录
            if ($proj->configs->draw->is_zhuli == 'Y' && $this->mPrizesInfo['total'] == 1 && isset($playerInfo['friendid'])) {
                if ($friendInfo = $prizesOperator->getPlayer($playerInfo['friendid'], false)) {
                    //添加助力记录
                    $prizesOperator->addPlayerZhuli($friendInfo['ticket_no'], $proj->configs->draw->etime, $playerInfo['info_wx_nickname']);
                    $zhuliLog = ['project_id'=>$proj->id, 'openid'=>$friendInfo['info_openid'],
                        'zhuli_name'=>$playerInfo['info_wx_nickname'], 'zhuli_openid'=>$openId,
                        'created_at'=>date('Y-m-d H:i:s'), 'ip'=>SiteUtils::getRemoteIp()];
                    dispatch(new PrizesJob('sync_zhuli_log', $zhuliLog));
                    //给邀请人增加抽奖次数
                    $redis = \RedisDB::connection();
                    $rKey = $prizesOperator->getPlayerPrizesKey($friendInfo['ticket_no']);
                    $redis->hincrby($rKey, 'fri_count', 1);
                    $redis->hincrby($rKey, 'sy_fri_count', 1);
                }
            }
            //更新数据库选手的抽奖次数和中奖次数
            $isWin = 0;
            if ($resData['status'] == PrizesHandler::STATUS_WIN) {
                $isWin = 1;
                //添加个人中奖记录
                $playerWin = ['prize'=>$resData['name'], 'date'=>date('Y/m/d H:i')];
                $prizesOperator->addPlayWins($playerInfo['ticket_no'], $proj->configs->draw->etime, $playerWin);
            }
            $firstTime = $this->mPrizesInfo['total'] == 1 ? date('Y-m-d H:i:s') : 0;
            dispatch(new PrizesJob('sync_update_player_draw', $proj->id, $openId, $isWin, $firstTime));
        } else {
            return wj_json_message('您的抽奖机会已用完', 1);
        }
        return wj_json_message($resData);
    }

    /**
     * 获取中奖信息
     */
    public function prizeWins()
    {
        $request = \Request::instance();
        $page = (int)$request->get('page');
        $proj = $request->attributes->get('project');
        return wj_json_message(PrizesOperator::instance($proj->id)->getWins($page));
    }

    /**
     * 完善信息
     */
    public function prizeWsInfo()
    {
        $request = \Request::instance();
        $proj = $request->attributes->get('project');
        if ($proj->configs->draw->player_info_type == 'N') {
            abort(404);
        }
        //获取选手信息
        $prizesOperator = PrizesOperator::instance($proj->id);
        $player = $prizesOperator->getPlayer(wx_openid());
        $filterArr = ['string','integer','name','phone','idcard','passport','email','qq','address','age','city'];
        $result = [];
        foreach ($proj->configs->base_form_design as $form) {
            $field_name = 'info_' . $form->field;
            if (in_array($form->type, $filterArr) && isset($form->registration)) {
                $req_val = $request->get($field_name);
                if (isset($form->required) && $form->required === 'on') {
                    if (empty($req_val)) {
                        return $this->fail('系统错误');
                    }
                }
                if (!isset($player[$field_name]) || $player[$field_name] != $req_val) {
                    $result[$field_name] = $req_val;
                    $player[$field_name] = $req_val;
                }
            }
        }
        //更新信息
        if (!empty($result)) {
            unset($player['md5key']);
            $prizesOperator->setPlayer($player['info_openid'], $player); //更新redis
            dispatch(new PrizesJob('sync_update_player', $proj->id, wx_openid(), $result)); //更新 mysql
        }
        return wj_json_message(['msg'=>'完善信息成功']);
    }

    /**
     * 抽奖完更新个人数据
     * @param $ticketNo
     * @return int
     */
    protected function updateRedis($ticketNo)
    {
        $proj = $this->getProject();
        $rKey = PrizesOperator::instance($proj->id)->getPlayerPrizesKey($ticketNo);
        $redis = \RedisDB::connection();
        $this->mPrizesInfo['total'] = $redis->hincrby($rKey, 'total', 1);
        $projLimitDayCount = (int)$proj->configs->draw->limit_day_count;
        $projLimitCount = (int)$proj->configs->draw->limit_count;
        if ($projLimitDayCount > 0) {
            if (strtotime(date('Y-m-d')) == $this->mPrizesInfo['prev_time']) {
                $this->mPrizesInfo['prev_count'] = $redis->hincrby($rKey, 'prev_count', 1);
            } else {
                $todayTime = strtotime(date('Y-m-d'));
                $redis->hmset($rKey, ['prev_count'=>1, 'prev_time'=>$todayTime]);
                $this->mPrizesInfo['prev_count'] = 1;
                $this->mPrizesInfo['prev_time'] = $todayTime;
            }
            $isDecr = $this->mPrizesInfo['prev_count'] > $projLimitDayCount;
        } else {
            $isDecr = $this->mPrizesInfo['total'] > $projLimitCount;
        }
        if ($isDecr) {
            $this->mPrizesInfo['sy_fri_count'] = $redis->hincrby($rKey, 'sy_fri_count', -1);
        }
        return $this->getDrawCount($ticketNo);
    }

    /**
     * 获取剩余抽奖次数
     * @param $ticketNo
     * @return int
     */
    protected function getDrawCount($ticketNo = null)
    {
        $proj = $this->getProject();
        $defaultInfo = ['prev_time'=>0, 'prev_count'=>0, 'fri_count'=>0, 'sy_fri_count'=>0, 'total'=>0];
        //获取选手的抽奖信息
        if ($this->mPrizesInfo == null && $ticketNo!=null) {
            $this->mPrizesInfo = PrizesOperator::instance($proj->id)->getPlayerPrizes($ticketNo);
        }
        //判断是否为第一次抽奖
        if ($this->mPrizesInfo == null) {
            $this->mPrizesInfo = $defaultInfo;
        } else {
            foreach ($defaultInfo as $k => $v) {
                !isset($this->mPrizesInfo[$k]) && $this->mPrizesInfo[$k] = 0;
            }
        }
        $projLimitCount = (int)$proj->configs->draw->limit_count;
        $projLimitDayCount = (int)$proj->configs->draw->limit_day_count;
        if ($projLimitDayCount>0) {
            //判断时间
            $drawCount = strtotime(date('Y-m-d')) == $this->mPrizesInfo['prev_time'] ?
                $projLimitDayCount - $this->mPrizesInfo['prev_count'] + $this->mPrizesInfo['sy_fri_count']
                : $projLimitDayCount + $this->mPrizesInfo['sy_fri_count'];
        } else {
            $drawCount = $projLimitCount - $this->mPrizesInfo['total'] + $this->mPrizesInfo['sy_fri_count'];
        }
        return $drawCount>0 ? $drawCount : 0;
    }

}