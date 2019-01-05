<?php
/**
 * Created by PhpStorm.
 * User: zhuzq
 * Date: 2018/11/26
 * Time: 08:45
 */

namespace App\Admin\Controllers;


use App\Admin\Extensions\HongbaoBillingExporter;
use App\Http\Controllers\Controller;
use App\Models\Hongbao\HongbaoBilling;
use App\Models\Hongbao\HongbaoLog;
use App\Models\Hongbao\HongbaoZhuli;
use App\Models\Project;
use App\Zhuanti\Redpacket\RedpacketOperator;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

class HongbaoController extends Controller
{

    public function majia($id)
    {
        $project = Project::where('id', $id)->first();
        if (\Request::isMethod('post')) {
            $fields = \Request::instance()->post('fields');
            if (isset($fields['n'])) {
                $startTime = strtotime($project->configs->hongbao->stime);
                $endTime = strtotime($project->configs->hongbao->etime);
                $time = time();
                foreach ($fields['n'] as $k => $v) {
                    if ($time > $startTime && $time < $endTime) {
                        $win = ['n' => $v, 'm' => bcmul($fields['m'][$k],100), 'i'=>uniqid()];
                        $winJson = wj_json_encode($win);
                        \RedisDB::connection()->zadd('prj:' . $id . ':wins', $time, $winJson);
                        $mj = ['n' => $v, 'm' => $fields['m'][$k], 'index' => $winJson];
                        \RedisDB::connection()->zadd('prj:' . $id . ':winsmj', $time, wj_json_encode($mj));
                    }
                }
            }
            $toastr = new MessageBag([
                'message' => '保存成功',
                'type' => 'success'
            ]);
            return back()->withInput(['fields' => $fields])->with(compact('toastr'));
        }
        return Admin::content(function (Content $content) use ($project) {
            $content->header('红包马甲');
            $content->description('');
            $datas = \RedisDB::connection()->zrange('prj:' . $project->id . ':winsmj', 0, 100);
            $content->row(view('admin::hongbao/majia', compact('datas', 'project')));
        });
    }

    public function del($id)
    {
        $mjJson = \Request::instance()->post('mj_json');
        $mj = wj_json_decode($mjJson);
        if ($mj) {
            $redis = \RedisDB::connection();
            $redis->zrem('prj:' . $id . ':winsmj', $mjJson);
            $redis->zrem('prj:' . $id . ':wins', $mj['index']);
        }
        return wj_json_message('删除成功');
    }

    public function setting($id)
    {
        $project = Project::repository()->findOneById($id);
        if (\Request::isMethod('post')) {
            return $this->storeSettingForm($project);
        }
        if (!isset($project->configs->hongbao)) {
            return back()->with(['toastr' => new MessageBag([
                'message' => '请先设置红包项目的红包相关参数',
                'type' => 'error'
            ])]);
        }
        if (!\Session::hasOldInput('hbsetting')) {
            $hongbao = @$project->configs->hongbao_setting;
            $hbsetting = wj_obj2arr($hongbao ?: []);
        } else {
            $hbsetting = old('hbsetting');
        }
        return Admin::content(function (Content $content) use ($project, $hbsetting) {
            $content->header('红包的配置项');
            $content->description('');
            $action = '/admin/projects/' . $project->id . '/hongbao/setting';
            $content->row(view('admin::hongbao/setting', compact('action', 'project', 'hbsetting')));
        });

    }

    public function billings($id)
    {
        $project = Project::repository()->findOneById($id);
        return Admin::content(function (Content $content) use ($project) {
            $content->header('红包账簿');
            $content->description('');
            $content->row(view('admin::hongbao/btn', compact('project')));
            $content->body(Admin::grid(HongbaoBilling::class, function (Grid $grid) use ($project) {
                $grid->disableCreation();
                $grid->disableRowSelector();
                $grid->disableActions();
                $grid->exporter(new HongbaoBillingExporter($grid));
                $grid->model()->where('project_id', '=', $project->id)->orderBy('id', 'desc');
                $grid->filter(function (Grid\Filter $filter) {
                    $filter->like('bill_no', '账单号');
                    $filter->like('wx_no', '微信账单号');
                    $filter->equal('player_id', '选手ID');
                    $filter->like('openid', 'OPEN ID');
                    $filter->between('created_at', '账单时间')->datetime();
                });
                $grid->id('ID')->sortable();
                $grid->column('player_id', '选手ID');
                $grid->column('bill_no', '账单号');
                $grid->column('wx_no', '微信账单号');
                $grid->column('money', '金额');
                $grid->column('data', '微信结果');

                $grid->created_at('账单时间');
            }));
        });
    }

