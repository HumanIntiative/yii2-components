<?php

namespace pkpudev\components\helpers;

use yii\helpers\ArrayHelper as YiiArrayHelper;

/**
 * Extending ArrayHelper
 */
class ArrayHelper extends YiiArrayHelper
{
	/**
	 * Get Year Range Options for Dropdown
	 *
	 * @param array $yearRange Example would be range(2017, 2014)
	 * @param bool $asc Sort Year Asc
	 * @return array
	 */
	public static function yearRangeOptions(array $yearRange, $placeholder=null)
	{
		return array_reduce($yearRange, function($retval, $year){
			$retval[$year] = $year;
			return $retval;
		}, [''=>$this->placeholder]);
	}
}