<?php

namespace App\Admin\Controllers;

use App\Models\Project;
use App\Models\News\Blocks;
use App\Zhuanti\News\NewsOperator;
use Illuminate\Support\MessageBag;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;


class NewsController extends Controller
{
  public function blocks($id)
  {

    $project = Project::where('id', $id)->first();
    $oldConfs = $project->getOriginal('configs');
    $oldConfs = json_decode($oldConfs);
    $news_channles = $oldConfs->news->news_channel;
    $news_channles_ = explode(",", $news_channles);
    $channels = array_combine($news_channles_, $news_channles_);

    return Admin::content(function (Content $content) use ($id, $channels) {
      $blocks_type = Blocks::$blockTypes;
      $content->header('板块列表');
      $content->description('');

      $create_html = [];
      $block_index = [1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六', 7 => '七', 8 => '八'];
      foreach($block_index as $bid => $index) {
        $create_html[] = '<a style="font-weight:bold;margin-right:30px;" href="/admin/news/'.$id.'/block/'.$bid.'">新增板块'.$index.'</a>';
      }

      $content->row('<p style="margin-bottom: 20px;">'.implode('',$create_html).'</p>');
      $content->body(Admin::grid(Blocks::class, function (Grid $grid) use ($id, $blocks_type, $channels) {
        $grid->disableCreation();
        $grid->disableRowSelector();
        //$grid->disableActions();
        $grid->actions(function ($actions) {
          $actions->disableDelete();
          $actions->append('<a href="javascript:if(confirm(\'确定删除吗？\')) { window.location.href = \'/admin/news/'.$actions->getKey().'/blocks/delete\'}" data-id="31" class="grid-row-delete"><i class="fa fa-trash"></i></a>');
        });


        $grid->model()->where('project_id', '=', $id)->orderBy('id', 'asc');
        $grid->filter(function (Grid\Filter $filter) use($channels) {
          $filter->equal('block_id', '板块')->radio(Blocks::$blockTypes);

          $filter->equal('channel_id', '频道')->radio($channels);

          $filter->between('created_at', '创建时间')->datetime();
        });

        $grid->id('ID')->sortable();
        $grid->column('channel_id', '频道');
        $grid->block_id('板块')->display(function ($id) use ($blocks_type) {
          return $blocks_type[$id];
        });



        $grid->created_at('创建时间');
      }));
    });
  }



  /*
   *            $grid->channel_id('频道')->display(function ($id) use ($channels) {
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
   *
   * */



  public function block(Request $request, $project_id, $block_id)
  {
    $project = Project::where('id', $project_id)->first();
    $oldConfs = $project->getOriginal('configs');
    $oldConfs = json_decode($oldConfs);
    $news_channles = $oldConfs->news->news_channel;
    if ($news_channles) {
      $news_channles = str_replace("，", ",", $news_channles);
      $news_channles = str_replace(" ", "", $news_channles);
      $news_channles_arr = explode(",", $news_channles);
    }
    $data = [];
    $blockTypes = Blocks::$blockTypes;
    if($request->isMethod('post')) {
      $arr = \Request::all();
      $id = $arr['id'];
      $field = $arr['fields'];

      if(!$field['channel_id']) {
        return back()->withInput(compact('project_id', 'block_id', 'id', 'field', 'blockTypes', 'news_channles_arr', 'project'))->with(['toastr' => new MessageBag([
          'message' => '请选择频道',
          'type' => 'error'
        ])]);
      }





  //传图 START


      if ($block_id == Blocks::TYPE_BLOCK_1) {
        $imgs = ['img' => 'img_name', 'big_img' => 'big_img_name'];
      } elseif($block_id == Blocks::TYPE_BLOCK_2) {
        $imgs = ['img' => 'img_name', 'big_img' => 'big_img_name', 'list_img1' => 'list_img1_name', 'list_img2' => 'list_img2_name', 'list_img3' => 'list_img3_name'];
      } else {
        $imgs = ['img' => 'img_name'];
      }

        foreach ($imgs as $mk => $mv) {

          if (isset($arr['fields'][$mk]) && $arr['fields'][$mk]) {
            $img = $arr['fields'][$mk];
            try {
              $pic = move_to_uploads($img, [
                'size' => 0.5 * 1024 * 1024,
                'ext' => ['jpg', 'png', 'jpeg'],
                'mime' => ['image/*']
              ]);
            } catch (UploadsStorageException $ex) {
              if ($ex->getCode() === UploadsStorageException::CODE_SIZE_LIMITED) {
                $err = '照片尺寸超限，需在500K 以内!';
              } elseif ($ex->getCode() === UploadsStorageException::CODE_EXT_LIMITED) {
                $err = '请上传正确的图片文件格式(jpg/png)!';
              } elseif ($ex->getCode() === UploadsStorageException::CODE_MIME_LIMITED) {
                $err = '请上传正确的图片文件格式(jpg/png)!';
              } else {
                $err = '图片保存失败!';
              }

              return back()->withInput(compact('project_id', 'block_id', 'id', 'field', 'blockTypes', 'news_channles_arr', 'project'))->with(['toastr' => new MessageBag([
                'message' => $err,
                'type' => 'error'
              ])]);

              //return response()->json(['message' => $err, 'status' => 1]);
            } catch (\Exception $e) {

              return back()->withInput(compact('project_id', 'block_id', 'id', 'field', 'blockTypes', 'news_channles_arr', 'project'))->with(['toastr' => new MessageBag([
                'message' => $e->getMessage(),
                'type' => 'error'
              ])]);

              //return response()->json(['message' => $e->getMessage(), 'status' => 1]);

            }
            $arr['fields'][$mv] = $pic;
            $arr['fields'][$mk] = null;
          }
        }


  //传图 END





        if(in_array($block_id,
        [Blocks::TYPE_BLOCK_3, Blocks::TYPE_BLOCK_4, Blocks::TYPE_BLOCK_5, Blocks::TYPE_BLOCK_6, Blocks::TYPE_BLOCK_7, Blocks::TYPE_BLOCK_8]))  {

        if(array_key_exists('img', $arr)) {
          for ($k = 0; $k < count($arr['title']); $k++) {
            if (!array_key_exists($k, $arr['img'])) {
              continue;
            }
            try {
              $pic = move_to_uploads($arr['img'][$k], [
                'size' => 0.5 * 1024 * 1024,
                'ext' => ['jpg', 'png', 'jpeg'],
                'mime' => ['image/*']
              ]);
            } catch (UploadsStorageException $ex) {
              if ($ex->getCode() === UploadsStorageException::CODE_SIZE_LIMITED) {
                $err = '照片尺寸超限，需在500K 以内!';
              } elseif ($ex->getCode() === UploadsStorageException::CODE_EXT_LIMITED) {
                $err = '请上传正确的图片文件格式(jpg/png)!';
              } elseif ($ex->getCode() === UploadsStorageException::CODE_MIME_LIMITED) {
                $err = '请上传正确的图片文件格式(jpg/png)!';
              } else {
                $err = '图片保存失败!';
              }
              return response()->json(['message' => $err, 'status' => 1]);
            } catch (\Exception $e) {
              return response()->json(['message' => $e->getMessage(), 'status' => 1]);
            }
            $arr['img_name'][$k] = $pic;
          }
        }
        $news_conf = [];





        for($i = 0;$i < count($arr['title']);$i++){

          $news_conf[$i]['title'] = $arr['title'][$i];
          $news_conf[$i]['img_name'] = $arr['img_name'][$i];
          $news_conf[$i]['link'] = $arr['link'][$i];

          if($block_id == Blocks::TYPE_BLOCK_4) {
            $news_conf[$i]['list_infos'] = $arr['list_infos'][$i];
          }
        }

        if($news_conf) {
          $arr['fields']['img_infos'] =  $news_conf;
        }






      }


      //$block = Blocks::where('project_id','=',$project_id)->where('block_id','=', $block_id)->first();
      $block = Blocks::where('id','=',$id)->first();
      $data = [
        'project_id' => $project_id,
        'merchant_id' => 0,
        'channel_id' => isset($arr['fields']['channel_id']) ? $arr['fields']['channel_id'] : 0,
        'block_id' => $block_id,
        'block'.$block_id =>  isset($arr['fields']) && $arr['fields'] ? json_encode($arr['fields']) : ''
      ];

      if (!$block) {
        $block = new Blocks();
        $block->fill($data);
        $block->save();
      } else {
        $block->update($data);
      }
      $id = $block->id;
      return redirect('/admin/news/'.$project_id.'/blocks/'.$id.'/edit');
    }



    $data = $field = [];
    //$path = $project->getOriginal('path');
    //dd($path);

    //$path = $project->path;
    //dd($project);

    //dd($path);
    return Admin::content(function (Content $content) use ($project_id, $block_id, $news_channles_arr, $blockTypes, $project, $data, $field) {
      $content->description('创建板块');
      $content->body(view('admin::news.block'.$block_id, compact('project_id', 'block_id','blockTypes','news_channles_arr', 'project', 'data', 'field')));
    });
  }






  public function detail(Project $project, Request $request, $id) {
    $block = Blocks::where('id','=',$id)->first();
    $project_id = $block->project_id;
    $block_id = $block->block_id;

    if ($block_id) {
      $arr['project_id'] = $project_id;
      $arr['block_id'] = $block_id;
      $arr['channel_id'] = $block->channel_id;
      $arr['block_id'] = $block->block_id;
      if($block_id == 1) {
        $block_info = $block->block1;
      } elseif($block_id == 2) {
        $block_info = $block->block2;
      } elseif($block_id == 3) {
        $block_info = $block->block3;
      } elseif($block_id == 4) {
        $block_info = $block->block4;
      } elseif($block_id == 5) {
        $block_info = $block->block5;
      } elseif($block_id == 6) {
        $block_info = $block->block6;
      } elseif($block_id == 7) {
        $block_info = $block->block7;
      } elseif($block_id == 8) {
        $block_info = $block->block8;
      }
    }

    if ($block_info) {
      $field = json_decode($block_info, true);
    } else {
      $field = [];
    }


    //dd($field);


    //$block->block


    //dd($field);


    return Admin::content(function (Content $content) use ($project_id, $block_id, $field, $id) {
      $project = Project::where('id', $project_id)->first();
      $oldConfs = $project->getOriginal('configs');
      $oldConfs = json_decode($oldConfs);
      $news_channles = $oldConfs->news->news_channel;
      if ($news_channles) {
        $news_channles = str_replace("，", ",", $news_channles);
        $news_channles = str_replace(" ", "", $news_channles);
        $news_channles_arr = explode(",", $news_channles);
      }
      $data = [];
      $blockTypes = Blocks::$blockTypes;
      $content->description('创建板块');
      $content->body(view('admin::news.block'.$block_id, compact('project_id', 'block_id', 'id', 'field', 'data','blockTypes','news_channles_arr', 'project')));
    });

  }





  public function storeBlock(Request $request, $project_id, $block_id)
  {
    if($request->isMethod('post')) {
      $arr = \Request::all();
      $imgs = ['img' => 'img_name', 'big_img' => 'big_img_name'];
      foreach($imgs as $mk => $mv) {

        if (isset($arr['fields'][$mk]) && $arr['fields'][$mk]) {
          $img = $arr['fields'][$mk];
          try {
            $pic = move_to_uploads($img, [
              'size' => 0.5 * 1024 * 1024,
              'ext' => ['jpg', 'png', 'jpeg'],
              'mime' => ['image/*']
            ]);
          } catch (UploadsStorageException $ex) {
            if ($ex->getCode() === UploadsStorageException::CODE_SIZE_LIMITED) {
              $err = '照片尺寸超限，需在500以内!';
            } elseif ($ex->getCode() === UploadsStorageException::CODE_EXT_LIMITED) {
              $err = '请上传正确的图片文件格式(jpg/png)!';
            } elseif ($ex->getCode() === UploadsStorageException::CODE_MIME_LIMITED) {
              $err = '请上传正确的图片文件格式(jpg/png)!';
            } else {
              $err = '图片保存失败!';
            }
            return response()->json(['message' => $err, 'status' => 1]);
          } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => 1]);
          }
          $arr['fields'][$mv] = $pic;
          $arr['fields'][$mk] = null;
        }
      }
      $block = Blocks::where('project_id','=',$project_id)->where('block_id','=', $block_id)->first();
      if (!$block) {
        $block = new Blocks();
      }
      $block->fill(
        [
          'project_id' => $project_id,
          'merchant_id' => 0,
          'channel_id' => isset($arr['fields']['channel_id']) ? $arr['fields']['channel_id'] : 0,
          'block_id' => $block_id,
          'block1' =>  isset($arr['fields']) && $arr['fields'] ? json_encode($arr['fields']) : ''
      ]
     );
     $block->save();

