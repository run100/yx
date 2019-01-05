<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/16
 * Time: 下午2:22
 */

namespace App\Admin;


use Admin;
use Encore\Admin\Form\Field;

abstract  class AbstractFormField extends Field
{
    public static $script_loaded = [];

    protected static function script()
    {
        return false;
    }

    public function render()
    {
        $script = static::script();
        if ($script !== false && !in_array(static::class, static::$script_loaded)) {
            Admin::script(script_in_php($script));
            static::$script_loaded[] = static::class;
        }
        return parent::render()->with(['options' => $this->options]);
    }
}