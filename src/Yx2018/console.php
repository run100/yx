<?php

//2018-07-01后定义的命令行自动消失
if (time() > strtotime('2018-07-01')) {
    return;
}

/**
 * 以下代码加入 App\Console\Kernel::schedule 中

$schedule->command('wanjia:yx2018:water_line')
    ->dailyAt('00:00')
    ->description("毅行:生成排行榜水位线");
$schedule->command('wanjia:yx2018:download_bill')
    ->dailyAt('09:30')
    ->description("毅行:下载微信对账单(每日9点可以下载前一天的对账单，微信建议10点之后下载)");
$schedule->command('wanjia:yx2018:bill_check')
    ->dailyAt('09:40')
    ->description("毅行:生成对账报表");

 */


//一键清空毅行项目数据(Redis和数据库)，慎用！！！
Artisan::command(
    'wanjia:yx2018:reset {--F|force}',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        if (!$this->option('force')) {
            if (!$this->confirm("将要清空毅行数据，是否确认?")) {
                return;
            }
        }

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $lua->destroy();

        $project = \App\Models\Project::matchByPath('/yx2018');
        \App\Models\Player::where('project_id', '=', $project->id)->delete();
        \App\Models\Payment::where('project_id', '=', $project->id)->delete();
        \App\Models\VoteLog::where('project_id', '=', $project->id)->delete();
    }
)->describe('毅行数据初始化');



//每日凌晨计算水位线的脚本
Artisan::command(
    'wanjia:yx2018:water_line',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */


        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();

        //TODO:确定人数
        $l1 = $lua->makeWaterLine('L1', 5000);    //MINI线 XX人
        $l2 = $lua->makeWaterLine('L2', 5000);    //半程线  XX人
        $l3 = $lua->makeWaterLine('L3', 5000);    //全程线  XX人

        $this->info("L1: $l1");
        $this->info("L2: $l2");
        $this->info("L3: $l3");
    }
)->describe('名额水位线计算');




//每日早晨9:00之后下载对账单的脚本
//对账单数据量比较大，按理说应该存储在文件系统里，而不是数据库里;
//但是uploads目录是公开目录，不适合存储隐私数据，即使做路径加密也不能完全保证信息安全;
//而其他目录在线上环境中都是容器中的目录，各自独立也不适用于存储。
//合理的做法是申请另一块NAS盘挂载到一个非公开目录，比如 storage/secured; 云商小程序发布平台lander就是这么做的；因为目前只有毅行一个专题用，不划算。
//所以选择了存数据库的方案
Artisan::command(
    'wanjia:yx2018:download_bill  {--F|force} {date?}',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */


        $proj = \App\Models\Project::matchByPath('/yx2018');

        //3.22开始4.20结束募捐; 生成这个时间段的日期列表
        $vote_start = "2018-03-22";
        $vote_end = "2018-04-20";

        $dates = [];
        $i = 0;
        $t = strtotime($vote_start);
        while ($t + $i * 3600 * 24 <= strtotime($vote_end)) {
            $dates[$i] = date('Ymd', $t + $i * 3600 * 24);
            $i++;
        }

        //强制重新下载
        $force = $this->option('force');
        //如果没有传日期参数，默认下载昨日的对账单
        $date = $this->argument('date') ?: date('Ymd', strtotime('yesterday'));


        if ($date) {
            $dates = [$date];
        }

        $billings = \App\Models\Billing::repository()->findByProjectId(
            $proj->id,
            [],
            [
                'orderby' => 'dateline',
                'columns' => ['dateline', 'id']     //只取dateline和id字段
            ])
            ->keyBy('dateline');

        $today = date('Ymd');
        foreach ($dates as $date) {
            if ($date >= $today) {
                break;
            }

            $billing = @$billings[$date];
            if (!$billing || $force) {
                $data = $proj->merchant->wechat_app->payment->downloadBill($date);
                $data = $data->getContents();

                //正常情况下$data会是一段CSV文本; 特定情况下会提示错误信息(账单未生成、当日没有对账单等)，错误信息是一段xml文本; 这里检查是xml还是csv
                if (preg_match('@^<xml><return_code>@', $data)) {
                    continue;
                }

                //微信给的CSV数据里，所有的纯数字字符型数据都带了一个前导`字符，熟悉Excel操作的都知道，这是为了避免让Excel当成数字来处理，否则就会显示成科学计数
                //这里要取出前导`便于后面做分析
                $data = preg_replace('@(^|,)`@m', '\1', $data);

                //微信给的CSV是UTF8编码带BOM头; BOM是WINDOWS下的概念，具体就是在普通文件前插入几个字节告诉Windows下的编辑器该用什么编码方式打开; Linux下读取这样的文件就比较恶心了
                //相关资料参考: https://baike.baidu.com/item/BOM/2790364
                //这里就是移除头部的UTF-BOM
                $data = str_replace_first("\xEF\xBB\xBF", '', $data);

                //文本拆成行
                $csv = explode("\r\n", $data);              //Windows下的换行符是\r\n所以不能用 PHP_EOL

                //解析每一行csv文本，变成数组; 处理完后的$csv是个二维数组
                $csv = array_map('str_getcsv', $csv);

                //最后一行是空行; 倒数第二、第三行是统计数据; 这里摘出统计数据和账单数据
                $data = array_slice($csv, 0, -3);
                $meta = array_slice($csv, -3, -1);

                //第一行是表头，之后是数据
                array_walk($data, function (&$a) use ($csv) {
                    $a = array_combine($csv[0], $a);
                });
                array_shift($data);

                //同上
                array_walk($meta, function (&$a) use ($meta) {
                    $a = array_combine($meta[0], $a);
                });
                array_shift($meta);

                //按交易排序; 微信给的对账单时间顺序是乱的
                $data = collect($data)->sortBy('交易时间');

                $meta = $meta[0];
                $meta['billings'] = $data;

                //写入到数据库
                if (!$billing) {
                    $billing = new \App\Models\Billing();
                }
                $billing->dateline = $date;
                $billing->data = $meta['billings'];
                $billing->billings = $meta['总交易单数'];
                $billing->payed = $meta['总交易额'];
                $billing->refund = $meta['总退款金额'];
                $billing->hongbao_refund = $meta['总企业红包退款金额'];
                $billing->tax = $meta['手续费总金额'];
                $billing->project_id = $proj->id;
                if ($date !== $today) {
                    $billing->save();
                }
                $billings[$date] = $billing;
            }
        }
    }
)->describe('下载微信对账单');



