<?php

namespace pkpudev\components\helpers;

use yii\base\Object;
use yii\db\ActiveRecordInterface;

class DateTimeHelper extends Object
{
	const DECEMBER = 12;
	const N_SATURDAY = 6;
	const N_SUNDAY = 7;

	/**
	 * @var ActiveRecordInterface $model Holiday model
	 */
	public static $model;

	/**
	 * @param DateTimeInterface $start
	 * @param integer $days
	 * @return DateTime[]
	 */
	private static function getWorkDays(\DateTimeInterface $start, $days=7)
	{
		$dates = [];
		$interval = new \DateInterval('P1D');
		$end = $start->add(new \DateInterval("P{$days}D"));
		$period = new \DatePeriod($start, $interval, $end);

		foreach ($period as $date)
			array_push($dates, $date);

		$year  = $start->format('Y');
		$month = $start->format('n');
		$day   = $start->format('j');

		$holidays = self::getHolidays($year, $month);
		if (($day + $days) >= 30) {
			if ($month == self::DECEMBER) $year++; else $month++;
			array_merge($holidays, self::getHolidays($year, $month));
		}

		$workDays = array_filter($dates, function($date) use ($holidays) {
			return !in_array($date, $holidays)
				&& !in_array($date->format('N'), [self::N_SATURDAY, self::N_SUNDAY]);
		});

		return array_values($workDays);
	}

	/**
	 * @param integer $year
	 * @param integer $month
	 * @return DateTime[]
	 */
	public static function getHolidays($year, $month)
	{
		if (!(static::$model instanceof ActiveRecordInterface)) {
			throw new \yii\base\InvalidConfigException("Model Holiday not found");
		}

		$rows = static::$model::find()
			->where('EXTRACT(YEAR FROM holiday_date)::INTEGER='.$year)
			->andWhere('EXTRACT(MONTH FROM holiday_date)::INTEGER='.$month)
			->all();

		return array_map(function($day) {
			return new \DateTimeImmutable($day->holiday_date);
		}, $rows);
	}

	/**
	 * @param integer $interval
	 * @return DateTime
	 */
	public static function getDateFromNow($interval=1)
	{
		$today = new \DateTimeImmutable;
		return static::getDateFrom($today, $interval);
	}

	/**
	 * @param DateTimeInterface $data
	 * @param integer $interval
	 * @return DateTime
	 */
	public static function getDateFrom(\DateTimeInterface $date, $interval=1)
	{
		$index = $interval;
		$workDays = static::getWorkDays($date);
		if (in_array($date->format('N'), [self::N_SATURDAY, self::N_SUNDAY])) {
			$index = ($interval-1);
		}
		return $workDays[$index];
	}
}