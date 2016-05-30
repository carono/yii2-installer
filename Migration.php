<?php
namespace carono\yii2installer;

use yii\db\Migration as BaseMigration;
use yii\helpers\ArrayHelper;


class Migration extends BaseMigration
{
    public static function foreignKey($table, $columnName = null)
    {
        return new ForeignKeyColumn($table, $columnName);
    }

//    public static function pivot()
//    {
//        return 'pv';
//    }
//
//    /**
//     * @param array $array
//     *
//     * @return array
//     */
//    public function fk($array = [])
//    {
//        return $array;
//    }
//
//    /**
//     * @param array $array
//     *
//     * @return array
//     */
//    public function newColumns($array = [])
//    {
//        return $array;
//    }
//
//    /**
//     * @param array $array
//     *
//     * @return array
//     */
//    public function removeColumns($array = [])
//    {
//        return $array;
//    }
//
//    /**
//     * /**
//     * @param array $array
//     *
//     * @return array
//     */
//    public function tables($array = [])
//    {
//        return $array;
//    }
//
//    /**
//     * @param array $array
//     *
//     * @return array
//     */
//    public function renameColumns($array = [])
//    {
//        return $array;
//    }
//
//    /**
//     * @param array $array
//     *
//     * @return array
//     */
//    public function index($array = [])
//    {
//        return $array;
//    }
//
//    /**
//     * @param array $array
//     *
//     * @return array
//     */
//    public function schemes($array = [])
//    {
//        return $array;
//    }
//
//    public function downRenameColumns($array = [])
//    {
//        $this->_applyNewColumns(true, $array);
//    }
//
//    public function upRenameColumns($array = [])
//    {
//        $this->_applyNewColumns(false, $array);
//    }
//
//    /**
//     * @param bool $revert
//     */
//    protected function _applyRenameColumns($revert = false, $array = [])
//    {
//        if ($revert) {
//            foreach (array_reverse($this->renameColumns($array)) as $column) {
//                $this->renameColumn($column[0], $column[2], $column[1]);
//            }
//        } else {
//            foreach ($this->renameColumns($array) as $column) {
//                $this->renameColumn($column[0], $column[1], $column[2]);
//            }
//        }
//    }
//
//    public function downNewColumns($array = [])
//    {
//        $this->_applyNewColumns(true, $array ? $array : $this->newColumns());
//    }
//
//    public function upNewColumns($array = [])
//    {
//        $this->_applyNewColumns(false, $array ? $array : $this->newColumns());
//    }
//
//    private static function formPvTableName($sourceTable, $targetTable)
//    {
//        return join('_', [$sourceTable, $targetTable, "pv"]);
//    }
//
//    private static function formPvTable($sourceTable, $sourceColumn, $targetTable, $targetColumn)
//    {
//        if (!$sourceColumn) {
//            $sourceColumn = $sourceTable . "_id";
//        }
//        if (!$targetColumn) {
//            $targetColumn = $targetTable . "_id";
//        }
//        return [
//            $sourceColumn => ['pk', $sourceTable, 'id'],
//            $targetColumn => ['pk', $targetTable, 'id'],
//        ];
//    }
//
//    /**
//     * @param bool $revert
//     */
//    protected function _applyNewColumns($revert = false, $array = [])
//    {
//        if ($revert) {
//            foreach (array_reverse($this->newColumns($array)) as $column) {
//                if ($column[2] == self::pivot()) {
//                    $pvTableName = self::formPvTableName($column[0], $column[1]);
//                    $pvTableColumns = self::formPvTable(
//                        $column[0], ArrayHelper::getValue($column, 5), $column[3], ArrayHelper::getValue($column, 4)
//                    );
//                    $this->downTables([$pvTableName => $pvTableColumns]);
//                } else {
//                    $this->dropColumn($column[0], $column[1]);
//                }
//            }
//        } else {
//            foreach ($this->newColumns($array) as $column) {
//                if ($column[2] == self::pivot()) {
//                    $pvTableName = self::formPvTableName($column[0], $column[1]);
//                    $pvTableColumns = self::formPvTable(
//                        $column[0], ArrayHelper::getValue($column, 5), $column[3], ArrayHelper::getValue($column, 4)
//                    );
//                    $this->upTables([$pvTableName => $pvTableColumns]);
//                } else {
//                    $this->addColumn($column[0], $column[1], $column[2]);
//                }
//            }
//        }
//    }
//
//    public function downRemoveColumns($array = [])
//    {
//        $this->_applyRemoveColumns(true, $array);
//    }
//
//    public function upRemoveColumns($array = [])
//    {
//        $this->_applyRemoveColumns(false, $array);
//    }
//
//    /**
//     * @param bool $revert
//     */
//    protected function _applyRemoveColumns($revert = false, $array = [])
//    {
//        if ($revert) {
//            foreach (array_reverse($this->removeColumns($array)) as $column) {
//                $this->addColumn($column[0], $column[1], $column[2]);
//            }
//        } else {
//            foreach ($this->removeColumns($array) as $column) {
//                $this->dropColumn($column[0], $column[1]);
//            }
//        }
//    }
//
//    public function newTables()
//    {
//        return [];
//    }
//
//    public function upNewTables($tables = [])
//    {
//        $tables = $tables ? $tables : $this->newTables();
//        foreach ($tables as $name => $columns) {
//        }
//    }
//
//    public function downNewTables($tables = [])
//    {
//    }
//
//    public function downTables($array = [])
//    {
//        $this->_applyTables(true, $array ? $array : $this->tables());
//    }
//
//    public function upTables($array = [])
//    {
//        $this->_applyTables(false, $array ? $array : $this->tables());
//    }
//
//    protected function _applyTables($revert = false, $array = [])
//    {
//        $tables = $revert ? array_reverse($array) : $array;
//        while (list($key, $data) = each($tables)) {
//            $name = is_string($key) ? $key : $data[0];
//            $columns = is_string($key) ? $data : $data[1];
//            if ($revert) {
//                foreach ($columns as $cName => $type) {
//                    if (is_array($type) && $type[0] == self::pivot()) {
//                        $this->dropTable($name . "_" . $cName . "_pv");
//                    }
//                }
//                $this->dropTable($name);
//            } else {
//                $options = null;
//                if (isset($columns["inherited"])) {
//                    $inherited = $columns["inherited"];
//                    unset($columns["inherited"]);
//                    $options = 'INHERITS (' . $inherited . ')';
//                }
//                if (isset($columns["scheme"])) {
//                    $scheme = $columns["scheme"];
//                    $name = "$scheme.$name";
//                    unset($columns["scheme"]);
//                }
//                $pks = 0;
//                $pkColumns = [];
//                foreach ($columns as $cName => &$type) {
//                    if (is_array($type) && $type[0] == self::pivot()) {
//                        $pvTableName = $name . "_" . $cName . "_pv";
//                        $refTable1 = $name;
//                        $refTable2 = $type[1];
//                        $refColumnName1 = self::removeSchema(isset($type[4]) ? $type[4] : $name . "_id");
//                        $refColumnName2 = self::removeSchema(isset($type[3]) ? $type[3] : $type[1] . "_id");
//                        $tables[$pvTableName] = [
//                            $refColumnName1 => [self::primaryKey(), $refTable1, $refColumnName1],
//                            $refColumnName2 => [self::primaryKey(), $refTable2, $refColumnName2],
//                        ];
//                        unset($columns[$cName]);
//                    }
//                    if (is_array($type)) {
//                        $type = $type[0];
//                    }
//                    if ($type == 'pk') {
//                        $pks++;
//                    }
//                }
//
//                if ($pks > 1) {
//                    foreach ($columns as $cName => &$type) {
//                        if ($type == 'pk') {
//                            $pkColumns[] = $cName;
//                            $type = self::integer()->notNull();
//                        }
//                    }
//                }
//                $this->createTable($name, $columns, $options);
//                if ($pks > 1) {
//                    $this->addPrimaryKey(
//                        self::formIndexName(self::removeSchema($name), $pkColumns, 'pk'), $name, $pkColumns
//                    );
//                }
//            }
//            reset($tables);
//            unset($tables[$name]);
//        }
//    }
//
//    /**
//     * @param $table
//     * @param $column
//     * @param $refTable
//     * @param $refColumn
//     *
//     * @return string
//     */
//    public static function formFkName($table, $column, $refTable, $refColumn)
//    {
//        $table = count(($t = explode('.', $table))) > 1 ? $t[1] : $t[0];
//        $refTable = count(($t = explode('.', $refTable))) > 1 ? $t[1] : $t[0];
//        return "{$table}[{$column}]_{$refTable}[{$refColumn}]_fk";
//    }
//
//    /**
//     * @param        $table
//     * @param        $columns
//     * @param string $suffix
//     *
//     * @return string
//     */
//    public static function formIndexName($table, $columns, $suffix = "idx")
//    {
//        $table = self::removeSchema($table);
//        $column = join(':', array_map('trim', (array)$columns));
//        return "{$table}:{$column}_$suffix";
//    }
//
//    public function downFk($array = [])
//    {
//        $this->_applyFk(true, $array);
//    }
//
//    public function upFk($array = [])
//    {
//        $this->_applyFk(false, $array);
//    }
//
//    protected function _applyFk($revert = false, $array = [])
//    {
//        $fks = $revert ? array_reverse($this->fk($array)) : $this->fk($array);
//        $fks = array_merge($fks, $this->collectFks());
//        foreach ($fks as $fk) {
//            if (count($fk) === 4) {
//                array_unshift($fk, self::formFkName($fk[0], $fk[1], $fk[2], $fk[3]));
//            }
//            if ($revert) {
//                $this->dropForeignKey($fk[0], $fk[1], $fk[2], $fk[3], $fk[4]);
//            } else {
//                $delete = isset($fk[5]) ? $fk[5] : "CASCADE";
//                $update = isset($fk[5]) ? $fk[5] : null;
//                $this->addForeignKey($fk[0], $fk[1], $fk[2], $fk[3], $fk[4], $delete, $update);
//                $this->createIndex(self::formIndexName($fk[1], $fk[2]), $fk[1], $fk[2]);
//            }
//        }
//    }
//
//    protected function _applySchemes($revert = false, $array = [])
//    {
//        $schemes = $revert ? array_reverse($this->schemes($array)) : $this->schemes($array);
//        foreach ($schemes as $scheme) {
//            if ($revert) {
//                $this->execute("DROP SCHEMA $scheme CASCADE");
//            } else {
//                $this->execute("CREATE SCHEMA IF NOT EXISTS $scheme");
//            }
//        }
//    }
//
//    public function downSchemes($array = [])
//    {
//        $this->_applySchemes(true, $array);
//    }
//
//    public function upSchemes($array = [])
//    {
//        $this->_applySchemes(false, $array);
//    }
//
//    /**
//     * @param bool $revert
//     */
//    protected function _applyIndex($revert = false, $array = [])
//    {
//        $indexes = $revert ? array_reverse($this->index($array)) : $this->index($array);
//        foreach ($indexes as $key => $data) {
//            $unq = isset($data[2]) && $data[2];
//            $columns = is_array($data[1]) ? $data[1] : explode(',', $data[1]);
//            $name = self::formIndexName($data[0], $columns, $unq ? "unq" : "idx");
//            if ($revert) {
//                $this->dropIndex($name, $data[0]);
//            } else {
//                $this->createIndex($name, $data[0], join(',', $columns), $unq);
//            }
//        }
//    }
//
//    public function downIndex($array = [])
//    {
//        $this->_applyIndex(true, $array);
//    }
//
//    public function upIndex($array = [])
//    {
//        $this->_applyIndex(false, $array);
//    }
//
//    public function collectFks($array = [])
//    {
//        $fk = [];
//        foreach ($this->tables($array) as $name => $columns) {
//            foreach ($columns as $cName => $type) {
//                if (is_array($type)) {
//                    if ($type[0] == self::pivot()) {
//                        $pvTableName = $name . "_" . $cName . "_pv";
//                        $refTable1 = $name;
//                        $refTable2 = $type[1];
//                        $refColumnName1 = self::removeSchema(isset($type[4]) ? $type[4] : $name . "_id");
//                        $refColumnName2 = self::removeSchema(isset($type[3]) ? $type[3] : $type[1] . "_id");
//                        $fk[] = [
//                            $pvTableName,
//                            $refColumnName1,
//                            $refTable1,
//                            isset($type[5]) ? $type[5] : 'id'
//                        ];
//                        $fk[] = [
//                            $pvTableName,
//                            $refColumnName2,
//                            $refTable2,
//                            isset($type[6]) ? $type[6] : 'id'
//                        ];
//                    } else {
//                        $fk[] = array_merge([$name, $cName], array_slice($type, 1));
//                    }
//                }
//            }
//        }
//
//        foreach ($this->newColumns() as $column) {
//            if ($column[2] == self::pivot()) {
//                $pvTableName = self::formPvTableName($column[0], $column[1]);
//                $pvTableColumns = self::formPvTable(
//                    $column[0], ArrayHelper::getValue($column, 5), $column[3], ArrayHelper::getValue($column, 4)
//                );
//                foreach ($pvTableColumns as $name => $item) {
//                    $fk[] = [$pvTableName, $name, $item[1], $item[2]];
//                }
//            } elseif (isset($column[4])) {
//                $fk[] = [$column[0], $column[1], $column[3], $column[4]];
//            }
//        }
//        return $fk;
//    }
//
//    public function insertTo($table, $rows, $idStart = 1, $updateSeq = 'id')
//    {
//        $c = $idStart;
//        foreach ($rows as $row) {
//            if (!isset($row["id"]) && !is_null($idStart)) {
//                $row += ["id" => $c++];
//            }
//            $this->insert($table, $row);
//        }
//        if ($updateSeq) {
//            $c = (int)\Yii::$app->db->createCommand("SELECT count(*) FROM [[$table]]")->queryScalar() + 1;
//            $this->execute("ALTER SEQUENCE {$table}_{$updateSeq}_seq RESTART WITH $c;");
//        }
//    }
//
//    public static function removeSchema($str)
//    {
//        if (strpos($str, '.') !== false) {
//            $arr = explode('.', $str);
//            return $arr[1];
//        } else {
//            return $str;
//        }
//    }
//
}