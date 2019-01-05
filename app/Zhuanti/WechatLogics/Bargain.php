<?php

namespace App\Zhuanti\WechatLogics;


use App\Models\Project;
use App\Zhuanti\Bargain\BargainOperator;
use App\Zhuanti\WechatLogic;
use EasyWeChat\Message\News;


class Bargain extends WechatLogic
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

        if (!$this->project->can('bargain')) {
            \Log::error("Project[{$this->project->id}] not support bargain.");
            return false;
        }

        $m = $this->getMatchResult($msg);
        if (!$m) {
            return false;
        }

        if (isset($this->project->configs->bargain->stime)
            && (strtotime($this->project->configs->bargain->stime) > time())
        ) {
            return '本次活动砍价还未开始';
        }

        if (isset($this->project->configs->bargain->etime)
            && (strtotime($this->project->configs->bargain->etime) <= time())
        ) {
            return '本次活动砍价已结束';
        }
        $operator = BargainOperator::instance($this->project->id);
        if ($operator->getBargainCount() >= $this->project->configs->bargain->goods_count) {
            return '奖品已砍完，感谢您的参与！';
        }
        $ticket_no = $m['num'];

        //判断是否有助力机会
        if ($operator->isZhuli($msg->FromUserName)) {
            return '您的助力机会已用完';
        }
        //添加助力记录
        $operator->addZhuliSet($msg->FromUserName);
        //判断选手是否存在
        if (!$operator->hasPlayerByTicket($ticket_no)) {
            return '选手不存在或在审核中';
        }
        //获取助力用户的信息
        $userService = $this->merchant->wechat_app->user;
        $user = $userService->get($msg->FromUserName);
        $wxNickName = '**';
        if (isset($user['nickname'])) {
            $wxNickName = $user['nickname'];
        } else {
            \Log::error('Project[{$this->project->id}] not support getUserInfo');
        }
        $openId = $operator->getPlayerOpenId($ticket_no);
        $player = $operator->getPlayer($openId);
        //砍价
        $status = $operator->bargain($this->project, $player, ['openid' => $msg->FromUserName, 'name' => $wxNickName]);
        switch ($status) {
            case -1:
                return '奖品已砍完，感谢您的参与！';
            case -2:
                return new News([
                    'title' => "您的好友\"{$player['info_wx_nickname']}\"已砍价完成，感谢参与!",
                    'description' => $this->project->configs->share_desc,
                    'url' => getenv('APP_URL') . "{$this->project->path}/{$player['md5key']}",
                    'image' => isset($this->project->configs->bargain->send_img) ? uploads_url($this->project->configs->bargain->send_img) : $player['info_wx_headimg']
                ]);
            default:
                return new News([
                    'title' => "恭喜您! 成功为好友\"{$player['info_wx_nickname']}\"砍掉了{$status}元！",
                    'description' => $this->project->configs->share_desc,
                    'url' => getenv('APP_URL') . "{$this->project->path}/{$player['md5key']}?openid={$msg->FromUserName}",
                    'image' => isset($this->project->configs->bargain->send_img) ? uploads_url($this->project->configs->bargain->send_img) : $player['info_wx_headimg']
                ]);

        }

    }


}