//每日造成9:00之后，账单下载完毕后，开始执行对账逻辑
Artisan::command(
    'wanjia:yx2018:bill_check {date?}',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        set_time_limit(0);
        ini_set('memory_limit', '102400M');

        $date = $this->argument('date');
        if (!$date) {
            $date = date('Ymd', strtotime('yesterday'));
        }

        $time = strtotime($date);

        $proj = \App\Models\Project::matchByPath('/yx2018');
        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $billing = \App\Models\Billing::repository()->findOneByProjectId($proj->id, ['dateline' => $date]);
        if (!$billing) {
            $this->error("Billing not found.");
            return;
        }

        $trade_nos = collect($billing->data)->pluck('商户订单号')->all();

        //取交易数据;
        //问题1: 为什么不用 trade_no in (xxx) 的方式查，而用时间范围?
        //因为流水单的数据量可能非常大，比如毅行首日数据就是6W笔交易记录，用IN查询方式，最终组成的SQL语句会非常长; 阿里的RDS有最长SQL限制
        //问题2: 为什么不用\DB::select而用PDO?
        //同样是数据量问题，\DB::select会一次性取出所有符合条件的数据，存于内存中，很可能内存不够用就崩了; PDO->prepare是游标方式。
        $stm = \DB::connection()->getPdo()->prepare("
                      select trade_no, data, (payment IS NOT NULL) payed, updated_at
                      from zt_payments 
                      where project_id = :proj_id
                        and updated_at >= :from
                        and updated_at <= :to
                ");
        $stm->execute([
            'proj_id'   => $proj->id,
            'from'      => date('Y-m-d H:i:s', $time - 3600 * 6),   //微信的账单在日期临界点可能有交叠，前后多取6小时;兼顾性能和完整性
            'to'        => date('Y-m-d H:i:s', $time + 3600 * 30)
        ]);

        $trades = [];

        //其实这里没有真正用到游标模式的精髓，每一行的$row或$data应该用完就扔，不应该存到数组里
        while ($row = $stm->fetch()) {
            if (!in_array($row['trade_no'], $trade_nos)) {
                continue;
            }
            $data = json_decode($row['data']);
            $data->payed = !!$row['payed'];
            $data->updated_at = $row['updated_at'];
            $trades[$row['trade_no']] = $data;
        }

        $ret = [];
        $ret['微信交易数量']  = count($trade_nos);
        $ret['系统已支付订单数']  = count($trades);

        $checkes = [];
        $billing_data = $billing->data;

        //业务系统中找关联数据
        foreach ($billing_data as $item) {
            $trade_no = $item['商户订单号'];

            $wxinfo0 = $lua->getMemberAndParse($item['用户标识']);

            $checkes[$trade_no]['交易单号'] = $trade_no;
            $checkes[$trade_no]['流水号'] = $item['微信订单号'];
            $checkes[$trade_no]['捐助者OpenID'] = $item['用户标识'];
            $checkes[$trade_no]['捐助者昵称'] = @$wxinfo0['nickname'] ?: '';
            $checkes[$trade_no]['交易金额'] = $item['总金额'];
            $checkes[$trade_no]['货币种类'] = $item['货币种类'];
            $checkes[$trade_no]['备注'] = $item['商品名称'];
            $checkes[$trade_no]['交易状态'] = $item['交易状态'];
            $checkes[$trade_no]['交易时间'] = $item['交易时间'];

            //针对业务流水，看业务系统中有没有对应订单
            $order = @$trades[$trade_no];
            if (!$order) {
                $checkes[$trade_no]['订单状态'] = "缺失";
                $checkes[$trade_no]['日志状态'] = "缺失";
                continue;
            }

            //看业务系统中对应订单是不是支付状态
            if ($order->payed) {
                $checkes[$trade_no]['订单状态'] = "正常";
            } else {
                $checkes[$trade_no]['订单状态'] = "异常";
            }

            $groupid = $order->groupid;
            $wxinfo1 = $lua->getMemberAndParse($groupid);
            $checkes[$trade_no]['助力对象OpenID'] = $groupid;
            $checkes[$trade_no]['助力对象昵称'] = @$wxinfo1['nickname'] ?: '';

            //看业务流水有没有生成有效的助力日志(检查支付结果，一个业务流水对应有且仅有一条助力日志)
            //助力日志中应该记录业务单号或订单号，这里没有记录，所以用蠢方法 grep出数量做检查。其实这种检查方法并不准确，但勉强能用
            $logKey = \App\Features\Yx2018\RedisOperator::pVoteLog . $groupid;
            $logs = $redis->lRange($logKey, 0, -1);
            $validLogs = preg_grep('@,'.preg_quote($item['用户标识']).',@', $logs);
            $validLogs = count($validLogs);
            if ($validLogs == 0) {
                $checkes[$trade_no]['日志状态'] = '缺失';
            } elseif ($validLogs == 1) {
                $checkes[$trade_no]['日志状态'] = '正常';
            } elseif ($validLogs == 2 && $groupid === $item['用户标识']) {
                $checkes[$trade_no]['日志状态'] = '正常';
            } else {
                $checkes[$trade_no]['日志状态'] = '重复';
            }
            $checkes[$trade_no]['上包时间'] = $order->updated_at;
        }

        //因为对账结果保存到uploads目录供运营下载，所以此处生成一个加密的地址，避免被枚举
        $filename = md5(\App\Features\Yx2018\Controllers\Controller::VALIDATION_TOKEN . 'billing' . $date);
        $path = uploads_path("yx2018/data/$filename.csv");
        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fp = fopen($path, 'w');

        //导出的UTF8编码 CSV最终是给人看的，不考虑程序分析；所以加BOM头
        //当然也可以导出GBK编码的CSV就不用加BOM头，但是微信昵称里可能有特殊字符在GBK编码里找不到，所以老实用UTF8编码吧
        fwrite($fp, "\xEF\xBB\xBF");


        $writeline = function ($format, ... $args) use ($fp) {
            $line = sprintf($format . "\r\n", ... $args);
            fwrite($fp, $line);
        };

        $writeline("交易单号,流水号,交易金额,货币种类,备注,交易状态,订单状态,日志状态,上包时间,交易时间,捐助者OpenID,助力对象OpenID,捐助者昵称,助力对象昵称");
        foreach ($checkes as $trade_no => $item) {
            $writeline(
                "\"%s\",\"%s\t\",\"%s\t\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\t\",\"%s\t\",",
                @$item['交易单号'] ?: '',
                @$item['流水号'] ?: '',
                @$item['交易金额'] ?: '',
                @$item['货币种类'] ?: '',
                @$item['备注'] ?: '',
                @$item['交易状态'] ?: '',
                @$item['订单状态'] ?: '',
                @$item['日志状态'] ?: '',
                @$item['上包时间'] ?: '',
                @$item['交易时间'] ?: '',
                @$item['捐助者OpenID'] ?: '',
                @$item['助力对象OpenID'] ?: '',
                str_replace('"', '""', @$item['捐助者昵称'] ?: ''),
                str_replace('"', '""', @$item['助力对象昵称'] ?: '')
            );
        }

        fclose($fp);

        $this->info('ok');
        $this->info('URL: ' . uploads_url("yx2018/data/$filename.csv"));
    }
)->describe('微信交易对账');





