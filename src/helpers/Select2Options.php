<?php

namespace pkpudev\components\helpers;

use yii\base\BaseObject;
use yii\web\JsExpression;

/**
 * Using options for component kartik\select2\Select2
 *
 * Coding sample:
 *
 * ```php
 * $form->field($model, 'project_id')->widget(Select2::classname(), [
 *   'options'=>compact('placeholder'),
 *   'pluginOptions'=>Select2Options::toArray(ArrayHelper::merge($options, [
 *     'url'=>Url::to(['api/project'], 'https'),
 *     'placeholder'=>$placeholder,
 *     'codeField'=>'project_no',
 *     'idValue'=>$model->project_id,
 *   ])),
 * ]);
 * ```
 */
class Select2Options extends BaseObject
{
	/**
	 * @var bool $allowClear
	 */
	public $allowClear;
	/**
	 * @var array $ajax
	 */
	public $ajax;
	/**
	 * @var integer $minimumInputLength
	 */
	public $minimumInputLength;
	/**
	 * @var JsExpression $templateResult
	 */
	public $templateResult;
	/**
	 * @var JsExpression $templateSelection
	 */
	public $templateSelection;
	/**
	 * @var JsExpression $escapeMarkup
	 */
	public $escapeMarkup;
	/**
	 * @var JsExpression $initSelection
	 */
	public $initSelection;
	/**
	 * @var array $config
	 */
	protected $config;

	/**
	 * Constructing Object Options
	 */
	public function __construct($config=[])
	{
		parent::__construct([]);

		foreach (['url', 'codeField'] as $field) {
			if (is_null($config[$field])) {
				throw new \yii\base\InvalidConfigException(ucfirst($field)." config for select2options not found!");
			}
		}

		$codeField = $config['codeField'] ?: 'code';
		$textField = $config['textField'] ?: 'text';
		$codeValue = $config['codeValue'];
		// Sizzle bugfix
		$textValue = str_replace(['(', ')', '.', '&', "'", '/', '"', '_'], null, $config['textValue']);
		$placeholder = $config['placeholder'];

		$callbackOptions = ($idValue = $config['idValue']) ?
			"{id:$idValue, $codeField:'$codeValue', text:'$textValue'}" :
			"{id:0, $codeField:'---', text:' $placeholder ---'}";

		/* allowClear */
		$this->allowClear = $config['allowClear'] ?: true;
		/* ajax */
		$this->ajax = $config['ajax'] ?: [
			'url'            => $config['url'],
			'delay'          => $config['ajax.delay'] ?: 250,
			'dataType'       => $config['ajax.dataType'] ?: 'json',
			'data'           => $config['ajax.data'] ?: new JsExpression('function(params) { return {q:params.term} }'),
			'processResults' => $config['ajax.result'] ?: new JsExpression('function(data) {
				var results = [];
				$.each(data, function (index, item) {
					results.push({
						id: item.id,
						text: "<div><strong>" + item.'.$codeField.' + "</strong> &mdash; " + item.'.$textField.' + "</div>"
					})
				})
				return { results: results }
			}'),
		];
		/* minimumInputLength */
		$this->minimumInputLength = $config['minLength'] ?: 3;
		/* templateResult */
		$this->templateResult = $config['templateResult'] ?: new JsExpression("function(p) {
			if (p.id) {
				return p.text
			}
			return jQuery('' + p.text + '')
		}");
		/* templateSelection */
		$this->templateSelection = $config['templateSelection'] ?: new JsExpression("function(p) {
			if (p.text) {
				jQuery('' + p.text + '')
			}
			return p.text
		}");
		/* escapeMarkup */
		$this->escapeMarkup = $config['escapeMarkup'] ?: new JsExpression("function (markup) { return markup }");
		/* initSelection */
		$this->initSelection = $config['initSelection'] ?: new JsExpression("function (element, callback) {
			callback({$callbackOptions})
		}");
	}

	/**
	 * Transform from object properties to array
	 */
	public static function toArray($config=[])
	{
		$object = new static($config);

		$reflector = new \ReflectionClass($object);
		$properties = $reflector->getProperties(\ReflectionProperty::IS_PUBLIC);

		$options = [];
		foreach ($properties as $property) {
			$prop = $property->getName();
			$options[$prop] = $object->$prop;
		}
		return $options;
	}
}