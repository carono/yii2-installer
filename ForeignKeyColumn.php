<?php
/**
 * Created by PhpStorm.
 * User: Карно
 * Date: 30.05.2016
 * Time: 11:53
 */

namespace carono\yii2installer;



use yii\db\ColumnSchema;

class ForeignKeyColumn extends ColumnSchema
{
    const FK_CASCADE = 'CASCADE';
    const FK_DEFAULT = 'SET DEFAULT';
    const FK_NULL = 'SET NULL';
    public $onDelete = self::FK_CASCADE;
    public $onUpdate = null;
    public $table;
    public $columnName = null;

    public function onDeleteCascade()
    {
        $this->onDelete = self::FK_CASCADE;
    }

    public function onDeleteNull()
    {
        $this->onDelete = self::FK_NULL;
    }

    public function onDeleteDefault()
    {
        $this->onDelete = self::FK_DEFAULT;
    }

    public function __construct($table, $columnName = null)
    {
        $this->table = $table;
        $this->columnName = $columnName ? $columnName : $table . "_id";
    }
}