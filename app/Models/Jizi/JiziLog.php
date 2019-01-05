<?php

namespace App\Models\Jizi;

use App\Models\Player;
use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class JiziLog extends Model
{
    use ExModel;

    protected $table = 'jizi_log';

    protected $fillable = ['player_id', 'project_id', 'merchant_id', 'openid', 'ip', 'note', 'field', 'content', 'created_at'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

}
