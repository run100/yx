<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/31
 * Time: 下午2:18
 */

namespace App\Models\Wechat;


use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class AutoReply extends Model
{
    use ExModel;

    protected $table = 'wechat_auto_reply';
    protected static $unguarded = true;

    public function getTmodeAttribute()
    {
        return strtoupper(substr($this->match_mode, 0, 1));
    }

    public function getRmodeAttribute()
    {
        return strtoupper(substr($this->reply_mode, 0, 1));
    }
}