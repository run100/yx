<?php
namespace App\Admin\Controllers\Yx;


use App\Http\Controllers\Controller;
use App\Models\Yx\YxBanner;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class YxBannerController extends Controller
{

    public static $sCategorys = [
        YxBanner::CATEGORY_PC_INDEX => 'PC端首页 - 轮播图 - 1920*700',
        YxBanner::CATEGORY_PC_TEMP => 'PC端模版展示 - 头图 - 1920*200',
        YxBanner::CATEGORY_PC_CLASSIC => 'PC端经典案例 - 头图 - 1920*200',
        YxBanner::CATEGORY_MOB_INDEX => '手机端首页 - 头图 - 750*380',
    ];

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('广告位管理');
            $content->description('');
            $content->body($this->grid());
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('编辑广告位');
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
            $content->header('新增广告位');
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
        $grid = Admin::grid(YxBanner::class, function (Grid $grid) {
            $grid->disableExport();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('category', '位置')->select(self::$sCategorys);
                $filter->between('created_at', '创建时间')->datetime();
            });
            $grid->id('ID')->sortable();
            $grid->column('picture_name', '图片')->display(function(){
                return '<img width=150 src=\''.uploads_url($this->picture).'\'>';
            });
            $grid->column('category_name', '位置')->display(function(){
               return isset(self::$sCategorys[$this->category]) ? self::$sCategorys[$this->category] : '';
            });
            $grid->column('url_name', '跳转链接')->display(function(){
                return '<a target="_blank" href="'.$this->url.'">'.$this->url.'</a>';
            });
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
        });

        return $grid;
    }


    protected function form()
    {
        $form = Admin::form(YxBanner::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->select('category', '位置')->options(self::$sCategorys)->rules('required');
            $form->image('picture', '图片')->help('建议：尺寸请参考位置备注，大小1M')->rules('required')->uniqueName()->move(date('Y/md'));
            $form->text('url', '跳转链接')->help('备注：不填就不会跳转，填写请确保该链接可以正常访问！');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
        return $form;
    }

}