<?php
namespace App\Models\Bargain;


use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class BargainLog extends Model
{
    use ExModel;

    protected $table = 'bargain_log';

    protected $fillable = ['project_id', 'player_id', 'merchant_id', 'openid', 'name', 'zhuli_openid', 'zhuli_name', 'price', 'created_at', 'note'];



}