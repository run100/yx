<?php
/**
 * Created by PhpStorm.
 * User: zhuzq
 * Date: 2018/11/22
 * Time: 14:41
 */

namespace App\Zhuanti\Redpacket;


use App\Http\Controllers\BaseController;
use App\Jobs\RedpacketJob;
use App\Lib\SiteUtils;

class RedpacketController extends BaseController
{

    public function loginStart()
    {
        $proj = $this->getProject();
        if ($this->isTeamHongbao()) {
            $this->loginTeam();
        } else {
            $this->loginCommon();
        }
        \Cookie::queue('js:'.substr($proj->path, 1).':login', 1, 0, null, null, false, false);
        return redirect(\Request::instance()->get('redirectUrl', $proj->path));
    }

    public function index()
    {
        $proj = $this->getProject();
        $wins = RedpacketOperator::instance($proj->id)->getWins();
        if ($this->isTeamHongbao()) {
            return $this->render('zhuanti::public_redpacket/zudui', ['wins'=>$wins, 'path'=>'zudui']);
        } else {
            return $this->render(['wins' => $wins, 'path'=>'index']);
        }
    }

    public function player()
    {
        !$this->isTeamHongbao() && abort(404);
        $playerId = $this->getRouteParam('player_id');
        empty($playerId) && abort(404);
        $proj = $this->getProject();
        $operator = RedpacketOperator::instance($proj->id);
        $wins = $operator->getWins();
        $player = $operator->getPlayer($playerId, false);
        empty($player) && abort(404);
        $player['sy_count'] = $proj->configs->hongbao->hb_zl_count - (isset($player['zls']) ? count($player['zls']) : 0);
        $this->setWxShareData($proj->path.'/'.$playerId);
        return $this->render(['wins'=>$wins, 'player'=>$player, 'money'=>\Request::instance()->get('money', -1), 'path'=>'player']);
    }

    public function user()
    {
        $wxUser = wx_user();
        $proj = $this->getProject();
        $operator = RedpacketOperator::instance($proj->id);
        if (($user = $operator->getPlayer($wxUser['id']))) {
            $total = 0;
            $wins = [];
            if (isset($user['wins']) && !empty($user['wins'])) {
                $wins = $user['wins'];
                unset($user['wins']);
                $wins = collect($wins)->sortByDesc('t')->map(function($item) use (&$total){
                    $total += $item['m'];
                    return ['t'=>date('Y/m/d H:i', $item['t']),'m'=>bcdiv($item['m'], 100, 2)];

                })->values();
            }
            $data = [
                'name'=>$user['name'],
                'status'=>$user['identity'],
                'poster'=>$user['poster'],
                'total'=>bcdiv($total, 100, 2),
                'wins'=>$wins,
                'count'=>(int)$user['count'],
                'limit'=>(int)$proj->configs->hongbao->hb_count,
            ];
            if ($this->isTeamHongbao()) {
                $data['plyid'] = $user['md5key'];
                $data['zls'] = $user['zls'];
                $data['reset'] = isset($user['reset']) ? $user['reset'] : 0;
            }
            return wj_json_message($data);
        }
        return wj_json_message('', 10014);
    }

    public function drawRedpacket()
    {
        $proj = $this->getProject();
        if (!isset($proj->configs->hongbao_setting)) {
            return wj_json_message('配置错误', 1);
        }
        $time = time();
        if ($proj->configs->hongbao->hb_count <= 0 || $time<strtotime($proj->configs->hongbao->stime)
            || $time>=strtotime($proj->configs->hongbao->etime)) {
            abort(404);
        }
        $operator = RedpacketOperator::instance($proj->id);
        $openId = wx_openid();
        $player = $operator->getPlayer($openId);
        if (empty($player)) {
            return wj_json_message('', 10014);
        }
        if ($player['count']>=$proj->configs->hongbao->hb_count) {
            abort(404);
        }
        if ($this->isTeamHongbao()) {
            $status = $operator->sendTeamRedpacket($proj, $player['md5key']);
            $rpMoney = $status == 1 ? $player['money'] : 0;
        } else {
            list($status, $rpMoney) = $operator->sendCommonRedpacket($proj, $player['md5key']);
        }
        if ($status > -2) {
            $log = [
                'project_id' => $proj->id,
                'openid' => $player['id'],
                'wx_name' => $player['name'],
                'ip' => SiteUtils::getRemoteIp(),
                'money' => $rpMoney,
            ];
            dispatch(new RedpacketJob('send_hb', $log, $proj->path));
        }
        return $rpMoney == 0 ? wj_json_message('', 1) : wj_json_message(['money'=>bcdiv($rpMoney, 100, 2)]);
    }

