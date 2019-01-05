<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/31
 * Time: 下午2:01
 */

namespace App\Models\Wechat;


use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

class Menu extends Model
{
    use ModelTree, AdminBuilder, ExModel;

    protected $table = 'wechat_menu';
    protected static $unguarded = true;

    public static function selectOptions()
    {

        $options = (new static())->buildSelectOptions();

        return collect($options)->prepend('Root', 0)->all();
    }

    public static function rootSelectOptions($merchant_id)
    {
        $nodes = static::repository()->findByMerchantId($merchant_id, ['parent_id' => 0], ['orderby' => 'order'])->toArray();

        if ($nodes) {
            $options = (new static())->buildSelectOptions($nodes);
            return collect($options)->prepend('Root', 0)->all();
        } else {
            return [0   => 'Root'];
        }

    }

}
