<?php
namespace App\Admin\Controllers\Yx;


use App\Http\Controllers\Controller;
use App\Models\Yx\YxPartner;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class YxPartnerController extends Controller
{

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('合作商户管理');
            $content->description('');
            $content->body($this->grid());
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('编辑合作商户');
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

            $content->header('新增合作商户');
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
        $grid = Admin::grid(YxPartner::class, function (Grid $grid) {
            $grid->disableExport();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name', '合作商户名');
                $filter->between('created_at', '创建时间')->datetime();
            });
            $grid->id('ID')->sortable();
            $grid->column('name', '合作商户名');
            $grid->column('picture', '图片')->display(function(){
                return '<img src='.uploads_url($this->picture).' width="150" >';
            });
            $grid->sort('排序')->sortable();
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
        });

        return $grid;
    }


    protected function form()
    {
        $form = Admin::form(YxPartner::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name','合作商户名');
            $form->image('picture','图片')->help('尺寸220*124')->uniqueName()->move(date('Y/md'));
            $form->number('sort','排序')->help('从小到大排序');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
        return $form;
    }

}