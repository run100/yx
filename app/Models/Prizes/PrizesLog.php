<?php
namespace App\Models\Prizes;


use App\Models\Player;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Wanjia\Common\Database\ExModel;

class PrizesLog extends Model
{
    use ExModel;

    protected $table = 'prizes_log';

    protected $fillable = ['player_id', 'project_id', 'openid', 'wx_name', 'ip',
        'is_win', 'name', 'type', 'tip', 'field', 'draw_info', 'created_at'];

    const TYPE_COMMON = 1;
    const TYPE_TEXT = 2;
    const TYPE_INTERFACE = 3;

    public static $sTypes = [
        self::TYPE_COMMON => '兑奖码',
        self::TYPE_TEXT => '谢谢参与',
        self::TYPE_INTERFACE => '接口类',
    ];

    public function getWinTextAttribute()
    {
        return $this->is_win ? '是' : '否';
    }

    public function getDrawTextAttribute()
    {
        return $this->is_draw ? '已领取' : '未领取';
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