    public function resetTeam()
    {
        $proj = $this->getProject();
        if (!isset($proj->configs->hongbao_setting)) {
            return wj_json_message('配置错误', 1);
        }
        $time = time();
        if ($proj->configs->hongbao->hb_count <= 0 || $time<strtotime($proj->configs->hongbao->stime)
            || $time>=strtotime($proj->configs->hongbao->etime) || !$this->isTeamHongbao()) {
            abort(404);
        }
        $operator = RedpacketOperator::instance($proj->id);
        $openId = wx_openid();
        $player = $operator->getPlayer($openId);
        if (empty($player)) {
            return wj_json_message('', 10014);
        }
        if ($player['count']>=$proj->configs->hongbao->hb_count) {
            abort(404);
        }
        list($money, $moneys) = $this->distributeRedpacket();
        $player['reset'] = 0;
        $player['zls'] = [];
        $player['money'] = $money;
        $player['moneys'] = $moneys;
        $operator->setPlayer($openId, $player);
        return wj_json_message('');
    }


    /**
     * 是否为组队红包
     * @return bool
     */
    private function isTeamHongbao()
    {
        $proj = $this->getProject();
        return isset($proj->configs->hongbao->category) && $proj->configs->hongbao->category == 1;
    }

    /**
     * 处理组队红包登录逻辑
     */
    private function loginTeam()
    {
        $proj = $this->getProject();
        $operator = RedpacketOperator::instance($proj->id);
        $wxUser = wx_user();
        if (!$operator->hasPlayer($wxUser['id'])) {
            list($money, $moneys) = $this->distributeRedpacket();
            $user = [
                'id'=>$wxUser['id'], 'name'=>$wxUser['nickname'], 'poster'=>$wxUser['avatar'], 'identity'=>1, 'count'=>0,
                'zls'=>[], 'money'=>$money, 'moneys'=>$moneys
            ];
            $tickNo = $operator->addPlayer($user, $proj);
            $operator->addPlayerTickMap($tickNo, $wxUser['id']);
        }
    }

    /**
     * 处理普通红包登录逻辑
     */
    private function loginCommon()
    {
        $code = \Request::instance()->get('code');
        $proj = $this->getProject();
        $operator = RedpacketOperator::instance($proj->id);
        $identity = -1;
        if ($code && $operator->getLoginCode($code) == 1) {
            $identity = 0;
            $operator->closeLoginCode($code);
        }

        $wxUser = wx_user();
        $user = $operator->getPlayer($wxUser['id']);
        if ($user) {
            if ($identity === 0 && $user['identity'] == -1) {
                $user['identity'] = 0;
                $operator->updatePlayer($proj->id, $user);
            }
        } else {
            $user = ['id'=>$wxUser['id'], 'name'=>$wxUser['nickname'], 'poster'=>$wxUser['avatar'], 'identity'=>$identity, 'count'=>0];
            $operator->addPlayer($user, $proj);
        }
    }

    private function distributeRedpacket()
    {
        $proj = $this->getProject();
        $min = bcmul($proj->configs->hongbao_setting->min_money, 100);
        $min < 30 && $min = 30;
        $max = bcmul($proj->configs->hongbao_setting->max_money, 100);
        $max < $min && $max = $min;
        $money = mt_rand($min, $max);
        $moneys = [];
        $remainMoney = bcdiv($money, 100, 2);
        $remainSize = $proj->configs->hongbao->hb_zl_count;
        while ($remainSize>1) {
            $max = bcmul(bcdiv($remainMoney, $remainSize, 2), 2, 2);
            $min = 0.01;
            if ($remainSize < 5) {
                $random = rand(1, 50) / 100;
            } else {
                $random = rand(1, 100) / 100;
            }
            $m = bcmul($max, $random, 4);
            $m = bccomp($m, $min, 2) > 0 ? round($m, 2) : $min;
            $remainSize--;
            $moneys[] = $m;
            $remainMoney = bcsub($remainMoney, $m, 2);
        }
        $moneys[] = (double)$remainMoney;
        return [$money, $moneys];
    }


}