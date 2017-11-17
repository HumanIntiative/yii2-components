<?php

namespace pkpudev\components\data;

use yii\base\Object;
use yii\db\ActiveQueryInterface;

class QueryCollection extends Object implements \IteratorAggregate, \Countable
{
	protected $data;

	public function __construct(ActiveQueryInterface $query)
	{
		$this->data = $query->all();
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->data);
	}

	public function count()
	{
		return count($this->data);
	}

	public function exists()
	{
		return !empty($this->data);
	}

	public function map(callable $callback)
	{
		$this->data = array_map($callback, $this->data);
		return $this;
	}

	public function filter(callable $callback)
	{
		$this->data = array_filter($this->data, $callback);
		return $this;
	}

	public function reduce(callable $callback, $initial=null)
	{
		$this->data = array_reduce($this->data, $callback, $initial);
		return $this;
	}
}