//紧急修复排行榜异常;Redis应用下 日志很重要，一定要做好业务日志，以利于重建Redis数据
Artisan::command(
    'wanjia:yx2018:fix_ranking',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */


        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $proj = \App\Models\Project::matchByPath('/yx2018');

        //取所有已支付订单
        $data = \DB::select("
select `type` threadid, m.openid voter, m.updated_at time
from zt_payments m 
where m.created_at > '2018-03-23'
    and payment IS NOT NULL
ORDER BY id
        ");

        //按threadid分组
        $data = collect($data)->groupBy('threadid')->all();

        //取所有的队长(str9) openid(str1) 和 线路(str8)
        $players = \DB::select("
select p.str1 register, str8 line
from zt_player p
where p.project_id = {$proj->id} and str9 = 'Y'
        ");

        $kDonate = \App\Features\Yx2018\RedisOperator::kTotalDonate;
        $redis->del($kDonate);

        //回复每一队的排行数据
        foreach ($players as $player) {
            $register = $player->register;
            $line = $player->line;

            echo $register, ':', $line, ':';

            $kRanking = \App\Features\Yx2018\RedisOperator::pRanking . $line;
            $kLog = \App\Features\Yx2018\RedisOperator::pVoteLog . $register;
            $kLimit = \App\Features\Yx2018\RedisOperator::pVoteLimit . $register;

            //修正前的数据
            printf('(%d,%.6f)', $redis->zRevRank($kRanking, $register), $redis->zScore($kRanking, $register));

            echo ' => ';

            $lastLog = $redis->lIndex($kLog, -1);
            $first = substr($lastLog, strrpos($lastLog, ',') + 1, -1);

            $redis->zAdd($kRanking, $first, $register);
            $redis->del($kLog);
            $redis->del($kLimit);
            $redis->lPush($kLog, $lastLog);

            $logs = @$data[md5($register)] ?: [];
            foreach ($logs as $log) {
                $lua->vote($register, $log->voter, 100, strtotime($log->time));
            }

            //修正后的数据
            printf('(%d,%.6f)', $redis->zRevRank($kRanking, $register), $redis->zScore($kRanking, $register));

            echo PHP_EOL;
        }
    }
)->describe('排行榜异常修复');





//补全海报、重建海报
Artisan::command(
    'wanjia:yx2018:fix_cover {--F|force}',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        set_time_limit(0);
        ini_set('memory_limit', '102400M');

        $force = $this->option('force');

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $kGroups = \App\Features\Yx2018\RedisOperator::kGroups;
        $kMembers = \App\Features\Yx2018\RedisOperator::kWxMembers;
        $groups = $redis->hKeys($kGroups);

        $lost = [];
        $success = [];
        foreach ($groups as $groupid) {
            $threadid = md5($groupid);
            $path = uploads_path("yx2018/covers/$threadid.png");
            if ($force || !is_file($path)) {
                $member = $redis->hGet($kMembers, $groupid);
                if (!$member) {
                    $this->error("用户资料缺失:$groupid");
                    $lost[] = $groupid;
                    continue;
                }

                $info = json_decode($member, 1);
                if (!$info) {
                    $this->error("用户资料缺失:$groupid");
                    $lost[] = $groupid;
                    continue;
                }

                $job = new \App\Features\Yx2018\Jobs\DataSyncJob('make_cover', md5($groupid), @$info['nickname'], @$info['headimgurl']);
                $job->onQueue('yx2018_cover');
                $this->info("海报生成中:$groupid," . @$info['nickname'] . ',' . uploads_url("yx2018/covers/$threadid.png"));

                $success[] = $groupid;
                dispatch_now($job);
            }
        }
        $this->info("成功生成:" . count($success));
        $this->info("生成失败:" . count($lost) . '; ' . implode(',', $lost));
    }
)->describe('修复毅行海报');




//补全海报、重建海报
Artisan::command(
    'wanjia:yx2018:fix_honor {--F|force}',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        set_time_limit(0);
        ini_set('memory_limit', '102400M');

        $force = $this->option('force');

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $kGroups = \App\Features\Yx2018\RedisOperator::kGroups;
        $kMembers = \App\Features\Yx2018\RedisOperator::kWxMembers;
        $groups = $redis->hKeys($kGroups);

        $lost = [];
        $success = [];
        foreach ($groups as $groupid) {
            $threadid = md5($groupid);
            $path = uploads_path("yx2018/covers/honor_$threadid.png");
            @unlink($path);
            $path = uploads_path("yx2018/covers/honor_$threadid.jpg");
            if ($force || !is_file($path)) {
                $member = $redis->hGet($kMembers, $groupid);
                if (!$member) {
                    $this->error("用户资料缺失:$groupid");
                    $lost[] = $groupid;
                    continue;
                }

                $info = json_decode($member, 1);
                if (!$info) {
                    $this->error("用户资料缺失:$groupid");
                    $lost[] = $groupid;
                    continue;
                }

                $job = new \App\Features\Yx2018\Jobs\DataSyncJob('make_honor', md5($groupid), @$info['nickname'], @$info['headimgurl']);
                $job->onQueue('yx2018_cover');
                $this->info("海报生成中:$groupid," . @$info['nickname'] . ',' . uploads_url("yx2018/covers/honor_$threadid.jpg"));

                $success[] = $groupid;
                dispatch_now($job);
            }
        }
        $this->info("成功生成:" . count($success));
        $this->info("生成失败:" . count($lost) . '; ' . implode(',', $lost));
    }
)->describe('生成毅行称号图');



