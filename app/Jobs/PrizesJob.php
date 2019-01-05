<?php

namespace App\Jobs;


use App\Models\Player;
use App\Models\Prizes\PrizesLog;
use App\Models\Prizes\ZhuliLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Wanjia\Common\Job\AutoDelay;

class PrizesJob implements ShouldQueue
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
        $player = new Player();
        $player->fill($playerInfo);
        $player->save();
    }

    /**
     * 添加抽奖记录
     * @param array $prizesLogData
     * @param string $openId
     */
    public function onActSyncPrizesLog($prizesLogData, $openId)
    {
        $player = Player::where('project_id', $prizesLogData['project_id'])->where('uniqid', $openId)
            ->select(['id'])->first();
        if ($player) {
            $prizesLogData['player_id'] = $player->id;
            $prizesLog = new PrizesLog();
            $prizesLog->fill($prizesLogData);
            $prizesLog->save();
        } else {
            \Log::error('PrizesLog Save Error! OpenId:' . $openId . ' ' . wj_json_encode($prizesLogData));
        }
    }

    /**
     * 添加助力记录
     * @param array $logData
     */
    public function onActSyncZhuliLog($logData)
    {
        $openId = $logData['openid'];
        $player = Player::where('project_id', $logData['project_id'])->where('uniqid', $openId)
            ->select(['id'])->first();
        if ($player) {
            $logData['player_id'] = $player->id;
            $zhuliLog = new ZhuliLog();
            $zhuliLog->fill($logData);
            $zhuliLog->save();
        } else {
            \Log::error('ZhuliLog Save Error! OpenId:' . $openId . ' ' . wj_json_encode($logData));
        }
    }

    /**
     * 更新选手的抽奖次数和中奖次数
     * @param int $projectId
     * @param string $openId
     * @param int $isWin
     * @param string $firstTime
     */
    public function onActSyncUpdatePlayerDraw($projectId, $openId, $isWin, $firstTime)
    {
        $player = Player::where('project_id', $projectId)->where('uniqid', $openId)->first();
        if ($player) {
            $player->info_draw_count++;
            $isWin && $player->info_win_count++;
            $firstTime && $player->info_draw_time = $firstTime;
            $player->save();
        } else {
            \Log::error('Player Update DRAW_COUNT Error! OpenId:' . $openId . ' isWin:' . ((int)$isWin));
        }
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

}
