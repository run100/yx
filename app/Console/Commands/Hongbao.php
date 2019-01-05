<?php

namespace App\Console\Commands;

use App\Models\Hongbao\HongbaoBilling;
use App\Models\Hongbao\HongbaoLog;
use App\Models\Hongbao\HongbaoZhuli;
use App\Models\Player;
use App\Models\Project;
use Illuminate\Console\Command;

class Hongbao extends Command
{

    protected $signature = 'wanjia:hongbao:clear {projectId}';

    protected $description = '清除公版红包项目的数据';

    public function handle()
    {
        $projectId = $this->argument('projectId');
        if (!preg_match("/^[1-9]\d*$/", $projectId)) {
            $this->error('请输入正确的项目 ID');
            return false;
        }
        $proj = Project::where('id', $projectId)->first();
        if (empty($proj) || !$proj->can('hongbao')) {
            $this->error('该项目不是抽奖项目');
            return false;
        }
        //删除 Redis缓存
        $projKey = 'prj:'.$projectId;
        $cacheKeys = [
            $projKey.':players',//选手基本信息
            $projKey.':auto_counter',//选手编号key
            $projKey.':queue',//排队信息
            $projKey.':wins',//中奖信息
            $projKey.':plyticks',//编号=》openid
            $projKey.':hblogs',//助力
            $projKey.':winsmj',//马甲
        ];
        $redis = \RedisDB::connection();
        $redis->del($cacheKeys);

        // 红包 置空
        $redis->set($projKey.':hbmoney', 0);
        $redis->set($projKey.':hbcount', 0);

        //删除数据库数据
        try {
            $playerCount = Player::where('project_id', $projectId)->delete();
            //$billingCount = HongbaoBilling::where('project_id', $projectId)->delete();
            $logCount = HongbaoLog::where('project_id', $projectId)->delete();
            $zhuliCount = HongbaoZhuli::where('project_id', $projectId)->delete();
            $this->line('共清除 '.$playerCount.' 位选手, '.$logCount.' Log记录，'.$zhuliCount.' 助力记录');
        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }


}
