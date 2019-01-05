<?php
namespace App\Zhuanti\Prizes\Handler;



use App\Jobs\PrizesJob;
use App\Lib\SiteUtils;

class TextPrizesHandler extends PrizesHandler
{

    public function handle()
    {
        $prizesLog = [
            'project_id'=>$this->project->id,
            'created_at'=>time(),
            'openid'=>$this->playerInfo['info_openid'],
            'wx_name'=>$this->playerInfo['info_wx_nickname'],
            'ip'=>SiteUtils::getRemoteIp(),
            'is_win' => 0,
            'name' => $this->prizes->name,
            'type' => $this->prizes->type,
            'tip' => $this->prizes->tips,
            'field' => $this->prizes->key,
        ];
        //添加记录
        dispatch(new PrizesJob('sync_prizes_log', $prizesLog, $this->playerInfo['info_openid']));
        $resData = ['status'=> self::STATUS_NOT_WIN, 'name'=>'', 'content'=>'',
            'point'=>(int)substr($this->prizes->key, 3)];
        return $resData;
    }

}