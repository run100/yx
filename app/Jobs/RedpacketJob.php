<?php

namespace App\Jobs;


use App\Models\Hongbao\HongbaoBilling;
use App\Models\Hongbao\HongbaoLog;
use App\Models\Hongbao\HongbaoZhuli;
use App\Models\Player;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Wanjia\Common\Job\AutoDelay;

class RedpacketJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, AutoDelay;


    protected $act;
    protected $params;

    public function __construct($act, ... $params)
    {
        $this->act = $act;
        $this->params = $params;
    }

    public function handle()
    {
        try {
            $method = "onAct" . ucfirst(Str::camel($this->act));
            if (method_exists($this, $method)) {
                $this->$method(... $this->params);
            }
        } catch (\Exception $e) {
            \Log::error($e);
        }
    }

    /**
     * 添加选手
     * @param $playerInfo
     */
    public function onActAddPlayer($playerInfo)
    {
        $player = new Player();
        $player->fill($playerInfo);
        $player->save();
    }

    public function onActUpdatePlayerIdentity($projectId, $openId, $identity)
    {
        $identitys = [
            -1 => 'NOBIND',
            0 => 'COMMON',
            1 => 'CAPTAIN',
        ];
        $player = Player::where('project_id', $projectId)->where('uniqid', $openId)->first();
        $player->info_hb_identity = $identitys[$identity];
        $player->save();
    }

    public function onActSendHb($log, $projPath)
    {
        $log['is_win'] = $log['money'] > 0 ? 1 : 0;
        $money = $log['money'];
        $log['money'] = bcdiv($log['money'], 100, 2);
        $player = Player::where('project_id', $log['project_id'])->where('uniqid', $log['openid'])->first();
        if ($player) {
            $log['player_id'] = $player->id;
            $hbLog = new HongbaoLog();
            $hbLog->fill($log);
            $hbLog->save();
            $player->info_hb_count++;
            if ($log['is_win'] == 1) {
                $player->info_hb_win_count++;
                $player->info_hb_money = bcadd($player->info_hb_money, $log['money'],2);
            }
            $player->save();
        } else {
            \Log::error('HongbaoLog Save Error! OpenId:' . $log['openid'], $log);
            return;
        }
        if ($log['is_win'] == 1) {
            $proj = Project::matchByPath($projPath);
            $hbBilling = new HongbaoBilling();
            $hbBilling->project_id = $proj->id;
            $hbBilling->player_id = $player->id;
            $hbBilling->openid = $log['openid'];
            $hbBilling->bill_no = make_order_no();
            $hbBilling->wx_no = '';
            $hbBilling->is_error = 1;
            $hbBilling->money = $log['money'];
            if (config('app.env') == 'production') {
                $merchantPay = $proj->merchant->wechat_certed_app->merchant_pay;
                $merchantPayData = [
                    'partner_trade_no' => $hbBilling->bill_no,
                    'openid' => $log['openid'],
                    'check_name' => 'NO_CHECK',
                    'amount' => $money,
                    'desc' => $proj->name,
                    'spbill_create_ip' => '192.168.0.1',
                ];
                $res = $merchantPay->send($merchantPayData);
            } else {
                $resJson = '{"return_code":"SUCCESS","return_msg":null,"mch_appid":"wx879e8ff74bf25932","mchid":"1240694102","nonce_str":"5c0623e80fd64","result_code":"SUCCESS","partner_trade_no":"ZT40VU20181204P","payment_no":"1240694102201812041961166323","payment_time":"2018-12-04 14:51:20"}';
                $res = wj_json_decode($resJson, false);
            }
            if (isset($res->return_code) && $res->return_code == 'SUCCESS' && isset($res->result_code) && $res->result_code=='SUCCESS') {
                $hbBilling->is_error = 0;
                $hbBilling->wx_no = $res->payment_no;
            }
            $hbBilling->data = $res;
            $hbBilling->save();
        }
    }

    public function onActAddZhuliLog($log)
    {
        $player = Player::where('project_id', $log['project_id'])->where('uniqid', $log['openid'])->select(['id'])->first();
        if ($player==null) {
            \Log::error('HongbaoLog Save Error! OpenId:' . $log['openid'], $log);
            return;
        }
        $log['player_id'] = $player->id;
        $zhuli = new HongbaoZhuli();
        $zhuli->fill($log);
        $zhuli->save();
    }

}
