<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/21
 * Time: 下午1:58
 */

namespace App\Zhuanti\Common;

use App\Http\Response\RedirectMessageResponse;
use App\Models\Project;

trait FTBaoming
{
    protected $conf_baoming;
    protected $conf_baoming_time_msgs;
    protected $conf_baoming_event_msgs;

    protected function checkBaomingCapacity($step = '')
    {
        //TODO: 跳转目标如何灵活控制

        /** @var Project $proj */
        $proj = $this->getProject();


        if (!$proj) {
            redirect_message('专题项目不存在', '/');
        }

        $home_url = $proj->path;

        if ($proj->start_at && strtotime($proj->start_at) >= time()) {
            redirect_message('项目未开始', '/');
        }

        if ($proj->end_at && strtotime($proj->end_at) <= time()) {
            redirect_message('项目已结束', '/');
        }
        if (!in_array('baoming', $proj->capacity_arr)) {
            redirect_message('专题项目未开启报名功能', $home_url);
        }

        if (!$proj->can('baoming')) {
            redirect_message('不支持报名', $home_url);
        }

        $conf_baoming = $proj->conf_baoming;
        if (!$conf_baoming) {
            redirect_message('未配置报名', $home_url);
        }


        $this->conf_baoming = $conf_baoming;
        $this->conf_baoming_time_msgs = collect(wj_obj2arr(@$conf_baoming->time_msgs ?: []))
            ->where('enable', '=', 1)
            ->values();
        $this->conf_baoming_event_msgs = collect(wj_obj2arr(@$conf_baoming->event_msgs ?: []))
            ->where('enable', '=', 1)
            ->filter(function ($item) {
                return (!@$item['start'] || strtotime($item['start']) <= time())
                    && (!@$item['end'] || strtotime($item['end']) >= time());
            })
            ->values();

        //检查时间表
        $time_msgs = $this->conf_baoming_time_msgs->filter(function ($v) use ($step) {
            return in_array($step, @$v['step_mode'] ?: []);
        });
        foreach ($time_msgs as $item) {
            if ((!@$item['start'] || strtotime($item['start']) <= time())
                && (!@$item['end'] || strtotime($item['end']) >= time())) {
                redirect_message($item['msg'], $item['url'] ?: $home_url);
            }
        }
        //检查事件表(报名时)
        if ($step === 'baoming') {
            $over_limit_msg = $this->conf_baoming_event_msgs
                ->where('event', '=', 'over_limit')
                ->values()
                ->offsetGet(0);

            $msg = '名额耗尽';
            if ($over_limit_msg) {
                $msg = $over_limit_msg['msg'];
            }

            //TODO: 检查剩余名额
            if (0) {
                redirect_message($msg, $over_limit_msg['url'] ?: $home_url);
            }
        }
    }

    public function baoming()
    {
//        print_r(\Request::session()->getId());die();
//        $this->checkBaomingCapacity();

        /** @var Project $proj */
        $proj = $this->getProject();
        $fields = $proj->conf_base_form_design;

        return compact('fields', 'proj');
    }

    public function tf()
    {
        $proj = $this->getProject();
        $fields = $proj->conf_base_form_design;

        return response()->json($fields);
    }
}
