<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/7
 * Time: 下午7:53
 */

namespace App\Zhuanti\WechatLogics;


use App\Models\Player;
use App\Models\Project;
use App\Models\VoteLog;
use App\Zhuanti\Vote\VoteOperator;
use App\Zhuanti\WechatLogic;
use EasyWeChat\Message\News;
use Wanjia\Common\Database\Limiter;

class Vote extends WechatLogic
{

    /**
     * @var Project
     */
    protected $project;

    /**
     * Vote constructor.
     */
    public function __construct($project_id)
    {
        $this->project = is_numeric($project_id) ?
            Project::repository()->retrieveByPK($project_id) : Project::matchByPath($project_id);
        if (!$this->project) {
            \Log::error("Project[$project_id] not exists.");
        }
    }

    /**
     * 单用户1分钟最多接受多少票，超出提示服务器繁忙
     */
    public function getMaxVoteLimitInOneMinute()
    {
        if ($this->project->id == 207) {
            return 5;
        }
        return 60;
    }

    public function checkPlayerMinuteLimit($player_id)
    {
        $time = time();
        $keyspan = date('YmdHi', $time) % 2;
        $key = "zt:common_vote:player_minute_limit_{$player_id}_$keyspan";

        $redis = \RedisDB::connection();
        return $redis->get($key) > $this->getMaxVoteLimitInOneMinute();
    }

    public function incrPlayerMinuteLimit($player_id)
    {
        //两个奇偶KeySpan; 每个KeySpan多存30秒，避免临界点读取失效。
        $time = time();
        $keyspan = date('YmdHi', $time) % 2;
        $expire = strtotime(date('Y-m-d H:i:30', $time + 60));  //下一个分钟的30秒时间点失效

        $key = "zt:common_vote:player_minute_limit_{$player_id}_$keyspan";

        $redis = \RedisDB::connection();
        if (!$redis->exists($key)) {
            $redis->setex($key, $expire - $time, 1);
        } else {
            $redis->incr($key);
        }
    }


    public function handle($msg)
    {
        if (!$this->project) {
            return false;
        }

        if (!$this->project->can('vote')) {
            \Log::error("Project[{$this->project->id}] not support vote.");
            return false;
        }

        $m = $this->getMatchResult($msg);
        if (!$m) {
            return false;
        }

        if (isset($this->project->configs->vote->stime) && (strtotime($this->project->configs->vote->stime) > time())) {
            return '本次活动投票还未开始';
        }

        if (isset($this->project->configs->vote->etime) && (strtotime($this->project->configs->vote->etime) <= time())) {
            return '本次活动投票已结束';
        }

        $ticket_no = $m['num'];
        $player = Player::repository()->findOne([
            'project_id'    => $this->project->id,
            'ticket_no'     => $ticket_no,
            'checked'       => 1
        ]);

        if (!$player) {
            return '选手不存在或审核中';
        }

        if ($this->checkPlayerMinuteLimit($player->id)) {
            return '投票太过频繁，请稍候再试';
        }

        $this->incrPlayerMinuteLimit($player->id);

        $limit_person_daily = $this->project->configs->vote->limit_person_daily;
        $num_daily = $this->project->configs->vote->limit_daily?:0;
        $num = $this->project->configs->vote->limit_all?:0;

        if ($this->project->id <= 220) {
            $conditions = [
                'openid' => $msg->FromUserName,
                'merchant_id' => $this->merchant->id,
                'project_id' => $this->project->id
            ];

            if ($num > 0) {
                //整个活动投票限制
                $count = VoteLog::repository()->count($conditions);

                if ($count >= $num) {
                    return "抱歉, 您的投票机会已用完!";
                }
            }
            if ($num_daily > 0) {
                //每日投票限制
                $conditions[] = Limiter::make('created_at', Limiter::GTE, date('Y-m-d 00:00:00'));
                $count = VoteLog::repository()->count($conditions);

                if ($count >= $num_daily) {
                    return "抱歉, 您今天的投票机会已用完!";
                }
            }

            //防止用户恶意刷票，可设置一个用户每天最多可得票数,如果数值为0不做判断
            if ($limit_person_daily > 0) {
                $options = [
                    'player_id' => $player->id,
                    'project_id' => $this->project->id
                ];
                $options[] = Limiter::make('updated_at', Limiter::GT, date('Y-m-d 00:00:00'));

                $count = VoteLog::repository()->count($options);
                if ($count >= $limit_person_daily) {
                    return "今日已达投票上限， 明日再投！";
                }
            }
        } else {
            $limit_person_daily = $this->project->configs->vote->limit_person_daily;
            $num_daily = $this->project->configs->vote->limit_daily?:0;
            $num = $this->project->configs->vote->limit_all?:0;

            $expireTime = strtotime(date('Y-m-d')) - time() + 86410;
            $res = VoteOperator::instance($this->project->id)
                ->luaCheckLimit($num, $num_daily, $limit_person_daily, $msg->FromUserName, $ticket_no, $expireTime);
            if ($res<0) {
                $resMsg = [
                    -1=>'抱歉, 您的投票机会已用完!',
                    -2=>'抱歉, 您今天的投票机会已用完!',
                    -3=>'今日已达投票上限， 明日再投！',
                ];
                return $resMsg[$res];
            }
        }

        //投票成功
        $player->withOperatorOpenId($this->merchant, $msg->FromUserName);
        $player->withOperatorNote("回复投票");
        $player->withOperatorUid(0);
        $player->vote1 += 1;
        $player->save();

        $ret = new News([
            'title'        => "恭喜您! 您为{$player->info_name}投票成功!",
            'description'  => $this->project->name,
            'url'          =>  getenv('APP_URL') ."{$this->project->path}/detail?id={$player->ticket_no}",
            'image'        => uploads_url(isset($this->project->configs->vote->vote_suc_img) ? $this->project->configs->vote->vote_suc_img : $player->info_img )
        ]);

        return $ret;
    }
}
