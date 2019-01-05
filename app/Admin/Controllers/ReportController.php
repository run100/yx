<?php

namespace App\Admin\Controllers;


use App\Models\Jizi\JiziLog;
use App\Models\Player;
use App\Models\Prizes\PrizesLog;
use App\Models\Project;
use App\Zhuanti\Jizi\JiziOperator;
use App\Zhuanti\Prizes\PrizesOperator;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\MessageBag;


class ReportController extends Controller
{

    public function index($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $project = Project::where('id', $id)->first();
            $content->header($project->name);
            $capacitys = explode(',', $project->capacity);
            $caps = [];
            $endAt = date('Y-m-d H:00');
            $hasJizi = false;
            if (in_array('jizi', $capacitys)) {
                $caps['jizi'] = '集字/图';
                if (strtotime($endAt) > strtotime($project->configs->jizi->etime)) {
                    $endAt = date('Y-m-d H:00', strtotime($project->configs->jizi->etime));
                }
                $hasJizi = true;
                //判断是否配置了集字
                if (!JiziOperator::instance($project->id)->existJzHash()) {
                    $toastr = new MessageBag([
                        'message'   => '请先设置集字/图',
                        'type'      => 'error'
                    ]);
                    return back()->with(compact('toastr'));
                }
            }
            if (in_array('draw', $capacitys)) {
                $caps['draw'] = '抽奖';
                if (strtotime($endAt) > strtotime($project->configs->draw->etime)) {
                    $endAt = date('Y-m-d H:00', strtotime($project->configs->draw->etime));
                }
                //判断是否配置了抽奖
                if (!PrizesOperator::instance($project->id)->existPrizeHash()) {
                    $toastr = new MessageBag([
                        'message'   => '请先设置奖品',
                        'type'      => 'error'
                    ]);
                    return back()->with(compact('toastr'));
                }
            }
            $content->description(implode('、', $caps));
            $content->body(view('admin::report.index', compact('caps', 'id', 'endAt', 'hasJizi', 'project')));
        });
    }

    public function datas($id)
    {
        $request = \Request::instance();
        $capacity = $request->get('capacity');
        $timeType = $request->get('timeType');
        $startAt = $request->get('startAt');
        $endAt = $request->get('endAt');
        $project = Project::where('id', $id)->first();
        if (!$project) {
            return [];
        }
        $data = $this->{$capacity.'Data'}($project, $timeType, $startAt, $endAt);
        return wj_json_message($data);
    }

    public function datasdetail($id)
    {
        $request = \Request::instance();
        $capacity = $request->get('capacity');
        $date = $request->get('date');
        $project = Project::where('id', $id)->first();
        if (!$project) {
            return [];
        }
        $data = $this->{$capacity.'Detail'}($project, $date);
        return wj_json_message($data);
    }

    /**
     * 集字具体时间段统计
     */
    public function jiziDetail($project, $startDate)
    {
        $startTime = strtotime($startDate);
        $endTime =  strlen($startDate) == 10 ? $startTime + 86400 : $startTime + 3600;
        $endDate = date('Y-m-d H:i:s', $endTime);
        //获取集字总数据
        $baseFonts = $project->configs->base_font_setting;
        $fcJizis = JiziLog::where('project_id', $project->id)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<', $endDate)
            ->select(new Expression('field,count(*) as total'))
            ->groupBy('field')
            ->get()->pluck('total', 'field');
        $data = [];
        foreach ($baseFonts as $v) {
            $yf = isset($fcJizis[$v->key]) ? $fcJizis[$v->key] : 0;
            $t = $v->is_limit_count=='N' ? '不限' : $v->total;
            $data['jiziDetail'][] = ['name'=> $v->name, 'total'=> $t, 'yf'=> $yf];
        }
        return $data;
    }

    /**
     * 集字整体数据（总计，图表，时间段统计）
     */
    public function jiziData($project, $timeType, $startAt, $endAt)
    {
        $data = [];
        //获取总的统计数据
        $bmCount = Player::where('project_id', $project->id)->count();
        $ffCount = JiziLog::where('project_id', $project->id)->count();
        $data['jiziTotal'] = [['date'=>$project->configs->jizi->stime.' ~ '.$project->configs->jizi->etime,
            'time'=>date('Y-m-d H:i:s'),
            'bm'=>$bmCount,    //报名总人数
            'ff'=>$ffCount,     //发放总字数
            'cy'=>$project->configs->jizi->is_first_give ? $ffCount : $bmCount+$ffCount,    //参与总人数
            ]];
        //获取集字总数据
        $baseFonts = $project->configs->base_font_setting;
        $fcJizis = JiziOperator::instance($project->id)->getJzHash();
        foreach ($baseFonts as $v) {
            $yf = isset($fcJizis[$v->key]) ? $fcJizis[$v->key] : 0;
            if ($v->is_limit_count=='N') {
                $t = '不限';
                $sy = '不限';
            } else {
                $t = $v->total;
                $sy = $t - $yf;
            }
            $data['jiziData'][] = ['name'=> $v->name, 'total'=> $t, 'yf'=> $yf, 'sy'=>$sy];
        }
        //获取图表数据
        $startAt = strtotime($project->configs->jizi->stime)>strtotime($startAt) ? $project->configs->jizi->stime : $startAt;
        $endAt = strtotime($project->configs->jizi->etime)>strtotime($endAt) ? $endAt : $project->configs->jizi->etime;
        $data['datas']['datasets'] = [
            ['label'=>'报名人数', 'fill'=>false, 'backgroundColor'=>'rgb(255,99,132)', 'borderColor'=>'rgb(255,99,132)', 'stacked'=>false],
            ['label'=>'参与人数', 'fill'=>false, 'backgroundColor'=>'rgb(75,192,192)', 'borderColor'=>'rgb(75,192,192)', 'stacked'=>false],
        ];
        $startTime = strtotime($startAt);
        $endTime = strtotime($endAt);
        if ($timeType == 'day') {
            $startAt = date('Y-m-d', $startTime);
            $endAt = date('Y-m-d 23:59:59', $endTime);
            $dateFormat = '%Y-%m-%d';
            $dateFormat2 = 'Y-m-d';
            $addTime = 86400;
        } else {
            $startAt = date('Y-m-d H:00:00', $startTime);
            $endAt = date('Y-m-d H:59:59', $endTime);
            $dateFormat = '%Y-%m-%d %H:00';
            $dateFormat2 = 'Y-m-d H:00';
            $addTime = 3600;
        }
        $startTime = strtotime($startAt);
        $endTime = strtotime($endAt);
        $bmTotal = Player::where('project_id', $project->id)->where('created_at', '>=', $startAt)
            ->where('created_at', '<=', $endAt)
            ->select(new Expression('DATE_FORMAT(created_at,\''.$dateFormat.'\') as date,count(*) as total'))
            ->groupBy('date')
            ->get()->pluck('total', 'date');
        $jzTotal = JiziLog::where('project_id', $project->id)->where('created_at', '>=', $startAt)
            ->where('created_at', '<=', $endAt)
            ->select(new Expression('DATE_FORMAT(created_at,\''.$dateFormat.'\') as date,count(*) as total'))
            ->groupBy('date')
            ->get()->pluck('total', 'date');
        while ($startTime < $endTime) {
            $time = date($dateFormat2, $startTime);
            $bm = isset($bmTotal[$time]) ? $bmTotal[$time] : 0;
            $ff = isset($jzTotal[$time]) ? $jzTotal[$time] : 0;
            $data['jizi'][] = ['time'=>$time, 'bm'=>$bm, 'ff'=>$ff, 'cz'=>'<button type="button" onclick="makeDetailTable(\'jizi\', \''.$time.'\')" class="btn btn-info">详情</button>'];
            $data['datas']['labels'][] = $time;
            $data['datas']['datasets'][0]['data'][] = $bm;
            $data['datas']['datasets'][1]['data'][] = $ff;
            $startTime += $addTime;
        }
        return $data;
    }

    /**
     * 抽奖具体时间段统计
     */
    public function drawDetail($project, $startDate)
    {
        $startTime = strtotime($startDate);
        $endTime =  strlen($startDate) == 10 ? $startTime + 86400 : $startTime + 3600;
        $endDate = date('Y-m-d H:i:s', $endTime);
        //获取集字总数据
        $basePrizes = $project->configs->base_form_prizes;
        $ffPrizes = PrizesLog::where('project_id', $project->id)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<', $endDate)
            ->select(new Expression('field,count(*) as total'))
            ->groupBy('field')
            ->get()->pluck('total', 'field');
        $data = [];
        foreach ($basePrizes as $v) {
            $ff = isset($ffPrizes[$v->key]) ? $ffPrizes[$v->key] : 0;
            $data['drawDetail'][] = ['name'=> $v->name, 'ff'=> $ff];
        }
        return $data;
    }

    /**
     * 抽奖整体数据（总计，图表，时间段统计）
     */
    public function drawData(Project $project, $timeType, $startAt, $endAt)
    {
        $data = [];
        if ($project->can('jizi')) {
            $cyCount = PrizesLog::where('project_id', $project->id)->groupBy('player_id')->count();
            $bmCount = Player::where('project_id', $project->id)->count();
        } else {
            $cyCount = Player::where('project_id', $project->id)->count();
            $bmCount = 0;
        }
        $cjCount = PrizesLog::where('project_id', $project->id)->count();
        $zjCount = PrizesLog::where('project_id', $project->id)->where('is_win', 1)->count();
        $ffCount = 0;
        $basePrizes = $project->configs->base_form_prizes;
        //获取奖品总数据
        $fcPrizes = PrizesOperator::instance($project->id)->getPrizeHash();
        foreach ($basePrizes as $v) {
            if (is_numeric($v->total)) {
                $ffCount += (int)$v->total;
            }
            $yf = isset($fcPrizes[$v->key]) ? $fcPrizes[$v->key] : 0;
            if ($v->is_limit) {
                $total = $v->total;
                $sy = $total - $yf;
            } else {
                $total =  '不限';
                $sy = '不限';
            }
            $data['drawData'][] = ['name'=> $v->name, 'total'=> $total, 'yf'=> $yf, 'sy'=>$sy];
        }
        //获取总的统计数据
        $data['drawTotal'] = [['date'=>$project->configs->draw->stime.' ~ '.$project->configs->draw->etime,
            'time'=>date('Y-m-d H:i:s'),
            'cy'=>$cyCount,     //参与总人数
            'bm'=>$bmCount,    //报名总人数
            'cj'=>$cjCount,     //抽奖次数
            'zj'=>$zjCount,     //中奖次数
            'ff'=>$ffCount,     //发放奖品总数
        ]];

        //获取图表数据
        $startAt = strtotime($project->configs->draw->stime)>strtotime($startAt) ? $project->configs->draw->stime : $startAt;
        $endAt = strtotime($project->configs->draw->etime)>strtotime($endAt) ? $endAt : $project->configs->draw->etime;
        $data['datas']['datasets'] = [
            ['label'=>'参与次数', 'fill'=>false, 'backgroundColor'=>'rgb(255,99,132)', 'borderColor'=>'rgb(255,99,132)', 'stacked'=>false],
            ['label'=>'中奖次数', 'fill'=>false, 'backgroundColor'=>'rgb(75,192,192)', 'borderColor'=>'rgb(75,192,192)', 'stacked'=>false],
        ];
        $startTime = strtotime($startAt);
        $endTime = strtotime($endAt);
        if ($timeType == 'day') {
            $startAt = date('Y-m-d', $startTime);
            $endAt = date('Y-m-d 23:59:59', $endTime);
            $dateFormat = '%Y-%m-%d';
            $dateFormat2 = 'Y-m-d';
            $addTime = 86400;
        } else {
            $startAt = date('Y-m-d H:00:00', $startTime);
            $endAt = date('Y-m-d H:59:59', $endTime);
            $dateFormat = '%Y-%m-%d %H:00';
            $dateFormat2 = 'Y-m-d H:00';
            $addTime = 3600;
        }
        $startTime = strtotime($startAt);
        $endTime = strtotime($endAt);
        $cyTotal = PrizesLog::where('project_id', $project->id)->where('created_at', '>=', $startAt)
            ->where('created_at', '<=', $endAt)
            ->select(new Expression('DATE_FORMAT(created_at,\''.$dateFormat.'\') as date,count(*) as total'))
            ->groupBy('date')
            ->get()->pluck('total', 'date');
        $zjTotal = PrizesLog::where('project_id', $project->id)->where('is_win', 1)->where('created_at', '>=', $startAt)
            ->where('created_at', '<=', $endAt)
            ->select(new Expression('DATE_FORMAT(created_at,\''.$dateFormat.'\') as date,count(*) as total'))
            ->groupBy('date')
            ->get()->pluck('total', 'date');
        while ($startTime < $endTime) {
            $time = date($dateFormat2, $startTime);
            $cy = isset($cyTotal[$time]) ? $cyTotal[$time] : 0;
            $zj = isset($zjTotal[$time]) ? $zjTotal[$time] : 0;
            $data['draw'][] = ['time'=>$time, 'cy'=>$cy, 'zj'=>$zj, 'cz'=>'<button type="button" onclick="makeDetailTable(\'draw\', \''.$time.'\')" class="btn btn-info">详情</button>'];
            $data['datas']['labels'][] = $time;
            $data['datas']['datasets'][0]['data'][] = $cy;
            $data['datas']['datasets'][1]['data'][] = $zj;
            $startTime += $addTime;
        }
        return $data;
    }


}
