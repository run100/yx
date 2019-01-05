<?php

namespace App\Models\Yx;

use Illuminate\Database\Eloquent\Model;
use Wanjia\Common\Database\ExModel;

/**
 * App\Models\Yx\YxBanner
 *
 * @property int $id
 * @property string|null $picture
 * @property int $category
 * @property string|null $url
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Yx\YxBanner whereUrl($value)
 * @mixin \Eloquent
 */
class YxBanner extends Model
{
    use ExModel;

    protected $table = 'yx_banner';

    const CATEGORY_PC_INDEX = 1;
    const CATEGORY_PC_TEMP = 2;
    const CATEGORY_PC_CLASSIC = 3;
    const CATEGORY_MOB_INDEX =4;

}
