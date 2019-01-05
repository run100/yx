<?php

namespace App\Admin\Controllers;

use App\Models\Project;
use App\Models\Jizi\JiziLog;
use App\Zhuanti\Jizi\JiziOperator;
use Illuminate\Support\MessageBag;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;

class JiziController extends Controller
{
    /**
     * 集字 Logs
     */
    public function logs($id)
    {
        $project = Project::repository()->findOneById($id);
        return Admin::content(function (Content $content) use ($project) {
            $content->header('集字/图记录');
            $content->description('');
            $content->row(view('admin::form/btn', compact('project')));
            $content->body($this->grid($project));
        });
    }

    /**
     * 集字设置
     */
    public function setting($id)
    {
        $project = Project::repository()->findOneById($id);
        if (\Request::isMethod('post')) {
            return $this->storeSettingForm($project);
        }

        if (!\Session::hasOldInput('jizi_fields')) {
            $form_font = @$project->configs->base_font_setting;
            $form_font = wj_obj2arr($form_font ?: []);
            \Session::flashInput(['jizi_fields' => $form_font]);
        }

        return Admin::content(function (Content $content) use ($project) {
            $content->header('设计字/图的配置项');
            $content->description('');

            $isLimitCount = [
                'Y' => '限量',
                'N' => '不限量',
            ];
            $action = '/admin/projects/' . $project->id . '/jizi/setting';
            $content->row(view('admin::jizi/setting', compact('action', 'project', 'isLimitCount')));
        });
    }

    /**
     * 集字设置
     */
    private function storeSettingForm(Project $project)
    {
        $fields = \Request::input('fields');
        $time_plans = [];
        $diff = [];
        $plan = [];
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
            $plans[$k]['time_total'] = array_sum($diff[$k]);
            $plans[$k]['plans'] = $plan[$k];
            $time_plans[$k]['key'] = trim($val['key']);
            $time_plans[$k]['name'] = trim($val['name']);
            $time_plans[$k]['is_limit_count'] = trim($val['is_limit_count']);
            $time_plans[$k]['total'] = trim($val['total']);
            $time_plans[$k]['timeplan'] = $plans[$k];
            if (empty($val['key'])) {
                unset($time_plans[$k]);
            }
        }
        $fields = collect($time_plans);
        $confs = $project->configs;
        $confs->base_font_setting = collect($fields)->values();
        $project->configs = $confs;
        $project->save();
        $jiziOperator = JiziOperator::instance($project->id);
        $hashs = $jiziOperator->getJzHash();
        foreach ($time_plans as $v) {
            if (!isset($hashs[$v['key']])) {
                $fontCache[$v['key']] = 0;
            } else {
                unset($hashs[$v['key']]);
            }
        }
        foreach ($hashs as $k => $v) {
            $jiziOperator->delJzHash($k);
        }
        !empty($fontCache) && $jiziOperator->setJzHash($fontCache);

        $toastr = new MessageBag([
            'message' => '保存成功',
            'type' => 'success'
        ]);
        return back()->withInput(['fields' => $fields])->with(compact('toastr'));
    }


    protected function grid($project)
    {
        return Admin::grid(JiziLog::class, function (Grid $grid) use ($project) {
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->model()->with('player')->where('project_id', '=', $project->id)->orderBy('id', 'desc');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('player_id', '选手ID');
                $filter->equal('operator_uid', '操作人ID');
                $filter->like('openid', 'OPEN ID');
                $filter->between('created_at', '集字/图时间')->datetime();
            });
            $grid->id('ID')->sortable();
            $grid->column('player_id', '选手ID');
            $grid->column('ply_name', '姓名')->display(function(){
                return isset($this->player['info']->name) ? $this->player['info']->name : '';
            });
            $grid->column('ply_phone', '手机号')->display(function(){
                return isset($this->player['info']->phone) ? $this->player['info']->phone : '';
            });
            $grid->column('openid', '投票人OPEN ID');
            $grid->column('operator_uid', '操作人 ID');
            $grid->column('content', '获得的字/图');
            $grid->column('note', '备注');

            $grid->created_at('集字/图时间');
        });
    }


    protected function form()
    {
        return Admin::form(JiziLog::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('project_id', '项目 ID');
            $form->text('merchant_id', '客户ID');
            $form->text('player_id', '选手ID');
            $form->hidden('operator_uid')->value(Admin::user()->id);
            $form->hidden('ip')->value(\Request::ip());
            $form->hidden('note')->value('人为操作');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}
