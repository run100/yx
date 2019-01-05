<?php

namespace App\Zhuanti\Bargain;


use App\Http\Controllers\BaseController;
use App\Jobs\BargainJob;

class BargainController extends BaseController
{

    public static $player_extend_proj_id = [233, 229];

    /**
     * 开启砍价的页面
     */
    public function start()
    {
        $proj = $this->getProject();
        $operator = BargainOperator::instance($proj->id);
        if ($operator->hasPlayer(wx_openid())) {
            return redirect($proj->path);
        }
        $this->assign['syCount'] = bcsub($proj->configs->bargain->goods_count, $operator->getBargainCount());
        return $this->render();
    }

    /**
     * 首页
     */
    public function index()
    {
        $proj = $this->getProject();
        $operator = BargainOperator::instance($proj->id);
        $openId = wx_openid();
        if (!$operator->hasPlayer($openId)) {
            return redirect($proj->path . '/start');
        }
        //获取用户信息
        $playerInfo = $operator->getPlayer($openId);
        $price = $operator->getPlayerPrice($playerInfo['ticket_no']);
        $request = \Request::instance();
        $exchange_status = 0;
        $exc_code = $request->get('exchange_code', '');
        $this->assign['exchange_code'] = $exc_code;
        if (!empty($exc_code)) {
            $exc_code = explode('-', wj_decrypt($exc_code));
            if (count($exc_code) == 2) {
                if ($exc_code[0] == $proj->id && $exc_code[1] == $proj->path) {
                    $exchange_status = 1;
                }
            }
        }
        $perfect = 0;
        if (isset($proj->configs->bargain->award_info) && $proj->configs->bargain->award_info == 1 && (!isset($playerInfo['info_phone']) || empty($playerInfo['info_phone']))) {
            $perfect=1;
        }
        $this->assign['exchange_status'] = $exchange_status;
        $this->assign['currentPrice'] = bcdiv($price, 100, 2);
        $this->assign['syCount'] = bcsub($proj->configs->bargain->goods_count, $operator->getBargainCount());
        $this->assign['isBargain'] = bccomp($price, 0, 2) == 0;
        $this->assign['playerInfo'] = $playerInfo;
        $this->assign['zhulis'] = $operator->getPlayerZhulis($playerInfo['ticket_no']);
        $this->assign['proj_id'] = $proj->id;
        $this->assign['perfect'] = $perfect;
        $this->assign['player_extend_proj_id'] = self::$player_extend_proj_id;
        $this->assign['exchange_prize_name'] = $proj->configs->bargain->goods_name;
        return $this->render();
    }



    /**
     * 报名
     */
    public function reg()
    {
        $proj = $this->getProject();
        if (isset($proj->configs->baoming->starttime) && (strtotime($proj->configs->baoming->starttime) > time())) {
            $this->fail('活动报名暂未开始,活动开始时间为：' . $proj->configs->baoming->starttime);
        }
        if (isset($proj->configs->baoming->endtime) && (strtotime($proj->configs->baoming->endtime) <= time())) {
            $this->fail('活动报名已结束');
        }
        $wxInfo = wx_user()->getOriginal();
        $openId = $wxInfo['openid'];
        $operator = BargainOperator::instance($proj->id);
        if ($operator->getBargainCount() >= $proj->configs->bargain->goods_count) {
            $this->fail('奖品已砍完，感谢您的参与！');
        }
        if ($operator->hasPlayer($openId)) {
            return wj_json_message('', 0);
        }
        $result = [
            'info_wx_nickname' => $wxInfo['nickname'],
            'info_wx_headimg' => $wxInfo['headimgurl'],
            'project_id' => $proj->id,
            'merchant_id' => $proj->merchant_id,
            'info_openid' => $openId,
            'info_price' => $proj->configs->bargain->goods_price
        ];
        $this->addPlayerExtend($proj->id, $result);
        if ($ticketNo = $operator->addPlayer($proj, $openId, $result)) {
            $result['ticket_no'] = $ticketNo;
        } else {
            $this->fail('未知错误');
        }
        dispatch(new BargainJob('sync_player_info', $result));
        return wj_json_message('', 0);
    }

