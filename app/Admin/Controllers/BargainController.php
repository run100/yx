<?php

namespace App\Admin\Controllers;


use App\Models\Bargain\BargainLog;
use App\Models\Project;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;


class BargainController extends Controller
{

    /**
     *  砍价记录
     */
    public function logs($id)
    {

        return Admin::content(function (Content $content) use ($id) {
            $content->header('砍价记录');
            $content->description('');
            $content->body(Admin::grid(BargainLog::class, function (Grid $grid) use ($id) {
                $grid->disableCreation();
                $grid->disableRowSelector();
                $grid->disableActions();
                $grid->actions(function ($actions) {
                    $actions->disableDelete();
                });
                $grid->model()->where('project_id', '=', $id)->orderBy('id', 'desc');
                $grid->filter(function (Grid\Filter $filter) {
                    $filter->equal('player_id', '邀请人ID');
                    $filter->equal('openid', '邀请人OPENID');
                    $filter->equal('zhuli_openid', '助力人OPENID');
                    $filter->equal('zhuli_name', '助力人昵称');
                    $filter->equal('operator_uid', '操作人ID');
                    $filter->between('created_at', '砍价时间')->datetime();
                });

                $grid->id('ID')->sortable();
                $grid->column('player_id', '邀请人ID');
                $grid->column('openid', '邀请人OPENID');
                $grid->column('name', '邀请人昵称');
                $grid->column('zhuli_name', '助力人昵称');
                $grid->column('zhuli_openid', '助力人OPENID');
                $grid->column('price', ' 砍价金额');
                $grid->column('operator_uid', '操作人 ID');
                $grid->created_at('砍价时间');
            }));
        });
    }

    public function exchangeQrCodeDownload($id)
    {
        $proj = Project::find($id);
        //创建临时文件用于存储二维码
        $tmpfile = tempnam('/tmp', 'ZT' . $id);
        //制作二维码并写入临时文件
        \QrCode::format('png')
            ->size(202)
            ->margin(0)
            ->generate(
                url($proj->path) . '?exchange_code=' . wj_encrypt($id . '-' . $proj->path),
                $tmpfile
            );
        return response()->download($tmpfile, 'QrCode.png');
    }

}
