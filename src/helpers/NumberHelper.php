<?php

namespace pkpudev\components\helpers;

use yii\base\Object;

/**
 * Formatter for Number and Numeric
 */
class NumberHelper extends Object
{
	/**
	 * @param string $value The amount of money
	 * @return string Formatted amount of money (with Rp)
	 */
	public static function currency($value)
	{
		if (!is_numeric($value)) return;
		return 'Rp '.static::format($value);
	}

	/**
	 * @param string $value The amount of money
	 * @param integer $precision Precision point
	 */
	public static function format($value, $precision=0)
	{ 
		return number_format($value, $precision, ',', '.');
	}
}