<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Models\Prizes\PrizesLog;
use App\Models\Prizes\ZhuliLog;
use App\Models\Project;
use Illuminate\Console\Command;

class Prizes extends Command
{

    protected $signature = 'wanjia:prizes:clear {projectId}';

    protected $description = '清除公版抽奖项目的选手数据';

    public function handle()
    {
        $projectId = $this->argument('projectId');
        if (!preg_match("/^[1-9]\d*$/", $projectId)) {
            $this->error('请输入正确的项目 ID');
            return false;
        }
        $proj = Project::where('id', $projectId)->first();
        if (empty($proj) || !$proj->can('draw')) {
            $this->error('该项目不是抽奖项目');
            return false;
        }
        //删除 Redis缓存
        $projKey = 'prj:'.$projectId;
        $cacheKeys = [
            $projKey.':players',//选手基本信息
            $projKey.':auto_counter',//选手编号key
            $projKey.':queue',//排队信息
            $projKey.':wins',//整个项目中奖记录（抽奖）
            $projKey.':winsmj',//整个项目中奖马甲记录（抽奖）
        ];
        $redis = \RedisDB::connection();
        $keyMax = (int)$redis->get($projKey.':auto_counter');
        if ($keyMax > 0) {
            if (isset($proj->configs->baoming->ticket_mode) && $proj->configs->baoming->ticket_mode == 'auto') {
                $ticketLength = $proj->configs->baoming->ticket_length;
            } else {
                $ticketLength = 0;
            }
            for ($i = 1; $i <= $keyMax; $i++) {
                $ticketNo = $ticketLength > 0 ? sprintf("%0{$ticketLength}d", $i) : $i;
                $cacheKeys[] = $projKey . ':ppz:' . $ticketNo;
                $cacheKeys[] = $projKey . ':pwins:' . $ticketNo;
                $cacheKeys[] = $projKey . ':pzhuli:' . $ticketNo;
            }
        }
        $redis->del($cacheKeys);
        // prize 置空
        $prizes = $redis->hgetall($projKey.':prizes');
        $prizes0 = [];
        foreach ($prizes as $k => $v) {
            $v>0 && $prizes0[$k] = 0;
        }
        !empty($prizes0) && $redis->hmset($projKey.':prizes', $prizes0);
        //删除数据库数据
        $playerCount = Player::where('project_id', $projectId)->delete();
        $prizesCount = PrizesLog::where('project_id', $projectId)->delete();
        $zhuliCount = ZhuliLog::where('project_id', $projectId)->delete();
        $this->line('共清除 '.$playerCount.' 位选手, '.$prizesCount.' 抽奖记录，'.$zhuliCount.' 助力记录');
    }


}
