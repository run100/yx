<?php

namespace App\Zhuanti\Redpacket;


use App\Jobs\RedpacketJob;
use App\Models\Project;
use App\Zhuanti\WechatLogic;
use EasyWeChat\Message\News;


class RedpacketVote extends WechatLogic
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

        if (!$this->project->can('hongbao')) {
            \Log::error("Project[{$this->project->id}] not support hongbao.");
            return false;
        }

        if (isset($this->project->configs->hongbao->stime) && (strtotime($this->project->configs->hongbao->stime) > time())) {
            return '本次活动还未开始';
        }
        if (isset($this->project->configs->hongbao->etime) && (strtotime($this->project->configs->hongbao->etime) <= time())) {
            return '本次活动已结束';
        }
        $operator = RedpacketOperator::instance($this->project->id);
        if ($this->project->configs->hongbao->category == 0) {
            // 返回领红包链接
            $code = md5($msg->FromUserName . ':' . $this->project->path);
            $operator->makeLoginCode($code);
            $ret = new News([
                'title' => '确认过口令，你是对的人！可以抢红包了~',
                'description' => $this->project->name,
                'url' => getenv('APP_URL') . $this->project->path . '/login_start?code=' . $code,
                'image' => isset($this->project->configs->hongbao->reply_img) ? uploads_url($this->project->configs->hongbao->reply_img) : ''
            ]);
        } else {
            $m = $this->getMatchResult($msg);
            if (!$m || !isset($m['num']) || empty($m['num'])) {
                return false;
            }
            $openId = $operator->getPlayerOpenId($m['num']);
            if (empty($openId)) {
                return false;
            }
            if ($openId == $msg->FromUserName) {
                return '自己不能为自己助力！';
            }
            if ($operator->isZhuli($msg->FromUserName)) {
                return '你的助力机会已用完！';
            }
            $pId = $operator->md5OpenId($openId);

            $userService = $this->merchant->wechat_app->user;
            $user = $userService->get($msg->FromUserName);
            $poster = isset($user['headimgurl']) ? $user['headimgurl'] : '';
            $status = $operator->luaZhuli($msg->FromUserName, $pId, $this->project->configs->hongbao->hb_zl_count, $this->project->configs->hongbao->hb_count, $poster);
            if (!is_numeric($status)) {
                list($username, $money) = explode('|wjhb6|', $status);
                dispatch(new RedpacketJob('add_zhuli_log', [
                    'openid' => $openId, 'zhuli_name' => $user['nickname'],
                    'zhuli_openid' => $user['openid'], 'created_at' => date('Y-m-d H:i:s'),
                    'project_id' => $this->project->id
                ]));
                $ret = new News([
                    'title' => '恭喜你，成功帮好友' . $username . '拆了' . $money . '元！',
                    'description' => $this->project->name,
                    'url' => getenv('APP_URL') . $this->project->path . '/' . $pId . '?money='.$money,
                    'image' => isset($this->project->configs->hongbao->reply_img) ? uploads_url($this->project->configs->hongbao->reply_img) : ''
                ]);
            } else {
                switch ($status) {
                    case -3:
                        $ret = ' 你的帮拆机会已用完！';
                        break;
                    case -2:
                        $ret = '该选手组队次数已经使用完';
                        break;
                    case -1:
                        $ret = '你的好友已邀请完成！';
                        break;
                }
            }
        }

        return $ret;
    }


}