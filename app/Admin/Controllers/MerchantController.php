<?php

namespace App\Admin\Controllers;

use App\Admin\AbstractField;
use App\Admin\AbstractRowAction;
use App\Admin\CleanContent;
use App\Models\Channel;
use App\Models\Merchant;

use App\Models\Wechat\AutoReply;
use App\Models\Wechat\Menu;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Column;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    use ModelForm;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * 页面: 列表页
     *
     * @return Content
     */
    public function index()
    {

        //注册自定义 Field 样式
        Column::extend('pre_auth_url', PreAuthUrlField::class);
        Column::extend('wechat_info', WechatInfoField::class);


        return Admin::content(function (Content $content) {

            $content->header('客户管理');
            $content->description('管理微信客户及预授权');


            //构造 Grid 字段
            $grid = Admin::grid(Merchant::class, function (Grid $grid) {
                $channels = Channel::get()->pluck('name', 'id');
                if (!Admin::user()->inRoles(['administrator'])) {

                    $grid->model()->where('channel', '=', Admin::user()->channel_id);
                }
                
                $grid->id('ID')->sortable();

                $grid->name('客户名称');

                $grid->channel('所属频道')->display(function ($id) use ($channels) {
                    return $channels[$id] ?? '';
                });

                $grid->appinfo('公众号信息')->wechat_info();

                $grid->url('预授权地址')->pre_auth_url();

                $grid->created_at('创建时间');
            });

            //构造 Grid 行操作
            $grid->actions(function (Actions $actions) {
                $actions->prepend('<a href="'.route('material.index', ['merchant' => $actions->getKey()]).'" title="微信素材"><i class="fa fa-th"></i></a>');
                if ($actions->row->type == 1) {
                    $actions->append(new RefreshAction($actions->getKey()));
                }
                $actions->prepend(linkto(' <i class="fa fa-cloud-download"></i> ', route('merchants.refresh_info', ['merchant' => $actions->getKey()])));

                $actions->prepend(linkto(' <i class="fa fa-user"></i> ', route('merchants.manage', ['merchant' => $actions->getKey()])));
            });

            $grid->model()->orderBy('id', 'desc');


            $content->body($grid);
        });
    }

    /**
     * 页面: 公众号管理
     * @param $merchant
     * @return Content
     */
    public function manage(Merchant $merchant)
    {
        return Admin::content(function (Content $content) use ($merchant) {

            $content->header('公众号管理');
            $content->description('');


            $tab = \Session::get('tab') ?: \Request::input('tab');
            if (!$tab) {
                if (\Request::routeIs('merchants.reply.edit')) {
                    $tab = 'reply';
                } elseif (\Request::routeIs('merchants.menu.edit')) {
                    $tab = 'menu';
                }
            }


            $otab = new Tab();
            $otab->add('自动回复', $this->manageTabAutoReply($merchant), $tab == 'reply');
            $otab->add('自定义菜单', $this->manageTabMenu($merchant), $tab == 'menu');



            $content->body($otab);
        });
    }

    protected function manageTabAutoReply(Merchant $merchant)
    {
        $content = new CleanContent();
        $content->body(function (Row $row) use ($merchant) {
            $row->column(6, $this->manageTabAutoReplyTable()->render());
            $row->column(6, function (\Encore\Admin\Layout\Column $column) use ($merchant) {
                $action = route('merchants.reply', ['merchant' => \Request::route('merchant')]);
                if (\Request::routeIs('merchants.reply.edit')) {
                    $reply = \Session::get('editing_reply');
                    $action .= '/' . \Request::route('reply');
                } else {
                    $reply = [];
                }

                $form = new \Encore\Admin\Widgets\Form($reply);
                $form->action($action);
                $form->method('POST');
                $form->radio('match_mode', '匹配模式')
                    ->options(['text' => '全匹配', 'prefix' => '前缀', 'contains' => '包含', 'regexp' => '正则'])
                    ->rules('required')->help('公版专题匹配模式——正则');
                $form->text('keyword', '关键字')->rules('required')->help('公版专题设置：(?i)^编号前缀(?&lt;num&gt;\d{编号长度})$');
                $form->radio('reply_mode', '应答模式')->options(['text' => '文本', 'project' => '逻辑处理'])->rules('required')->help('公版专题应答模式——逻辑处理');
                $form->textarea('reply', '应答对象')->rules('required')->help('公版专题应答对象：<br>投票 — vote:/tp-1(路径)<br>集字 — jizi:/jz99(路径)<br>抽奖 — prizes:/cj99(路径)<br>砍价 — bargain:/kj99(路径)<br>红包 — hongbao:/hb99(路径)<br>图片 — material:微信素材ID');
                $form->dateTimeRange('start_at', 'end_at', '有效期');
                $column->append((new Box('关键字回复', $form))->style('success'));



                $path = \Request::getPathInfo();
                $form = new \Encore\Admin\Widgets\Form([
                    'enabled'   => @$merchant->conf_wechat_enable_subscribe_reply,
                    'text'      => @$merchant->conf_wechat_subscribe_reply,
                ]);
                $form->action(str_replace_last('/manage', '/update_subscribe_reply', $path));
                $form->method('POST');
                $form->switch('enabled', '启用');
                $form->textarea('text', '内容');
                $column->append((new Box('关注回复', $form))->style('success'));
            });
        });

        return $content;
    }


    protected function manageTabAutoReplyTable()
    {

        $content = new CleanContent();


        $headers = ['关键字', '应答'];
        $rows = [];

        $merchant = \Request::route('merchant');
        $replys = AutoReply::repository()->findByMerchantId($merchant->id, [], ['orderby'=>'id desc']);
        $link = route('merchants.reply', ['merchant' => $merchant->id]);

        Admin::script(script_in_php(<<<eot
<script>
        $('.merchant-reply-delete').click(function() {
            var id = $(this).data('id');
            swal({
              title: "确认删除?",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#DD6B55",
              confirmButtonText: "确认",
              closeOnConfirm: false,
              cancelButtonText: "取消"
            }).then(function(ok){
                if (!ok) {
                    return false;
                }
        
                $.ajax({
                    method: 'post',
                    url: '$link/' + id,
                    data: {
                        _method:'delete',
                        _token:LA.token,
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

                        if (typeof data === 'object') {
                            if (data.status) {
                                swal(data.message, '', 'success');
                            } else {
                                swal(data.message, '', 'error');
                            }
                        }
                    }
                });
            });
        });
</script>
eot
));

        foreach ($replys as $reply) {
            $rows[] = [
                "{$reply->tmode}:" . htmlentities($reply->keyword),
                "{$reply->rmode}:{$reply->reply}",
                <<<eot
<a href="$link/{$reply->id}/edit"><i class="fa fa-edit"></i></a> <a class="merchant-reply-delete" href="javascript:;" data-id="{$reply->id}"><i class="fa fa-trash"></i></a>
eot

            ];
        }

        $table = new Table($headers, $rows);


        $link_edit = route('merchants.manage', ['merchant' => \Request::route('merchant')]);
        $content->row((new Box('操作', <<<eot
<a class="btn btn-info" href="$link_edit">新增</a>
eot
        ))->style('success'));
        $content->row($table);
        return $content;
    }

    protected function manageTabMenu(Merchant $merchant)
    {
        $content = new CleanContent();
        $content->body(function(Row $row) use ($merchant) {
            $row->column(6, $this->manageTabMenuTreeView()->render());
            $row->column(6, function (\Encore\Admin\Layout\Column $column) use ($merchant) {
                $ref = new \ReflectionObject($this->tree);
                $f = $ref->getProperty('elementId');
                $f->setAccessible(true);
                $tree_id = $f->getValue($this->tree);

                $link_publish = route('merchants.menu.publish', ['merchant' => \Request::route('merchant')]);
                $link_edit = route('merchants.manage', ['merchant' => \Request::route('merchant')]);

                $column->append((new Box('操作', <<<eot
<a class="btn btn-info" href="$link_edit?tab=menu">新增</a>
<a class="btn btn-info $tree_id-save" href="javascript:;">保存</a>
<a class="btn btn-info" href="$link_publish" onclick="return confirm('用当前配置覆盖线上版本?')">发布</a>
eot
))->style('success'));


                $action = route('merchants.menu', ['merchant' => \Request::route('merchant')]);
                if (\Request::routeIs('merchants.menu.edit')) {
                    $menu = \Session::get('editing_menu');
                    $action .= '/' . \Request::route('menu');
                } else {
                    $menu = [];
                }
                $form = new \Encore\Admin\Widgets\Form($menu);
                $form->disablePjax();
                $form->action($action);
                $form->select('parent_id', '父级菜单')->options(Menu::rootSelectOptions($merchant->id));
                $form->text('title', '标题')->rules('required');
                $form->select('type', '类型')->options([
                    'view'          => '跳转URL',
                    'miniprogram'   => '跳转小程序',
                    'media_id'      => '下发素材',
                    'view_limited'  => '跳转素材',
                    'scancode_push' => '扫一扫跳转',
                    'scancode_waitmsg' => '扫一扫消息',
                    'click'         => '自定义消息'
                ]);
                $form->text('uri', '链接');
                $form->text('target', '应答对象');
                $column->append((new Box('新增', $form))->style('success'));
            });
        });

        return $content;
    }

    protected function manageTabMenuTreeView()
    {
        $merchant = \Request::route('merchant');
        return $this->tree = Menu::tree(function (Tree $tree) use ($merchant) {
            $tree->path = str_replace_last('/0/edit', '', route('merchants.menu.edit', ['menu' => 0, 'merchant' => $merchant->id]));
            $tree->disableCreate();
            $tree->disableRefresh();
            $tree->disableSave();
            $tree->nestable(['maxDepth' => 2]);

            $tree->branch(function ($branch) {
                $payload = "<strong>{$branch['title']}</strong>";

                if (!isset($branch['children'])) {
                    $uri = $branch['uri'];
                    $payload .= "&nbsp;&nbsp;&nbsp;<a href=\"javascript:;\" class=\"dd-nodrag\">$uri</a>";
                }

                return $payload;
            });
        })->query(function($qb) use ($merchant) {
            return $qb->where('merchant_id', '=', $merchant->id);
        });
    }

    /**
     * 页面: 编辑页
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('修改客户资料');
            $content->description('');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * 页面: 新建页
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('创建客户');
            $content->description('');

            $content->body($this->form());
        });
    }

    /**
     * 构造表单
     *
     * @return Form
     */
    protected function form()
    {
        $form = Admin::form(Merchant::class, function (Form $form) {
            $form->hidden('id', 'ID');
            $form->text('name', '客户名称');
            $form->select('channel', '所属频道')->options(Channel::all()->pluck('name','id'));
            $form->hidden('pre_auth_code')->default(Str::random(32));
            $form->radio('type', '类型')->options(Merchant::model()->listType())->default(1);
            $form->text('appid', 'AppId')->addElementClass('for-type type-2');
            $form->text('configs.dev_secret', 'AppSecret')->addElementClass('for-type type-2');
            $form->text('configs.dev_token', 'Token')->addElementClass('for-type type-2');
            $form->text('configs.dev_aes_key', 'AESKey')->addElementClass('for-type type-2');
            $form->html(function () {
                if (!$this->mch_key) {
                    return '商户号: 未绑定';
                }

                return "商户号: " . preg_replace('@\d{4}(\d{4})$@', '****$1', $this->clear_mch_id);
            }, '商户支付信息');
            $form->text('mch_id', '商户号');
            $form->hidden('mch_key');
            $form->text('mch_apikey', 'API密钥');
            $form->file('mch_apip12', 'API证书');

            $form->html(function () {
                return WechatInfoField::displayRow($this);
            }, '公众号信息');

            $form->display('created_at', '创建时间');



            Admin::script(script_in_php(<<<eot
<script>
$(function() {
    
    function update_form_group_for_type(sel) {
        $('.for-type').closest('.form-group').addClass('hidden');
        $('.for-type.type-' + sel).closest('.form-group').removeClass('hidden');
    }
    
    var checker = $('input[type=radio][name=type]').iCheck({radioClass:'iradio_minimal-blue'})
        .on('ifChanged', function () {
            if (this.checked) {
                update_form_group_for_type($(this).val());
            }
        });
    
    update_form_group_for_type($('input[type=radio][name=type]:checked').val());
})
</script>
eot
));
        });


        $form->saving(function (Form $form) {
            if ($form->input('mch_id')) {
                /** @var UploadedFile $p12 */
                $p12 = $form->input('mch_apip12');
                $p12 = !$p12 ? '' : file_get_contents($p12->path());
                $data = sprintf('%s:%s:%s', $form->input('mch_id'), $form->input('mch_apikey'), $p12);
                $form->input('mch_key', encrypt($data));
            }
            $form->forgetInput(['mch_id', 'mch_apikey', 'mch_apip12']);
        });

        $form->saved(function (Form $form) {
            /** @var Merchant $model */
            $model = $form->model();
            if ($model->type == 2) {
                $model->refreshAuthorizerInfo(true);
            }
        });

        return $form;
    }


    /**
     * 动作: 刷新预授权码
     * @param Merchant $merchant
     * @return \Illuminate\Http\Response
     */
    public function refreshAuth(Merchant $merchant)
    {
        $merchant->pre_auth_code = Str::random(32);
        $merchant->save();

        return wj_json_message('授权地址已生成，请不要将授权地址发给无关人员,24小时内有效,请联系客户尽快完成授权。');
    }



    /**
     * 动作: 刷新公众号信息
     * @param Merchant $merchant
     * @return \Illuminate\Http\Response
     */
    public function refreshInfo(Merchant $merchant)
    {
        $merchant->refreshAuthorizerInfo(true);

        $toastr = new MessageBag([
            'message'   => '公众号信息更新成功',
            'type'      => 'success'
        ]);

        return back()->with(compact('toastr'));
    }


    public function updateSubscribeReply(Merchant $merchant)
    {
        $confs = $merchant->configs;
        $confs->wechat_enable_subscribe_reply = \Request::input('enabled') === 'on';
        $confs->wechat_subscribe_reply = \Request::input('text');
        $merchant->configs = $confs;
        $merchant->save();

        $toastr = new MessageBag([
            'message'   => '关注回复保存成功',
            'type'      => 'success'
        ]);
        return back()->with(compact('toastr'));
    }


    public function editReply(Merchant $merchant, $reply)
    {
        \Session::flash('tab', 'reply');
        \Session::flash('editing_reply', AutoReply::repository()->retrieveByPK($reply)->toArray());
        return $this->manage($merchant);
    }

    public function storeReply(Merchant $merchant, AutoReply $reply = null)
    {
        if (!$reply) {
            $reply = new AutoReply();
            $reply->merchant_id = $merchant->id;
        }
        $reply->fill(wj_mask(\Request::all(), ['match_mode', 'reply_mode', 'keyword', 'reply', 'start_at', 'end_at']));
        $reply->save();

        $toastr = new MessageBag([
            'message'   => '自动回复保存成功',
            'type'      => 'success'
        ]);

        $tab = 'reply';

        return back()->with(compact('toastr', 'tab'));
    }

    public function deleteReply(Merchant $merchant, AutoReply $reply = null)
    {
        $reply->delete();
        return response()->json([
            'status'  => true,
            'message' => trans('admin.delete_succeeded'),
        ]);
    }

    public function storeMenu(Merchant $merchant, Menu $menu = null)
    {
        if (!$menu) {
            $menu = new Menu();
            $menu->merchant_id = $merchant->id;

            if (\Request::has('_order')) {
                \Session::flash('tab', 'menu');
                $menu->save();
                return response('ok');
            }
        }
        $menu->fill(wj_mask(\Request::all(), ['title', 'type', 'uri', 'target', 'parent_id']));
        $menu->save();


        $toastr = new MessageBag([
            'message'   => '本地菜单已更新,等待发布',
            'type'      => 'success'
        ]);

        $tab = 'menu';

        return back()->with(compact('toastr', 'tab'));
    }

    public function deleteMenu(Merchant $merchant, Menu $menu = null)
    {
        $menu->delete();
        \Session::flash('tab', 'menu');
        return response()->json([
            'status'  => true,
            'message' => trans('admin.delete_succeeded'),
        ]);
    }

    public function editMenu(Merchant $merchant, $menu)
    {
        \Session::flash('tab', 'menu');
        \Session::flash('editing_menu', Menu::repository()->retrieveByPK($menu)->toArray());
        return $this->manage($merchant);
    }

    public function publishMenu(Merchant $merchant)
    {
        $tree = Menu::tree()->query(function($qb) use ($merchant) {
            return $qb->where('merchant_id', '=', $merchant->id);
        });
        $items = $tree->getItems();
        $ret = [];

        $parser = function($item, &$menu) {
            if (in_array($item['type'], ['media_id', 'view_limited'])) {
                $menu['media_id'] = $item['uri'];
            } elseif ($item['type'] == 'view') {
                $menu['url'] = $item['uri'];
            } elseif ($item['type'] == 'miniprogram') {
                $segments = explode(':', $item['uri'], 3);
                $menu['appid'] = $segments[0];
                $menu['pagepath'] = $segments[1];
                if (@$segments[2]) {
                    $menu['url'] = $segments[2];
                }
            } else {
                $menu['key'] = $item['uri'];
            }
        };

        do {
            if (count($items) > 3) {
                $toastr = new MessageBag([
                    'message'   => '一级菜单不可以超过3个',
                    'type'      => 'error'
                ]);
                break;
            }

            foreach ($items as $item) {
                $children = @$item['children'];

                $menu = ['name' => $item['title']];
                if (count($children)) {
                    if (count($children) > 5) {
                        $toastr = new MessageBag([
                            'message'   => '二级菜单不可以超过5个',
                            'type'      => 'error'
                        ]);
                        break 2;
                    }

                    foreach ($children as $subitem) {
                        $submenu = ['name' => $subitem['title']];
                        $submenu['type'] = $subitem['type'];
                        $parser($subitem, $submenu);
                        $menu['sub_button'][] = $submenu;
                    }
                } else {
                    $menu['type'] = $item['type'];
                    $parser($item, $menu);
                }
                $ret[] = $menu;
            }

            $merchant->wechat_app->menu->destroy();
            $merchant->wechat_app->menu->add($ret);

            $toastr = new MessageBag([
                'message'   => '菜单已发布',
                'type'      => 'success'
            ]);
        } while(0);

        $tab = 'menu';

        return back()->with(compact('toastr', 'tab'));
    }
}

