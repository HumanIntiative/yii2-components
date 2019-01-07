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
	public $placeholder = '--- Pilih Penerima Manfaat ---';
	/**
	 * @var bool $enabled
	 */
	public $enabled = true;

	protected $idValue;
	protected $benef;

	public function init()
	{
		parent::init();
		$this->idValue = $this->model->{$this->attribute};
		$this->benef = is_numeric($this->idValue) ? $this->model->beneficiary : null;
	}

	public function run()
	{
		$activeLabel = Html::activeLabel($this->model, $this->attribute, [
			'class'=>'control-label '.($this->isHorizontal?'col-md-2':null),
			'required'=>$this->model->isAttributeRequired($this->attribute),
		]);
		$dropdown = $this->getDropdown();
		$summary = $this->getValueSummary();
		$errorBlock = Html::error($this->model, $this->attribute, [
			'class'=>'help-block error'
		]);

		if ($this->isHorizontal) {
			echo $this->templateHorizontal($activeLabel, $dropdown, $summary, $errorBlock);
		} else {
			echo $this->templateVertical($activeLabel, $dropdown, $summary, $errorBlock);
		}
	}

	protected function getDropdown()
	{
		$options = $this->getValueOptions();

		return Select2::widget([
			'model'=>$this->model,
			'attribute'=>$this->attribute,
			'options'=>[
				'class'=>'bigdrop',
				'placeholder'=>$this->placeholder,
				'prompt'=>'Cari berdasarkan No Ktp, No PM, Nama Panggilan',
			],
			'disabled' => !$this->enabled,
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
        'placeholder'=>'Cari berdasarkan No Ktp, No PM, Nama Panggilan',
        'idValue'=>$this->idValue,
        'codeField'=>'beneficiary_no',
        'textField'=>'full_name',
        'minLength'=>3,
			])),
		]);
	}

	protected function getValueOptions()
	{
		return ($this->benef) ? [
			'codeValue'=>$this->benef->beneficiary_no,
			'textValue'=>$this->benef->full_name,
		] : [];
	}

	protected function getValueSummary()
	{
		return ($this->benef)
			? sprintf("%s - %s", $this->benef->beneficiary_no, $this->benef->full_name)
			: null;
	}

	protected function templateHorizontal($activeLabel, $dropdown, $summary, $errorBlock)
	{
		?>
		<div class="form-group">
			<?= $activeLabel ?>
			<div class="<?=$this->colspanText?>">
				<?= $dropdown; ?>
				<?= $summary; ?>
				<?= $errorBlock ?>
			</div>
		</div>
		<?php
	}

	protected function templateVertical($activeLabel, $dropdown, $summary, $errorBlock)
	{
		?>
		<div class="form-group">
			<?= $activeLabel ?>
			<?= $dropdown ?>
			<?= $summary; ?>
			<?= $errorBlock ?>
		</div>
		<?php
	}
}