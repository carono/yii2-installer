<?php
/**
 * Created by PhpStorm.
 * User: Карно
 * Date: 30.05.2016
 * Time: 11:53
 */

namespace carono\yii2installer;


use yii\db\Migration as BaseMigration;

class ForeignKeyColumn
{
    const FK_CASCADE = 'CASCADE';
    const FK_DEFAULT = 'SET DEFAULT';
    const FK_NULL = 'SET NULL';
    public $_onDelete = self::FK_CASCADE;
    public $_onUpdate = null;
    protected $_refTable = null;
    protected $_refColumn = null;
    protected $_sourceTable = null;
    protected $_sourceColumn = null;
    /**
     * @var BaseMigration
     */
    public $migrate;

    public function getName()
    {
        return Migration::formFkName(
            $this->getSourceTable(), $this->getSourceColumn(), $this->getRefTable(), $this->getRefColumn()
        );
    }

    public function apply()
    {
        return $this->migrate->addForeignKey(
            $this->getName(), $this->getSourceTable(), $this->getSourceColumn(), $this->getRefTable(),
            $this->getRefColumn(), $this->getOnDelete(), $this->getOnUpdate()
        );
    }

    public function remove()
    {
        return $this->migrate->dropForeignKey($this->getName(), $this->getSourceTable());
    }

    public function getRefTable()
    {
        return $this->_refTable;
    }

    public function getRefColumn()
    {
        if (!$this->_refColumn && $this->migrate) {
            $pk = $this->migrate->db->getTableSchema($this->getRefTable())->primaryKey;
            $this->refColumn(current($pk));
        }
        return $this->_refColumn;
    }

    public function getSourceTable()
    {
        return $this->_sourceTable;
    }

    public function getSourceColumn()
    {
        return $this->_sourceColumn;
    }

    public function getOnDelete()
    {
        return $this->_onDelete;
    }

    public function getOnUpdate()
    {
        return $this->_onUpdate;
    }

    public function onDelete($string)
    {
        $this->_onDelete = $string;
        return $this;
    }

    public function onDeleteCascade()
    {
        return $this->onDelete(self::FK_CASCADE);
    }

    public function onDeleteNull()
    {
        return $this->onDelete(self::FK_NULL);
    }

    public function onDeleteDefault()
    {
        $this->_onDelete = self::FK_DEFAULT;
    }

    public function refTable($name)
    {
        $this->_refTable = $name;
        return $this;
    }

    public function sourceColumn($name)
    {
        $this->_sourceColumn = $name;
        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function sourceTable($name)
    {
        $this->_sourceTable = $name;
        return $this;
    }

    public function refColumn($name)
    {
        $this->_refColumn = $name;
        return $this;
    }
}