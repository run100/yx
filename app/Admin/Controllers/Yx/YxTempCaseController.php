<?php
namespace App\Admin\Controllers\Yx;


use App\Http\Controllers\Controller;
use App\Models\Yx\YxBusiness;
use App\Models\Yx\YxFunc;
use App\Models\Yx\YxPurpose;
use App\Models\Yx\YxTempCase;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

class YxTempCaseController extends Controller
{

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('模版案例管理');
            $content->description('');
            $content->body($this->grid());
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('编辑模版案例');
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
            $content->header('新增模版案例');
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
        $grid = Admin::grid(YxTempCase::class, function (Grid $grid) {
            $grid->disableExport();
            $grid->model()->with('business')->with('purpose')->with('funcs');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name', '模版案例名');
                $filter->where(function($query){
                    if (!empty($this->input)) {
                        if(count($this->input) == 1) {
                            $query->where('cases', 'like', "%{$this->input[0]}%");
                        } else {
                            $query->where('cases', 'like', "%,%");
                        }
                    }
                }, '案例形式')->multipleSelect(['pc'=>'PC端', 'mobile'=>'手机端']);
                $filter->equal('business_id', '所属行业')->select(collect(YxBusiness::all())->mapWithKeys(function($item){
                    return [$item['id']=>$item['name']];
                })->all());
                $filter->equal('purpose_id', '主要用途')->select(collect(YxPurpose::all())->mapWithKeys(function($item){
                    return [$item['id']=>$item['name']];
                })->all());
                $filter->where(function($query){
                    if (!empty($this->input)){
                        $query->has('funcs', '>=', 1, 'and', function($query){
                            //判断是否为一级
                            $yxFunc = YxFunc::find($this->input);
                            if ($yxFunc->parent_id > 0) {
                                $query->where('yx_temp_case_func.func_id', $yxFunc->id);
                            } else {
                                $funcIds = YxFunc::where('parent_id', $yxFunc->id)->get()->pluck('id');
                                $query->whereIn('yx_temp_case_func.func_id', $funcIds);
                            }

                        });
                    }
                }, '主要功能')->select(YxFunc::otherSelects());
                $filter->between('created_at', '创建时间')->datetime();
            });
            $grid->id('ID')->sortable();
            $grid->column('name', '模版案例名');
            $grid->column('cases', '案例形式');
            $grid->column('business_name', '所属行业')->display(function(){
               return isset($this->business['name']) ? $this->business['name'] : '';
            });
            $grid->column('purpose_name', '主要用途')->display(function(){
                return isset($this->purpose['name']) ? $this->purpose['name'] : '';
            });
            $grid->column('func_name', '主要功能')->display(function(){
                return collect($this->funcs)->implode('name',',');
            });
            $grid->column('is_top_txt', '置顶')->display(function(){
                return $this->is_top == 1 ? '是' : '否';
            });
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
        });

        return $grid;
    }


    protected function form()
    {
        $form = Admin::form(YxTempCase::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name','模版案例名')->rules('required');
            $form->textarea('desc','案例简介')->help('尽量在30个字以内')->rules('required');
            $form->tags('keywords','专题关键字')->default([])->rules('required');
            $form->url('url', '案例链接')->rules('required');
            $form->checkbox('cases','案例形式')->options(['pc'=>'PC端', 'mobile'=>'手机端']);
            $form->image('picture', '案例图片')->help('建议：尺寸270*200，大小1M')->uniqueName()->move(date('Y/md'));
            $form->select('business_id', '所属行业')->options(YxBusiness::all()->mapWithKeys(function($item){
                return [$item->id => $item->name];
            }))->rules('required');
            $form->select('purpose_id', '主要用途')->options(YxPurpose::all()->mapWithKeys(function($item){
                return [$item->id => $item->name];
            }))->rules('required');
            $form->multipleSelect('funcs', '主要功能')->options(YxFunc::otherSelects())->rules('required');
            $form->radio('is_top', '是否置顶')->options(['否', '是'])->default(0);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
        $form->saved(function(Form $form){
           $fileName = md5('yingxiao:'.$form->model()->url).'.png';
           $path = uploads_path('yingxiao');
           $pathFile = $path.'/'.$fileName;
           if (!is_file($pathFile)) {
               if (!is_dir($path)) {
                   mkdir($path, 0777);
               }
               \QrCode::format('png')->size(165)->margin(0)
                   ->generate($form->model()->url, $pathFile);
           }
        });
        return $form;
    }

}