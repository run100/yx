<?php
/**
 * Created by PhpStorm.
 * User: zhuzq
 * Date: 2018/11/22
 * Time: 10:19
 */

namespace App\Zhuanti\Redpacket;


use App\Models\Project;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class RedpacketServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->listenProjectModelEvent();
    }

    /**
     * 监听
     */
    protected function listenProjectModelEvent()
    {
        \Event::listen('model.project.*', function ($event, $args) {
            $project = $args[0];
            if (!$project instanceof Project || !$project->can('hongbao')) {
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
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "wx_nickname",
                    "name" => "昵称",
                    "type" => "string",
                    "key" => "str1",
                    "default" => null,
                    "comment" => null,
                    "list" => "on"
                ],
                [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "hb_count",
                    "name" => "抢红包次数",
                    "type" => "integer",
                    "key" => "int1",
                    "default" => "0",
                    "comment" => null,
                    "list" => "on"
                ],
                [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "hb_win_count",
                    "name" => "抢到次数",
                    "type" => "integer",
                    "key" => "int2",
                    "default" => "0",
                    "comment" => null,
                    "list" => "on"
                ],
                [
                    "options" => [
                        "sets" => [
                            "option_lists" => "on"
                        ]
                    ],
                    "field" => "hb_money",
                    "name" => "红包总额",
                    "type" => "string",
                    "key" => "str2",
                    "default" => "0",
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
                                    "key" => "NOBIND",
                                    "name" => "未绑定"
                                ],
                                [
                                    "is_default" => false,
                                    "key" => "CAPTAIN",
                                    "name" => "队长"
                                ],
                                [
                                    "is_default" => false,
                                    "key" => "COMMON",
                                    "name" => "普通用户"
                                ]
                            ]
                        ]
                    ],
                    "field" => "hb_identity",
                    "name" => "用户身份",
                    "type" => "radio",
                    "key" => "str3",
                    "default" => "COMMON",
                    "comment" => null,
                    "list" => "on"
                ],
            ];
            $confs->base_form_design = collect($fields)->values();
            $project->configs = $confs;
        }
    }

}