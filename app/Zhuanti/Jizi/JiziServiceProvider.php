<?php

namespace App\Zhuanti\Jizi;

use App\Models\Project;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class JiziServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->listenProjectModelEvent();
    }


    public function register()
    {
    }

    /**
     * 监听Player数据库事件
     */
    protected function listenProjectModelEvent()
    {
        \Event::listen('model.project.*', function ($event, $args) {
            $project = $args[0];
            if (!$project instanceof Project || !$project->isJizi()) {
                return;
            }

            $event = substr($event, 14);
            $method = "onEventProject" . ucfirst(Str::camel($event));

            if (method_exists($this, $method)) {
                $this->$method($project);
            }
        });
    }

    protected function onEventProjectSaving(Project $project)
    {
        if (!isset($project->configs->base_form_design)) {
            $confs = $project->configs;
            $fields = [
                [
                    "options" => [
                        "sets" => []
                    ],
                    "field" => "openid",
                    "name" => "openid",
                    "type" => "openid",
                    "required" => "on",
                    "key" => "uniqid",
                    "default" => null,
                    "comment" => null
                ],
                [
                    "options" => [
                        "sets" => [
                            "option_baoming" => "on",
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "name",
                    "name" => "姓名",
                    "type" => "name",
                    "required" => "on",
                    "key" => "str1",
                    "default" => null,
                    "comment" => null,
                    "list" => "on",
                    "registration" => "on"
                ],
                [
                    "options" => [
                        "sets" => [
                            "option_baoming" => "on",
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "phone",
                    "name" => "手机号",
                    "type" => "phone",
                    "required" => "on",
                    "key" => "str2",
                    "default" => null,
                    "comment" => null,
                    "list" => "on",
                    "registration" => "on"
                ],
                [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ],
                        "radio" => [
                            "options" => [
                                [
                                    "is_default" => false,
                                    "key" => "Y",
                                    "name" => "是"
                                ],
                                [
                                    "is_default" => false,
                                    "key" => "N",
                                    "name" => "否"
                                ]
                            ]
                        ]
                    ],
                    "field" => "is_jiqi",
                    "name" => "是否集齐",
                    "type" => "radio",
                    "key" => "str3",
                    "default" => "N",
                    "comment" => null,
                    "list" => "on"
                ],
                [
                    "options" => [
                        "sets" => [
                        ]
                    ],
                    "field" => "draw_time",
                    "name" => "参与时间",
                    "type" => "string",
                    "key" => "str5",
                    "default" => null,
                    "comment" => null,
                ],  //参与时间
                [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "draw_count",
                    "name" => "抽奖次数",
                    "type" => "integer",
                    "key" => "int1",
                    "default" => "0",
                    "comment" => null,
                    "list" => "on"
                ],  //抽奖次数
                [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "win_count",
                    "name" => "中奖次数",
                    "type" => "integer",
                    "key" => "int2",
                    "default" => "0",
                    "comment" => null,
                    "list" => "on"
                ],  //中奖次数
            ];
            $confs->base_form_design = collect($fields)->values();
            $project->configs = $confs;
        }
    }

}

