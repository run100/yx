<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/3/4
 * Time: 下午4:01
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class Payment extends Model
{
    use ExModel;

    protected $table = 'payments';


    protected $casts = [
        'refund'    => 'array',
        'data'      => 'object',
        'payment'   => 'object',
        'order'     => 'object'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Payment $m) {
            if (!$m->exists) {
                $m->trade_no = make_trade_no($m->order_no);
            }
        });
    }

    public function addRefund($refund_data)
    {
        $this->refund[] = $refund_data;
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}