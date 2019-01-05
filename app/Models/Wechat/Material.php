<?php
namespace App\Models\Wechat;


use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class Material extends Model
{
    use ExModel;

    protected $table = 'wechat_material';

    protected static function boot()
    {
        static::deleted(function (Material $m) {
            if ($m->media_id!=null) {
                $m->merchant->wechat_app->material->delete($m->media_id);
            }
        });

        parent::boot();
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

}
