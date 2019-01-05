<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class Billing extends Model
{
    use ExModel;

    protected $table = 'billing';
    protected static $unguarded = true;


    protected $casts = [
        'data'   => 'array'
    ];
}
