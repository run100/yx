<?php
namespace App\Admin\Controllers\Yx;


use App\Http\Controllers\Controller;
use App\Models\Yx\YxPurpose;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class YxPurposeController extends Controller
{

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('用途管理');
            $content->description('');
            $content->body($this->grid());
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('编辑用途');
            $content->description('');

            $content->body($this->form()->edit($id));

            $err = \Request::session()->get('errors');
            if ($err && $err->get('msg')) {
                $content->withError('错误', $err->get('msg'));
            }
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

            $content->header('新增用途');
            $content->description('');

            $content->body($this->form());
        });
    }


    public function show($id)
    {
        return redirect(\URL::current() . '/edit');
    }

    public function update($id)
    {
        return $this->form()->update($id);
    }

    public function destroy($id)
    {
        if ($this->form()->destroy($id)) {
            return response()->json([
                'status' => true,
                'message' => trans('admin.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('admin.delete_failed'),
            ]);
        }
    }

    public function store()
    {
        return $this->form()->store();
    }


    protected function grid()
    {
        $grid = Admin::grid(YxPurpose::class, function (Grid $grid) {
            $grid->disableExport();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name', '用途名');
                $filter->between('created_at', '创建时间')->datetime();
            });
            $grid->id('ID')->sortable();
            $grid->column('name', '用途名');
            $grid->sort('排序')->sortable();
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
        });

        return $grid;
    }


    protected function form()
    {
        $form = Admin::form(YxPurpose::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name','用途名');
            $form->number('sort','排序')->help('从小到大排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
        return $form;
    }

}