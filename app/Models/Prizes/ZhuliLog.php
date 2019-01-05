<?php
namespace App\Models\Prizes;


use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class ZhuliLog extends Model
{
    use ExModel;

    protected $table = 'zhuli_log';

    protected $fillable = ['project_id', 'player_id', 'openid', 'zhuli_name',
         'zhuli_openid', 'created_at', 'note'];



}