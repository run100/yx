<?php

namespace Tests\Unit;

use App\Features\Yx2018\Jobs\DataSyncJob;
use App\Jobs\AppReleaseJob;
use App\Jobs\AuditCallbackJob;
use App\Jobs\PaymentJob;
use App\Jobs\TestJob;
use App\Lib\SiteUtils;
use App\Model\LocalAppRelease;
use App\Model\Merchant;
use App\Models\Payment;
use App\Models\Player;
use App\Models\PlayerJizi;
use App\Models\JiziLog;
use App\Models\Prizes\PrizesLog;
use App\Models\Project;
use App\Zhuanti\WechatLogics\JiZi;
use App\Zhuanti\WechatLogics\Prizes;
use PhpParser\Node\Expr\Cast\Object_;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        echo read_qrcode('http://mmbiz.qpic.cn/mmbiz/5lcsjAnj0k8jgGmpBq3LSKs2y0ULGErHaAfa9ZIHHOic8FheFLZVibrYpWcKTX8mRMw6SmnWdJwwLSNYtH5Elxbw/0');
die();
        $job = new DataSyncJob('test');
        $job->makeHonor("0b16a0986f66784e91f0e650e1ee3da2", "不俗爱上不俗爱上不俗爱上不俗爱上不俗爱上不俗爱上", "http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLKfdvRIRmcCyrJwxyNyYAFzKAtMlPIIfUwa9wDKplmjvNLLHDvKOXZCdyOQB1NoBn6vwI1cZKhuQ/132");
        //$job->makeCover("0b16a0986f66784e91f0e650e1ee3da2", "12345678901234567", "http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLKfdvRIRmcCyrJwxyNyYAFzKAtMlPIIfUwa9wDKplmjvNLLHDvKOXZCdyOQB1NoBn6vwI1cZKhuQ/132");
    }

    public function testWxSend(){
        /*$json = '{"ToUserName":"gh_c328ca4f5784","FromUserName":"oesVGwpsd3TnaU1u5BqcQkUiNidc","CreateTime":"1524044064","MsgType":"text","Content":"jz00001","MsgId":"6545719413039879554","__wj_match_result":{"0":"jz00001","num":"00001","1":"00001"}}';
        $arr = json_decode($json, false);
        $Jizi = new JiZi(89);
        var_dump($Jizi->handle($arr));*/
        /*$log = new PlayerJiziLog();
        dd($log->getProjectStatistics());*/

        //dd(JiziLog::getJzStcByProId(89));

        $proj = Project::where('id', 89)->first();
        $player = Player::where('id', 39742)->first();
        $openId = 'xxxxxxxx';
        $prize = Prizes::prizesV1($proj->configs->base_form_prizes, $proj->id, $proj->configs->draw->stime, $proj->configs->draw->etime);
        $prizesLog = new PrizesLog(['player_id'=>$player->id, 'project_id'=>$proj->id, 'openid'=>$openId, 'wx_name'=>$player->info->wx_nickname, 'ip'=>'']);
        $resData['status'] = 0;
        for ($i=0; $i<10000; $i++) {
            if ($prize && Prizes::getPrizeLuaRedisIns($proj->id)->luaGivePrize($prize->key, $prize->total)) {
                $prizesLog->is_win = 1;
                $prizesLog->name = $prize->name;
                $prizesLog->type = $prize->type;
                $prizesLog->tip = $prize->tips;
                if ($prize->type == 2) {
                    $prizesLog->draw_info = Prizes::encode($proj->id);
                } elseif ($prize->type == 3) {
                    $prizesLog->draw_info = $prize->content;
                }
                $resData['status'] = 1;
                $resData['name'] = $prize->name;
                $resData['content'] = $prize->tips;
            }
            $prizesLog->save();
        }
    }
}
