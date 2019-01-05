<?php
/**
 * Created by PhpStorm.
 * User: staff
 * Date: 2018/8/27
 * Time: 上午10:38
 */

namespace App\Models;

use Wanjia\Common\Database\Limiter;
use Wanjia\Common\Database\Repository;

class ProjectRepository extends Repository
{
    const TYPE_OTHER = 1;
    const TYPE_VOTE = 2;
    const TYPE_JIZI = 3;
    const TYPE_CJ = 4;
    const TYPE_CUT = 5;
    const TYPE_CS = 6;
    const TYPE_NEWS = 7;
    const TYPE_HONGBAO = 8;

    const TYPES = [
        self::TYPE_OTHER => "定制开发",
        self::TYPE_VOTE => "投票公版",
        self::TYPE_JIZI => "集字集图公版",
        self::TYPE_CJ => "抽奖公版",
        self::TYPE_CUT => "砍价公版",
        self::TYPE_CS => "测试专题",
        self::TYPE_NEWS => "PC新闻专题",
        self::TYPE_HONGBAO => '红包公版'
    ];


}