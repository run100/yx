<?php
namespace App\Models\Hongbao;


use App\Models\Player;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Wanjia\Common\Database\ExModel;

class HongbaoLog extends Model
{
    use ExModel;

    protected $fillable = ['player_id', 'project_id', 'openid', 'wx_name', 'ip',
        'is_win', 'money', 'created_at'];

    public function getWinTextAttribute()
    {
        return $this->is_win ? '是' : '否';
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id', 'id');
    }

    public function __get($key)
    {
        if (Str::contains($key, 'ply')) {
            $infoKey = Str::substr($key, 4);
            return isset($this->player['info']->{$infoKey}) ? $this->player['info']->{$infoKey} : '';
        }
        return parent::__get($key);
    }

}