<?php

namespace pkpudev\components\widgets;

use kartik\select2\Select2;
use pkpudev\components\Select2AjaxOptions;
use yii\base\Widget;
use yii\db\ActiveRecordInterface;
use yii\helpers\Html;
use yii\web\JsExpression;

/**
 * Using Dropdown for Selecting Donatur
 */
class DonaturDropdown extends Widget
{
	/**
	 * @var ActiveRecordInterface $model
	 */
	public $model;
	/**
	 * @var string $apiUrl Default to 'api/donatur'
	 */
	public $apiUrl = '/api/donatur';
	/**
	 * @var string $attribute Default to 'donor_id'
	 */
	public $attribute = 'donor_id';
	/**
	 * @var string $colspanText
	 */
	public $colspanText = 'col-sm-10';
	/**
	 * @var bool $isHorizontal
	 */
	public $isHorizontal = true;
	/**
	 * @var string $placeholder
	 */
	public $placeholder = '--- Pilih Donatur ---';

	public function run()
	{
		$activeLabel = Html::activeLabel($this->model, $this->attribute, [
			'class'=>'control-label '.($this->isHorizontal?'col-md-2':null),
			'required'=>$this->model->isAttributeRequired($this->attribute),
		]);
		$dropdown = $this->getDropdown();
		$errorBlock = Html::error($this->model, $this->attribute, [
			'class'=>'help-block error'
		]);

		if ($this->isHorizontal) {
			echo $this->templateHorizontal($activeLabel, $dropdown, $errorBlock);
		} else {
			echo $this->templateVertical($activeLabel, $dropdown, $errorBlock);
		}
	}

	protected function getDropdown()
	{
		$idValue = $this->model->{$this->attribute};

		if (null === ($donatur = $this->model->donatur)) {
			$placeholder = $this->placeholder;
		} else {
			$placeholder = (object)['id'=>$idValue, 'text'=>$donatur->full_name];
		}

		return Select2::widget([
			'model'=>$this->model,
			'attribute'=>$this->attribute,
			'options'=>['placeholder'=>$placeholder],
			'pluginOptions'=>Select2AjaxOptions::toArray([
				'url'=>$this->apiUrl,
				'placeholder'=>$placeholder,
				'idValue'=>$idValue,
				'codeField'=>'no',
				'ajax'=>[
					'delay'=>250,
					'type'=>'GET',
					'url'=>$this->apiUrl,
					'dataType'=>'json',
					'data'=>new JsExpression('function(p) { return {q:p.term} }'),
					'processResults'=>new JsExpression('function(data) {
						var results = [];
						jQuery.each(data, function (index, item) {
							results.push({
								id: item.id,
								text: item.text
							})
						})
						return { results: results }
					}'),
				],
			]),
		]);
	}

	protected function templateHorizontal($activeLabel, $dropdown, $errorBlock)
	{
		?>
		<div class="form-group">
			<?= $activeLabel ?>
			<div class="<?=$this->colspanText?>">
				<?= $dropdown; ?>
				<?= $errorBlock ?>
			</div>
		</div>
		<?php
	}

	protected function templateVertical($activeLabel, $dropdown, $errorBlock)
	{
		?>
		<div class="form-group">
			<?= $activeLabel ?>
			<?= $dropdown ?>
			<?= $errorBlock ?>
		</div>
		<?php
	}
}