//补全捐赠证书、重建捐赠证书
Artisan::command(
    'wanjia:yx2018:fix_cert {--F|force}',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        set_time_limit(0);
        ini_set('memory_limit', '102400M');


        $force = $this->option('force');

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $kGroups = \App\Features\Yx2018\RedisOperator::kGroups;
        $kMembers = \App\Features\Yx2018\RedisOperator::kWxMembers;
        $groups = $redis->hKeys($kGroups);

        $lost = [];
        $success = [];
        foreach ($groups as $groupid) {
            $threadid = md5($groupid);
            $path = uploads_path("yx2018/covers/cert_$threadid.png");
            if ($force) {
                @unlink($path);
            }

            $donate = $lua->getDonate($groupid);

            if ($donate > 0 && !is_file($path)) {
                $member = $redis->hGet($kMembers, $groupid);
                if (!$member) {
                    $this->error("用户资料缺失:$groupid");
                    $lost[] = $groupid;
                    continue;
                }

                $info = json_decode($member, 1);
                if (!$info) {
                    $this->error("用户资料缺失:$groupid");
                    $lost[] = $groupid;
                    continue;
                }

                $job = new \App\Features\Yx2018\Jobs\DataSyncJob('make_cert', md5($groupid), @$info['nickname']);
                $job->onQueue('yx2018_cover');
                $this->info("证书生成中:$groupid," . @$info['nickname'] . ',' . uploads_url("yx2018/covers/cert_$threadid.png"));

                $success[] = $groupid;
                dispatch_now($job);
            }
        }
        $this->info("成功生成:" . count($success));
        $this->info("生成失败:" . count($lost) . '; ' . implode(',', $lost));
    }
)->describe('修复毅行捐赠证书');



//检查投票日志
Artisan::command(
    'wanjia:yx2018:verify_vote_logs',
    function () {

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $keys = $redis->keys(\App\Features\Yx2018\RedisOperator::pVoteLog . '*');
        foreach ($keys as $k) {
            $logs = $redis->lRange($k, 0, -2);

            if (!$logs) {
                continue;
            }


            $logs = array_map('str_getcsv', $logs);
            $logs = collect($logs)->groupBy(1)->filter(function ($v) {
                return count($v) > 1;
            });

            if (!count($logs)) {
                continue;
            }

            $logs = $logs->jsonSerialize();

            echo "FOUND! $k", PHP_EOL;
            foreach ($logs as $k0 => $slogs) {
                echo '-- ', $k0, PHP_EOL;
                foreach ($slogs as $slog) {
                    echo $slog[1], ',', $slog[0], ',', date('Y-m-d:H:i:s', $slog[0]), PHP_EOL;
                }
            }
        }
    }
)->describe('检查投票日志');



//检查投票日志
Artisan::command(
    'wanjia:yx2018:revert_conflict_log {groupid} {openid}',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $groupid = $this->argument('groupid');
        $openid = $this->argument('openid');

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $kVoteLog = \App\Features\Yx2018\RedisOperator::pVoteLog . $groupid;
        $logs = $redis->lRange($kVoteLog, 0, -2);
        $logs = preg_grep('@,' . preg_quote($openid) . ',@', $logs);

        $logs = array_map('str_getcsv', $logs);
        $logs = collect($logs)->groupBy(0)->filter(function ($v) {
            return count($v) > 1;
        });

        foreach ($logs as $k => $slogs) {
            $log = $slogs[0];
            $n = count($slogs) - 1;

            if ($n) {
                $line = $lua->getLine($groupid);

                $kRanking = App\Features\Yx2018\RedisOperator::pRanking . $line;

                $x = $redis->lRem($kVoteLog, "{$log[0]},{$log[1]},{$log[2]}", $n);
                if ($x != $n) {
                    $this->error("错误: 移除日志时未达到期望数字, 日志数据已出错, 请先设法恢复");
                    exit(0);
                }

                $this->info("移除{$x}行重复日志");

                $incr = bcdiv(-100 * $x, 1, 0);

                $score0 = $redis->zScore($kRanking, $groupid);
                $redis->zIncrBy($kRanking, $incr, $groupid);
                $score1 = $redis->zScore($kRanking, $groupid);
                $this->info("Score变化: $score0 => $score1");

                $donate0 = $redis->get(App\Features\Yx2018\RedisOperator::kTotalDonate);
                $redis->incrby(App\Features\Yx2018\RedisOperator::kTotalDonate, $incr);
                $donate1 = $redis->get(App\Features\Yx2018\RedisOperator::kTotalDonate);
                $this->info("Donate变化: $donate0 => $donate1");

            }
        }

    }
)->describe('检查投票日志');




//检查投票日志
Artisan::command(
    'wanjia:yx2018:export_ranking',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $filename = \Illuminate\Support\Str::random(32);
        $path = uploads_path("yx2018/data/$filename.csv");

        $lines = [
            'L1'    => 'MINI线',
            'L2'    => '半程线',
            'L3'    => '全程线'
        ];
        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();




        $fp = fopen($path, 'w');

        //导出的UTF8编码 CSV最终是给人看的，不考虑程序分析；所以加BOM头
        //当然也可以导出GBK编码的CSV就不用加BOM头，但是微信昵称里可能有特殊字符在GBK编码里找不到，所以老实用UTF8编码吧
        fwrite($fp, "\xEF\xBB\xBF");


        $writeline = function ($format, ... $args) use ($fp) {
            $line = sprintf($format . "\r\n", ... $args);
            fwrite($fp, $line);
        };

        $writeline("线路,序号,队长姓名,微信昵称,金额");
        foreach ($lines as $line => $line_name) {
            $i = 0;
            foreach ($redis->zRevRange($lua::pRanking . $line, 0, -1, true) as $openid => $score) {
                $wx_info = $lua->getMemberAndParse($openid);
                $phone = explode(',', $redis->hGet($lua::kGroups, $openid))[0];
                $info = $lua->getPlayerAndParse($phone);

                $writeline(
                    "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"",
                    $line_name,
                    ++ $i,
                    str_replace('"', '""', $info['name']),
                    str_replace('"', '""', $wx_info['nickname']),
                    bcdiv($score, 100, 0)
                );
            }
        }

        fclose($fp);
        $this->info('下载地址: ' . uploads_url("yx2018/data/" . $filename) . '.csv');
    }
)->describe('导出全部排行榜数据');