    public function logs($id)
    {
        $project = Project::repository()->findOneById($id);
        return Admin::content(function (Content $content) use ($project) {
            $content->header('抽取记录');
            $content->description('');
            $content->row(view('admin::hongbao/btn', compact('project')));
            $content->body(Admin::grid(HongbaoLog::class, function(Grid $grid) use ($project){
                $grid->disableCreation();
                $grid->disableRowSelector();
                $grid->disableActions();
                $grid->disableExport();
                $grid->model()->where('project_id', '=', $project->id)->orderBy('id', 'desc');
                $grid->filter(function (Grid\Filter $filter) {
                    $filter->ilike('wx_name', '选手昵称');
                    $filter->equal('player_id', '选手ID');
                    $filter->like('openid', 'OPEN ID');
                    $filter->between('created_at', '抽取时间')->datetime();
                });
                $grid->id('ID')->sortable();
                $grid->column('player_id', '选手ID');
                $grid->column('wx_name', '选手昵称');
                $grid->column('is_win_txt', '是否中奖')->display(function(){
                    return $this->is_win ? '<span class="label label-success">中奖</span>' : '';
                });
                $grid->column('money', '红包金额');
                $grid->column('ip', '操作IP');

                $grid->created_at('抽取时间');
            }));
        });
    }

    public function zhulis($id)
    {
        $project = Project::repository()->findOneById($id);
        return Admin::content(function (Content $content) use ($project) {
            $content->header('助力记录');
            $content->description('');
            $content->row(view('admin::hongbao/btn', compact('project')));
            $content->body(Admin::grid(HongbaoZhuli::class, function(Grid $grid) use ($project){
                $grid->disableCreation();
                $grid->disableRowSelector();
                $grid->disableActions();
                $grid->disableExport();
                $grid->model()->where('project_id', '=', $project->id)->orderBy('id', 'desc');
                $grid->filter(function (Grid\Filter $filter) {
                    $filter->equal('player_id', '选手ID');
                    $filter->like('openid', 'OPEN ID');
                    $filter->like('zhuli_openid', '助力者openid');
                    $filter->like('zhuli_name', '助力者昵称');
                    $filter->between('created_at', '助力时间')->datetime();
                });
                $grid->id('ID')->sortable();
                $grid->column('player_id', '选手ID');
                $grid->column('openid', '选手OPENID');
                $grid->column('zhuli_name', '助力者昵称');
                $grid->column('zhuli_openid', '助力者OPENID');
                $grid->column('ip', '操作IP');

                $grid->created_at('助力时间');
            }));
        });
    }

    /**
     * 红包设置
     */
    private function storeSettingForm(Project $project)
    {
        $fields = \Request::input('hongbao');
        $hongbao = [
            $fields
        ];
        $time_plans = [];
        $diff = [];
        $plan = [];
        foreach ($hongbao as $k => $val) {
            //timeplan
            $startTime = 0;
            foreach ($val['timeplan'] as $key => $value) {
                $start = strtotime($value['start']);
                $end = strtotime($value['end']);
                if ($startTime>0 && $start<=$startTime) {
                    return back()->withInput(['fields' => $fields])->with(['toastr' => new MessageBag([
                        'message' => '时间计划有误，后面计划的开始时间必须大于前次的结束时间',
                        'type' => 'error'
                    ])]);
                }
                $startTime = $end;
                if ($start >= $end) {
                    return back()->withInput(['fields' => $fields])->with(['toastr' => new MessageBag([
                        'message' => '开始时间不能大于结束时间',
                        'type' => 'error'
                    ])]);
                }
                $plan[$k][$key]['start'] = $start;
                $plan[$k][$key]['end'] = $end;
                $diff[$k][$key] = $end - $start;
                if ($key == 0) {
                    $len[$k][$key] = 0;
                } else {
                    $len[$k][$key] = array_sum($diff[$k]) - $diff[$k][$key];
                }
                $plan[$k][$key]['len'] = $len[$k][$key];
            }
            $plans[$k]['time_total'] = array_sum($diff[$k]);
            $plans[$k]['plans'] = $plan[$k];
            $time_plans[$k]['total'] = (int)trim($val['total']);
            $time_plans[$k]['timeplan'] = $plans[$k];
        }
        $fields['timeplan'] = collect($time_plans[0]);
        $confs = $project->configs;
        $confs->hongbao_setting = collect($fields);
        $project->configs = $confs;
        $project->save();

        //初始化红包参数
        RedpacketOperator::instance($project->id)->initHongbao();

        $toastr = new MessageBag([
            'message' => '保存成功',
            'type' => 'success'
        ]);
        return back()->withInput(['hbsetting' => $fields])->with(compact('toastr'));
    }

}