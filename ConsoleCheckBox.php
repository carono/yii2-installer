<?php
/**
 * User: Карно
 * Date: 01.01.2016
 * Time: 21:08
 */

namespace carono\yii2installer;

use yii\base\Model;
use Yii;

/**
 * Class ConsoleCheckBox
 *
 * @package carono\components\commands
 * @property bool              $disabled
 * @property bool              $error
 * @property bool              $value
 * @property ConsoleCheckBox[] $items
 *
 */
class ConsoleCheckBox extends Model
{
	public $id;
	public $index;
	public $text;
	public $checked;
	/**
	 * @var ConsoleCheckBox
	 */
	public $owner;
	public $inherit;

	private $_disabled;
	private $_error;
	private $_exec;
	/**
	 * @var ConsoleCheckBox[]
	 */
	private $_items = [];

	public function exec()
	{
		if (!$this->value) {
			echo "Exec " . $this->text . ": SKIP\n";
		}
		if ($this->_exec instanceof \Closure && $this->value) {
			echo "Exec " . $this->text . ": ";
			ob_start();
			$transaction = Yii::$app->db->beginTransaction();
			$res = call_user_func($this->_exec, $this);
			if ($res) {
				$transaction->commit();
			} else {
				$transaction->rollBack();
			}
			ob_clean();
			ob_end_flush();
			echo ($res ? "OK" : "FAIL") . "\n";
		}
		if ($this->items) {
			foreach ($this->items as $child) {
				$child->exec();
			}
		}
	}

	public function setExec($value)
	{
		$this->_exec = $value;
	}

	public function getValue()
	{
		return $this->checked && !$this->disabled;
	}

	public function getError()
	{
		if ($this->inherit && $this->owner) {
			return $this->owner->error;
		} else {
			return $this->_error instanceof \Closure ? call_user_func($this->_error, $this) : $this->_error;
		}
	}

	public function setError($value)
	{
		$this->_error = $value instanceof \Closure ? $value : (bool)$value;
	}

	public function getDisabled()
	{
		if ($this->inherit && $this->owner) {
			return $this->owner->disabled;
		} else {
			return $this->_disabled instanceof \Closure ? call_user_func($this->_disabled, $this) : $this->_disabled;
		}
	}

	public function setDisabled($value)
	{
		$this->_disabled = $value instanceof \Closure ? $value : (bool)$value;
	}

	public function getItems()
	{
		return $this->_items;
	}

	public function setItems($value)
	{
		if (!is_array($value)) {
			$value = [$value];
		}
		$this->_items = [];
		$x = 1;
		foreach ($value as $item) {
			if ($item instanceof self) {
				$model = $item;
			} else {
				$model = new self($item);
			}
			$model->owner = $this;
			$model->index = $x;
			$this->_items[] = $model;
			$x++;
		}
	}

	public function __toString()
	{
		try {
			$disabled = $this->disabled;
			$check = $this->error ? "!" : ($disabled ? '-' : ($this->checked ? "#" : " "));
		} catch (\Exception $e) {
			$check = "!";
			$this->addError('', $e->getMessage());
		}
		return $this->getFullIndex() . "   [{$check}] " . $this->text;
	}

	/**
	 * @return string
	 */
	public function getFullIndex()
	{
		$parentId = $this->owner ? $this->owner->getFullIndex() : null;
		$id = array_filter([$parentId, $this->index]);
		return join('.', $id);
	}

	public function findByIndex($index)
	{
		$index = preg_replace("/[^0-9\.]/", "", $index);
		if ($this->getFullIndex() == $index) {
			return $this;
		} elseif ($this->_items) {
			foreach ($this->items as $item) {
				if ($res = $item->findByIndex($index)) {
					return $res;
				}
			}
		}
		return null;
	}

	public function findById($id)
	{
		if ($this->id == $id) {
			return $this;
		} elseif ($this->_items) {
			foreach ($this->items as $item) {
				if ($res = $item->findById($id)) {
					return $res;
				}
			}
		}
		return null;
	}

	public function check($index)
	{
		if ($item = $this->findByIndex($index)) {
			$item->checked = !$item->checked;
		}
	}
}