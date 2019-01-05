<?php

namespace App\Zhuanti\Bargain;

use App\Models\Project;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class BargainServiceProvider extends ServiceProvider
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
            if (!$project instanceof Project || !$project->can('bargain')) {
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
                        ]
                    ],
                    "field" => "price",
                    "name" => "价格",
                    "type" => "string",
                    "key" => "str3",
                    "default" => null,
                    "comment" => null,
                    "list" => "on",
                ],
                [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "bargain_count",
                    "name" => "被砍次数",
                    "type" => "integer",
                    "key" => "int1",
                    "default" => 0,
                    "comment" => null,
                    "list" => "on",
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
                    "field" => "is_bargain",
                    "name" => "是否砍完",
                    "type" => "radio",
                    "key" => "str4",
                    "default" => "N",
                    "comment" => null,
                    "list" => "on"
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
                    "field" => "is_exchange",
                    "name" => "是否兑换",
                    "type" => "radio",
                    "key" => "str5",
                    "default" => "N",
                    "comment" => null,
                    "list" => "on"
                ],
                [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "exchange_time",
                    "name" => "兑换时间",
                    "type" => "string",
                    "key" => "str6",
                    "default" => null,
                    "comment" => null,
                    "list" => "on"
                ],
            ];
            $confs->base_form_design = collect($fields)->values();
            $project->configs = $confs;
        }
    }

}

