<?php

namespace carono\yii2installer;


class PivotColumn
{
    protected $_refTable = null;
    protected $_refColumn = null;
    protected $_sourceTable = null;
    protected $_sourceColumn = null;
    protected $_name = null;
    protected $_tableName = null;
    /**
     * @var Migration
     */
    public $migrate;

    public function __toString()
    {
        return 'pv';
    }

    public function setMigrate($migrate)
    {
        $this->migrate = $migrate;
        return $this;
    }

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function tableName($name)
    {
        $this->_tableName = $name;
        return $this;
    }

    public function getName()
    {
        $name = $this->_tableName ? $this->_tableName : join('_', ["pv", $this->_sourceTable, $this->_name]);
        return $this->migrate->expandTablePrefix("{{%" . Migration::setTablePrefix($name, '') . "}}");
    }

    public function remove()
    {
        $this->migrate->dropTable($this->getName());
    }

    public function apply()
    {
        /**
         * @var ForeignKeyColumn $type
         */
        $columns = [
            $this->getSourceColumn() => Migration::foreignKey($this->getSourceTable()),
            $this->getRefColumn() => Migration::foreignKey($this->getRefTable()),
        ];
        $columnsInt = array_combine(array_keys($columns), [$this->migrate->integer(), $this->migrate->integer()]);
        if ($this->migrate->db->driverName == "mysql") {
            $this->migrate->createTable($this->getName(), $columnsInt);
            $this->migrate->addPrimaryKey(null, $this->getName(), array_keys($columns));
            foreach ($columns as $name => $type) {
                $type->migrate = $this->migrate;
                $type->sourceTable($this->getName())->sourceColumn($name);
                $type->apply();
            }
        } else {
            $this->migrate->createTable($this->getName(), $columns);
            $this->migrate->addPrimaryKey(null, $this->getName(), array_keys($columns));
        }
    }

    public function getRefTable()
    {
        return $this->_refTable;
    }

    public function getRefColumn()
    {
        if (!$this->_refColumn) {
            $name = join("_", [$this->getRefTable(), "id"]);
        } else {
            $name = $this->_refColumn;
        }
        return Migration::setTablePrefix($name, '');
    }

    public function getSourceTable()
    {
        return $this->_sourceTable;
    }

    public function getSourceColumn()
    {
        if (!$this->_sourceColumn) {
            $name = join("_", [$this->getSourceTable(), "id"]);
        } else {
            $name = $this->_sourceColumn;
        }
        return Migration::setTablePrefix($name, '');
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