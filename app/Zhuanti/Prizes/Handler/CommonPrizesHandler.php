<?php
namespace App\Zhuanti\Prizes\Handler;



use App\Jobs\PrizesJob;
use App\Lib\SiteUtils;
use App\Zhuanti\Prizes\PrizesOperator;

class CommonPrizesHandler extends PrizesHandler
{

    public function handle()
    {
        $prizesLog = [
            'project_id'=>$this->project->id,
            'created_at'=>time(),
            'openid'=>$this->playerInfo['info_openid'],
            'wx_name'=>$this->playerInfo['info_wx_nickname'],
            'ip'=>SiteUtils::getRemoteIp(),
            'is_win' => 1,
            'name' => $this->prizes->name,
            'type' => $this->prizes->type,
            'tip' => $this->prizes->tips,
            'field' => $this->prizes->key,
            'draw_info' => wj_uuid()
        ];
        //添加中奖记录
        $winInfo = ['name'=>$this->playerInfo['info_wx_nickname'], 'prize'=>$this->prizes->name];
        PrizesOperator::instance($this->project->id)->addWins($this->project->configs->draw->etime, $winInfo);
        //添加记录
        dispatch(new PrizesJob('sync_prizes_log', $prizesLog, $this->playerInfo['info_openid']));
        $resData = ['status'=> self::STATUS_WIN, 'name'=>$this->prizes->name, 'content'=>$this->prizes->tips,
            'point'=>(int)substr($this->prizes->key, 3)];
        return $resData;
    }

}