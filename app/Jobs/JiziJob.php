<?php

namespace App\Jobs;


use App\Models\Jizi\JiziLog;
use App\Models\Player;
use App\Models\Prizes\PrizesLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Wanjia\Common\Job\AutoDelay;

class JiziJob implements ShouldQueue
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
     * 添加集字记录
     * @param $jiziLogData
     * @param $ticketNo
     */
    public function onActSyncJiziLog($jiziLogData, $ticketNo)
    {
        $player = Player::where('project_id', $jiziLogData['project_id'])->where('ticket_no', $ticketNo)
            ->select(['id'])->first();
        if ($player) {
            $jiziLogData['player_id'] = $player->id;
            $jiziLog = new JiziLog();
            $jiziLog->fill($jiziLogData);
            $jiziLog->save();
        } else {
            \Log::error('JiziLog Save Error! Ticket_NO:' . $ticketNo . ' ' . wj_json_encode($jiziLogData));
        }
    }

    /**
     * 添加抽奖记录 即将不使用 （2018-06-07） 使用 PrizesJob中的
     * @param $prizesLogData
     * @param $ticketNo
     */
    public function onActSyncPrizesLog($prizesLogData, $ticketNo)
    {
        $player = Player::where('project_id', $prizesLogData['project_id'])->where('ticket_no', $ticketNo)
            ->select(['id'])->first();
        if ($player) {
            $prizesLogData['player_id'] = $player->id;
            $prizesLog = new PrizesLog();
            $prizesLog->fill($prizesLogData);
            $prizesLog->save();
        } else {
            \Log::error('PrizesLog Save Error! Ticket_NO:' . $ticketNo . ' ' . wj_json_encode($prizesLogData));
        }
    }

    /**
     * 是否集齐
     * @param $projectId
     * @param $ticketNo
     */
    public function onActSyncIsJiqi($projectId, $ticketNo)
    {
        $player = Player::where('project_id', $projectId)->where('ticket_no', $ticketNo)->first();
        if ($player && $player->info_is_jiqi !== 'Y') {
            $player->info_is_jiqi = 'Y';
            $player->save();
        } else {
            \Log::error('Update Player IsJiqi Error! Ticket_NO:' . $ticketNo . ' Project_Id:' . $projectId);
        }
    }

}