//检查投票日志
Artisan::command(
    'wanjia:yx2018:export_voters',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $filename = \Illuminate\Support\Str::random(32);
        $path = uploads_path("yx2018/data/$filename.csv");

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();


        $fp = fopen($path, 'w');

        //导出的UTF8编码 CSV最终是给人看的，不考虑程序分析；所以加BOM头
        //当然也可以导出GBK编码的CSV就不用加BOM头，但是微信昵称里可能有特殊字符在GBK编码里找不到，所以老实用UTF8编码吧
        fwrite($fp, "\xEF\xBB\xBF");


        $writeline = function ($format, ... $args) use ($fp) {
            $line = sprintf($format . "\r\n", ... $args);
            fwrite($fp, $line);
        };

        $writeline("序号,微信昵称,金额");


        $proj = \App\Models\Project::matchByPath('/yx2018');
        $stm = \DB::connection()->getPdo()->prepare("
                      select openid, count(1) donate
                      from zt_payments
                      where project_id = :proj_id
                        && payment is not null
                      group by openid
                      order by donate desc, openid
                ");
        $stm->execute([
            'proj_id'       => $proj->id
        ]);

        $i = 0;
        foreach ($stm as $row) {
            $openid = $row['openid'];
            $donate = $row['donate'];

            $wx_info = $lua->getMemberAndParse($openid);
            $writeline(
                "\"%s\",\"%s\",\"%s\"",
                ++ $i,
                str_replace('"', '""', $wx_info['nickname']),
                $donate
            );
        }

        fclose($fp);
        $this->info('下载地址: ' . uploads_url("yx2018/data/" . $filename) . '.csv');
    }
)->describe('导出独立捐赠者');


//检查投票日志
Artisan::command(
    'wanjia:yx2018:export_low_money',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $filename = \Illuminate\Support\Str::random(32);
        $path = uploads_path("yx2018/data/$filename.csv");

        $lines = [
            'L1'    => 'MINI线',
            'L2'    => '半程线',
            'L3'    => '全程线'
        ];
        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $donates = [];
        foreach (['L1', 'L2', 'L3'] as $line) {
            $kRanking = App\Features\Yx2018\RedisOperator::pRanking . $line;
            $ret = $redis->zRangeByScore($kRanking, 0, 100.999, array('withscores' => true));
            $donates = array_merge($donates, $ret);
        }

        $groups = array_keys($donates);
        $phones = $redis->hMGet(App\Features\Yx2018\RedisOperator::kGroups, $groups);
        $infos = $redis->hMGet(App\Features\Yx2018\RedisOperator::kWxMembers, $groups);
        $ret = [];
        foreach ($phones as $openid => $phone) {
            foreach (explode(',', $phone) as $p) {
                $ret[$p] = [
                    'groupid'       => $openid,
                    'master_info'   => json_decode($infos[$openid], 1),
                    'donate'        => bcdiv($donates[$openid], 100, 0)
                ];
            }
        }

        $players = $redis->hMGet(App\Features\Yx2018\RedisOperator::kPlayers, array_keys($ret));
        foreach ($ret as $phone => &$item) {
            if (!@$players[$phone]) {
                continue;
            }

            $item['info']   = json_decode($players[$phone], 1);
        }
        unset($item);


        $fp = fopen($path, 'w');

        //导出的UTF8编码 CSV最终是给人看的，不考虑程序分析；所以加BOM头
        //当然也可以导出GBK编码的CSV就不用加BOM头，但是微信昵称里可能有特殊字符在GBK编码里找不到，所以老实用UTF8编码吧
        fwrite($fp, "\xEF\xBB\xBF");


        $writeline = function ($format, ... $args) use ($fp) {
            $line = sprintf($format . "\r\n", ... $args);
            fwrite($fp, $line);
        };

        $writeline("手机号,姓名,是否队长,团队募捐额,队长微信昵称,队别,路线");
        foreach ($ret as $phone => $item) {
            $writeline(
                "\"`%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"",
                @$phone ?: '',
                @$item['info']['name'] ?: '',
                @$item['info']['is_master'] ?: '',
                @$item['donate'] ?: '0',
                @$item['master_info']['nickname'] ?: '',
                @$item['groupid'] ? md5($item['groupid']) : '',
                @$lines[$item['info']['line']]
            );
        }

        fclose($fp);
        $this->info('下载地址: ' . uploads_url("yx2018/data/" . $filename) . '.csv');
    }
)->describe('导出募捐金额少的信息');


// 重新建立通过昵称搜索用户的hash数据
Artisan::command(
    'wanjia:yx2018:rebuildnickname',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        function getPlayers($lua = null)
        {
            if (is_null($lua)) {
                return false;
            }
            $redis = $lua->getRedis();
            $cursor = null;
            while ($cursor !== 0) {
                $ret = $redis->hScan($lua::kGroups, $cursor, '*', 10);
                if ($ret) {
                    $arr = [];
                    foreach ($ret as $k => $v) {
                        $arr[] = $k;
                    }
                    yield $arr;
                }
            }
        }

        $rows = $redis->hKeys($lua::kGroups) ?: [];
        $players_num = count($rows);
        $i = 0;

        foreach ($rows as $openid) {
            if ($arr = $redis->hGet($lua::kWxMembers, $openid)) {
                $playerinfo = wj_json_decode($arr);
                $playernickname = strtolower($playerinfo['nickname']);
                $arr = [];
                $i++;
                if ($str = $redis->hGet($lua::kNicknamesNew, $playernickname)) {
                    $arr = explode(',', $str);
                    if (in_array($openid, $arr)) {
                        $this->warn(sprintf(
                            "(%d/%d) OpenID:%s 在键\"%s\" 中已存在",
                            $i,
                            $players_num,
                            $openid,
                            $playernickname
                        ));
                        continue;
                    } else {
                        array_push($arr, $openid);
                    }
                } else {
                    array_push($arr, $openid);
                }
                if ($str = implode(',', $arr)) {
                    $ret = $redis->hSet($lua::kNicknamesNew, $playernickname, $str);
                    if ($ret) {
                        $this->info(sprintf(
                            "(%d/%d) OpenID:%s 在键\"%s\" 的值更新成功",
                            $i,
                            $players_num,
                            $openid,
                            $playernickname
                        ));
                    }
                }
            }
        }
    }
)->describe('重新建立通过昵称搜索用户的hash数据');

//检查投票日志
Artisan::command(
    'wanjia:yx2018:search_star',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();

        $jl = json_decode(file_get_contents(__DIR__ . '/star.json'));
        foreach ($jl as $jo) {
            $threadid = $jo->threadid;
            $groupid = $lua->getThread($threadid);
            $player = @$lua->getGroupAndParse($groupid)['players'][0];
            $jo->player = $player;

            echo @$jo->info->nickname, "\t|", @$jo->player['name'], "\t|", @$jo->player['phone'], PHP_EOL;
        }
    }
)->describe('筛选给定选手');


