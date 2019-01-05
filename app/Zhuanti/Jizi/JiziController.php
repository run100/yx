<?php

namespace App\Zhuanti\Jizi;


use App\Http\Controllers\BaseController;
use App\Jobs\JiziJob;
use App\Zhuanti\Common\TPrizes;
use Request;

class JiziController extends BaseController
{
    use TPrizes;

    /**
     * 报名页
     */
    public function baoming()
    {
        $proj = $this->getProject();
        $openId = wx_openid();
        if (JiziOperator::instance($proj->id)->hasPlayer($openId)) {
            return redirect($proj->path);
        }
        return $this->render();
    }

    /**
     * 首页
     */
    public function index()
    {
        $openId = wx_openid();
        if (empty($openId)) {
            abort(404);
        }
        $proj = $this->getProject();
        $jiziOperator = JiziOperator::instance($proj->id);
        if (!$jiziOperator->hasPlayer($openId)) {
            return redirect($proj->path . '/baoming');
        }
        //获取选手集到的字
        $playerInfo = $jiziOperator->getPlayer($openId);
        $jizi = $jiziOperator->getPlayerJizi($playerInfo['ticket_no']);
        $this->assign['fonts'] = $proj->configs->base_font_setting;
        $this->assign['jizi'] = $jizi;
        $this->assign['playerInfo'] = $playerInfo;
        $this->assign['isPrize'] = $this->checkDrawPermission($playerInfo, $jizi) ? 1 : 0;
        return $this->render();
    }

    /**
     * 报名
     */
    public function reg()
    {
        $request = Request::instance();
        $proj = $this->getProject();
        if (isset($proj->configs->baoming->starttime) && (strtotime($proj->configs->baoming->starttime) > time())) {
            $this->fail('活动报名暂未开始,活动开始时间为：' . $proj->configs->baoming->starttime);
        }

        if (isset($proj->configs->baoming->endtime) && (strtotime($proj->configs->baoming->endtime) <= time())) {
            $this->fail('活动报名已结束');
        }
        $wxInfo = wx_user()->getOriginal();
        $openId = $wxInfo['openid'];
        $jiziOperator = JiziOperator::instance($proj->id);
        if ($jiziOperator->hasPlayer($openId)) {
            return wj_json_message(['msg' => '您已经报过名了！', 'url' => $proj->path]);
        }
        $result = [
            'info_wx_nickname' => $wxInfo['nickname'],
            'info_wx_headimg' => $wxInfo['headimgurl'],
            'info_draw_count' => 0,
            'info_win_count' => 0,
            'info_is_jiqi' => 'N',
            'project_id' => $proj->id,
            'merchant_id' => $proj->merchant_id,
            'checked' => 1,
            'info_openid' => $wxInfo['openid'],
        ];
        $player = [
            'info_wx_nickname' => $wxInfo['nickname'],
            'info_wx_headimg' => $wxInfo['headimgurl'],
            'info_openid' => $wxInfo['openid'],
            'info_wx_sex' => $wxInfo['sex'] == 1 ? '他' : '她',
        ];
        $filterArr = ['string', 'integer', 'name', 'phone', 'idcard', 'passport', 'email', 'qq', 'address', 'age', 'city'];
        foreach ($proj->configs->base_form_design as $form) {
            $field_name = 'info_' . $form->field;
            if (in_array($form->type, $filterArr)) {
                $req_val = $request->get($field_name);
                if (isset($form->required) && $form->required === 'on') {
                    if (empty($req_val)) {
                        $this->fail('系统错误');
                    }
                }
                $result[$field_name] = $req_val;
            }
        }

        if ($ticketNo = $jiziOperator->addPlayer($proj, $openId, $player)) {
            $result['ticket_no'] = $ticketNo;
        } else {
            $this->fail('未知错误');
        }
        dispatch(new JiziJob('sync_player_info', $result));
        if ($proj->configs->jizi->is_first_give == 1) {
            $jiziOperator->givePlayerGift(
                $proj,
                ['openid' => $openId, 'note' => '系统发放'],
                $ticketNo,
                true
            );
        }
        return wj_json_message(['msg' => '报名成功', 'url' => $proj->path]);
    }

    /**
     * 选手页
     */
    public function player()
    {
        $md5Key = $this->getRouteParam('player_id');
        $openId = \Request::instance()->get('openid', '');
        if (empty($md5Key)) {
            abort(404);
        }
        $proj = $this->getProject();

        //获取选手 ID info
        $jiziOperator = JiziOperator::instance($proj->id);
        $player = $jiziOperator->getPlayer($md5Key, false);
        if ($player == null) {
            return redirect($proj->path);
        }

        //获取选手集到的字
        $jizi = $jiziOperator->getPlayerJizi($player['ticket_no']);

        //查看openid
        $isZhuli = 0;
        if (!empty($openId)) {
            $isZhuli = $jiziOperator->isZhuli($openId);
        }

        $this->assign['fonts'] = $proj->configs->base_font_setting;
        $this->assign['jizi'] = $jizi;
        $this->assign['playerInfo'] = $player;
        $this->assign['isZhuli'] = $isZhuli;
        return $this->render();
    }

    public function checkDrawPermission(... $params)
    {
        $playerInfo= $params[0];
        $jizi = isset($params[1]) ? $params[1] : null;
        $proj = $this->getProject();
        if ($jizi == null) {
            $jizi = JiziOperator::instance($proj->id)->getPlayerJizi($playerInfo['ticket_no']);
        }
        $jiziCount = 0;
        foreach ($proj->configs->base_font_setting as $v) {
            if ($jizi[$v->key] == 0) {
                return false;
            } elseif ($jiziCount == 0 || $jiziCount > $jizi[$v->key]) {
                $jiziCount = $jizi[$v->key];
            }
        }
        return $jiziCount > 0;
    }

}