<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;


class ExcelExporter extends AbstractExporter
{
    public $project;

    public function __construct(Grid $grid, $project)
    {
        parent::__construct($grid);
        $this->project = $project;
    }

    public function export()
    {
        // 定义文件名和下载地址
        $str = \Illuminate\Support\Str::random(32);
        $filename = $str . ".csv";
        $file = uploads_path("data/" . $filename);

        // 检查存放文件路径
        if (!is_dir($dir = dirname($file))) {
            mkdir($dir);
        }

        $form_design = @$this->project->conf_base_form_design ?: [];

        // 定义要写入的字段
        $field_name = [];
        $field_name['id'] = ['name' => 'id', 'type' => 'id'];
        $field_name['ticket_no'] = ['name' => '选手编号', 'type' => 'ticket_no'];
        $field_name['checked'] = ['name' => '审核状态', 'type' => 'checked'];
        foreach ($form_design as $field) {
            switch ($field->key) {
                case 'integer':
                case 'age':
                case 'vote':
                        $field->field = $field->key ?:$field->field;
                    break;
            }
            $field_name[$field->field] = ['name' => $field->name, 'type' => $field->type];
        }
        $field_name['created_at'] = ['name' => '创建时间', 'type' => 'string'];
        $field_name['updated_at'] = ['name' => '更新时间', 'type' => 'string'];


        $fp = fopen($file, 'w');
        fwrite($fp, "\xEF\xBB\xBF");

        // 定义写入 cvs 文档的函数
        $writeline = function ($format, ... $args) use ($fp) {
            $line = sprintf($format . "\r\n", ... $args);
            fwrite($fp, $line);
        };
        // 根据 $field_name 数组写入 cvs 文档的标题
        $title_arr = [];
        array_walk($field_name, function ($item) use (&$title_arr) {
            $title_arr[] = $item['name'];
        });
        $writeline(implode(',', array_values($title_arr)));

        /**
         * 创建 sql 语句准备查询
         * @var \Illuminate\Database\Eloquent\Builder $builder
         */
        $builder = $this->getData(true);
        $query = $builder->toBase();

        $sql = $query->toSql();
        $params = $query->getBindings();

        $stm = \DB::getPdo()->prepare($sql);
        $stm->execute($params);

        // 定义需要在写入 cvs 文档之前加\t的类型
        $field_sp_type = ['phone', 'idcard', 'qq'];

        // 从数据库逐行查出数据并写入 cvs 文档
        while ($row = $stm->fetch()) {
            $arr = wj_json_decode($row['info']);
            $arr['vote'] = (string)$row['vote1'];
            $data = [];
            foreach ($field_name as $k => $v) {
                $data[$k] = str_replace('%', '%%', isset($arr[$k]) ? $arr[$k]: (isset($row[$k]) ? $row[$k] : ''));
                if (in_array($v['type'], $field_sp_type)) {
                    $data[$k] = "\t" . $data[$k];
                }
                $data[$k] = '"' . str_replace('"', '""', $data[$k]) . '"';
            }
            if (!empty($data)) {
                $writeline(implode(',', array_values($data)));
            }
        }
        fclose($fp);

        $resp = response()->download($file);
        $resp->deleteFileAfterSend(true);
        $resp->prepare(\Request::instance());
        $resp->send();
        exit;
    }
}