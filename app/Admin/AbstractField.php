<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/10/30
 * Time: 上午9:46
 */

namespace App\Admin;


use Admin;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Column;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

abstract class AbstractField extends AbstractDisplayer
{
    public static $script_loaded = [];


    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct($value, Grid $grid = null, Column $column = null, $row = null)
    {
        $this->value = $value;
        $this->grid = $grid;
        $this->column = $column;
        $this->row = $row;
    }

    protected static function script()
    {
        return false;
    }

    /**
     * Display method.
     *
     * @return mixed
     */
    public function display()
    {
        $script = static::script();
        if ($script !== false && !in_array(static::class, static::$script_loaded)) {
            Admin::script(script_in_php($script));
            static::$script_loaded[] = static::class;
        }

        return $this->render();
    }

    public static function displayRow($row)
    {
        return (new static(null, null,  null, $row))->display();
    }

    abstract protected function render();
}