    /**
     *  选手页
     */
    public function player()
    {
        $playerId = $this->getRouteParam('player_id');
        $openId = \Request::instance()->get('openid', '');
        $proj = $this->getProject();
        $operator = BargainOperator::instance($proj->id);
        if (!$operator->hasPlayer($playerId, false)) {
            abort(404);
        }
        $playerInfo = $operator->getPlayer($playerId, false);
        $status = 1;
        $zhuliPrice = 0;
        if ($openId != '') {
            $status = 2;
            $zhuliPrice = $operator->getZhuliPrice($playerInfo['ticket_no'], $openId);
        }

        $price = $operator->getPlayerPrice($playerInfo['ticket_no']);
        if ($status != 2 && bccomp($price, 0, 2) == 0) {
            $status = 3;
        }
        $this->assign['currentPrice'] = bcdiv($price, 100, 2);
        $this->assign['syCount'] =
            bcsub($proj->configs->bargain->goods_count, $operator->getBargainCount());
        $this->assign['playerInfo'] = $playerInfo;
        $this->assign['price'] = $zhuliPrice;
        $this->assign['status'] = $status;
        $this->assign['zhulis'] = $operator->getPlayerZhulis($playerInfo['ticket_no']);
        return $this->render();
    }

    /**
     * 完善信息
     */
    public function commitInfo()
    {
        $openId = wx_openid();
        $proj = $this->getProject();
        $operator = BargainOperator::instance($proj->id);
        $playerInfo = $operator->getPlayer($openId);
        if (empty($playerInfo)) {
            return wj_json_message('还未报名', 1);
        }
        $price = $operator->getPlayerPrice($playerInfo['ticket_no']);
        if ($price > 0) {
            return wj_json_message('您还没砍价成功', 1);
        }
        $filterArr = ['string', 'integer', 'name', 'phone', 'idcard', 'passport', 'email', 'qq', 'address', 'age', 'city'];
        $request = \Request::instance();
        $result = [];
        foreach ($proj->configs->base_form_design as $form) {
            $field_name = 'info_' . $form->field;
            if ($form->type != 'openid' && isset($form->registration)) {
                if (in_array($form->type, $filterArr)) {
                    $req_val = $request->get($field_name);
                    if (isset($form->required) && $form->required === 'on') {
                        if (empty($req_val)) {
                            return $this->fail('系统错误');
                        }
                    }
                    if (!isset($playerInfo[$field_name]) || $playerInfo[$field_name] != $req_val) {
                        $result[$field_name] = $req_val;
                        $playerInfo[$field_name] = $req_val;
                    }
                }
            }
        }
        if (!empty($result)) {
            unset($playerInfo['md5key']);
            $operator->setPlayer($openId, $playerInfo); //更新redis
            dispatch(new BargainJob('sync_update_player', $proj->id, $openId, $result)); //更新 mysql
        }
        return wj_json_message(['msg' => '完善信息成功']);
    }

    public function zhulis()
    {
        $request = \Request::instance();
        $ticketNo = $request->get('player');
        $page = (int)$request->get('page');
        if (empty($ticketNo) || $page <= 0) {
            abort(404);
        }
        $operator = BargainOperator::instance($this->getProject()->id);
        return wj_json_message($operator->getPlayerZhulis($ticketNo, $page));
    }

    public function rakings()
    {
        $page = (int)\Request::instance()->get('page');
        $operator = BargainOperator::instance($this->getProject()->id);
        return wj_json_message($operator->getRakings($page));
    }

