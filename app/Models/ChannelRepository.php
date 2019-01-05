<?php
/**
 * Created by PhpStorm.
 * User: staff
 * Date: 2018/8/27
 * Time: 下午2:49
 */

namespace App\Models;

use App\Lib\SiteUtils;
use Illuminate\Support\Collection;
use Wanjia\Common\Database\Limiter;
use Wanjia\Common\Database\Repository;

class ChannelRepository extends Repository
{
    /**
     * @return Collection
     */
    public function getCacheChannel()
    {
        $key = "cache_channel_redis";
        $redis = \RedisDB::connection('default');
        if (!$redis->exists($key)) {
            $channels = Channel::selectRaw("id, name")->get();
            $rets = $channels->map(function ($item) {
                return ["name" => $item->name, "id" => $item->id];
            })->toArray();
            $redis->setex($key, 3600, serialize($rets));
        }
        return (new Collection(unserialize($redis->get($key))))->sortBy("id");

    }

}