<?php

namespace App\Zhuanti\WechatLogics;


use App\Models\Project;
use App\Zhuanti\Jizi\JiziOperator;
use App\Zhuanti\WechatLogic;
use EasyWeChat\Message\News;


class JiZi extends WechatLogic
{

    /**
     * @var Project
     */
    protected $project;

    public function __construct($projectPath)
    {
        $this->project = Project::matchByPath($projectPath);
        if (!$this->project) {
            \Log::error("Project[$projectPath] not exists.");
        }
    }

    /**
     * @param \SimpleXMLElement $msg
     * @return mixed
     */
    public function handle($msg)
    {
        if (!$this->project) {
            return false;
        }

        if (!$this->project->can('jizi')) {
            \Log::error("Project[{$this->project->id}] not support jizi.");
            return false;
        }

        $m = $this->getMatchResult($msg);
        if (!$m) {
            return false;
        }

        if (isset($this->project->configs->jizi->stime) && (strtotime($this->project->configs->jizi->stime) > time())) {
            return '本次活动助力还未开始';
        }

        if (isset($this->project->configs->jizi->etime) && (strtotime($this->project->configs->jizi->etime) <= time())) {
            return '本次活动助力已结束';
        }
        $ticket_no = $m['num'];
        $jiziOperator = JiziOperator::instance($this->project->id);
        $openId = $jiziOperator->getPlayerJizi($ticket_no, '_openid');
        if (!$jiziOperator->hasPlayerByTicket($ticket_no)) {
            return '选手不存在或审核中';
        }

        if ($jiziOperator->isZhuli($msg->FromUserName)) {
            return '您的助力机会已用完';
        }

        $player = $jiziOperator->getPlayer($openId);
        //集字
        $jiziOperator->givePlayerGift(
            $this->project,
            ['openid'=>$msg->FromUserName, 'note'=>'好友助力'],
            $ticket_no,
            false
        );
        //添加集字LogSet
        $jiziOperator->addZhuliSet($msg->FromUserName);
        $ret = new News([
            'title'        => "恭喜您! 您为{$player['info_wx_nickname']}助力成功!",
            'description'  => $this->project->name,
            'url'          =>  getenv('APP_URL') ."{$this->project->path}/{$player['md5key']}?openid={$msg->FromUserName}",
            'image'        => isset($this->project->configs->jizi->jizi_suc_img) ? uploads_url($this->project->configs->jizi->jizi_suc_img) : $player['info_wx_headimg']
        ]);

        return $ret;
    }


}