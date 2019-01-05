<?php

namespace App\Admin\Controllers;

use App\Models\Channel;
use App\Models\Merchant;
use App\Models\Project;
use App\Models\ProjectRepository;
use Encore\Admin\Form;
use Encore\Admin\Form\EmbeddedForm;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use App\Admin\ModelForm;
use Illuminate\Database\QueryException;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use URL;
use Wanjia\Common\Exceptions\AppException;

class ProjectController extends Controller
{
    use ModelForm;

    /**
     * 页面: 列表页
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('项目管理');
            $content->description('管理专题项目');

            //构造 Grid 字段
            $grid = Admin::grid(Project::class, function (Grid $grid) {
                $channels = Channel::get()->pluck('name', 'id');
                $grid->model()->orderBy('id', 'desc');
                if (!Admin::user()->inRoles(['administrator', 'ChannelManage'])) {
                    $grid->disableCreation();
                }
                if (!Admin::user()->inRoles(['administrator'])) {
                    $grid->model()->where('channel_id', '=', Admin::user()->channel_id);
                }
                $grid->filter(function ($filter) use ($channels) {
                    $filter->like('name', '项目名称');
                    $merchants = Merchant::get()->pluck('name', 'id');
                    $filter->in('merchant_id', '关联客户')->multipleSelect($merchants);
                    $filter->between('created_at', '创建时间')->datetime();
                    $filter->in('channel_id', '频道 ')->multipleSelect($channels);
                    $filter->where(function ($query) {
                        $date = date('Y-m-d H:i:s');
                        switch ($this->input) {
                            case 1:
                                $query->whereRaw("`use_start_at` > '{$date}'");
                                break;
                            case 2:
                                $query->whereRaw("`use_start_at` <= '{$date}' AND `use_end_at` > '{$date}'");
                                break;
                            case 3:
                                $query->whereRaw("`use_end_at` <= '{$date}'");
                                break;
                        }
                    }, '项目状态')->radio([
                        '' => '所有',
                        1 => '未开始',
                        2 => '进行中',
                        3 => '已结束',
                    ]);
                });
                $grid->id('项目ID');
                $grid->qrcode('二维码')->display(function () {
                    $qrcode = \QrCode::format('png')->size(80)->margin(0)
                        ->generate(\Request::getSchemeAndHttpHost() . $this->path);
                    $qrcode = base64_encode($qrcode);
                    return <<<eot
            <img src="data:image/png;base64, $qrcode" style="width: 80px;" />
eot;
                });
                $grid->name('项目名称')->display(function ($name) {
                    $links = [];
                    if ($this->can('baoming') || $this->can('vote')) {
                        if (Admin::user()->inRoles(['administrator', 'ChannelManage'])) {
                            $links[] = linkto('[表单设计]', route('projects.design_form', ['project_id' => $this->id]));
                        }
                    }

                    if ($this->can('vote')) {
                        $links[] = linkto('[投票记录]', route('vote_logs.index', ['project_id' => $this->id]));
                    }

                    if ($this->can('hongbao')) {
                        $links[] = linkto('[红包配置]', route('hongbao.setting', ['project_id' => $this->id]));
                        $links[] = linkto('[红包账簿]', route('hongbao.billings', ['project_id' => $this->id]));
                    }

                    if ($this->isJizi()) {
                        $links[] = linkto('[集字/图配置]', route('jizi.setting', ['project_id' => $this->id]));
                        $links[] = linkto('[集字/图统计]', route('jizi.logs', ['project_id' => $this->id]));
                    } else if ($this->can('draw')) {
                        $links[] = linkto('[抽奖配置]', route('prizes.setting', ['project_id' => $this->id]));
                        $links[] = linkto('[抽奖统计]', route('prizes.logs', ['project_id' => $this->id]));
                    }

                    if ($this->can('bargain')) {
                        $links[] = linkto('[砍价记录]', route('bargain.logs', ['project_id' => $this->id]));
                        $links[] = linkto('[下载兑奖二维码]', route('bargain.exchange_qrcode_download', ['project_id' => $this->id]),'_blank');
                    }


                    if ($this->can('news')) {
                      $links[] = linkto('[板块列表/创建板块]', route('news.blocks', ['project_id' => $this->id]));
                    }

                    if (!$this->can('news')) {
                      $links[] = linkto('[选手管理]', route('players.index', ['project_id' => $this->id]));
                    }

                    foreach ($this->manage_urls as $route => $linkname) {
                        if (\Route::has($route)) {
                            $links[] = linkto("[$linkname]", route($route));
                        }
                    }
                    $links = implode(' ', $links);
                    return <<<eot
$name<br/>
$links
eot;
                });
                $grid->channel_id('频道')->display(function ($id) use ($channels) {
                    return $channels[$id] ?? '';
                });
                $grid->merchant_name('关联客户')->display(function () {
                    return linkto($this->merchant->name, route('merchants.edit', ['merchant' => $this->merchant_id]));
                });
                $grid->column('项目状态')->display(function () {
                    $time = time();
                    if ($this->use_start_at == null || $this->use_end_at == null) {
                        return '未配置';
                    }
                    return $time < strtotime($this->use_start_at) ? '未开始' : ($time > strtotime($this->use_end_at) ? '已结束' : '进行中');
                });
                $grid->path('项目地址');
                $grid->created_at('创建时间');
            });
            $content->body($grid);
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
            $content->header('修改项目信息');
            $content->description('');
            $content->body($this->form()->edit($id));
        });
    }

    public function designForm(Project $project)
    {
        if (\Request::isMethod('post')) {
            return $this->storeDesignForm($project);
        }

        if (!\Session::hasOldInput('fields')) {
            $form_design = @$project->conf_base_form_design;
            $form_design = wj_obj2arr($form_design ?: []);
            \Session::flashInput(['fields' => $form_design]);
        }

        return Admin::content(function (Content $content) use ($project) {

            $content->header('设计报名表单');
            $content->description('');


            $dbTypes = [
                'string' => '<字符型>',
                'text' => '<长文本>',
                'rich' => '<富文本>',
                'integer' => '<数字型>',
                'checkbox' => '<多选>',
                'radio' => '<单选>',
                'select' => '<下拉>',
                'upload' => '<图片上传>',
                'uploads' => '<多图片上传>',
                'video' => '<视频>',
                'datetime' => '<日期选择>',
                'vote' => '<票额>',
                'openid' => '微信OpenID(隐藏字段)',
                'name' => '姓名',
                'phone' => '电话',
                'gender' => '性别',
                'idcard' => '身份证',
                'passport' => '证件号码',
                'email' => '邮箱',
                'qq' => 'QQ号',
                'address' => '地址',
                'birthday' => '生日',
                'age' => '年龄',
                'city' => '城市',
            ];

            $dbIndexes = [
                'uniqid' => '唯一标识',
                'str1' => '字符型1',
                'str2' => '字符型2',
                'str3' => '字符型3',
                'str4' => '字符型4',
                'str5' => '字符型5',
                'str6' => '字符型6',
                'str7' => '字符型7',
                'str8' => '字符型8',
                'str9' => '字符型9',
                'str10' => '字符型10',
                'int1' => '数字型1',
                'int2' => '数字型2',
                'int3' => '数字型3',
                'int4' => '数字型4',
                'int5' => '数字型5',
                'vote1' => '票额1',
                'vote2' => '票额2',
                'vote3' => '票额3',
            ];

            $dbCityTypes = [
                '1' => '国家',
                '2' => '省',
                '3' => '市',
                '4' => '区县'
            ];

            $action = URL::current();

            $content->row(view('admin::projects/design_form', compact('dbTypes', 'dbIndexes', 'action', 'dbCityTypes')));
        });
    }

    protected function storeDesignForm(Project $project)
    {
        $fields = \Request::input('fields');
        $fields = collect($fields);

        do {
            //检查空定义
            $null_fields = collect($fields)->filter(function ($item) {
                return trim($item['name']) === '' || trim($item['field'] === '');
            })->keys()->all();

            if (count($null_fields) > 0) {
                $toastr = new MessageBag([
                    'message' => 'Key或名称未填写完整',
                    'type' => 'error'
                ]);
                break;
            }


            //唯一标识只能有一个
            if ($fields->where('key', '=', 'uniqid')->count() > 1) {
                $toastr = new MessageBag([
                    'message' => '只能有一个唯一标识',
                    'type' => 'error'
                ]);
                break;
            }

            //检查同名 Key
            $field_conflict = $fields->groupBy('field', true)->map(function ($v) {
                return [
                    'count' => count($v),
                    'names' => collect($v)->keys()->all()
                ];
            })->where('count', '>', 1)->map(function ($v) {
                $line0 = $v['names'][0] + 1;
                $line1 = $v['names'][1] + 1;
                return "行$line0 与 行$line1 Key冲突";
            })->values();
            if ($field_conflict->count()) {
                $toastr = new MessageBag([
                    'message' => $field_conflict->get(0),
                    'type' => 'error'
                ]);
                break;
            }

            //检查同名索引
            $key_conflict = $fields->where('key', '>', '')->groupBy('key')->map(function ($v) {
                return [
                    'count' => count($v),
                    'names' => collect($v)->pluck('field')->all()
                ];
            })->where('count', '>', 1)->map(function ($v) {
                return "[{$v['names'][0]}] 与 [{$v['names'][1]}] 索引冲突";
            })->values();
            if ($key_conflict->count()) {
                $toastr = new MessageBag([
                    'message' => $key_conflict->get(0),
                    'type' => 'error'
                ]);
                break;
            }

            $fields = $fields->all();
            foreach ($fields as &$field) {
                $options = json_decode(@$field['options'], 1) ?: [];
                $options = (object)$options;
                if (@$options->{$field['type']}) {
                    $field['options'] = $options;
                } else {
                    if ($field['type'] === 'passport') {
                        $options->{$field['type']} = [
                            'passport_type' => ['SFZ', 'GAT', 'TBZ', 'HUZ', 'OTH']
                        ];
                    } elseif ($field['type'] === 'datetime') {
                        $options->{$field['type']} = [
                            'input_type' => 'datetime',
                            'datetime_type' => ['date', 'time']
                        ];
                    } elseif ($field['type'] === 'city') {
                        $options->{$field['type']} = [
                            'min_city_type' => 'region',
                            'max_city_type' => 'region',
                            'country' => '86',
                            'province' => '340000',
                            'city' => '340100'
                        ];
                    } elseif ($field['type'] === 'upload') {
                        $options->{$field['type']} = [
                            'input_type' => 'normal',
                            'min' => '0',
                            'max' => '2048',
                            'errmsg' => '图片尺寸超限'
                        ];
                    } elseif (in_array($field['type'], ['select', 'checkbox', 'radio'])) {
                        $options->{$field['type']} = [
                            'blank_text' => '--请选择--'
                        ];
                    }
                    $field['options'] = $options;
                }
            }

            $requires = 0;
            foreach ($fields as &$field) {
                //未设置字段类型
                if (!$field['type']) {
                    $toastr = new MessageBag([
                        'message' => "[{$field['field']}]字段类型未设置",
                        'type' => 'error'
                    ]);
                    break 2;
                }

                if ($field['type'] === 'vote' && strpos($field['key'], 'vote') !== 0) {
                    //投票索引限定类型
                    $toastr = new MessageBag([
                        'message' => "字段[{$field['field']}]必须绑定一个票额索引",
                        'type' => 'error'
                    ]);
                    break 2;
                } elseif (in_array($field['type'], ['upload', 'datetime', 'text', 'rich']) && $field['key']) {
                    //图片上传索引限定类型
                    $toastr = new MessageBag([
                        'message' => "字段[{$field['field']}]当前类型不能绑定索引",
                        'type' => 'error'
                    ]);
                    break 2;
                } elseif (in_array($field['type'], ['checkbox', 'radio', 'select']) && $field['key'] && strpos($field['key'], 'str') !== 0) {
                    //枚举型索引限定类型
                    $toastr = new MessageBag([
                        'message' => "字段[{$field['field']}]选中类型只能绑定字符型索引",
                        'type' => 'error'
                    ]);
                    break 2;
                } elseif (in_array($field['type'], ['integer', 'age']) && $field['key'] && strpos($field['key'], 'int') !== 0) {
                    //数字型索引限定类型
                    $toastr = new MessageBag([
                        'message' => "字段[{$field['field']}]选中类型只能绑定数字型索引",
                        'type' => 'error'
                    ]);
                    break 2;
                } elseif (!in_array($field['type'], ['integer', 'age']) && $field['key'] && strpos($field['key'], 'int') === 0) {
                    //字符型索引限定类型
                    $toastr = new MessageBag([
                        'message' => "字段[{$field['field']}]选中类型只能绑定字符型索引",
                        'type' => 'error'
                    ]);
                    break 2;
                }

                //唯一标识设为必填
                if ($field['key'] === 'uniqid') {
                    $field['required'] = 'on';
                }

                $requires += @$field['required'] === 'on';

                //add by chenfei
                $myoption = $field['options'];
                if (isset($myoption->sets['option_lists']) && !empty($myoption->sets['option_lists'])) {
                    $field['list'] = $myoption->sets['option_lists'];
                } else {
                    if (isset($field['list'])) {
                        unset($field['list']);
                    }
                }
                if (isset($myoption->sets['option_details']) && !empty($myoption->sets['option_details'])) {
                    $field['details'] = $myoption->sets['option_details'];
                } else {
                    if (isset($field['details'])) {
                        unset($field['details']);
                    }
                }
                if (isset($myoption->sets['option_indexs']) && !empty($myoption->sets['option_indexs'])) {
                    $field['indexs'] = $myoption->sets['option_indexs'];
                } else {
                    if (isset($field['indexs'])) {
                        unset($field['indexs']);
                    }
                }
                if (isset($myoption->sets['option_baoming']) && !empty($myoption->sets['option_baoming'])) {
                    $field['registration'] = $myoption->sets['option_baoming'];
                } else {
                    if (isset($field['registration'])) {
                        unset($field['registration']);
                    }
                }
            }
            unset($field);

            //至少一个必填项
            if (!$requires) {
                $toastr = new MessageBag([
                    'message' => "至少要有一个必填项",
                    'type' => 'error'
                ]);
                break;
            }

            $confs = $project->configs;
            $confs->base_form_design = collect($fields)->values();
            $project->configs = $confs;

            //更新 Player 索引
            try {
                \DB::transaction(function () use ($project, $fields) {
                    $project->save();
                    $oldConfs = $project->getOriginal('configs');
                    $oldConfs = json_decode($oldConfs);

                    $design = collect(@$project->conf_base_form_design ?: [])->pluck('key', 'field')->all();
                    $oldDesign = collect(@$oldConfs->base_form_design ?: [])->pluck('key', 'field')->all();

                    $ret = array_diff_assoc($design, $oldDesign);

                    if (!$ret) {
                        //no need to regenerateMeta
                        return;
                    }

                    $players = $project->players;
                    if ($players) {
                        foreach ($players as $player) {
                            $player->regenerateMeta();
                            $player->enableBroadcast(false);
                            $player->save();
                        }
                    }
                });
            } catch (QueryException $ex) {
                \Log::debug($ex);
                if ($ex->getCode() == 23000) {
                    $field = collect($fields)->where('key', '=', 'uniqid')->values()->get(0);
                    $toastr = new MessageBag([
                        'message' => "字段[{$field['field']}]有重复内容,不能作为唯一标识",
                        'type' => 'error'
                    ]);
                    break;
                }
            }

            $toastr = new MessageBag([
                'message' => '保存成功',
                'type' => 'success'
            ]);
        } while (0);


        return back()->withInput(['fields' => $fields])->with(compact('toastr'));
    }

    /**
     * 页面: 新建页
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('新建项目');
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
        if (!Admin::user()->inRoles(['administrator', 'ChannelManage'])) {
            return Admin::form(Project::class, function (Form $form) {
                $form->editor('rules', '活动规则');
            });
        }

        $form = Admin::form(Project::class, function (Form $form) {
            $form->hidden('id', 'ID');
            $form->text('name', '项目名称');
            $last_id = Project::orderBy('id', 'desc')->limit(1)->pluck('id');

            $form->text('path', '项目地址')
                ->placeholder('请直接输入根路径,不要带域名; 如: /xdf')
                ->prepend('<i class="fa fa-internet-explorer"></i>')
                ->rules('required|regex:@^(/[\w-]+)+$@', [
                    'regex' => '地址格式错误'
                ])->help("如果是公版专题，地址编号建议使用" . ($last_id[0] + 1));
            $form->select('merchant_id', '关联客户')->options(Merchant::get()->pluck('name', 'id'));
            $form->select('channel_id', '所属频道')->options(Channel::get()->pluck('name', 'id'));
            $form->switch('is_enabled', '是否激活')->default(1);
            if (Admin::user()->inRoles(['administrator'])) {
                $form->switch('configs.test_mode', '内网测试')->help("IP模式: 限制外网IP，只允许公司IP访问");
                $form->text('configs.test_cookie_token', 'Cookie私钥')
                    ->help('Cookie模式: 访问项目前先访问另一个隐藏地址，植入Cookie之后，外网也可参与测试<br/>填入私钥并保存项目后；点击生成按钮可以生成测试授权地址，2小时内有效，同一个地址可以授权多人<br/><button class="btn btn-default btn-gen-cookie-url" onclick="return false;">生成测试授权地址</button>');

                $form->html(script_in_php(
                    <<<eot
<script type="text/javascript">
    $('.btn-gen-cookie-url').click(function() {
        $.post('gen_cookie_url', function(ret) {
            if (ret.code !== 0) {
                swal(ret.msg);
                return;
            }
            
            swal(ret.data);
        });
    });
</script>
eot
                ));
            }

            $form->datetimeRange('start_at', 'end_at', '项目时效')->help('项目时效代表该专题可被打开的时间，可选择活动结束日期后三个月内');
            $form->datetimeRange('use_start_at', 'use_end_at', '项目时间')->help('项目时间代表该专题的有效活动时间，活动开始~活动结束');

            if (Admin::user()->inRoles(['administrator'])) {
                $form->checkbox('capacity', '功能')->options(['vote' => '投票', 'baoming' => '报名', 'hongbao' => '红包',
                    'jizi' => '集字/图', 'draw' => '抽奖', 'bargain' => '砍价', 'news' => '新闻']);
            } else {
                $form->checkbox('capacity', '功能')->options(['vote' => '投票', 'baoming' => '报名',
                    'jizi' => '集字/图', 'draw' => '抽奖', 'bargain' => '砍价']);
            }
            if (Admin::user()->inRoles(['administrator'])) {
                $form->textarea('configs.manage_urls', '管理功能')->help("格式: [链接名称]RouteName 每行一个，会显示在项目列表里");
            }
            $form->select("type", "类型")->options(ProjectRepository::TYPES);

            //add by chenfei
            $form->text('configs.share_title', '分享标题')->help('建议在10~20字');
            $form->text('configs.share_desc', '分享描述')->help('建议在10~20字');
            $form->image('configs.share_image', '分享图片')->uniqueName()->move(date('Y/md'))
                ->help('尺寸：建议100*100，支持JPG/GIF/PNG 文件格式');

            $form->editor('rules', '活动规则');
        });

        $id = \Request::route('project');
        $proj = Project::repository()->retrieveByPK($id);
        if ($proj) {
            //新闻模板配置
            if ($proj->can('news')) {
              $form->embeds('configs.news', '新闻设置', function (EmbeddedForm $form) {
                $form->image('news_top_pic', '顶部通栏图片')->uniqueName()->move(date('Y/md'))
                  ->help('尺寸：建议1920*550，支持JPG/GIF/PNG 文件格式');
                //$form->text('bg_color', '背景色值')->help('如:#00FFF');
                $form->color('bg_color', '背景色值')->default('#00FFF');
                $form->text('news_channel', '频道TAB')->help('填写模板的频道TAB,多个TAB用,分开');
              });
            }
            //砍价配置
            if ($proj->can('bargain')) {
                $form->embeds('configs.bargain', '砍价配置', function (EmbeddedForm $form) use ($proj) {
                    $form->datetime('stime', '砍价开始时间')->default($proj->use_start_at);
                    $form->datetime('etime', '砍价结束时间')->default($proj->use_end_at);
                    $form->image('img', '活动头图')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议640*450，支持JPG/GIF/PNG 文件格式');
                    $form->text('copywriting', '活动文案');
                    $form->textarea('guidetext', '引导语')->help('格式为：****PLAYER*****');
                    $form->text('goods_name', '商品名称');
                    $form->text('goods_price', '商品价格');
                    $form->text('bargain_price', '目标价格')->default(0);
                    $form->number('goods_count', '商品总个数');
                    $form->number('min', '砍价最小值');
                    $form->number('max', '砍价最大值');
                    $form->text('prefix', '选手编号前缀')->help('如:"XXX0001"中的"XXX"');
                    $form->switch('award_info', '核销是否需要完善信息');
                    $form->image('wechat_img1', '公众号二维码图片1')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议105*105，支持JPG/GIF/PNG 文件格式');
                    $form->image('wechat_img2', '公众号二维码图片2')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议105*105，支持JPG/GIF/PNG 文件格式');
                    $form->image('send_img', '砍价回复图片')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议430*200，支持JPG/GIF/PNG 文件格式');
                });
            }
            //抽奖配置
            if ($proj->can('draw')) {
                $form->embeds('configs.draw', '抽奖配置', function (EmbeddedForm $form) use ($proj) {
                    $form->datetime('stime', '抽奖开始时间')->default($proj->use_start_at);
                    $form->datetime('etime', '抽奖结束时间')->default($proj->use_end_at);
                    $form->image('img', '活动头图')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议640*200，支持JPG/GIF/PNG 文件格式');
                    $drawTypes = ['dzp' => '大转盘', 'jgg' => '九宫格', 'ggk' => '刮刮卡', 'zjd' => '砸金蛋', 'fp' => '翻牌'];
                    $form->radio('draw_type', '抽奖样式效果')->options($drawTypes)->default('dzp');
                    $form->color('bg_color', '背景色')->help('头图下面的背景色，可选')->default('#0A58BC');
                    $form->color('bg_font_color', '整体字体色')->help('页面中的文字色（主要为除按钮上外的其他文字颜色）')->default('#793C31');
                    $form->color('btn_color', '按钮背景色')->default('#FF4F1E');
                    $form->color('btn_font_color', '按钮字体颜色')->default('#fff');
                    $form->number('limit_day_count', '每天抽奖的次数')->attribute(['max' => 64, 'min' => 0])->default(0);
                    $form->number('limit_count', '整个活动的抽奖次数')->attribute(['max' => 1000, 'min' => 0])->default(0);
                    $form->radio('player_info_type', '完善中奖信息')
                        ->options(['N' => '否', 'NP' => '姓名+手机号', 'NPA' => '姓名+手机号+地址'])->default('N')
                        ->help('注意：初次设定有效，修改请在表单设计中');
                    if ($proj->isPrize()) {
                        $form->radio('is_zhuli', '邀请好友是否可获得额外次数')->help('每个好友助力一次增加一次抽奖机会')
                            ->options(['N' => '否', 'Y' => '是'])->default('N');
                        $form->radio('is_attention', '是否需要关注')->options(['N' => '否', 'Y' => '是'])->default('N');
                        $form->image('wechat_img', '公众号二维码图片')->uniqueName()->move(date('Y/md'))
                            ->help('尺寸：建议105*105，支持JPG/GIF/PNG 文件格式');
                        $form->text('wechat_name', '公众号的名称');
                        $form->text('keyword', '关键字');
                        $form->image('send_img', '抽奖回复图片')->uniqueName()->move(date('Y/md'))
                            ->help('尺寸：建议430*200，支持JPG/GIF/PNG 文件格式');
                    } else {
                        $form->hidden('is_zhuli', '邀请好友是否可获得额外次数')->default('N');
                        $form->hidden('is_attention', '是否需要关注')->default('N');
                    }
                });
            }

            //集字配置
            if ($proj->can('jizi')) {
                $form->embeds('configs.jizi', '集字/图设置', function (EmbeddedForm $form) use ($proj) {
                    $form->datetime('stime', '集字/图开始时间')->default($proj->use_start_at);
                    $form->datetime('etime', '集字/图结束时间')->default($proj->use_end_at);
                    $form->text('jizi_pre', '选手编号前缀')->help('如:XXX0001 XXX0002');
                    $form->switch('is_first_give', '首次报名是否发字')->default(1);
                    $form->radio('font_type', '发字样式效果')->options([
                        'css1' => '常规集字',
                        'picture9' => '集图(9个)',
                        'picture12' => '集图(12个)',
                    ])->default('css1')->help('默认常规集字，集图类型时集图图片必传');
                    $form->image('jizi_picture', '集图图片')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议9宫格(540*540)、12宫格(540*720)，支持JPG/GIF/PNG 文件格式');
                    $form->number('jizi_give_diffcount', '达到指定人数发不同字')
                        ->help('用户在活动期间，邀请人数达到所设置人数整数倍时，优先发放用户没有集得到的字');
                    $form->hidden('limit_give_font', '整个活动可转赠字的次数');

                    //新增投票相关字段 add by chenfei
                    $form->image('wechat_img', '公众号二维码')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议150*150，支持JPG/GIF/PNG 文件格式');
                    $form->image('jizi_img', '活动头图')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议375*300，支持JPG/GIF/PNG 文件格式');
                    $form->image('jizi_suc_img', '助力成功回复照片')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议430*200，支持JPG/GIF/PNG 文件格式');
                    $form->color('jizi_bgcolor', '背景色')->help('头图下面的背景色，可选')->default('#0A58BC');
                    $form->color('jizi_btcolor', '按钮色')->help('头图下面搜索，筛选按钮背景色')->default('#FEE900');
                    $form->color('jizi_ftcolor', '按钮字体颜色')->help('按钮里面字的颜色')->default('#fff');
                    $form->color('jizi_sfcolor', '集字的字颜色')->help('成功集字的字的颜色')->default('#0A58BC');
                    $form->text('jizi_btword', '邀请按钮文字')->default('邀请好友为我助力')->help('个人页按钮自定义文字');
                    $form->text('jizi_drawword', '抽奖按钮文字')->default('我要抽奖')->help('个人页抽奖按钮文字');
                });
            }
            //投票配置
            if ($proj->can('vote')) {
                $form->embeds('configs.vote', '投票设置', function (EmbeddedForm $form) {
                    $form->datetime('stime', '投票开始时间');
                    $form->datetime('etime', '投票结束时间');
                    $form->text('vote_pre', '选手编号前缀')->help('如:XXX0001 XXX0002');

                    $form->number('limit_daily', '每日可投票数');
                    $form->number('limit_all', '整个活动可投票数');
                    $form->number('limit_person_daily', '每天单个选手得票上限')
                        ->help('防止用户恶意刷票，可设置一个用户每天最多可得票数');

                    //新增默认排序字段 zhuzq
                    $form->radio('default_orderby', '默认排序')->options(['id' => '按编号', 'vote' => '按票数'])
                        ->default('id')->help('前台首页选手排序');
                    //新增投票相关字段 add by chenfei
                    $form->image('wechat_img', '公众号二维码')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议150*150，支持JPG/GIF/PNG 文件格式');
                    $form->image('wechat_img2', '公众号二维码2')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议150*150，支持JPG/GIF/PNG 文件格式');
                    $form->image('vote_img', '活动头图')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议640*500，支持JPG/GIF/PNG 文件格式');
                    $form->image('vote_suc_img', '投票成功封面')->uniqueName()->move(date('Y/md'))
                        ->help('尺寸：建议430*200，支持JPG/GIF/PNG 文件格式');
                    $form->color('vote_bgcolor', '背景色')->help('头图下面的背景色，可选');
                    $form->color('vote_btcolor', '按钮色')->help('头图下面搜索，筛选按钮背景色');
                    $form->text('vote_btword', '按钮文字')->default('赞美 ta')->help('投票按钮自定义文字');
                    $form->text('vote_regword', '按钮文字2')->default('邀请好友')->help('详情页投票旁边按钮文字');
                });
            }

            //红包配置
            if ($proj->can('hongbao')) {
                $form->embeds('configs.hongbao', '红包设置', function(EmbeddedForm $form) {
                    $form->datetime('stime', '活动开始时间');
                    $form->datetime('etime', '活动结束时间');
                    $form->text('ply_pre', '选手编号前缀')->help('如:XXX0001 XXX0002');
                    $form->radio('category', '红包样式')->options(['普通红包', '邀请红包'])->default(0);
                    $form->currency('virtual_money', '虚拟红包金额')->symbol('￥')->help('邀请红包文案显示');
                    $form->number('hb_count', '红包次数')->help('1、普通红包用户可抢红包的次数  2、邀请红包中队长组队的次数');
                    $form->number('hb_zl_count', '邀请红包的邀请人数');
                    $form->color('bg_color', '背景色')->default('#E41D0D');
                    $form->color('btn_bg_color', '按钮色');
                    $form->color('btn_txt_color', '按钮文字色')->default('#8f0f07');
                    $form->text('word', '普通红包口令')->help('普通红包口令生效需要去配置相应回复');
                    $form->image('img', '活动头图')->help('图片尺寸建议宽750*高480')->uniqueName()->move(date('Y/md'));
                    $form->image('qrcode', '二维码图片')->uniqueName()->move(date('Y/md'));
                    $form->image('reply_img', '拆包成功回复图片')->uniqueName()->move(date('Y/md'));
                });
            }

            //报名配置
            if ($proj->can('baoming')) {
                $form->embeds('configs.baoming', '报名设置', function (EmbeddedForm $form) use ($proj) {
                    $form->datetime('starttime', '报名开始时间')->default($proj->use_start_at);
                    $form->datetime('endtime', '报名结束时间')->default($proj->use_end_at);
                    if (Admin::user()->inRoles(['administrator'])) {
                        if (!$proj->can('jizi')) {
                            $form->switch('can_queue', '开启排队');
                            $form->rate('queue_rate', '放行概率')->default(50);
                            $form->switch('can_limit', '开启名额限制');
                            $form->number('limits', '名额限制')->attribute(['min' => 0]);
                            $form->switch('can_single', '开启单人报名');
                            $form->switch('can_group', '开启团队报名');
                            $form->number('group_max_players', '团队最大人数')->attribute(['max' => 10, 'min' => 2])->default(2);
                        }
                    }
                    $form->radio('ticket_mode', '编号模式')->options([
                        '' => '无编号',
                        'manual' => '手动',
                        'auto' => '自动'
                    ])->default('auto')->help(<<<eot
说明: <br/>
自动编号只保障编号递增，不保障极端状况下编号连续性，可能会发生丢号现象(万人报名实际编号可能会超过10000)<br/>
手动编号用于报名中止到报名查询空洞期人工分配编号，针对编号敏感型业务有最大的灵活性(特殊号段的保留、分配)
eot
                    );

                    $form->number('ticket_length', '编码长度')
                        ->attribute(['max' => 64, 'min' => 0])
                        ->default(0)
                        ->help(<<<eot
说明: <br/>
0表示不定长度<br/>
其他数字表示补零至多少位<br/>
仅自动编号模式有效
eot
                        );

                    $form->checkbox('step_mode', '报名流程')->options([
                        'book' => '预约',
                        'baoming' => '填报',
                        'query' => '查询',
                        'confirm' => '确认'
                    ])->default(['baoming']);

                    if (Admin::user()->inRoles(['administrator'])) {
                        $form->switch('can_quota', '开启配额功能')->help('类似小米 F码 模式');
                    }
                });
            }

        }
        //保存前检查 path 占用
        $form->saving(function (Form $form) {
            //检查集图是否上传
            if (in_array($form->input('configs.jizi.font_type'), ['picture9', 'picture12'])) {
                if ($form->input('configs.jizi.jizi_picture') == null && !isset($form->model()->configs->jizi->jizi_picture)) {
                    $error = new MessageBag([
                        'title' => '错误',
                        'message' => "集图图片没上传",
                    ]);
                    return back()->withInput()->with(compact('error'));
                }
            }
            if ($form->input('id')) {
                return;
            }


            $url = $form->input('path');

            $p = Project::matchByPath($url);
            if ($p == null) {
                return;
            }

            $error = new MessageBag([
                'title' => '错误',
                'message' => "地址[$url]已被项目[$p->name]占用",
            ]);
            return back()->withInput()->with(compact('error'));
        });

        //保存后刷新编辑页，出现功能配置界面
        $form->saved(function (Form $form) {
            $model = $form->model();
            $toastr = new MessageBag([
                'message' => '保存成功',
                'type' => 'success'
            ]);

            return redirect(route('projects.edit', ['project' => $model->id]))->with(compact('toastr'));
        });

        return $form;
    }

    public function rules($id)
    {
        $form = Admin::form(Project::class, function ($form) {
            $form->editor('rules', '活动规则');
        });

        $form->tools(function ($tools) {
            $tools->disableListbutton();
        });
        $form->setAction(route('projects.rules', ['project_id' => $id]));

        if (\Request::isMethod('put')) {
            $form->update($id);
        }
        return Admin::content(function (Content $content) use ($id, $form) {
            $content->header('编辑活动规则');
            $content->description('');

            $content->body($form->edit($id));
        });
    }

    public function genCookieUrl(Project $project)
    {
        $test_mode = $project->conf_test_mode;
        $test_cookie_token = $project->conf_test_cookie_token;

        if (!$test_mode) {
            throw new AppException('未开启内网测试，外网不需要授权即可测试', 1);
        }

        if (!$test_cookie_token) {
            throw new AppException('请先设置Cookie私钥', 1);
        }

        $salt = Str::random();
        $time = time() + 7200;

        $clearText = "access_$salt$time";
        $sign = md5($clearText . $test_cookie_token);

        $url = \Request::getSchemeAndHttpHost() . $project->path . "?__testing=$sign$clearText";
        return wj_json_message("以下地址两小时内有效，请尽快完成测试授权操作: $url");
    }
}