Artisan::command(
    'wanjia:yx2018:forecasting_cloth_size',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $lines = [
            'L1'    => 'MINI线',
            'L2'    => '半程线',
            'L3'    => '全程线'
        ];

        //尺码分组
        $clothes = [
            'C140'      => [
                'name'      =>  '10岁以下儿童(140)',
                'age'       =>  [0, 10],
                'gender'    =>  ['W', 'M']
            ],
            'M170'      =>  [
                'name'      =>  '11~18男性(170)',
                'age'       =>  [11, 18],
                'gender'    => ['M']
            ],
            'W160'      =>  [
                'name'      =>  '11~18女性(160)',
                'age'       =>  [11, 18],
                'gender'    => ['W']
            ],
            'M180'      =>  [
                'name'      =>  '19岁以上男性(180)',
                'age'       =>  [19, 200],
                'gender'    => ['M']
            ],
            'W170'      =>  [
                'name'      =>  '19岁以上女性(170)',
                'age'       =>  [19, 200],
                'gender'    => ['W']
            ]
        ];

        $line_players = [
            'L1'    => 5000,
            'L2'    => 5000,
            'L3'    => 5000,
        ];


        $players = [];
        foreach ($line_players as $line => $nums) {
            $phones = $lua->selectPlayers($line, $nums);

            $_players = $redis->hMGet($lua::kPlayers, $phones);
            $_players = array_values(array_filter($_players));
            $line_players[$line] = count($_players);
            $players = array_merge($players, $_players);
        }

        $players = collect($players)->map(function ($v) {
            return json_decode($v);
        })->each(function ($v) use ($clothes) {
            $v->cloth = 'UNKNOWN';
            foreach ($clothes as $cloth => $q) {
                if ($v->age < $q['age'][0] || $v->age > $q['age'][1]) {
                    continue;
                }

                if (!in_array($v->gender, $q['gender'])) {
                    continue;
                }

                $v->cloth = $cloth;
            }
        })->groupBy('cloth')->all();


        foreach ($clothes as $cloth => &$info) {
            $info['count'] = count($players[$cloth]);
        }
        unset($info);

        $this->info('线路实际人数:');
        foreach ($line_players as $line => $count) {
            $this->info("  - {$lines[$line]}: $count");
        }

        $this->info('衣服尺码预测:');
        foreach ($clothes as $cloth => $info) {
            $this->info("  - {$info['name']}: {$info['count']}");
        }
    }
)->describe('预测参赛选手衣服尺码;用于提前准备服装');


Artisan::command(
    'wanjia:yx2018:check_data',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $ret = $lua->checkData();

        if ($ret[0] !== 'OK') {
            $this->error($ret[1]);
            return;
        }


        $phones1 = $redis->hKeys($lua::kPlayers);
        $phones2 = $redis->hVals($lua::kSearchPlayer);
        $diff = array_diff($phones1, $phones2);
        if ($diff) {
            $this->error('kPlayers 与 kSearchPlayer 配对失败');
            return;
        }

        $proj = \App\Models\Project::matchByPath('/yx2018');
        $cnt = \App\Models\Player::repository()->countByProjectId($proj->id);

        if ($cnt != $ret[1]) {
            $this->error('Redis/DB 数据校验失败');
            return;
        }

        $this->info('ok');
    }
)->describe('数据完整性检查');


Artisan::command(
    'wanjia:yx2018:check_last_group {line} {nums}',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $line = $this->argument('line');
        $nums = $this->argument('nums');

        $proj = \App\Models\Project::matchByPath('/yx2018');

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $lines = [
            'L1'    => 'MINI线',
            'L2'    => '半程线',
            'L3'    => '全程线'
        ];

        $phones = $lua->selectPlayers($line, $nums);

        $player = \App\Models\Player::repository()->findOneByProjectId($proj->id, [
            \App\Features\Yx2018\ServiceProvider::getPlayerIndex('phone')   => array_last($phones)
        ]);

        $group = $lua->getGroupAndParse($player->info_openid);

        $this->info(sprintf('%s最后一名:', $lines[$line]));
        $this->info(sprintf('  - 募捐额:%d', $lua->getDonate($player->info_openid)));
        $this->info(sprintf('  - 报名时间:%s', date('Y-m-d H:i:s', $group['players'][0]['registed_at'])));
        $this->info('  - 选手列表:');
        foreach ($group['players'] as $player) {
            $this->info(sprintf('    - %s,%s,%s', $player['name'], $player['phone'], $player['passport']));
        }
    }
)->describe('检查最后一名信息');



Artisan::command(
    'wanjia:yx2018:fix_player_sfz',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $proj = \App\Models\Project::matchByPath('/yx2018');

        $stm = \DB::connection()->getPdo()->prepare("
                      select id, str3
                      from zt_player
                      where str3 like :passport_type
                        and project_id = :proj_id
                ");
        $stm->execute([
            'proj_id'       => $proj->id,
            'passport_type' => 'SFZ:%'
        ]);

        $total = $redis->hLen($lua::kPlayers);

        $i = 0;
        foreach ($stm as $row) {
            $player = \App\Models\Player::repository()->retrieveByPK($row['id']);
            $player->info_sfz = wj_parse_sfz(substr($player->info_passport, 4));
            $player->info_hash = $lua->hashPlayer($player->info_name, $player->info_passport);
            $redis->hSet($lua::kSearchPlayer, $player->info_hash, $player->info_phone);
            $player->save();

            $this->info(sprintf(
                "fixed %05d/$total %s (%s=>%s) %s/%s/%s/%s",
                ++$i,
                @$player->info_passport,
                @$player->info_hash,
                @$player->info_phone,
                @$player->info_sfz->location->province_name,
                @$player->info_sfz->location->city_name,
                @$player->info_sfz->location->region_name,
                @$player->info_sfz->xingzuo
            ));
        }
    }
)->describe('身份证信息重新解析');



Artisan::command(
    'wanjia:yx2018:fix_player_other',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $proj = \App\Models\Project::matchByPath('/yx2018');

        $stm = \DB::connection()->getPdo()->prepare("
                      select id, str3
                      from zt_player
                      where str3 not like :passport_type
                        and project_id = :proj_id
                ");
        $stm->execute([
            'proj_id'       => $proj->id,
            'passport_type' => 'SFZ:%'
        ]);

        $total = $redis->hLen($lua::kPlayers);

        $i = 0;
        foreach ($stm as $row) {
            $player = \App\Models\Player::repository()->retrieveByPK($row['id']);
            $player->info_hash = $lua->hashPlayer($player->info_name, $player->info_passport);
            $redis->hSet($lua::kSearchPlayer, $player->info_hash, $player->info_phone);
            $player->save();

            $this->info(sprintf(
                "fixed %05d/$total %s (%s=>%s)",
                ++$i,
                @$player->info_passport,
                @$player->info_hash,
                @$player->info_phone
            ));
        }
    }
)->describe('Hash重建');


