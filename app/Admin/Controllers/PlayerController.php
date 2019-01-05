<?php

namespace App\Admin\Controllers;

use App\Admin\CityField;
use App\Admin\PassportField;
use App\Models\Merchant;
use App\Models\Player;

use App\Models\Project;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use App\Admin\Extensions\ExcelExporter;
use App\Admin\Extensions\Tools\ShowArtwork;
use App\Admin\Extensions\ArtImgExcelExporter;

class PlayerController extends Controller
{

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Project $project)
    {
        return Admin::content(function (Content $content) use ($project) {

            $content->header('选手列表');
            $content->description('');
            $content->body($this->grid($project));
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit(Project $project, $id)
    {
        return Admin::content(function (Content $content) use ($project, $id) {

            $content->header('编辑选手信息');
            $content->description('');

            $content->body($this->form($project)->edit($id));

            $err = \Request::session()->get('errors');
            if ($err && $err->get('msg')) {
                $content->withError('错误', $err->get('msg'));
            }
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Project $project)
    {
        return Admin::content(function (Content $content) use ($project) {

            $content->header('新增选手');
            $content->description('');

            $content->body($this->form($project));
        });
    }


    public function show(Project $project, $id)
    {
        return redirect(\URL::current() . '/edit');
    }

    public function update(Project $project, $id)
    {
        return $this->form($project)->update($id);
    }

    public function destroy(Project $project, $id)
    {
        if ($this->form($project)->destroy($id)) {
            return response()->json([
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ]);
        }
    }

    public function store(Project $project)
    {
        return $this->form($project)->store();
    }

    /**
     * 旋转图片
     */
    public function imgRotate()
    {
        $request = \Request::instance();
        $img = $request->post('img');
        $img = substr($img, strlen(config('app.url').'/uploads/'));
        $id = $request->post('id');
        $deg = $request->post('deg');
        $field = $request->post('field');
        $field = 'info_'.$field;
        $player = Player::repository()->findOneById($id);
        if($img=='' || !isset($player->{$field}) || $img!=$player->{$field}){
            return wj_json_message('数据已发生变更', 1);
        }
        $fileUrl = date('Y/md/') . md5('player_'.$id.'_'.uniqid()) .'.png';
        $path = uploads_path($fileUrl);
        $dir = dirname($path);
        if(file_exists($path)){
            return wj_json_message(['url'=>uploads_url($fileUrl)]);
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $image = new \Imagick(uploads_path($img));
        $image->rotateImage(new \ImagickPixel(), $deg);
        $image->writeImage($path);
        $player->{$field} = $fileUrl;
        $player->save();
        $image->destroy();
        return wj_json_message(['url'=>uploads_url($fileUrl)]);
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(Project $project)
    {
        $grid = Admin::grid(Player::class, function (Grid $grid) use ($project) {
            $grid->model()->where('project_id', '=', \Request::route('project')->id);
            $grid->exporter(new ExcelExporter($grid, $project));
            $form_design = @$project->conf_base_form_design ?: [];

            $grid->model()->orderBy('id', 'desc');
            $grid->filter(function (Grid\Filter $filter) use ($project) {


                $filter->between('created_at','创建时间')->datetime();
                $form_design = @$project->conf_base_form_design ?: [];


                //闭包函数,用于循环中保持变量
                $add_field = function ($field, Grid\Filter $filter) use ($project) {
                    if (!@$field->key) {
                        return;
                    }

                    $options = wj_obj2arr($field->options);
                    $options = @$options[$field->type] ?: [];

                    $dbcol = $field->key;

                    switch ($field->type) {
                        case 'integer':
                        case 'age':
                        case 'vote':
                            $filter->between($field->key, $field->name);
                            break;
                        case 'phone':
                            $filter->where(function (Builder $query) use ($dbcol) {
                                $query->where($dbcol, 'like', "{$this->input}%");
                            }, $field->name)->mobile();
                            break;
                        case 'city':
                            $d = wj_city_data(true);
                            $d = collect($d)->map(function ($v) {
                                $name = '';
                                if (@$v['country_name']) {
                                    $name .= $v['country_name'];
                                } else {
                                    return '';
                                }
                                if (@$v['province_name']) {
                                    $name .= '-' . $v['province_name'];
                                } else {
                                    return $name;
                                }
                                if (@$v['city_name']) {
                                    $name .= '-' . $v['city_name'];
                                } else {
                                    return $name;
                                }
                                if (@$v['region_name']) {
                                    $name .= '-' . $v['region_name'];
                                } else {
                                    return $name;
                                }
                                return $name;
                            })->filter()->all();
                            $filter->where(function (Builder $query) use ($dbcol) {
                                $ids = wj_city_offspring($this->input);
                                $query->whereIn($dbcol, $ids);
                            }, $field->name)->multipleSelect($d);
                            break;
                        case 'passport':
                            $filter->where(function (Builder $query) use ($dbcol) {
                                $query->where($dbcol, 'like', "{$this->input}%");
                            }, $field->name . '-类型')->radio(wj_mask(Player::model()->listPassportType(), $options['passport_type']));
                            $filter->where(function (Builder $query) use ($dbcol) {
                                $query->where($dbcol, 'like', "%:{$this->input}%");
                            }, $field->name . '-号码');
                            break;
                        case 'gender':
                            $filter->in($field->key, $field->name)->checkbox(Player::model()->listGender());
                            break;
                        case 'checkbox':
                            $filter->where(function (Builder $query) use ($dbcol) {
                                foreach ($this->input ?: [] as $item) {
                                    $query->orWhere($dbcol, 'like', "%:$item:%");
                                }
                            }, $field->name)->checkbox(collect($options['options'])->pluck('name', 'key')->all());
                            break;
                        case 'select':
                        case 'radio':
                            $filter->in($field->key, $field->name)->checkbox(collect($options['options'])->pluck('name', 'key')->all());
                            break;
                        default:
                            $filter->where(function (Builder $query) use ($dbcol) {
                                $query->where($dbcol, 'like', "{$this->input}%");
                            }, $field->name);
                            break;
                    }
                };

                $filter->equal('ticket_no', '选手编号');
                $filter->in('checked', '审核状态')->checkbox(Player::model()->listChecked());
                foreach ($form_design as $field) {
                    $add_field($field, $filter);
                }
            });

            $grid->id('ID')->sortable();
            $grid->ticket_no('选手编号');
            $grid->checked('审核状态')->display(function($v) {
                return @$this->listChecked()[$v] ?: '未知';
            });

            //闭包函数,用于循环中保持变量
            $add_field = function ($field, $grid) use ($project) {
                if ($field->type === 'upload') {
                    $grid->{"{$field->field}"}($field->name)->display(function () use ($field, $grid, $project) {
                        $this->setRelation('project', $project);
                        return '<div class="xzImg" style="height: 150px;"><img data-id="'.$this->id
                            .'" data-field="'.$field->field
                            .'" src="'.uploads_url($this->{"info_{$field->field}"}, 100, 100).'" height=150 /></div>';
                    });
                } elseif ($field->type === 'uploads') {
                    $grid->{"{$field->field}"}($field->name)->display(function () use ($field, $grid, $project) {
                        $this->setRelation('project', $project);
                        $values = $this->{"info_{$field->field}"};
                        $html = '<div id="layer-photos-'.$this->id.'">';
                        foreach ($values as $v) {
                            $html .= '<img class="openImg" data-id="'.$this->id.'" layer-src="'.uploads_url($v).'" src="'.uploads_url($v, 100, 100).'" width=100 />';
                        }
                        $html .= '</div>';
                        return $html;
                    });
                } elseif ($field->type == 'video') {
                    $grid->{"{$field->field}"}($field->name)->display(function () use ($field, $grid, $project) {
                        $this->setRelation('project', $project);
                        return '<video controls="controls" preload="auto" width="240px" height="180px" src="'.$this->{'info_'.$field->field}.'"></video>';
                    });
                } elseif ($field->field == 'price') {
                    //优化-价格排序
                    $grid->{"{$field->key}"}($field->name)->display(function () use ($field, $grid, $project) {
                        $this->setRelation('project', $project);
                        return $this->{"fmt_info_{$field->field}"};
                    })->sortable();
                } else {
                    $grid->{"{$field->field}"}($field->name)->display(function () use ($field, $grid, $project) {
                        $this->setRelation('project', $project);
                        return $this->{"fmt_info_{$field->field}"};
                    });
                }
            };
            foreach ($form_design as $field) {
                if (@$field->list !== 'on') {
                    continue;
                }
                $add_field($field, $grid);
            }

            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
            $grid->actions(function (Actions $actions) {
                $actions->prepend(linkto('<i data-id="'.$actions->getKey().'" class="fa fa-check"></i> ', 'javascript:void(0)'));

                $actions->prepend(linkto('<i data-id="'.$actions->getKey().'" class="fa fa-close"></i> ', 'javascript:void(0)'));
            });
            Admin::script('createXuanZhuanImg();addUpdatePlayerStatusEvent();openImages()');

            if(in_array(\Request::route('project')->id,[179,215])) {
                $uri = \Request::getRequestUri();
                $artworkid = \Request::route('project')->id;
                $grid->tools(function ($tools) use ($uri) {
                    $url = str_replace('/players', '/players/artimage', $uri);
                    $icon = "fa-save";
                    $text = "导出作品";
                    $tools->append(new ShowArtwork($url,$icon,$text));

                });
            }
        });

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form(Project $project)
    {
        Form::extend('city', CityField::class);
        Form::extend('passport', PassportField::class);


        $form = Admin::form(Player::class, function (Form $form) use ($project) {
            $form->hidden('id', 'ID');
            $form->hidden('project_id', '项目ID')->default($project->id);
            $form->select('merchant_id', '关联客户')->options(Merchant::get()->pluck('name', 'id'));

            $form_design = @$project->conf_base_form_design ?: [];

            $form->radio('checked', '审核状态')->options(Player::model()->listChecked())->default(function(Form $form) {
                return $form->model()->checked;
            });

            if (@$project->conf_baoming->ticket_mode === 'manual') {
                $form->text('ticket_no', '编号');
            }

            //闭包函数,用于循环中保持变量
            $add_field = function ($field, $form) use ($project) {
                $col = strpos($field->key, 'vote') === 0 ? $field->key : "info_{$field->field}";

                $def = function ($v) use ($field) {
                    return $v->model()->{"info_{$field->field}"};
                };

                $options = wj_obj2arr($field->options);
                $options = @$options[$field->type] ?: [];

                switch ($field->type) {
                    case 'integer':
                    case 'age':
                    case 'vote':
                        $frmField = $form->number($col, $field->name)->default($def);
                        break;
                    case 'phone':
                        $frmField = $form->mobile($col, $field->name)->rules('regex:/^1/')->default($def);
                        break;
                    case 'text':
                        $frmField = $form->textarea($col, $field->name)->default($def);
                        break;
                    case 'rich':
                        $frmField = $form->editor($col, $field->name)->default($def);
                        break;
                    case 'city':
                        $frmField = $form->city($col, $field->name)->options($options)->default($def);
                        break;
                    case 'passport':
                        $frmField = $form->passport($col, $field->name)->options($options)->default($def);
                        break;
                    case 'gender':
                        $frmField = $form->radio($col, $field->name)->options(Player::model()->listGender())->default($def);
                        break;
                    case 'select':
                    case 'checkbox':
                    case 'radio':
                        $frmField = $form->{$field->type}($col, $field->name)->options($project->listOptionsByField($field->field))->default($def);
                        break;
                    case 'upload':
                        $frmField = $form->image($col, $field->name)->uniqueName()->move(date('Y/md'))->default($def);
                        break;
                    case 'uploads':
                        $frmField = $form->multipleImage($col, $field->name)->uniqueName()->move(date('Y/md'))->removable()->default($def);
                        break;
                    case 'birthday':
                        $frmField = $form->date($col, $field->name)->options([
                            'viewMode' => 'years'
                        ])->default($def);
                        break;
                    case 'datetime':
                        $options = $field->options->datetime;
                        $method = '';
                        $arr = is_string($options->datetime_type) ? [$options->datetime_type] : $options->datetime_type;
                        if (in_array('date', $arr)) {
                            $method .= 'date';
                        }
                        if (in_array('time', $arr)) {
                            $method .= 'time';
                        }
                        if ($options->input_type === 'range') {
                            $method .= 'Range';
                            $frmField = $form->$method($col . '_start', $col . '_end', $field->name)->default(function($v) use ($field) {
                                return [
                                    'start' => $v->model()->{"info_{$field->field}_start"},
                                    'end'   => $v->model()->{"info_{$field->field}_end"}
                                ];
                            });
                        } else {
                            $frmField = $form->$method($col, $field->name)->default($def);
                        }
                        break;
                    default:
                        $frmField = $form->text($col, $field->name)->default($def);
                        break;
                }

                if (@$field->required === 'on') {
                    if ($field->type != 'upload') {
                        $frmField->rules('required');
                    }
                }
            };


            foreach ($form_design as $field) {
                $add_field($field, $form);
            }
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });

        $form->saving(function (Form $form) {
            /** @var Player $model */
            $model = $form->model();
            $model->withOperatorIp(\Request::ip())
                ->withOperatorUid(Admin::user()->id)
                ->withOperatorNote('后台调整票额')
                ->withOperatorForce();
        });

        return $form;
    }

    public function updateStatus()
    {
        $request = \Request::instance();
        $id = (int)$request->post('id');
        $checked = (int)$request->post('checked');
        if ($id<=0 || $checked<=0 || !in_array($checked, [1,2])) {
            abort(404);
        }
        $player = Player::find($id);
        if ($player == null) {
            abort(404);
        }
        $player->checked = $checked;
        $player->save();
        return wj_json_message('success');
    }


    public function artImage(Project $project)
    {
        set_time_limit(0);
//        $project = Project::matchByPath('/txhysyds2018');
        $project = Project::matchByPath($project->path);
        $as = new ArtImgExcelExporter($this->grid($project), $project);
        $image = $as->export();

        //下面是实例操作过程：
        $dfile = tempnam('/tmp', 'tmp');//产生一个临时文件，用于缓存下载文件
        $zip = new zipfile();
        //----------------------
        $str = \Illuminate\Support\Str::random(32);
        $filename = $str.'.zip'; //下载的默认文件名

        foreach($image as $K => $v){
            $zip->add_file(file_get_contents($v['image_src']), iconv('utf-8','gbk//IGNORE',$v['image_name']));
        }
        //----------------------
        $zip->output($dfile);

        // 下载文件
        @ob_clean();
        header('Pragma: public');
        header('Last-Modified:'.gmdate('D, d M Y H:i:s') . 'GMT');
        header('Cache-Control:no-store, no-cache, must-revalidate');
        header('Cache-Control:pre-check=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding:binary');
        header('Content-Encoding:none');
        header('Content-type:multipart/form-data');
        header('Content-Disposition:attachment; filename="'.$filename.'"'); //设置下载的默认文件名
        header('Content-length:'. filesize($dfile));
        $fp = fopen($dfile, 'r');
        while(connection_status() == 0 && $buf = @fread($fp, 8192)){
            echo $buf;
        }
        fclose($fp);
        @unlink($dfile);
        @flush();
        @ob_flush();
        exit();
    }

}

//下载图片
class zipfile {
    var $datasec = array ();
    var $ctrl_dir = array ();
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
    var $old_offset = 0;

    function unix2_dostime($unixtime = 0){
        $timearray = ($unixtime == 0) ? getdate () : getdate($unixtime);
        if ($timearray ['year'] < 1980){
            $timearray ['year'] = 1980;
            $timearray ['mon'] = 1;
            $timearray ['mday'] = 1;
            $timearray ['hours'] = 0;
            $timearray ['minutes'] = 0;
            $timearray ['seconds'] = 0;
        }
        return (($timearray ['year'] - 1980) << 25) | ($timearray ['mon'] << 21) | ($timearray ['mday'] << 16) | ($timearray ['hours'] << 11) | ($timearray ['minutes'] << 5) | ($timearray ['seconds'] >> 1);
    }

    function add_file($data, $name, $time = 0){
        $name = str_replace('\\', '/', $name);

        $dtime = dechex($this->unix2_dostime($time));
        $hexdtime = '\x' . $dtime [6] . $dtime [7] . '\x' . $dtime [4] . $dtime [5] . '\x' . $dtime [2] . $dtime [3] . '\x' . $dtime [0] . $dtime [1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr = "\x50\x4b\x03\x04";
        $fr .= "\x14\x00";
        $fr .= "\x00\x00";
        $fr .= "\x08\x00";
        $fr .= $hexdtime;

        $unc_len = strlen($data);
        $crc = crc32($data);
        $zdata = gzcompress($data);
        $zdata = substr(substr($zdata, 0, strlen($zdata)- 4), 2);
        $c_len = strlen($zdata);
        $fr .= pack('V', $crc);
        $fr .= pack('V', $c_len);
        $fr .= pack('V', $unc_len);
        $fr .= pack('v', strlen($name));
        $fr .= pack('v', 0);
        $fr .= $name;

        $fr .= $zdata;
        $fr .= pack('V', $crc);
        $fr .= pack('V', $c_len);
        $fr .= pack('V', $unc_len);

        $this->datasec [] = $fr;

        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";
        $cdrec .= "\x14\x00";
        $cdrec .= "\x00\x00";
        $cdrec .= "\x08\x00";
        $cdrec .= $hexdtime;
        $cdrec .= pack('V', $crc);
        $cdrec .= pack('V', $c_len);
        $cdrec .= pack('V', $unc_len);
        $cdrec .= pack('v', strlen($name));
        $cdrec .= pack('v', 0);
        $cdrec .= pack('v', 0);
        $cdrec .= pack('v', 0);
        $cdrec .= pack('v', 0);
        $cdrec .= pack('V', 32);

        $cdrec .= pack('V', $this->old_offset);
        $this->old_offset += strlen($fr);

        $cdrec .= $name;

        $this->ctrl_dir[] = $cdrec;
    }

    function add_path($path, $l = 0){
        $d = @opendir($path);
        $l = $l > 0 ? $l : strlen($path) + 1;
        while($v = @readdir($d)){
            if($v == '.' || $v == '..'){
                continue;
            }
            $v = $path . '/' . $v;
            if(is_dir($v)){
                $this->add_path($v, $l);
            } else {
                $this->add_file(file_get_contents($v), substr($v, $l));
            }
        }
    }

    function file(){
        $data = implode('', $this->datasec);
        $ctrldir = implode('', $this->ctrl_dir);
        return $data . $ctrldir . $this->eof_ctrl_dir . pack('v', sizeof($this->ctrl_dir)) . pack('v', sizeof($this->ctrl_dir)) . pack('V', strlen($ctrldir)) . pack('V', strlen($data)) . "\x00\x00";
    }

    function add_files($files){
        foreach($files as $file){
            if (is_file($file)){
                $data = implode("", file($file));
                $this->add_file($data, $file);
            }
        }
    }

    function output($file){
        $fp = fopen($file, "w");
        fwrite($fp, $this->file ());
        fclose($fp);
    }
}