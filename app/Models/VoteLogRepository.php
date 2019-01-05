<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2018/1/2
 * Time: 下午4:59
 */

namespace App\Models;


use Wanjia\Common\Database\Limiter;
use Wanjia\Common\Database\Repository;

class VoteLogRepository extends Repository
{
    protected function queryFakeLog($limiters = [], $options = [])
    {
        $limiters[] = Limiter::make('operator_uid', '>', 0);
        return parent::query($limiters, $options);
    }

    protected function queryRealLog($limiters = [], $options = [])
    {
        $limiters[] = Limiter::make('operator_uid', '=', 0);
        return parent::query($limiters, $options);
    }

}