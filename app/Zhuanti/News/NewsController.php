<?php
namespace App\Zhuanti\News;
use App\Http\Controllers\BaseController;
use App\Models\News\Blocks;

class NewsController extends BaseController
{
  /**
   * 首页
   */
  public function index($id)
  {
    $proj = $this->getProject();

    $oldConfs = $proj->getOriginal('configs');
    $oldConfs = json_decode($oldConfs, true);
    $whole_configs = $oldConfs['news'];

    if (isset($whole_configs['news_channel'])) {
      $channel_arr = array_flip(explode(",", $whole_configs['news_channel']));
    }

 /*
  *  "bg_color" => "#d13f59"
  "news_channel" => "111,222,333"
  "news_top_pic" => "2018/0917/3ff7dccb8ed4ff2f4c63eeec1f5f20e6.png"
  * */


    $blocks = Blocks::where('project_id', $proj->id)->where('channel_id', '<>', 'NULL')->where('block_id', '>', 0)
        ->orderBy('channel_id', 'asc')->orderBy('block_id', 'asc')->get();

      $arr = [];
      foreach ($blocks as $k => $block) {

        if ($block->block_id == Blocks::TYPE_BLOCK_2) {
          if (!$block->block2) {
            continue;
          }
          $block2_infos = json_decode($block->block2, true);
          if (!isset($block2_infos['sub_block_tag'])) {
            continue;
          }
          $arr[$block->channel_id][$block2_infos['sub_block_tag']] = $block;
        } else {
          $arr[$block->block_id][$block->id] = $block;
        }

        //板块二的话有标签类别
        // echo 'channel_id:'.$block->channel_id . '========>   block_id:'. $block->block_id . '==========> block_id'. $block->id;
        // echo "<br/>";
      }

      //exit();

    $blocks = Blocks::where('project_id', $proj->id)->where('channel_id', '<>', 'NULL')->where('block_id', '=', 2)
      ->orderBy('channel_id', 'asc')->orderBy('block_id', 'asc')->get();

    $tag = [];
    foreach ($blocks as $k => $block) {
      if ($block2 = $block->block2) {
        $block2_ = json_decode($block2, true);
        if(isset($block2_['sub_block_tag']) && $block2_['sub_block_tag']) {
          $tag[] = $block2_['sub_block_tag'];
        }
      }
    }
      //return compact('players', 'pager', 'rawOrderBy', 'proj', 'keyword');



    $this->assign['whole_configs'] = $whole_configs;
    $this->assign['pro_name'] = $proj->name;
    $this->assign['arr'] = $arr;
    $this->assign['tags'] = $tag;
    $this->assign['channel_arr'] = $channel_arr;
    return $this->render();
  }

}