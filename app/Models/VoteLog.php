<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class VoteLog extends Model
{
    use ExModel;

    protected $table = 'vote_log';
    protected static $unguarded = true;

    protected static function boot()
    {
        static::saving(function (VoteLog $log) {

            //强制加票
            if ($log->getAttribute('force')) {
                $log->offsetUnset('force');
                return;
            }


        });

        parent::boot();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
