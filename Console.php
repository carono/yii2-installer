<?php
namespace carono\yii2installer;

class Console extends \yii\helpers\BaseConsole
{
	public static function clearLine()
	{
		if (self::isRunningOnWindows()) {
			echo "\x0D";
		} else {
			parent::clearLine();
		}
	}

	public static function clearScreen()
	{
		$size = \yii\helpers\Console::getScreenSize();
		echo str_repeat("\n", $size[0]);
	}
}