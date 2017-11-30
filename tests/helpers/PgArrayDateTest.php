<?php

use PHPUnit\Framework\TestCase;
use pkpudev\components\helpers\PgArrayDate;

class PgArrayDateTest extends TestCase
{
	public function testArrayToString()
	{
		$array = ['2017-01-01','2017-01-31'];
		$result = PgArrayDate::arrayToString($array);

		$this->assertEquals('{"2017-01-01","2017-01-31"}', $result);
	}

	public function testStringToArray()
	{
		$string = '{"2017-01-01","2017-01-31"}';
		$result = PgArrayDate::stringToArray($string);

		$this->assertEquals(['2017-01-01','2017-01-31'], $result);
	}
}