<?php

namespace pkpudev\components\helpers;

use yii\helpers\Html as YiiHtml;
use yii\web\JsExpression;

/**
 * Extending yii\helpers\Html
 */
class Html extends YiiHtml
{
	/**
	 * @param string $title
	 * @param string $content
	 * @param string $type
	 */
	public static function callout($title, $content, $type='danger')
	{
		echo static::beginTag('div', ['class'=>'callout callout-'.$type]);
			echo static::tag('h4', $title);
			echo static::tag('p', $content);
		echo static::endTag('div');
	}

	/**
	 * @param bool $history
	 */
	public static function backButton($history=false)
	{
		$title = '<i class="fa fa-reply"></i> Kembali';
		$url = $history ? null : ['index'];
		$options = ['class'=>'btn btn-default'];
		if ($history) {
			$options['onclick'] = new JsExpression('history.back()');
		}

		return static::a($title, $url, $options);
	}

	/**
	 * @param string $title
	 * @param array $options
	 */
	public static function saveButton($title=null, $options=[])
	{
		if (is_null($title)) {
			$title = '<span class="fa fa-pencil-square-o"></span> Kirim';
		}
		$options = ArrayHelper::merge($options, ['class' => 'btn btn-primary']);

		return static::submitButton($title, $options);
	}

	/**
	 * @param string $title
	 * @param array $options
	 */	
	public static function saveAndCancelButton($title, $options=[])
	{
		$buttons = static::beginTag('div', ['class'=>'btn-group', 'role'=>'group']);
		$buttons .= static::submitButton('<i class="fa fa-check"></i> '.$title,
			['class'=>'btn btn-success btn-approve']);
		$buttons .= static::a('<i class="fa fa-reply"></i> Cancel', ['index'],
			['class'=>'btn btn-default', /*'onclick'=>new JsExpression('history.back()')*/]);
		$buttons .= static::endTag('div');

		return $buttons;
	}

	/**
	 * @param integer $from
	 * @param integer $to
	 * @param integer $current
	 * @param string $baseUrl
	 * @param integer $companyId
	 */
	public static function yearButtonSearch($from, $to, $current, $baseUrl, $companyId)
	{
		$fnClassYear = function($linkyear) use ($current) {
			return $current == $linkyear ? 'success' : 'default';
		};

		$filters = '<strong>Filter</strong> : ' . static::beginTag('div', ['class'=>'btn-group']);
		foreach (range($from, $to) as $year) {
			$filters .= static::a($year, "{$baseUrl}{$year}", ['class'=>'btn btn-'.$fnClassYear($year)]);
		}
		$filters .= static::endTag('div');

		return $filters;
	}
}