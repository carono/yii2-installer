<?php

namespace carono\yii2installer;

use yii\console\Controller;
use Yii;
use yii\helpers\ArrayHelper;

class InstallController extends Controller
{
    public $errors;

    public function getMenu()
    {
        return [];
    }

    public function actionIndex()
    {
        $this->outMenu($this->getMenu());
    }

    public function outMenu($menu)
    {
        if (count($menu) > 1 || !array_key_exists('items', $menu)) {
            $menu = ["items" => $menu];
        }
        $data = new ConsoleCheckBox($menu);
        if ($this->checkBoxList($data)) {
            if ($this->interactive) {
                Console::clearScreen();
            }
            foreach ($data->items as $item) {
                $item->exec();
            }
        }
    }

    protected function check(&$array, $index)
    {
        $index = preg_replace("/[^0-9\.]/", "", $index);
        $arr = array_filter(explode(".", $index));
        $data = ["items" => &$array];
        foreach ($arr as $key) {
            if (isset($data["items"][$key - 1])) {
                $data = &$data["items"][$key - 1];
            }
        }
        if (isset($data["checked"])) {
            $data["checked"] = !$data["checked"];
        }
    }

    protected function outList($array, $index = 0, $deep = 0)
    {
        $x = 1;
        foreach ($array as $row) {
            if ($index) {
                echo str_repeat("   ", $deep);
            }
            echo $row . "\n";
            if ($row->hasErrors()) {
                $this->errors = array_unique(ArrayHelper::merge($this->errors, current($row->getErrors())));
            }
            if (isset($row["items"])) {
                $this->outList($row["items"], $x - 1, $deep + 1);
            }
        }
    }

    /**
     * @param ConsoleCheckBox $item
     *
     * @return bool
     */
    public function checkBoxList($item)
    {
        if (!$this->interactive) {
            return true;
        }
        $c = null;
        while (!in_array($c, ["y", "n"])) {
            if ($c) {
                $item->check($c);
            }
            Console::clearScreen();
            echo " Prompt Y for confirm choose or N for discard\n\n";
            $this->outList($item->items);
            echo "\n";
            if ($this->errors) {
                echo "\n";
                echo "Warning!! Error\n";
                foreach ($this->errors as $error) {
                    echo $error . "\n";
                }
                echo "\n";
            }
            $c = strtolower($this->prompt(" Check [1.." . count($item->items) . "], 'Y' for confirm:"));
        }
        return $c == "y";
    }


    public static function migrate($path, $asClosure = false)
    {
        $controller = new MigrateController(null, null);
        $controller->db = Yii::$app->db;
        $controller->interactive = false;
        if (is_dir(Yii::getAlias($path))) {
            $func = function () use ($controller, $path) {
                $controller->migrationPath = Yii::getAlias($path);
                return $controller->actionUp() == 0;
            };
        } else {
            $func = function () use ($controller, $path) {
                return $controller->exec($path);
            };
        }
        if ($asClosure) {
            return $func;
        } else {
            return $func();
        }
    }
}