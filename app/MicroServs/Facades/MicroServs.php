<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/24
 * Time: 上午10:44
 */

namespace App\MicroServs\Facades;


use Illuminate\Support\Facades\Facade;

class MicroServs extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'microservs';
    }
}