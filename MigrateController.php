<?php

namespace carono\yii2installer;


use yii\db\Connection;
use yii\di\Instance;

class MigrateController extends \yii\console\controllers\MigrateController
{
    public $interactive = false;

    public function exec($aliasPath)
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, \Yii::getAlias($aliasPath));
        $this->migrationPath = dirname($path);
        $this->db = Instance::ensure($this->db, Connection::className());
        $this->getNewMigrations();
        return $this->migrateUp(basename($path));
    }
}