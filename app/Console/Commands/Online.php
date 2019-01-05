<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\DescriptorHelper;

class Online extends Command
{

    protected $signature = "
         wanjia:online {action? : 动作，输入 help 查看支持的动作列表} {args?* : 参数列表}
    ";

    protected $description = '操作线上数据';


    public function handle()
    {
        if (config('app.env') !== 'local') {
            $this->error('本命令仅供线下开发者访问');
            return;
        }

        $action = $this->argument('action') ?: 'help';
        $args = $this->argument('args') ?: [];

        $method = "onAction" . ucfirst(Str::camel($action));
        if (method_exists($this, $method)) {
            $this->$method(... $args);
        }
    }

    public function onActionHelp()
    {
        $this->line('<comment>支持的动作:</comment>');
        $this->line(mb_sprintf('  <info>%-25s</info>%s%s', 'sync_wechat_ticket', '同步线上WechatComponentTicket到本地'));
        $this->line(mb_sprintf('  <info>%-25s</info>%s%s', 'upload_project', '根据项目ID或path,同步本地项目到线上'));
        $this->line(mb_sprintf('  <info>%-25s</info>%s%s', 'download_project', '根据项目ID或path,线上项目到本地'));
        $this->line(mb_sprintf('  <info>%-25s</info>%s%s', 'diff_project', '根据项目ID或path,比较线上线下项目'));
        $this->line(mb_sprintf('  <info>%-25s</info>%s%s', 'download_redis', '下载线上Redis数据到本地，需要提供Key参数，支持*'));
    }

    public function onActionSyncWechatTicket()
    {

        $this->call('wanjia:online', [
            'action'    => 'download_redis',
            'args'      => ['laravel:easywechat.open_platform*']
        ]);
    }

    public function onActionUploadProject($id)
    {
        if (is_numeric($id)) {
            $proj = Project::repository()->retrieveByPK($id);
        } else {
            $proj = Project::matchByPath($id);
        }

        if ($proj) {
            ms('online')->uploadProject($proj);
            $this->info('SUCCESSFUL OPERATION');
            return;
        } else {
            $this->info('FAIL');
            return;
        }
    }

    public function onActionDownloadProject($id)
    {
        if (is_numeric($id)) {
            $proj = Project::repository()->retrieveByPK($id);
        } else {
            $proj = Project::matchByPath($id);
        }

        if ($proj) {
            /** @var Project $online */
            $online = ms('online')->downloadProject($proj->path);
            $attrs = wj_mask($online->getAttributes(), ['id', 'merchant_id', 'channel_id'], true);
            $attrs['configs'] = json_decode($attrs['configs'] ?: '{}');
            $proj->setAttributes($attrs);
            $proj->conf_dev_id = null;
            $proj->save();

            $this->info('SUCCESSFUL OPERATION');
            return;
        } else {
            $this->info('FAIL');
            return;
        }
    }

    public function onActionDiffProject($id)
    {
        if (is_numeric($id)) {
            $proj = Project::repository()->retrieveByPK($id);
        } else {
            $proj = Project::matchByPath($id);
        }

        if ($proj) {
            /** @var Project $online */
            $online = ms('online')->downloadProject($proj->path);
            $attrs = wj_mask($online->getAttributes(), ['id', 'merchant_id', 'channel_id'], true);
            $proj->setAttributes($attrs);

            foreach ($proj->getDirty() as $field => $value) {
                if ($field === 'updated_at') {
                    continue;
                }

                $this->info("$field <<<<<<<<");
                $this->info($proj->getOriginal($field));
                $this->info("$field ========");
                $this->info($proj->getAttribute($field));
                $this->info("$field >>>>>>>>");
            }

            return;
        } else {
            $this->info('FAIL');
            return;
        }
    }

    public function onActionDownloadRedis($pattern = null)
    {
        $pattern = trim($pattern);

        if (!$pattern) {
            $this->error('请提供Key Pattern');
            return;
        }

        if (strpos($pattern, '*') === 0) {
            $this->error('不支持第一个字符为*');
            return;
        }

        if (strpos($pattern, '*') > 0) {
            if (strlen($pattern) < 5) {
                $this->error('Key太多，不支持下载。*Pattern长度至少为5位');
                return;
            }
        }

        $ret = ms('online')->readRemoteRedis($pattern);
        if ($ret['code']) {
            $this->error($ret['msg']);
        } else {
            $this->info($ret['msg']);

            foreach ($ret['data'] as $k => $v) {
                \RedisDB::del($k);
                \RedisDB::restore($k, 0, base64_decode($v));
            }
        }
    }
}
