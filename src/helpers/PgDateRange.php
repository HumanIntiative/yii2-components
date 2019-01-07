<?php

namespace pkpudev\components\helpers;

use yii\base\BaseObject;

/**
 * Formatter for Postgres Daterange data type
 */
class PgDateRange extends BaseObject
{
	const NULL_VALUES = ['(,)', '[,)', '(,]', '[,]'];
	const DEFAULT_VALUE = '(,)';

	/**
	 * @var string $first_date First Date
	 */
	protected $first_date;
	/**
	 * @var string $last_date Last Date
	 */
	protected $last_date;
	/**
	 * @var string $format Format
	 */
	protected $format;

	public static function parseRange($value)
	{
		$regex_date = '(\d{4}-\d{2}-\d{2})';
		$range = trim(str_replace([',', '[', ']', '(', ')'], ' ', $value));
		if (preg_match("/{$regex_date} {$regex_date}/", $range, $dates) === 1) {
			return new static("$dates[1] - $dates[2]");
		} elseif (preg_match("/{$regex_date} - {$regex_date}/", $range, $dates) === 1) {
			return new static("$dates[1] - $dates[2]");
		} elseif (preg_match("/{$regex_date}/", $range, $dates) === 1) {
			return new static("$dates[1]");
		} else {
			return null;
		}
	}

	public static function isNull($value)
	{
		if (is_null($value)) return true;
		if (in_array($value, static::NULL_VALUES)) return true;
		return false;
	}

	public static function toSQL($value)
	{
		if (static::isNull($value)) return static::DEFAULT_VALUE;

		$maps = array_map(function($value) {
			return empty($value) ? null : "'$value'";
		}, explode(' - ', $value));

		$last = null;
		if (count($maps)>1) {
			list($first, $last) = $maps;
		} else {
			$first = $maps[0];
		}

		if (!is_null($first) && !is_null($last)) return "[{$first}, {$last}]";
		if (!is_null($first) && is_null($last))  return "[{$first},)";
		if (is_null($first) && !is_null($last))  return "(,{$last}]";
		return "(,)";
	}

	public static function toWidget($value)
	{
		if (is_null($value)) return null;
		if (static::isNull($value)) return null;

		$dtrange = static::parseRange($value);

		if (!$dtrange->first_date) {
			return null;
		} elseif (!$dtrange->last_date) {
			return $dtrange->first_date;
		} else {
			return implode(' - ', [$dtrange->first_date, $dtrange->last_date]);
		}
	}

	public static function toString($value, $join='s/d')
	{
		if (is_null($value)) return null;

		$dtrange = static::parseRange($value);

		if (!is_null($dtrange->first_date) && !is_null($dtrange->last_date))
			return "{$dtrange->first_date} {$join} {$dtrange->last_date}";

		if (!is_null($dtrange->first_date) && is_null($dtrange->last_date))
			return $dtrange->first_date;

		if (is_null($dtrange->first_date) && !is_null($dtrange->last_date))
			return $dtrange->last_date;

		return null;
	}

	public static function create($daterange)
	{
		return new static($daterange);
	}

	// 
	// Constructor
	// 

	public function __construct($daterange, $format='Y-m-d', $separator=' - ')
	{
		$maps = explode(' - ', $daterange);

		$last_date = null;
		if (count($maps)>1) {
			list($first_date, $last_date) = $maps;
		} else {
			$first_date = $maps[0];
		}

		$this->first_date = $this->convertToFormat($first_date, $format);
		$this->last_date = $this->convertToFormat($last_date, $format);
	}

	public function getFirst_date()
	{
		return $this->first_date;
	}

	public function getLast_date()
	{
		return $this->last_date;
	}

	protected function convertToFormat($date, $format)
	{
		if (false == ($date = date_create_from_format($format, $date))) 
			return null;

		return $date->format($format);
	}
}