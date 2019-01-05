<?php

namespace App\Console\Commands;

use App\Models\Bargain\BargainLog;
use App\Models\Player;
use App\Models\Project;
use Illuminate\Console\Command;

class Bargain extends Command
{

    protected $signature = 'wanjia:bargain:clear {projectId}';

    protected $description = '清除公版砍价项目的选手数据';

    public function handle()
    {
        $projectId = $this->argument('projectId');
        if (!preg_match("/^[1-9]\d*$/", $projectId)) {
            $this->error('请输入正确的项目 ID');
            return false;
        }
        $proj = Project::where('id', $projectId)->first();
        if (empty($proj) || !$proj->can('bargain')) {
            $this->error('该项目不是砍价项目');
            return false;
        }
        //删除 Redis缓存
        $projKey = 'prj:'.$projectId;
        $cacheKeys = [
            $projKey.':players',//选手基本信息
            $projKey.':auto_counter',//选手编号key
            $projKey.':bgcount',//砍价成功的数量
            $projKey.':ranking',//排名
            $projKey.':bglogs',//助力记录
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
                $cacheKeys[] = $projKey . ':bg:' . $ticketNo;
                $cacheKeys[] = $projKey . ':pzhuli:' . $ticketNo;
            }
        }
        $redis->del($cacheKeys);
        //删除数据库数据
        $playerCount = Player::where('project_id', $projectId)->delete();
        $bargainCount =  BargainLog::where('project_id', $projectId)->delete();
        $this->line('共清除 '.$playerCount.' 位选手, '.$bargainCount.' 助力记录，');
    }


}
