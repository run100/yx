<?php

namespace App\Zhuanti\WechatLogics;


use App\Models\Project;
use App\Zhuanti\WechatLogic;
use EasyWeChat\Message\News;


class Prizes extends WechatLogic
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

        if (!$this->project->can('draw')) {
            \Log::error("Project[{$this->project->id}] not support draw.");
            return false;
        }

        if (isset($this->project->configs->draw->stime) && (strtotime($this->project->configs->draw->stime) > time())) {
            return '本次抽奖活动还未开始';
        }
        if (isset($this->project->configs->draw->etime) && (strtotime($this->project->configs->draw->etime) <= time())) {
            return '本次抽奖活动已结束';
        }
        $ret = new News([
            'title' => $this->project->name,
            'description' => $this->project->configs->share_desc,
            'url' => getenv('APP_URL') . "{$this->project->path}",
            'image' => isset($this->project->configs->draw->send_img) ? uploads_url($this->project->configs->draw->send_img) : ''
        ]);

        return $ret;
    }


}