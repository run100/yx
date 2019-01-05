<?php

namespace App\Admin\Controllers;

use App\Models\Merchant;
use App\Models\Wechat\Material;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class WechatMaterialController extends Controller
{

    public function index($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('微信素材列表');
            $content->description('');
            $content->body(Admin::grid(Material::class, function (Grid $grid) use ($id) {
                $grid->filter(function (Grid\Filter $filter) {
                    $filter->between('created_at', '创建时间')->datetime();
                });
                $grid->id('ID')->sortable();
                $grid->img('图片')->display(function () {
                    return '<img src="' . uploads_url($this->file) . '" width="150">';
                });
                $grid->column('media_id', '微信素材ID');
                $grid->created_at('创建时间');
                $grid->updated_at('更新时间');
                $grid->model()->where('merchant_id', '=', $id);
            }));
        });
    }


    public function edit($merchantId, $id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('编辑微信素材');
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

            $content->header('新增微信素材');
            $content->description('');

            $content->body($this->form());
        });
    }

    public function update($merchantId, $id)
    {
        return $this->form()->update($id);
    }

    public function destroy($merchantId, $id)
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

    public function store($id)
    {
        return $this->form($id)->store();
    }

    protected function form($id = 0)
    {
        $form = Admin::form(Material::class, function (Form $form) use ($id) {
            $form->hidden('id', 'ID');
            $form->display('media_id', '微信素材ID');
            $form->image('file', '图片')->uniqueName()->move(date('Y/md'))
                ->help('1M以内，支持JPG/GIF/PNG 文件格式');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
            $form->saving(function (Form $form) use ($id) {
                $id <= 0 && $id = $form->model()->merchant_id;
                $form->model()->merchant_id = $id;
                if ($form->file === null) {
                    $error = new MessageBag([
                        'title' => '错误',
                        'message' => '请选择上传的图片',
                    ]);
                    return back()->withInput()->with(compact('error'));
                }
            });
            $form->saved(function (Form $form) {
                $model = $form->model();
                $merchant = Merchant::find($model->merchant_id);
                $material = $merchant->wechat_app->material;
                if ($model->media_id!=null) {
                    $merchant->wechat_app->material->delete($model->media_id);
                }
                try {
                    $filePath = uploads_path($model->file);
                    $res = $material->uploadImage($filePath);
                    if (!isset($res->media_id)) {
                        throw new \Exception('上传素材出现问题！');
                    }
                    $model->media_id = $res->media_id;
                    $model->save();
                } catch (\Exception $e) {
                    $error = new MessageBag([
                        'title' => '错误',
                        'message' => $e->getMessage(),
                    ]);
                    return back()->withInput()->with(compact('error'));
                }
                $toastr = new MessageBag([
                    'message' => '保存成功',
                    'type' => 'success'
                ]);

                return redirect(route('material.index', ['merchant' => $model->merchant_id]))->with(compact('toastr'));
            });
        });
        return $form;
    }

}
