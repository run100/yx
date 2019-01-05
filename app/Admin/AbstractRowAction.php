<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/27
 * Time: 上午10:54
 */

namespace App\Admin;


use Admin;

abstract class AbstractRowAction
{
    public static $script_loaded = [];
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected static function script()
    {
        return false;
    }

    abstract protected function render();

    public function __toString()
    {
        $script = static::script();
        if ($script !== false && !in_array(static::class, static::$script_loaded)) {
            $script = preg_replace('@^\s*[<]script>([\s\S]+)[<]\/script>\s*$@', '$1', $script);
            Admin::script($script);
            static::$script_loaded[] = static::class;
        }

        return $this->render();
    }
}