<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;


class PrizesLogExcelExporter extends AbstractExporter
{

    public function export()
    {
        Excel::create('抽奖记录'.date('Y-m-d'), function($excel) {
            $excel->sheet('抽奖记录', function($sheet) {
                $sheet->rows([['ID', '选手ID', '微信昵称', '姓名', '手机号', 'OPEN_ID', '奖品名称', '是否中奖', '是否领取', '抽奖时间']]);
                $this->chunk(function($data) use ($sheet) {
                    $rows = [];
                    foreach ($data as $v) {
                        $rows[] = [$v->id, $v->player_id, $this->filterEmoji($v->wx_name),
                            isset($v->player->info->name) ? $v->player->info->name : '',
                            isset($v->player->info->phone) ? $v->player->info->phone.' ' : '',
                            $v->openid,
                            isset($v->name) ? $v->name : '',
                            $v->win_text,
                            $v->draw_text,
                            $v->created_at
                        ];
                    }
                    $sheet->rows($rows);
                });
            });
        })->export('xls');
    }

    public function filterEmoji($emojiStr){
        $emojiStr = preg_replace_callback('/./u',function(array $match){
            return strlen($match[0]) >= 4 ? '*' : $match[0];
        },$emojiStr);
        return $emojiStr;
    }
}