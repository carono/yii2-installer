<?php
namespace carono\yii2installer;

use yii\db\ColumnSchemaBuilder;
use yii\db\Migration as BaseMigration;


class Migration extends BaseMigration
{

    /**
     * @param      $refTable
     * @param null $refColumn
     *
     * @return ForeignKeyColumn
     */
    public static function foreignKey($refTable = null, $refColumn = null)
    {
        return (new ForeignKeyColumn())->refTable($refTable)->refColumn($refColumn);
    }

    /**
     * @param null $refTable
     * @param null $refColumn
     *
     * @return PivotColumn
     */
    public static function pivot($refTable = null, $refColumn = null)
    {
        return (new PivotColumn())->refTable($refTable)->refColumn($refColumn);
    }

    public function createTable($table, $columns, $options = null)
    {
        /**
         * @var PivotColumn[]      $pvs
         * @var ForeignKeyColumn[] $fks
         */
        echo "    > create table $table ...";
        $time = microtime(true);
        $pvs = [];
        $fks = [];
        $pks = [];
        foreach ($columns as $column => &$type) {
            if ($type == self::primaryKey()) {
                $pks[] = $column;
            }
            if ($type instanceof ForeignKeyColumn) {
                $type->migrate = $this;
                $type->sourceTable($table)->sourceColumn($column);
                $fks[] = $type;
                $type = self::integer();
            }

            if ($type instanceof PivotColumn) {
                $type->migrate = $this;
                $type->setName($column)->sourceTable($table);
                $pvs[] = $type;
                unset($columns[$column]);
            }
        }
        if (count($pks) > 1) {
            foreach ($columns as $column => &$type) {
                $type = self::integer();
            }
        }
        $this->db->createCommand()->createTable($table, $columns, $options)->execute();
        foreach ($columns as $column => $type) {
            if ($type instanceof ColumnSchemaBuilder && $type->comment !== null) {
                $this->db->createCommand()->addCommentOnColumn($table, $column, $type->comment)->execute();
            }
        }
        foreach ($fks as $fk) {
            $fk->apply();
        }
        if (count($pks) > 1) {
            $this->addPrimaryKey(null, $table, $pks);
        }
        foreach ($pvs as $pv) {
            $pv->apply();
        }
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addColumn($table, $column, $type)
    {
        if ($type instanceof ForeignKeyColumn) {
            parent::addColumn($table, $column, self::integer());
            $type->migrate = $this;
            $type->sourceTable($table);
            $type->sourceColumn($column);
            $type->apply();
        } else {
            return parent::addColumn($table, $column, $type);
        }
    }

    public function addPrimaryKey($name, $table, $columns)
    {
        if (is_null($name)) {
            $name = self::formIndexName($table, $columns, 'pk');
        }
        return parent::addPrimaryKey($name, $table, $columns);
    }
    
    public function newColumns()
    {
        return [];
    }

    public function downNewColumns($array = [])
    {
        $this->_applyNewColumns($array ? $array : $this->newColumns(), true);
    }

    public function upNewColumns($array = [])
    {
        $this->_applyNewColumns($array ? $array : $this->newColumns(), false);
    }

    protected function _applyNewColumns($columns = [], $revert = false)
    {
        $columns = $revert ? array_reverse($columns) : $columns;
        foreach ($columns as $column) {
            if ($column[2] instanceof PivotColumn) {
                $column[2]->migrate = $this;
                $column[2]->setName($column[1])->sourceTable($column[0]);
            }
            if ($revert) {
                if ($column[2] instanceof PivotColumn) {
                    $column[2]->remove();
                    continue;
                }
                $this->dropColumn($column[0], $column[1], $column[2]);
            } else {
                if ($column[2] instanceof PivotColumn) {
                    $column[2]->apply();
                    continue;
                }
                $this->addColumn($column[0], $column[1], $column[2]);
            }
        }
    }

    public function dropColumn($table, $column, $type = null)
    {
        return parent::dropColumn($table, $column);
    }

    public function newTables()
    {
        return [];
    }

    public function upNewTables($array = [])
    {
        $this->_applyNewTables($array ? $array : $this->newTables());
    }

    public function downNewTables($array = [])
    {
        $this->_applyNewTables($array ? $array : $this->newTables(), true);
    }

    protected function _applyNewTables($tables, $revert = false)
    {
        $tables = $revert ? array_reverse($tables) : $tables;
        foreach ($tables as $table => $columns) {
            if ($revert) {
                foreach ($columns as $column => $type) {
                    if ($type instanceof PivotColumn) {
                        $type->migrate = $this;
                        $type->setName($column)->sourceTable($table);
                        $type->remove();
                    }
                }
                $this->dropTable($table);
            } else {
                $this->createTable($table, $columns);
            }
        }
    }

    public static function formFkName($table, $column, $refTable, $refColumn)
    {
        $table = count(($t = explode('.', $table))) > 1 ? $t[1] : $t[0];
        $refTable = count(($t = explode('.', $refTable))) > 1 ? $t[1] : $t[0];
        return "{$table}[{$column}]_{$refTable}[{$refColumn}]_fk";
    }

    public static function formIndexName($table, $columns, $suffix = "idx")
    {
        $table = self::removeSchema($table);
        $column = join(':', array_map('trim', (array)$columns));
        return "{$table}:{$column}_$suffix";
    }

    public function insertTo($table, $rows, $idStart = 1, $updateSeq = 'id')
    {
        $c = $idStart;
        foreach ($rows as $row) {
            if (!isset($row["id"]) && !is_null($idStart)) {
                $row += ["id" => $c++];
            }
            $this->insert($table, $row);
        }
        if ($updateSeq) {
            $c = (int)\Yii::$app->db->createCommand("SELECT count(*) FROM [[$table]]")->queryScalar() + 1;
            $this->execute("ALTER SEQUENCE {$table}_{$updateSeq}_seq RESTART WITH $c;");
        }
    }

    public static function removeSchema($str)
    {
        if (strpos($str, '.') !== false) {
            $arr = explode('.', $str);
            return $arr[1];
        } else {
            return $str;
        }
    }
}