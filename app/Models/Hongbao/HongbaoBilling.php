<?php
namespace App\Models\Hongbao;


use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class HongbaoBilling extends Model
{
    use ExModel;

    protected $fillable = ['project_id', 'bill_no', 'wx_no', 'openid',
        'money', 'is_error'];

    protected $casts = [
        'data' => 'array'
    ];

}