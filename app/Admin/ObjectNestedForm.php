<?php
/**
 * Created by PhpStorm.
 * User: guxy
 * Date: 2017/11/21
 * Time: 下午7:06
 */

namespace App\Admin;


use Encore\Admin\Form\Field;
use Encore\Admin\Form\NestedForm;

class ObjectNestedForm extends NestedForm
{
    protected function formatField(Field $field)
    {
        $column = $field->column();

        $elementName = $elementClass = $errorKey = '';

        $key = $this->key === null ? static::DEFAULT_KEY_NAME : $this->key;
        $normal = str_replace(['[', ']'], ['-', ''], $this->relationName);

        if (is_array($column)) {
            foreach ($column as $k => $name) {
                $errorKey[$k] = sprintf('%s.%s.%s', $this->relationName, $key, $name);
                $elementName[$k] = sprintf('%s[%s][%s]', $this->relationName, $key, $name);
                $elementClass[$k] = [$normal, $name];
            }
        } else {
            $errorKey = sprintf('%s.%s.%s', $this->relationName, $key, $column);
            $elementName = sprintf('%s[%s][%s]', $this->relationName, $key, $column);
            $elementClass = [$normal, $column];
        }

        return $field->setErrorKey($errorKey)
            ->setElementName($elementName)
            ->setElementClass($elementClass);
    }

    protected function prepareRecord($record)
    {
        if ($record[static::REMOVE_FLAG_NAME] == 1) {
            return false;
        }

        $prepared = [];

        /* @var Field $field */
        foreach ($this->fields as $field) {
            $columns = $field->column();

            $value = $this->fetchColumnValue($record, $columns);

            if (is_null($value)) {
                continue;
            }

            if (method_exists($field, 'prepare')) {
                $value = $field->prepare($value);
            }

            if (($field instanceof \Encore\Admin\Form\Field\Hidden) || $value != $field->original()) {
                if (is_array($columns)) {
                    foreach ($columns as $name => $column) {
                        array_set($prepared, $column, $value[$name]);
                    }
                } elseif (is_string($columns)) {
                    array_set($prepared, $columns, $value);
                }
            }
        }

        unset($prepared[static::REMOVE_FLAG_NAME]);

        return $prepared;
    }

    public function prepare($input)
    {
        $ret = [];
        foreach ($input as $key => $record) {
            $record = $this->prepareRecord($record);
            if ($record !== false) {
                $ret[] = $record;
            }
        }
        return $ret;
    }
}