<?php

namespace App\Admin\Controllers;

use App\Models\Channel;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\UserController as BaseUserController;


class UserController extends BaseUserController
{
    public function form()
    {
        return Administrator::form(function (Form $form) {
            $form->display('id', 'ID');

            $form->text('username', trans('admin.username'))->rules('required');
            $form->text('name', trans('admin.name'))->rules('required');
            $form->select('channel_id', '所属频道')->options(Channel::get()->pluck('name', 'id'));
            $form->image('avatar', trans('admin.avatar'))->uniqueName()->move(date('Y/md'));
            $form->password('password', trans('admin.password'))->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });

            $form->ignore(['password_confirmation']);
            if(Admin::user()->isAdministrator()) {
                $form->multipleSelect('roles', trans('admin.roles'))->options(Role::all()->pluck('name', 'id'));
            }else{

                $form->multipleSelect('roles', trans('admin.roles'))->options(Role::where('name','=','小编')->pluck('name', 'id'));
            }

            if(Admin::user()->isAdministrator()) {
                $form->multipleSelect('permissions', trans('admin.permissions'))->options(Permission::all()->pluck('name', 'id'));
            }
            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }

                if (!@$form->channel_id) {
                    $form->channel_id = 0;
                }
            });
        });
    }

    protected function grid()
    {
        return Administrator::grid(function (Grid $grid) {

            $channels = Channel::get()->pluck('name', 'id');
            if (!Admin::user()->inRoles(['administrator'])) {

                $grid->model()->where('channel_id', '=', Admin::user()->channel_id);
            }
            $grid->filter(function ($filter) use ($channels) {
                $filter->in('channel_id', '频道')->multipleSelect($channels);
            });
            $grid->id('ID')->sortable();
            $grid->username(trans('admin.username'));
            $grid->name(trans('admin.name'));
            $grid->channel_id('频道')->display(function ($id) use ($channels) {
                return $channels[$id] ??  '';
            });
            $grid->roles(trans('admin.roles'))->pluck('name')->label();
            $grid->created_at(trans('admin.created_at'));
            $grid->updated_at(trans('admin.updated_at'));

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->getKey() == 1) {
                    $actions->disableDelete();
                }
                if((!Admin::user()->isAdministrator()) && ($actions->getKey() == 1)) {
                    $actions->disableEdit();
                }
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
            });
        });
    }

}