//给符合条件选手分配毅行编号
Artisan::command(
/**
 * 参数line表示要分配名额的路线
 * 参数num表示分配给该路线的名额数
 */
    'wanjia:yx2018:ticket_assign_all',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */

        $this->call("wanjia:yx2018:ticket_assign", ['line' => 'L1', 'num' => 5000]);
        $this->call("wanjia:yx2018:ticket_assign", ['line' => "L2", 'num' => 5000]);
        $this->call("wanjia:yx2018:ticket_assign", ['line' => "L3", 'num' => 5000]);
    }
)->describe('给三条线路分配名额');

//给符合条件选手分配毅行编号
Artisan::command(
    /**
     * 参数line表示要分配名额的路线
     * 参数num表示分配给该路线的名额数
     */
    'wanjia:yx2018:ticket_assign {line} {num}',
    function () {
        $input_line = $this->argument('line');
        $input_num = $this->argument('num');

        $lines = [ 'L1' => 'MINI线', 'L2' => '半程线', 'L3' => '全程线' ];
        if (!in_array($input_line, array_keys($lines))) {
            $this->error('毅行线路参数不正确 -- L1：MINI线，L2：半程线，L3：全程线');
            exit();
        }
        $input_num = intval($input_num);
        if ($input_num <= 0) {
            $this->error('毅行编号分配数量参数不正确');
            exit();
        }

        $project = \App\Models\Project::matchByPath('/yx2018');

        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();

        // 编号前缀数组
        $prefixs = [ 'L1' => 'A', 'L2' => 'B', 'L3' => 'C'];
        // 确定此次操作的编号前缀
        $ticket_no_prefix = $prefixs[$input_line];

        /**
         * 更新数据表，分配毅行编号
         * @param int $project_id 项目编号
         * @param string $phone 选手手机号
         * @param string $ticket_no 分配给选手的编号
         * @return array 返回更新结果
         */
        function assignPlayerTicketNo($project_id, $phone, $ticket_no)
        {
            $phoneIndex = \App\Features\Yx2018\ServiceProvider::getPlayerIndex('phone');
            $player = \App\Models\Player::repository()->findOneByProjectId($project_id, [
                $phoneIndex => $phone,
            ]);
            if (!$player) {
                return [ 'data' => '选手未找到', 'code' => 1 ];
            }
            $player->ticket_no = $ticket_no;
            try {
                $ret = $player->save();
            } catch (\Throwable $ex) {
                dd($ex->getTraceAsString());
            }
            if ($ret == true) {
                return [ 'data' => '更新成功', 'code' => 0 ];
            } else {
                return [ 'data' => '更新失败，选手id='.$player->id, 'code' => 2 ];
            }
        }

        $phones = $lua->selectPlayers($input_line, $input_num) ?: [];
        $total = count($phones);

        // 定义编号数字部分
        $i = 0;
        // 组成需要分配编号的人员数组
        foreach ($phones as $phone) {
            $i++;
            // 生成选手编号
            $ticket_no = sprintf('%s%04d', $ticket_no_prefix, $i);
            // 更新数据库
            $ret = assignPlayerTicketNo($project->id, $phone, $ticket_no);
            if ($ret['code'] != 0) {
                $this->error(sprintf("(%d/%d) 分配编号失败，信息：%s", $i, $total, $ret['data']));
            } else {
                $this->info(sprintf("(%d/%d) 分配编号：%s 给 phone=%s 的选手", $i, $total, $ticket_no, $phone));
            }
        }
    }
)->describe('给符合条件选手分配毅行编号');


// 收回已分配的毅行编号
Artisan::command(
    /**
     * 命令--type参数表示收回的类型 all表示全部/one代表个人/group代表团队
     * --phone参数可以设置多个手机号，使用逗号分割
     */
    'wanjia:yx2018:ticket_clear {type} {--phone=}',
    function () {
        $input_type = $this->argument('type');
        $input_phone = $this->option('phone');

        if ($this->confirm('将checked字段设置为0 ？')) {
            $input_checked = 0;
        } else {
            $input_checked = 1;
        }

        $project = \App\Models\Project::matchByPath('/yx2018');
        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();

        switch ($input_type) {
            case 'all':
                $players = \App\Models\Player::repository()->findByProjectId($project->id, [
                    \Wanjia\Common\Database\Limiter::make(function (\Illuminate\Database\Eloquent\Builder $b) {
                        $b->whereNotNull('ticket_no');
                    }, \Wanjia\Common\Database\Limiter::CALLBACK)
                ]);
                break;
            case 'one':
                if (!$input_phone) {
                    $this->error('收回类型为个人时，手机号参数--phone必须设置');
                    exit();
                } else {
                    // 将输入的手机号转换成数组
                    $input_phone = explode(',', $input_phone);
                    $phones = [];
                    foreach ($input_phone as $one) {
                        $phones[] = $one;
                    }
                    array_unique($phones);
                    $players = \App\Models\Player::repository()->findByProjectId(
                        $project->id,
                        [ \App\Features\Yx2018\ServiceProvider::getPlayerIndex('phone') => $phones ]
                    );
                }
                break;
            case 'group':
                if (!$input_phone) {
                    $this->error('收回类型为团队时，手机号参数--phone必须设置');
                    exit();
                } else {
                    // 收回类型为团队时，取出选手所在团队全部成员的手机号数据
                    // 将输入的手机号转换成数组
                    $input_phone = explode(',', $input_phone);
                    $phones = [];
                    array_walk($input_phone, function ($one) use ($lua, &$phones) {
                        $redis = $lua->getRedis();
                        if ($playinfo = $lua->getPlayerAndParse($one)) {
                            $group = $redis->hGet($lua::kGroups, $playinfo['openid']);
                            $group_arr = explode(',', $group);
                            foreach ($group_arr as $phone) {
                                $phones[] = $phone;
                            }
                        }
                    });
                    array_unique($phones);
                    $players = \App\Models\Player::repository()->findByProjectId(
                        $project->id,
                        [ \App\Features\Yx2018\ServiceProvider::getPlayerIndex('phone') => $phones ]
                    );
                }
                break;
            default:
                $this->error('type参数不正确');
                exit();
        }

        $players = $players ?: [];

        // 操作计数
        $i = 0;
        $total = count($players);

        foreach ($players as $player) {
            $i++;

            if ($player->info_supply_loc) {
                $this->error(sprintf("(%d/%d) 收回编号失败，选手id=%d，选手Phone=%s 已确认不能收回", $i, $total, $player->id, $player->info_phone));
                continue;
            }

            $ticket_no = $player->ticket_no;
            $player->ticket_no = null;
            $player->checked = $input_checked;
            $ret = $player->save();
            if ($ret == true) {
                $this->info(sprintf("(%d/%d) 收回编号：%s，选手id=%d，选手Phone=%s", $i, $total, $ticket_no, $player->id, $player->info_phone));
            } else {
                $this->error(sprintf("(%d/%d) 收回编号失败，选手id=%d，选手Phone=%s", $i, $total, $player->id, $player->info_phone));
            }
        }
    }
)->describe('收回已分配的毅行编号');