     $id = $project_id;
    }




  }




  /**
   * 集字设置
   */
  public function create_BAK($id)
  {
    $project = Project::repository()->findOneById($id);
    if (\Request::isMethod('post')) {
      //return $this->storeSettingForm($project);
    }

    $oldConfs = $project->getOriginal('configs');
    $oldConfs = json_decode($oldConfs);
    $news_channles = $oldConfs->news->news_channel;
    if($news_channles) {
      $news_channles = str_replace("，", ",", $news_channles);
      $news_channles = str_replace(" ", "", $news_channles);
      $news_channles_arr = explode(",", $news_channles);
    }


    return Admin::content(function (Content $content) use ($project) {

      $content->header('功能管理');
      $content->description('列表');
      $content->body($this->grid($project));
    });



    /*

    return Admin::content(function (Content $content) {
      $content->header('功能管理');
      $content->description('列表');
      $content->row(function (Row $row) {
        $row->column(12, function (Column $column) {
          $form = new \Encore\Admin\Widgets\Form();
          $form->action('/admin/yxfunction');
          $form->select('block_id', '板块名称')->options(Blocks::$blockTypes);
          $form->select('channel_id', '频道名称')->options(array('101' => '频道1', '102' => '频道2'));
          $form->image('block1_banner1', '板块一[导航图标]')->help('图片尺寸：1200*100')->uniqueName()->move(date('Y/md'));
          $form->image('block1_left_pic', '板块一[左图]')->help('图片尺寸：580*350')->uniqueName()->move(date('Y/md'));
          $form->text('block1_right_top_title', '板块一[右上-标题]')->help('标题+链接,标题和链接用#连接, 例如：习近平这些贺信，与未来紧密关联#http://365jia.cn');

          $form->textarea('block1_right_top_intro', '板块一[右上-引言]')->rows(5)->help('引言+链接,引言和链接用#连接, 例如：世界公众科学素质促进大会在北京召开#http://365jia.cn');
          $form->text('block1_right_down_list1', '板块一[右下-列表1]')->help('标题+链接,标题和链接用#连接, 例如：习近平这些贺信，与未来紧密关联#http://365jia.cn');
          $form->text('block1_right_down_list2', '板块一[右下-列表2]')->help('标题+链接,填写格式同上');
          $form->text('block1_right_down_list3', '板块一[右下-列表3]')->help('标题+链接,填写格式同上');
          $form->text('block1_right_down_list4', '板块一[右下-列表4]')->help('标题+链接,填写格式同上');







          $form->image('block2_banner1', '板块二[导航图标]')->help('图片尺寸：1200*100')->uniqueName()->move(date('Y/md'));
          $form->text('block2_tag', '板块二[标签]');
          $form->image('block2_left_pic', '板块二[左图]')->help('图片尺寸：630*380')->uniqueName()->move(date('Y/md'));
          $form->textarea('block2_left_pic_info', '板块二[左图-标题+链接]')->rows(3)->help('标题+链接,标题和链接用#连接, 例如：习近平这些贺信，与未来紧密关联#http://365jia.cn');

          $form->image('block2_right_pic1', '板块二[右-列表1-图片]')->help('图片尺寸：165*99')->uniqueName()->move(date('Y/md'));
          $form->textarea('block2_right_pic1_info1', '板块二[右-列表1-标题+引言+链接]')->rows(6)->help('填写格式：标题#引言#链接');
          $form->image('block2_right_pic2', '板块二[右-列表2-图片]')->help('图片尺寸：165*99')->uniqueName()->move(date('Y/md'));
          $form->textarea('block2_right_pic2_info2', '板块二[右-列表2-标题+引言+链接]')->rows(6)->help('填写格式：标题#引言#链接');
          $form->image('block2_right_pic3', '板块二[右-列表3-图片]')->help('图片尺寸：165*99')->uniqueName()->move(date('Y/md'));
          $form->textarea('block2_right_pic3_info3', '板块二[右-列表3-标题+引言+链接]')->rows(6)->help('填写格式：标题#引言#链接');







          $form->image('block4_banner1', '板块四[导航图标]')->help('图片尺寸：1200*100')->uniqueName()->move(date('Y/md'));
          //380x230
          $form->image('block4_down_area1', '板块四[图片1]')->help('图片尺寸：380*230')->uniqueName()->move(date('Y/md'));
          $form->text('block4_down_area1_info', '板块四[图片1-标题+链接]')->help('填写格式：标题#链接');
          $form->textarea('block4_down_area1_list_info', '板块四[图片1-下方列表]')->rows(12)->help('填写格式：<br/>标题#链接<br/>标题#链接<br/>标题#链接');

          $form->image('block4_down_area2', '板块四[图片2]')->help('图片尺寸：380*230')->uniqueName()->move(date('Y/md'));
          $form->text('block4_down_area2_info', '板块四[图片2-标题+链接]')->help('填写格式：标题#链接');
          $form->textarea('block4_down_area2_list_info', '板块四[图片2-下方列表]')->rows(12)->help('填写格式：<br/>标题#链接<br/>标题#链接<br/>标题#链接');

          $form->image('block4_down_area3', '板块四[图片3]')->help('图片尺寸：380*230')->uniqueName()->move(date('Y/md'));
          $form->text('block4_down_area3_info', '板块四[图片3-标题+链接]')->help('填写格式：标题#链接');
          $form->textarea('block4_down_area3_list_info', '板块四[图片3-下方列表]')->rows(12)->help('填写格式：<br/>标题#链接<br/>标题#链接<br/>标题#链接');

          $column->append((new Box('新增', $form))->style('success'));
        });
      });
    });
    */
  }


  public function delete($id) {
    $block = Blocks::where('id', $id)->first();
    $block->delete();
    return  redirect('/admin/news/'.$block->project_id.'/blocks');
  }


  /**
   * 集字设置
   */
  private function storeSettingForm(Project $project)
  {
    $fields = \Request::input('fields');
    $time_plans = [];
    $diff = [];
    $plan = [];
    foreach ($fields as $k => $val) {
      //timeplan
      $startTime = 0;
      foreach ($val['timeplan'] as $key => $value) {
        $start = strtotime($value['start']);
        $end = strtotime($value['end']);
        if ($startTime>0 && $start<=$startTime) {
          return back()->withInput(['fields' => $fields])->with(['toastr' => new MessageBag([
            'message' => '时间计划有误，后面计划的开始时间必须大于前次的结束时间',
            'type' => 'error'
          ])]);
        }
        $startTime = $end;
        if ($start >= $end) {
          return back()->withInput(['fields' => $fields])->with(['toastr' => new MessageBag([
            'message' => '开始时间不能大于结束时间',
            'type' => 'error'
          ])]);
        }
        $plan[$k][$key]['start'] = $start;
        $plan[$k][$key]['end'] = $end;
        $diff[$k][$key] = $end - $start;
        if ($key == 0) {
          $len[$k][$key] = 0;
        } else {
          $len[$k][$key] = array_sum($diff[$k]) - $diff[$k][$key];
        }
        $plan[$k][$key]['len'] = $len[$k][$key];
      }
      $plans[$k]['time_total'] = array_sum($diff[$k]);
      $plans[$k]['plans'] = $plan[$k];
      $time_plans[$k]['key'] = trim($val['key']);
      $time_plans[$k]['name'] = trim($val['name']);
      $time_plans[$k]['is_limit_count'] = trim($val['is_limit_count']);
      $time_plans[$k]['total'] = trim($val['total']);
      $time_plans[$k]['timeplan'] = $plans[$k];
      if (empty($val['key'])) {
        unset($time_plans[$k]);
      }
    }
    $fields = collect($time_plans);
    $confs = $project->configs;
    $confs->base_font_setting = collect($fields)->values();
    $project->configs = $confs;
    $project->save();
    $jiziOperator = JiziOperator::instance($project->id);
    $hashs = $jiziOperator->getJzHash();
    foreach ($time_plans as $v) {
      if (!isset($hashs[$v['key']])) {
        $fontCache[$v['key']] = 0;
      } else {
        unset($hashs[$v['key']]);
      }
    }
    foreach ($hashs as $k => $v) {
      $jiziOperator->delJzHash($k);
    }
    !empty($fontCache) && $jiziOperator->setJzHash($fontCache);

    $toastr = new MessageBag([
      'message' => '保存成功',
      'type' => 'success'
    ]);
    return back()->withInput(['fields' => $fields])->with(compact('toastr'));
  }


  protected function grid($project)
  {
    return Admin::grid(JiziLog::class, function (Grid $grid) use ($project) {
      $grid->disableCreation();
      $grid->disableRowSelector();
      $grid->disableActions();
      $grid->model()->with('player')->where('project_id', '=', $project->id)->orderBy('id', 'desc');

      $grid->filter(function (Grid\Filter $filter) {
        $filter->equal('player_id', '选手ID');
        $filter->equal('operator_uid', '操作人ID');
        $filter->like('openid', 'OPEN ID');
        $filter->between('created_at', '集字/图时间')->datetime();
      });
      $grid->id('ID')->sortable();
      $grid->column('player_id', '选手ID');
      $grid->column('ply_name', '姓名')->display(function(){
        return isset($this->player['info']->name) ? $this->player['info']->name : '';
      });
      $grid->column('ply_phone', '手机号')->display(function(){
        return isset($this->player['info']->phone) ? $this->player['info']->phone : '';
      });
      $grid->column('openid', '投票人OPEN ID');
      $grid->column('operator_uid', '操作人 ID');
      $grid->column('content', '获得的字/图');
      $grid->column('note', '备注');

      $grid->created_at('集字/图时间');
    });
  }


  protected function form()
  {
    return Admin::form(JiziLog::class, function (Form $form) {

      $form->display('id', 'ID');
      $form->text('project_id', '项目 ID');
      $form->text('merchant_id', '客户ID');
      $form->text('player_id', '选手ID');
      $form->hidden('operator_uid')->value(Admin::user()->id);
      $form->hidden('ip')->value(\Request::ip());
      $form->hidden('note')->value('人为操作');

      $form->display('created_at', 'Created At');
      $form->display('updated_at', 'Updated At');
    });
  }

}
