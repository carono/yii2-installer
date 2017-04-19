<?php
namespace carono\yii2installer\traits;

use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

trait PivotTrait
{
    protected $_storage = [];

    /**
     * @param ActiveRecord $class
     *
     * @return mixed
     */
    public function deletePivots($class)
    {
        return $class::deleteAll([$this->getMainPkField() => $this->getMainPk()]);
    }

    /**
     * @param $pivotClass
     *
     * @return array
     */
    public function getStoragePivots($pivotClass)
    {
        if (isset($this->_storage[$pivotClass])) {
            return $this->_storage[$pivotClass];
        } else {
            return [];
        }
    }

    /**
     * @return array
     */
    public function getPivotStorage()
    {
        return $this->_storage;
    }

    /**
     * @param      ActiveRecord $pivotClass
     * @param                   $column
     * @param array             $condition
     *
     * @return mixed
     */
    public function getPivotAttribute($pivotClass, $column, $condition = [])
    {
        /**
         * @var ActiveRecord $pv
         */
        if (is_numeric($condition)) {
            $pv = new $pivotClass;
            $mainPk = $this->getMainPkField();
            $pk = $pv->primaryKey();
            $slavePk = current(array_diff($pk, [$mainPk]));
            $condition = [$slavePk => $condition];
        }
        $condition = array_merge($condition, [$this->getMainPkField() => $this->getMainPk()]);
        return $pivotClass::find()->andWhere($condition)->select([$column])->scalar();
    }

    /**
     * @param ActiveRecord $pivotClass
     * @param              $value
     * @param null         $column
     * @param array        $condition
     */
    public function updatePivotAttribute($pivotClass, $value, $column = null, $condition = [])
    {
        /**
         * @var ActiveRecord $pv
         */
        if (is_numeric($condition)) {
            $pv = new $pivotClass;
            $mainPk = $this->getMainPkField();
            $pk = $pv->primaryKey();
            $slavePk = current(array_diff($pk, [$mainPk]));
            $condition = [$slavePk => $condition];
        }
        $condition = array_merge($condition, [$this->getMainPkField() => $this->getMainPk()]);
        $pivotClass::updateAll([$column => $value], $condition);
    }

    /**
     * @param $pivotClass
     */
    public function clearStorage($pivotClass)
    {
        unset($this->_storage[$pivotClass]);
    }

    /**
     * @param      $models
     * @param      $pivotClass
     * @param null $modelClass
     */
    public function storagePivots($models, $pivotClass, $modelClass = null)
    {
        if (!is_array($models)) {
            $models = [$models];
        }
        foreach ($models as $model) {
            $this->storagePivot($model, $pivotClass, $modelClass);
        }
    }

    /**
     * @param ActiveRecord $model
     * @param ActiveRecord|string $pivotClass
     * @param ActiveRecord $modelClass
     *
     * @throws \Exception
     */
    public function storagePivot($model, $pivotClass, $modelClass = null)
    {
        if (is_numeric($model) && $modelClass) {
            $model = $modelClass::findOne($model);
        } elseif (is_array($model)) {
            $model = \Yii::createObject($model);
        }
        if (!($model instanceof Model)) {
            throw new \Exception('Cannot determine or model not found');
        }
        $this->_storage[$pivotClass][] = $model;
    }

    /**
     * @param bool $clear
     */
    public function savePivots($clear = false)
    {
        foreach ($this->getPivotStorage() as $pivotClass => $items) {
            if ($clear) {
                $this->deletePivots($pivotClass);
            }
            foreach ($items as $item) {
                $this->addPivot($item, $pivotClass);
            }
        }
    }

    /**
     * @param $model
     * @param $pivotClass
     * @return array|null|ActiveRecord
     * @throws \Exception
     */
    public function addPivot($model, $pivotClass)
    {
        /**
         * @var ActiveRecord $pv
         */
        $pv = new $pivotClass;
        $mainPk = $this->getMainPkField();
        $pk = $pv->primaryKey();
        if (!in_array($mainPk, $pk)) {
            throw  new \Exception("Fail found pk $mainPk in " . $pivotClass);
        }
        $slavePk = current(array_diff($pk, [$mainPk]));
        $attr[$mainPk] = $this->getMainPk();
        $attr[$slavePk] = $model->id;
        if ($find = (new ActiveQuery($pivotClass))->andWhere($attr)->one()) {
            return $find;
        } else {
            $pv->setAttributes($attr);
            $pv->save();
            return $pv;
        }
    }

    /**
     * @return mixed
     */
    protected function getMainPk()
    {
        /**
         * @var ActiveRecord $this
         */
        return $this->{$this->primaryKey()[0]};
    }

    /**
     * @param ActiveRecord $pivotClass
     * @return string
     */
    protected function getMainPkField($pivotClass)
    {
        /**
         * @var ActiveRecord $this
         */
        return $this::getDb()->getTableSchema($pivotClass::tableName())->primaryKey[0];
    }
}