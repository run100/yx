<?php

namespace App\Admin\Controllers;


use App\Admin\Extensions\PrizesLogExcelExporter;
use App\Models\Prizes\PrizesLog;
use App\Models\Prizes\ZhuliLog;
use App\Models\Project;
use App\Zhuanti\Prizes\PrizesOperator;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;


class PrizesController extends Controller
{

    /**
     * 中奖马甲
     */
    public function majia($id)
    {
        $project = Project::where('id', $id)->first();
        if (\Request::isMethod('post')) {
            $fields = \Request::instance()->post('fields');
            if (isset($fields['name'])) {
                $startTime = strtotime($project->configs->draw->stime);
                $endTime = strtotime($project->configs->draw->etime);
                $time = time();
                foreach ($fields['name'] as $k => $v) {
                    if ($time > $startTime && $time < $endTime) {
                        $win = ['name' => $v, 'prize' => $fields['prize'][$k], 'unique' => uniqid()];
                        $winJson = wj_json_encode($win);
                        $index = $endTime - $time;
                        \RedisDB::connection()->zadd('prj:' . $id . ':wins', $index, $winJson);
                        $mj = ['name' => $v, 'prize' => $fields['prize'][$k], 'index' => $winJson];
                        \RedisDB::connection()->zadd('prj:' . $id . ':winsmj', $index, wj_json_encode($mj));
                    }
                }

            }
            $toastr = new MessageBag([
                'message' => '保存成功',
                'type' => 'success'
            ]);
            return back()->withInput(['fields' => $fields])->with(compact('toastr'));
        }
        if (!isset($project->configs->base_form_prizes)) {
            $toastr = new MessageBag([
                'message' => '请先设置奖品',
                'type' => 'error'
            ]);
            return back()->with(compact('toastr'));
        }
        return Admin::content(function (Content $content) use ($project) {
            $content->header('中奖马甲');
            $content->description('');
            $datas = \RedisDB::connection()->zrange('prj:' . $project->id . ':winsmj', 0, 100);
            $content->row(view('admin::prizes/majia', compact('datas', 'project')));
        });
    }

    /*
     * 删除马甲
     */
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

    /**
     * 奖品设置
     */
    public function setting($projectId)
    {
        $project = Project::where('id', $projectId)->first();
        //判断是否配置了抽奖
        if (!isset($project->configs->draw)) {
            $toastr = new MessageBag([
                'message' => '请先设置抽奖模块',
                'type' => 'error'
            ]);
            return back()->with(compact('toastr'));
        }
        if (\Request::isMethod('post')) {
            return $this->storePrizesForm($project);
        }

        if (!\Session::hasOldInput('prizes_fields')) {
            $form_prizes = @$project->conf_base_form_prizes;
            $form_prizes = wj_obj2arr($form_prizes ?: []);
            \Session::flashInput(['prizes_fields' => $form_prizes]);
        }

        return Admin::content(function (Content $content) use ($project) {

            $content->header('奖品配置');
            $content->description('');

            $dbIndexes = PrizesLog::$sTypes;

            $content->row(view('admin::prizes/setting', compact('dbIndexes', 'project')));
        });
    }

    /**
     * 抽奖记录
     */
    public function logs($id)
    {
        $project = Project::repository()->findOneById($id);

        return Admin::content(function (Content $content) use ($project, $id) {
            $content->header('抽奖记录');
            $content->description('');
            $content->row(view('admin::form/btn', compact('project')));
            $content->body($this->grid($project));
        });
    }

    /**
     * 助力记录
     */
    public function zhuliLogs($id)
    {
        $project = Project::repository()->findOneById($id);

        return Admin::content(function (Content $content) use ($project) {
            $content->header('助力记录');
            $content->description('');
            $content->row(view('admin::form/btn', compact('project')));
            $content->body(Admin::grid(ZhuliLog::class, function (Grid $grid) use ($project) {
                $grid->disableCreation();
                $grid->disableRowSelector();
                $grid->disableActions();
                $grid->actions(function ($actions) {
                    $actions->disableDelete();
                });
                $grid->model()->where('project_id', '=', $project->id)->orderBy('id', 'desc');
                $grid->filter(function (Grid\Filter $filter) {
                    $filter->equal('player_id', '邀请人ID');
                    $filter->equal('openid', '邀请人OPENID');
                    $filter->equal('operator_uid', '操作人ID');
                    $filter->between('created_at', '助力时间')->datetime();
                });

                $grid->id('ID')->sortable();
                $grid->column('id', 'ID');
                $grid->column('player_id', '邀请人ID');
                $grid->column('openid', '邀请人OPENID');
                $grid->column('zhuli_name', '助力人昵称');
                $grid->column('zhuli_openid', '助力人OPENID');
                $grid->column('operator_uid', '操作人 ID');
                $grid->created_at('助力时间');
            }));
        });
    }

