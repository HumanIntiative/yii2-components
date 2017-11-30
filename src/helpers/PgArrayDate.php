<?php

namespace pkpudev\components\helpers;

use yii\base\BaseObject;

class PgArrayDate extends BaseObject
{
	public static function arrayToString($array)
	{
		if (empty($array)) return null; //'{}'

		$array = array_filter($array, function($date){
			return !empty($date);
		});

		return '{' . implode(',', array_map(function($date){
			return empty($date) ? '' : '"' . $date . '"';
		}, $array)) . '}';
	}

	public static function stringToArray($string)
	{
		$string = str_replace(['{','}'], ['',''], $string);
		$string = str_replace('"', '', $string);
		return explode(',', $string);
	}
}