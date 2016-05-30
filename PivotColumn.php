<?php

namespace carono\yii2installer;

use yii\db\Migration as BaseMigration;

class PivotColumn
{
    protected $_refTable = null;
    protected $_refColumn = null;
    protected $_sourceTable = null;
    protected $_sourceColumn = null;
    protected $_name = null;
    /**
     * @var BaseMigration
     */
    public $migrate;

    public function __toString()
    {
        return 'pv';
    }
    
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getName()
    {
        return join('_', ["pv", $this->_sourceTable, $this->_name]);
    }

    public function remove()
    {
        $this->migrate->dropTable($this->getName());
    }

    public function apply()
    {
        $columns = [
            $this->getSourceColumn() => Migration::foreignKey($this->getSourceTable()),
            $this->getRefColumn()    => Migration::foreignKey($this->getRefTable()),
        ];
        $this->migrate->createTable($this->getName(), $columns);
    }

    public function getRefTable()
    {
        return $this->_refTable;
    }

    public function getRefColumn()
    {
        if (!$this->_refColumn) {
            return join("_", [$this->getRefTable(), "id"]);
        } else {
            return $this->_refColumn;
        }
    }

    public function getSourceTable()
    {
        return $this->_sourceTable;
    }

    public function getSourceColumn()
    {
        if (!$this->_sourceColumn) {
            return join("_", [$this->getSourceTable(), "id"]);
        } else {
            return $this->_sourceColumn;
        }
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