    /**
     * 抽奖记录修改
     * @param $projectId
     * @param $id
     * @return Content
     */
    public function logsEdit($projectId, $id)
    {
        $prizesLog = PrizesLog::find($id);
        if (\Request::isMethod('put')) {
            $fields = \Request::instance()->post();
            if ($prizesLog->type != PrizesLog::TYPE_TEXT) {
                $prizesLog->is_draw = $fields['is_draw'];
                $prizesLog->save();
            }
            $toastr = new MessageBag([
                'message' => '保存成功',
                'type' => 'success'
            ]);
            return back()->with(compact('toastr'));
        }
        return Admin::content(function (Content $content) use ($id, $prizesLog) {
            $content->header('修改抽奖记录');
            $content->description('');
            $content->body($this->form($prizesLog)->edit($id));
        });
    }

    private function grid($project)
    {
        return Admin::grid(PrizesLog::class, function (Grid $grid) use ($project) {
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
            });
            $grid->model()->with('player')->where('project_id', '=', $project->id)->orderBy('id', 'desc');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('player_id', '选手ID');
                $filter->equal('wx_name', '昵称');
                $filter->equal('operator_uid', '操作人ID');
                $filter->like('phone', '手机号');
                $filter->like('draw_info', '券码');
                $filter->equal('is_draw', '是否领取')->radio([
                    '' => '所有',
                    0 => '未领取',
                    1 => '已领取',
                ]);
                $filter->in('type', '奖品类型')->checkbox(PrizesLog::$sTypes);
                $filter->between('created_at', '抽奖时间')->datetime();
            });

            $grid->id('ID')->sortable();
            $grid->column('player_id', '选手ID');
            $grid->column('wx_name', '昵称');
            $grid->column('ply_name', '姓名')->display(function () {
                return isset($this->player['info']->name) ? $this->player['info']->name : '';
            });
            $grid->column('ply_phone', '手机')->display(function () {
                return isset($this->player['info']->phone) ? $this->player['info']->phone : '';
            });
            $grid->column('openid', 'openid');
            $grid->column('operator_uid', '操作人 ID');
            $grid->column('name', '奖品名称');
            $grid->column('win_text', '是否中奖')->display(function () {
                return $this->win_text;
            });
            $grid->column('draw_text', '是否领取')->display(function () {
                return $this->draw_text;
            });
            $grid->created_at('抽奖时间');
            $grid->exporter(new PrizesLogExcelExporter());
        });
    }

    private function form($prizesLog)
    {
        return Admin::form(PrizesLog::class, function (Form $form) use ($prizesLog) {
            $form->display('id', 'ID');
            $form->display('openid', 'OPEN ID');
            $form->display('wx_name', '昵称');
            $form->text('ply_name', '姓名')->default($prizesLog->ply_name);
            $form->text('ply_phone', '手机号')->default($prizesLog->ply_phone);
            $form->display('name', '奖品名称');
            $form->radio('type', '类型')->options(PrizesLog::$sTypes);
            $form->display('draw_info', '兑奖码');
            $form->radio('is_draw', '是否领取')->options([0 => '未领取', '1' => '已领取']);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
            $form->ignore(['ply_name', 'ply_phone', 'type']);
            if ($prizesLog->type == PrizesLog::TYPE_TEXT) {
                $form->disableSubmit();
            }
            $form->setAction(route('prizes.logs_edit', ['project' => $prizesLog->project_id, 'id' => $prizesLog->id]));
        });
    }


    /**
     * 奖品数据保存
     */
    private function storePrizesForm(Project $project)
    {
        $fields = \Request::input('fields');
        $diff = [];
        $plan = [];
        $time_plans = [];
        foreach ($fields as $k => $val) {
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
            $time_plans[$k]['is_limit'] = isset($val['is_limit']) && $val['is_limit'] == 1 ? 1:0;
            $time_plans[$k]['is_yes'] = isset($val['is_yes']) && $val['is_yes'] == 1 ? 1:0;
            $plans[$k]['time_total'] = array_sum($diff[$k]);
            $plans[$k]['plans'] = $plan[$k];
            $time_plans[$k]['key'] = trim($val['key']);
            $time_plans[$k]['name'] = trim($val['name']);
            $time_plans[$k]['type'] = (int)trim($val['type']);
            $time_plans[$k]['total'] = (int)$val['total'];
            $time_plans[$k]['peizhi'] = trim($val['peizhi']);
            $time_plans[$k]['tips'] = trim($val['tips']);
            $time_plans[$k]['timeplan'] = $plans[$k];
            if (empty($val['key'])) {
                unset($time_plans[$k]);
            }
        }
        $fields = collect($time_plans);
        $confs = $project->configs;
        $confs->base_form_prizes = collect($fields)->values();
        $project->configs = $confs;
        $project->save();
        $prizesOperator = PrizesOperator::instance($project->id);
        $hashs = $prizesOperator->getPrizeHash();
        $prizeCache = [];
        foreach ($time_plans as $v) {
            if (!isset($hashs[$v['key']])) {
                $prizeCache[$v['key']] = 0;
            } else {
                unset($hashs[$v['key']]);
            }
        }
        foreach ($hashs as $k => $v) {
            $prizesOperator->delPrizeHash($k);
        }
        !empty($prizeCache) && $prizesOperator->setPrizeHash($prizeCache);

        $toastr = new MessageBag([
            'message' => '保存成功',
            'type' => 'success'
        ]);
        return back()->withInput(['fields' => $fields])->with(compact('toastr'));
    }


}
