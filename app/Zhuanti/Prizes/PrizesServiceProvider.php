<?php

namespace App\Zhuanti\Prizes;

use App\Models\Project;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class PrizesServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->listenProjectModelEvent();
    }


    public function register()
    {
    }

    /**
     * 监听
     */
    protected function listenProjectModelEvent()
    {
        \Event::listen('model.project.*', function ($event, $args) {
            $project = $args[0];
            if (!$project instanceof Project || !$project->can('draw')) {
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
        if (!isset($project->configs->draw->player_info_type)) {
            return false;
        }
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
                ]   //open_id
            ];
            //是否有姓名手机号
            if ($project->configs->draw->player_info_type == 'N') {
                $fields[] = [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "wx_nickname",
                    "name" => "昵称",
                    "type" => "string",
                    "key" => "str7",
                    "default" => null,
                    "comment" => null,
                    "list" => "on"
                ];
            } elseif ($project->configs->draw->player_info_type == 'NP') {
                $fields[] = [
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
                ];  //姓名
                $fields[] = [
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
                ];  //手机号
            } elseif ($project->configs->draw->player_info_type == 'NPA') {
                $fields[] = [
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
                ];  //姓名
                $fields[] = [
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
                ];  //手机号
                $fields[] = [
                    "options" => [
                        "sets" => [
                            "option_baoming" => "on"
                        ]
                    ],
                    "field" => "address",
                    "name" => " 地址",
                    "type" => "address",
                    "required" => "on",
                    "key" => "str6",
                    "default" => null,
                    "comment" => null,
                    "registration" => "on"
                ];  //地址
            }
            $fields[] = [
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
            ];  //参与时间
            $fields[] = [
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
            ];  //抽奖次数
            $fields[] = [
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
            ];  //中奖次数

            $confs->base_form_design = collect($fields)->values();
            $project->configs = $confs;
        }
    }

}