    public function exchange()
    {
        $request = \Request::instance();
        $code = $request->get('code', '');
        $openId = wx_openid();
        $proj = $this->getProject();
        $operator = BargainOperator::instance($proj->id);
        if (empty($code)) {
            return wj_json_message('非法操作！', 1);
        }
        $code = explode('-', wj_decrypt($code));
        if (count($code) != 2 || $code[0] != $proj->id || $code[1] != $proj->path) {
            return wj_json_message('非法操作！', 1);
        }
        if (!$operator->hasPlayer($openId)) {
            return wj_json_message('还未报名', 1);
        }
        $playerInfo = $operator->getPlayer($openId);
        if ($playerInfo['info_is_exchange'] == 'Y') {
            return wj_json_message('您已领取过', 1);
        }
        if (!isset($playerInfo['info_phone']) || !isset($playerInfo['info_name'])) {
            return wj_json_message('请先完善兑奖信息', 1);
        }

        dispatch(new BargainJob('sync_update_player', $proj->id, $openId, ['info_is_exchange' => 'Y', 'info_exchange_time' => date('Y-m-d H:i:s')]));
        $operator->setPlayer($openId, array_merge($playerInfo, [
            'info_is_exchange' => 'Y',
            'info_exchange_time' => date('Y-m-d H:i:s'),
        ]));
        return wj_json_message(['msg' => '兑奖成功', 'data' => ['url' => $proj->path . '?time=' . date('Y-m-d H:i:s')]]);
    }

    public function validate()
    {
        $request = \Request::instance();
        $code = $request->get('code');
        $time = time();
        $str_time = strtotime('2018-11-9 0:0:0');
        $end_time = strtotime('2018-11-11 23:59:59');
        $today_str = strtotime(date('Y-m-d 09:30:00'));
        $today_end = (date('md') == '1111') ? strtotime(date('Y-m-d 20:00:00')) : strtotime(date('Y-m-d 16:00:00'));
        $openId = wx_openid();
        $proj = $this->getProject();
        $operator = BargainOperator::instance($proj->id);
        if (!in_array($proj->id, self::$player_extend_proj_id)) {
            return wj_json_message('非法操作', 1);
        }
        if (empty($code)) {
            return wj_json_message('请填写兑奖码', 1);
        }
        if (!$operator->hasPlayer($openId)) {
            return wj_json_message('还未报名', 1);
        }
        if ($str_time > $time || $today_str > $time) {
            return wj_json_message('兑奖未开始', 1);
        }
        if ($end_time < $time || $today_end < $time) {
            return wj_json_message('兑奖已结束', 1);
        }
        $playerInfo = $operator->getPlayer($openId);
        if ($playerInfo['info_is_validate'] != 0) {
            return wj_json_message('您已领取过', 1);
        }
        if (!isset($playerInfo['info_phone']) || !isset($playerInfo['info_name'])) {
            return wj_json_message('请先完善兑奖信息', 1);
        }
        if ($code != 'ft68') {
            return wj_json_message('兑奖码错误，请重新填写', 1);
        }
        dispatch(new BargainJob('sync_update_player', $proj->id, $openId, ['info_is_validate' => 1, 'info_validate_time' => date('Y-m-d H:i:s')]));
        $operator->setPlayer($openId, array_merge($playerInfo, [
            'info_is_validate' => 1,
            'info_validate_time' => date('m-d H:i'),
        ]));
        return wj_json_message(['msg' => '兑奖成功', 'data' => ['url' => $proj->path . '?time=' . date('Y-m-d H:i:s')]]);
    }

    private function addPlayerExtend($proj_id = 0, &$data)
    {
        if (in_array($proj_id, self::$player_extend_proj_id)) {
            $data = array_merge($data, [
                'info_is_validate' => 0,
                'info_validate_time' => '',
            ]);
        } else {
            $data = array_merge($data, [
                'info_is_exchange' => 'N',
                'info_exchange_time' => '',
            ]);
        }
    }

}