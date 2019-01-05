<?php

namespace App\Admin\Controllers;

use App\Models\Project;
use App\Models\VoteLog;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\ModelForm;
use Illuminate\Database\Eloquent\Builder;

class VoteLogController extends Controller
{

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid($id));
        });
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($projectId, $id)
    {
        if ($this->form()->destroy($id)) {
            return response()->json([
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ]);
        }
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($projectId, $id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($id)
    {
        $project = Project::repository()->findOneById($id);

        return Admin::grid(VoteLog::class, function (Grid $grid) use ($project) {
            $form_design = @$project->conf_base_form_design ?: [];

            //找姓名列
            $name_field = null;

            //优先用 name 类型列
            foreach ($form_design as $field) {
                if ($field->type === 'name' && $field->key) {
                    $name_field = $field->key;
                    break;
                }
            }

            //若没有则用指定名称(['name'])的列
            if (!$name_field) {
                foreach ($form_design as $field) {
                    if ($field->type === 'string' && $field->key && in_array($field->field, ['name'])) {
                        $name_field = $field->key;
                        break;
                    }
                }
            }

            //若没有则找第一个列名中含name的列
            if (!$name_field) {
                foreach ($form_design as $field) {
                    if ($field->type === 'string' && $field->key && strpos($field->field, 'name') !== false) {
                        $name_field = $field->key;
                        break;
                    }
                }
            }

            $grid->model()->where('project_id', '=', $project->id)->orderBy('id', 'desc');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('player_id', '选手ID');
                $filter->equal('operator_uid', '操作人ID');
                $filter->like('openid', 'OPEN ID');
                $filter->between('created_at', '创建时间')->datetime();
                $filter->where(function (Builder $q) {
                    if ($this->input == 'B') {
                        $q->where('operator_uid', '>', 0);
                    } elseif ($this->input == 'N') {
                        $q->where('operator_uid', '=', 0);
                    }
                }, "加票类型")->radio([
                    '' => '所有',
                    'B' => '后台加票',
                    'N' => '正常加票',
                ]);
            });
            $grid->id('ID')->sortable();
            $grid->column('player_id', '选手ID');
            if ($name_field) {
                $grid->player()->$name_field('选手名称');
            }
            $grid->column('openid', '投票人OPEN ID');
            $grid->column('operator_uid', '操作人 ID');
            $grid->column('incr', '票数');
            $grid->column('note', '备注');

            $grid->created_at('时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(VoteLog::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->project_id('project_id');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
