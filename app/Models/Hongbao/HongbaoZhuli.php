<?php
namespace App\Models\Hongbao;


use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class HongbaoZhuli extends Model
{
    use ExModel;

    protected $fillable = ['project_id', 'player_id', 'openid', 'zhuli_name',
         'zhuli_openid', 'created_at'];



}