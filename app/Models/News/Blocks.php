<?php
namespace App\Models\News;


use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Wanjia\Common\Database\ExModel;

class Blocks extends Model
{
  use ExModel;

  protected $table = 'blocks';

  protected $fillable = ['project_id', 'merchant_id', 'channel_id', 'block_id', 'block1',
    'block2', 'block3', 'block4', 'block5', 'block6', 'block7', 'block8', 'created_at', 'updated_at'];

  const TYPE_BLOCK_1 = 1;
  const TYPE_BLOCK_2 = 2;
  const TYPE_BLOCK_3 = 3;
  const TYPE_BLOCK_4 = 4;
  const TYPE_BLOCK_5 = 5;
  const TYPE_BLOCK_6 = 6;
  const TYPE_BLOCK_7 = 7;
  const TYPE_BLOCK_8 = 8;


  public static $blockTypes = [
    self::TYPE_BLOCK_1 => '板块一',
    self::TYPE_BLOCK_2 => '板块二',
    self::TYPE_BLOCK_3 => '板块三',
    self::TYPE_BLOCK_4 => '板块四',
    self::TYPE_BLOCK_5 => '板块五',
    self::TYPE_BLOCK_6 => '板块六',
    self::TYPE_BLOCK_7 => '板块七',
    self::TYPE_BLOCK_8 => '板块八',
  ];


  public function myproject()
  {
    return $this->belongsTo(Project::class, 'project_id', 'id');
  }

}