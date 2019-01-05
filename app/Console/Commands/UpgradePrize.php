<?php

namespace App\Console\Commands;

use App\Models\Prizes\PrizesLog;
use App\Models\Project;
use Illuminate\Console\Command;

class UpgradePrize extends Command
{

    protected $signature = 'wanjia:prize:upgrade';

    protected $description = '升级公版抽奖';

    public function handle()
    {
        $ids = [];
        foreach (Project::where('capacity', 'like', '%draw%')->cursor() as $proj) {
            if (isset($proj->configs->base_form_prizes) && !empty($proj->configs->base_form_prizes)) {
                $baseFormPrizes = wj_obj2arr($proj->configs->base_form_prizes);
                foreach ($baseFormPrizes as $k=>$v) {
                    $baseFormPrizes[$k]['is_yes'] = 0;
                    switch ($v['type']) {
                        case PrizesLog::TYPE_COMMON:
                        case PrizesLog::TYPE_INTERFACE:
                            $baseFormPrizes[$k]['is_limit'] =1;
                            break;
                        case 4:
                            $baseFormPrizes[$k]['type'] = 1;
                            $baseFormPrizes[$k]['is_limit'] = 0;
                            break;
                        default:
                            $baseFormPrizes[$k]['is_limit'] = 0;
                    }
                }
                $confs = $proj->configs;
                $confs->base_form_prizes = collect($baseFormPrizes)->values();
                $proj->configs = $confs;
                $proj->save();
                $ids[] = $proj->id;
            }
        }
        $this->info('完成了'.implode(',', $ids).'专题项目的升级');
    }
}