// 取消未及时确认信息选手的毅行资格，收回毅行编号
Artisan::command(
    'wanjia:yx2018:ticket_gc',
    function () {
        $project = \App\Models\Project::matchByPath('/yx2018');

        $players = \App\Models\Player::repository()->findByProjectId($project->id, [
            \Wanjia\Common\Database\Limiter::make(function (\Illuminate\Database\Eloquent\Builder $b) {
                $b->whereNotNull('ticket_no');
                $b->where(\App\Features\Yx2018\ServiceProvider::getPlayerIndex('supply_loc'), '=', '');
            }, \Wanjia\Common\Database\Limiter::CALLBACK)
        ]);

        // 操作计数
        $i = 0;
        $total = count($players);

        foreach ($players as $player) {
            $i++;
            $ticket_no = $player->ticket_no;
            $player->ticket_no = null;
            $ret =$player->save();
            if ($ret == true) {
                $this->info(sprintf("(%d/%d) 收回编号：%s，选手id=%d，选手Phone=%d", $i, $total, $ticket_no, $player->id, $player->info_phone));
            } else {
                $this->error(sprintf("(%d/%d) 收回编号失败，选手id=%d，选手Phone=%d", $i, $total, $player->id, $player->info_phone));
            }
        }
    }
)->describe('取消未及时确认信息选手的毅行资格，收回毅行编号');

// 取消未及时确认信息选手的毅行资格，收回毅行编号
Artisan::command(
    'wanjia:yx2018:fix_player_meta',
    function () {

        $proj = \App\Models\Project::matchByPath('/yx2018');

        $stm = \DB::connection()->getPdo()->prepare("
                      select id
                      from zt_player 
                      where project_id = :proj_id
                ");
        $stm->execute([
            'proj_id'   => $proj->id
        ]);

        $repo = \App\Models\Player::repository();
        foreach ($stm as $row) {
            $player = $repo->findOneById($row['id']);
            $player->enableBroadcast(false);
            $player->regenerateMeta();
            $player->save();

            echo $row['id'], PHP_EOL;
        }

    }
)->describe('取消未及时确认信息选手的毅行资格，收回毅行编号');



// 获得各线路未获得选手的毅行资格前200名二维码
Artisan::command(
    'wanjia:yx2018:create_sp_200_qrcode',
    function () {
        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $kNoQualificationSp200 = $lua::REDIS_NS . ":noqualification:sp200";

        $total = $redis->lLen($kNoQualificationSp200);
        if ($total && $total > 0) {
            $this->error('二维码已生成');
            exit();
        }

        $lines = [ 'L1', 'L2', 'L3'];
        $sp_200_groups = [];
        $i = 0;

        foreach ($lines as $line) {
            $kRanking = $lua::pRanking . $line;
            $members = $redis->zRevRange($kRanking, 0, 5200) ?: [];
            $groups = $redis->hMGet($lua::kGroups, $members) ?: [];

            $flag = false;
            $conut = 0;
            foreach ($members as $openid) {
                $phones = $groups[$openid];
                $group_num = substr_count($phones, ",") + 1;
                $conut += $group_num;
                if ($conut >= 5000) {
                    $flag = true;
                }

                if ($flag) {
                    // 生成二维码
                    $threadid = md5($openid);
                    $file = uploads_path("yx2018/phpqrcodes/$threadid.png");
                    if (!is_dir($dir = dirname($file))) {
                        mkdir($dir);
                    }
                    if (!is_file($file)) {
                        $url = route('yx2018.start', [
                            'from'   => "cert:$threadid",
                            'act' => 'vote',
                            'threadid' => $threadid
                        ]);
                        SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                            ->margin(0)->size(260)->generate($url, $file);
                        $redis->lPush($kNoQualificationSp200, $openid);
                    }
                    // 操作计数
                    $i++;
                    $this->info(sprintf("(%d) 已完成 openid=%s", $i, $openid));

                    $sp_200_groups[$line][] = $openid;
                    if (count($sp_200_groups[$line]) >= 200) {
                        break;
                    }
                }
            }
        }
    }
)->describe('各线路未获得选手的毅行资格前200名二维码');



// 删除获得各线路未获得选手的毅行资格前200名二维码
Artisan::command(
    'wanjia:yx2018:delete_sp_200_qrcode',
    function () {
        $lua = \App\Features\Yx2018\Controllers\Controller::getLuaRedis();
        $redis = $lua->getRedis();

        $kNoQualificationSp200 = $lua::REDIS_NS . ":noqualification:sp200";
        $redis->del($kNoQualificationSp200);
        $this->info("已删除");
    }
)->describe('删除获得各线路未获得选手的毅行资格前200名二维码');




// 删除获得各线路未获得选手的毅行资格前200名二维码
Artisan::command(
    'wanjia:yx2018:make_ticket_card',
    function () {
        /** @var Illuminate\Foundation\Console\ClosureCommand $this */




        foreach (['A', 'B', 'C'] as $prefix) {
            $bg = new Imagick(module_resource_path('yx2018', "ticket_card_$prefix.png"));
            for ($i = 1; $i <= 2; $i++) {
                $image = clone $bg;

                $ticket = sprintf("%s%04d", $prefix, $i);

                $path = uploads_path("yx2018/ticket_card/$ticket.jpg");
                $dir = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $image_text = new \ImagickDraw();
                $image_text->setStrokeWidth(1);
                $image_text->setTextEncoding('UTF-8');
                $image_text->setGravity(\Imagick::GRAVITY_SOUTH);
                $image_text->setFontSize(320);
                $image_text->setFillColor('#ffffff');
                $image_text->setFont(resource_path('MSYH.ttf'));
                $image_text->annotation(0, 300, $ticket);

                $ret = new Imagick();
                $ret->newImage($image->getImageWidth(), $image->getImageHeight(), "white");
                $ret->compositeimage($image, Imagick::COMPOSITE_OVER, 0, 0);
                $ret->setImageFormat('jpg');
                $ret->drawImage($image_text);
                $ret->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
                $ret->setImageResolution(300, 300);
                $ret->setImageCompression(imagick::COMPRESSION_JPEG);
                $ret->setImageCompressionQuality(90);

                $ret->writeImage($path);

                $this->info($ticket);
            }
        }


    }
)->describe('制作号码牌');