/**
 * 刷新预授权码按钮
 * @package App\Admin\Controllers
 */
class RefreshAction extends AbstractRowAction
{
    protected static function script()
    {
        return <<<eot
<script>
$('.grid-row-refresh').unbind('click').on('click', function(e) {
    if (confirm('将重新生成预授权链接，是否继续?')) {
        $.post($(this).data('action'), function(ret) {
            $.pjax.reload('#pjax-container');
            alert(ret.data);
        }, 'json');
    }
});
</script>
eot;
    }

    protected function render()
    {
        $url = route('merchants.refresh_auth', ['merchant' => $this->id]);
        return <<<eot
<a href="javascript:void(0);" class="grid-row-refresh" data-action="$url">
    <i class="fa fa-refresh"></i>
</a>
eot;
    }
}

/**
 * 字段: 预授权地址
 * @package App\Admin\Controllers
 */
class PreAuthUrlField extends AbstractField
{
    protected static function script()
    {
        return <<<eot
<script>
$('.grid-row-clipboard').unbind('click').on('click', function(e) {
    this.select();
    document.execCommand('copy');
    alert('已复制到剪贴板'); 
});
</script>
eot;
    }


    protected function render()
    {
        $row = $this->row;

        if ($row->type == 2) {
            return '无须授权';
        }

        if (!$row->pre_auth_code) {
            return '无';
        }

        $encoded = htmlentities($row->pre_auth_url);
        return <<<eot
<textarea title="点击复制" readonly="readonly" class="grid-row-clipboard" style="border: dashed 1px #ccc; border-radius: 5px; padding: 0 10px; display: block; width: 100%;" rows="5">$encoded</textarea>
eot;
    }
}

