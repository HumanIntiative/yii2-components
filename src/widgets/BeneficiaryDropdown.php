<?php

namespace pkpudev\components\widgets;

use kartik\select2\Select2;
use pkpudev\components\helpers\Select2Options;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

/**
 * Using Dropdown for Selecting Beneficiary
 */
class BeneficiaryDropdown extends Widget
{
	/**
	 * @var ActiveRecordInterface $model
	 */
	public $model;
	/**
	 * @var string $apiUrl Default to 'api/beneficiary'
	 */
	public $apiUrl = '/api/beneficiary';
	/**
	 * @var string $attribute Default to 'beneficiary_id'
	 */
	public $attribute = 'beneficiary_id';
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
	public $placeholder = 'Cari berdasarkan No Ktp, No PM, Nama Panggilan';

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
		$options = $idValue ? [
			'codeValue'=>$this->model->beneficiary->beneficiary_no,
			'textValue'=>$this->model->beneficiary->full_name,
		] : [];

		return Select2::widget([
			'model'=>$this->model,
			'attribute'=>$this->attribute,
			'options'=>[
				'class'=>'bigdrop',
				'prompt'=>$this->placeholder,
			],
			'pluginOptions'=>Select2Options::toArray(ArrayHelper::merge($options, [
				'url'=>$this->apiUrl,
				'ajax.data'=>new JsExpression("function(params) { return {
          ktp_no:params.term,
          beneficiary_no:params.term,
          full_name:params.term,
          nickname:params.term
        } }"),
				'ajax.result'=>new JsExpression('function(data) {
          var results = [];
          $.each(data, function (index, item) {
            item.text = "<div><strong>" + item.beneficiary_no + "</strong> &mdash; " + item.full_name + "</div>";
            results.push(item);
          })
          return { results: results }
        }'),
				'templateResult'=>new JsExpression("function(o) {
          if (o.loading) {
            return o.full_name
          }
          var photoUrl = 'https://mulia.c27g.com/uploads/beneficiary-file/' + o.photo_url;
          var tableClass = 'table table-condensed table-responsive';
          var benef = '<div class=\"row\">';
          benef += '  <div class=\"col-sm-3\">';
          benef += '    <img class=\"img-thumbnail\" src=\"' + photoUrl + '\" width=\"150\">';
          benef += '  </div>';
          benef += '  <div class=\"col-sm-9\">';
          benef += '    <strong>' + o.beneficiary_no + '</strong><br>';
          benef += '    <table class=\"' + tableClass + '\">';
          benef += '      <tbody>';
          benef += '        <tr>';
          benef += '          <td>Nama Mustahik:</td>';
          benef += '          <td>' + o.full_name + '</td>';
          benef += '        </tr>';
          benef += '        <tr>';
          benef += '          <td>Tempat Tgl Lahir:</td>';
          benef += '          <td>' + o.birth_place + ', ' + o.birth_date + '</td>';
          benef += '        </tr>';
          benef += '        <tr>';
          benef += '          <td>Alamat:</td>';
          benef += '          <td>' + o.adress_full + '</td>';
          benef += '        </tr>';
          benef += '        <tr>';
          benef += '          <td>No KTP</td>';
          benef += '          <td>' + o.ktp_no + '</td>';
          benef += '        </tr>';
          benef += '        <tr>';
          benef += '          <td>No KK</td>';
          benef += '          <td>' + o.kk_no + '</td>';
          benef += '        </tr>';
          benef += '      </tbody>';
          benef += '  </div>';
          benef += '</div>';

          return jQuery('' + benef + '')
        }"),
        'placeholder'=>$this->placeholder,
        'idValue'=>$idValue,
        'codeField'=>'beneficiary_no',
        'textField'=>'full_name',
        'textValue'=>'full_name',
        'minLength'=>3,
			])),
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