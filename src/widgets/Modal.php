<?php

namespace pkpudev\components\widgets;

use yii\bootstrap\Modal as BaseModal;
use yii\helpers\Html;

/**
 * Extending yii\bootstrap\Modal
 */
class Modal extends BaseModal
{
	/**
	 * @var integer $tabindex
	 */
	public $tabindex = -1;

	/**
	 * @inheritdoc
	 */
	protected function initOptions()
	{
		$this->options = array_merge([
			'class' => 'fade',
			'role' => 'dialog',
		], $this->options);

		if (!is_null($this->tabindex)) {
			$this->options['tabindex'] = $this->tabindex;
		}

		Html::addCssClass($this->options, ['widget' => 'modal']);

		if ($this->clientOptions !== false) {
			$this->clientOptions = array_merge(['show' => false], $this->clientOptions);
		}

		if ($this->closeButton !== false) {
			$this->closeButton = array_merge([
				'data-dismiss' => 'modal',
				'aria-hidden' => 'true',
				'class' => 'close',
			], $this->closeButton);
		}

		if ($this->toggleButton !== false) {
			$this->toggleButton = array_merge([
				'data-toggle' => 'modal',
			], $this->toggleButton);
			if (!isset($this->toggleButton['data-target']) && !isset($this->toggleButton['href'])) {
				$this->toggleButton['data-target'] = '#' . $this->options['id'];
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function renderBodyBegin()
	{
		return Html::beginTag('div', ['class' => 'modal-body', 'style' => 'overflow:hidden']);
	}
}