/**
 * 字段: 公众号信息
 * @package App\Admin\Controllers
 */
class WechatInfoField extends AbstractField
{
    protected function render()
    {
        $row = $this->row;

        if ($row->type == 2) {
            if ($row->wechat_qrcode_text) {
                $qrcode = \QrCode::format('png')->size(80)->margin(0)->generate($row->wechat_qrcode_text);
                $qrcode = base64_encode($qrcode);
            } else {
                $qrcode = null;
            }

            return <<<eot
<table style="width: 350px;">
    <tr>
        <td colspan="2">
            <img src="data:image/png;base64, $qrcode" style="width: 80px;" />
        </td>
    </tr>
    <tr>
        <td width="50">AppID:</td>
        <td>{$row->appid}</td>
    </tr>
    <tr>
        <td>URL:</td>
        <td>http://{$_SERVER['HTTP_HOST']}/callbacks/app/{$row->appid}/event</td>
    </tr>
</table>
eot;
        }

        if (!$row->is_authed) {
            return '未授权';
        }

        if ($row->wechat_qrcode_text) {
            $qrcode = \QrCode::format('png')->size(80)->margin(0)->generate($row->wechat_qrcode_text);
            $qrcode = base64_encode($qrcode);
        } else {
            $qrcode = null;
        }

        $businfo = $row->wechat_business_info_str ?: '-';

        return <<<eot
<table style="width: 350px;">
    <tr>
        <td colspan="2">
            <img src="{$row->wechat_head_img}" style="width: 80px;" />
            <img src="data:image/png;base64, $qrcode" style="width: 80px;" />
        </td>
    </tr>
    <tr>
        <td width="50">{$row->wechat_account_type}:</td>
        <td>{$row->wechat_nickname} (<span style="color:red">{$row->wechat_alias}</span>)</td>
    </tr>
    <tr>
        <td>原始ID:</td>
        <td>{$row->wechat_username}</td>
    </tr>
    <tr>
        <td>AppID:</td>
        <td>{$row->appid}</td>
    </tr>
    <tr>
        <td>主体:</td>
        <td>{$row->wechat_principal}</td>
    </tr>
    <tr>
        <td>能力:</td>
        <td>{$businfo}</td>
    </tr>
    <tr>
        <td>授权:</td>
        <td>{$row->wechat_func_info_str}</td>
    </tr>
</table>
eot;
    }
}