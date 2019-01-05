<?php

namespace App\Admin\Controllers\Yx;

use App\Models\Yx\YxFunc;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use Illuminate\Routing\Controller;

class YxFunctionController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('功能管理');
            $content->description('列表');

            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action('/admin/yxfunction');
                    $form->select('parent_id', '父级功能')->options(YxFunc::selectOptions());
                    $form->text('name', '功能名')->rules('required');
                    $form->image('picture', '图标')->help('建议：80*80')->uniqueName()->move(date('Y/md'));
                    $form->image('background', '背景图')->help('建议：94*94')->uniqueName()->move(date('Y/md'));
                    $form->radio('is_feature', '特色功能')->options([0=>'否', 1=>'是'])->default(0);
                    $form->text('desc', '描述');
                    $column->append((new Box('新增', $form))->style('success'));
                });
            });
        });
    }

    /**
     * Redirect to edit page.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        return redirect()->route('yxpurpose.edit', ['id' => $id]);
    }

    protected function treeView()
    {
        return YxFunc::tree(function (Tree $tree) {
            $tree->disableCreate();

            $tree->branch(function ($branch) {
                $payload = "<strong>{$branch['name']}</strong>";
                return $payload;
            });
        });
    }

    /**
     * Edit interface.
     *
     * @param string $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('功能管理');
            $content->description('修改功能');

            $content->row($this->form()->edit($id));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return YxFunc::form(function (Form $form) {
            $form->display('id', 'ID');

            $form->select('parent_id', '父级功能')->options(YxFunc::selectOptions());
            $form->text('name', '功能名')->rules('required');
            $form->image('picture', '图标')->help('建议：80*80')->uniqueName()->move(date('Y/md'));
            $form->image('background', '背景图')->help('建议：94*94')->uniqueName()->move(date('Y/md'));
            $form->radio('is_feature', '特色功能')->options([0=>'否', 1=>'是'])->default(0);
            $form->text('desc', '描述');
            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
        });
    }

}
