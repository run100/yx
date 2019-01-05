<?php

namespace App\Jobs;


use App\Models\Bargain\BargainLog;
use App\Models\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Wanjia\Common\Job\AutoDelay;

class BargainJob implements ShouldQueue
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
    public function onActSyncPlayerInfo($playerInfo)
    {
        $playerInfo['checked'] = 1;
        $playerInfo['info_bargain_count'] = 0;
        $playerInfo['info_is_bargain'] = 'N';
        $player = new Player();
        $player->fill($playerInfo);
        $player->save();
    }

    /**
     * 更新选手信息
     * @param int $projectId
     * @param string $openId
     * @param array $info 要更新的内容
     */
    public function onActSyncUpdatePlayer($projectId, $openId, $info)
    {
        $player = Player::where('project_id', $projectId)->where('uniqid', $openId)->first();
        if ($player) {
            foreach ($info as $k => $v) {
                $player->{$k} = $v;
            }
            $player->save();
        } else {
            \Log::error('Player Update Info Error! OpenId:' . $openId . ' data:' . wj_json_encode($info));
        }
    }

    /**
     * 添加砍价记录，更新选手的价格
     * @param $log
     * @param $openId
     */
    public function onActSyncLog($log, $openId, $currentPrice)
    {
        $player = Player::where('project_id', $log['project_id'])->where('uniqid', $openId)
            ->first();
        if ($player) {
            $log['player_id'] = $player->id;
            $log['project_id'] = $player->project_id;
            $log['merchant_id'] = $player->merchant_id;
            $bargainLog = new BargainLog();
            $bargainLog->fill($log);
            $bargainLog->save();
            $player->info_bargain_count++;
            $player->info_price = $currentPrice;
            if (bccomp($currentPrice, 0, 2) == 0) {
                $player->info_is_bargain = 'Y';
            }
            $player->save();
        } else {
            \Log::error('BargainLog Save Error! UNIQID:' . $openId . ' ' . wj_json_encode($log));
        }
    }

}
