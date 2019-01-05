<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;


class ArtImgExcelExporter extends AbstractExporter
{
    public $project;

    public function __construct(Grid $grid, $project)
    {
        parent::__construct($grid);
        $this->project = $project;
    }

    public function export()
    {
        $form_design = @$this->project->conf_base_form_design ?: [];

        // 定义要写入的字段
        $field_name = [];
        $field_name['id'] = ['name' => 'id', 'type' => 'id'];
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

        /**
         * 创建 sql 语句准备查询
         * @var \Illuminate\Database\Eloquent\Builder $builder
         */
        $builder = $this->getData(true);
        $query = $builder->toBase();

        $search = '';
        $input = \Request::all();
        if(isset($input['created_at']['start']) && isset($input['created_at']['end']) && !empty($input['created_at']['start'])  && !empty($input['created_at']['end'])) {
            $search .= ' and created_at >="'.$input['created_at']['start'].'"  and created_at<="'.$input['created_at']['end'].'"';
        }

        if(isset($input['id']) && $input['id'] > 0) {
            $search .= ' and id='.$input['id'];
        }
        if(isset($input['ticket_no']) && !empty($input['ticket_no'])) {
            $search .= ' and ticket_no="'.$input['ticket_no'].'"';
        }

        $sql = 'select * from `zt_player` where project_id='.$this->project->id.$search;

        $stm = \DB::getPdo()->prepare($sql);
        $stm->execute([]);

        // 从数据库逐行查出数据并写入 cvs 文档
        $return = [];
        $i = 0;
        while ($row = $stm->fetch()) {
            $arr = wj_json_decode($row['info']);
            $arr['vote'] = (string)$row['vote1'];
            $data = [];
            foreach ($field_name as $k => $v) {
                $data[$k] = str_replace('%', '%%', isset($arr[$k]) ? $arr[$k]: (isset($row[$k]) ? $row[$k] : ''));
            }
            if (!empty($data)) {
                if(isset($data['pics'])){
                    $pics = explode(',', $data['pics']);
                    $tits = explode(',', $data['tits']);

                    foreach($pics as $k=>$v) {
                        $return[$i]['image_name'] =  $data['name'].'-'.$tits[$k].'.'.substr(strrchr($v, '.'), 1);
                        $return[$i]['image_src'] =  $v;
                        $i++;
                    }

                }
                if(isset($data['img'])){
                    $img = explode(',', $data['img']);
                    foreach($img as $k=>$v) {
                        $return[$i]['image_name'] =  $data['name'].'-'.$k.'.'.substr(strrchr($v, '.'), 1);
                        $return[$i]['image_src'] =  uploads_url($v);
                        $i++;
                    }
                }
            }
        }
        return $return;
    }
}