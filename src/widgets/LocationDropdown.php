<?php

namespace pkpudev\components\widgets;

use kartik\select2\Select2;
use pkpudev\components\web\ViewBehavior;
use yii\base\Widget;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Using Dropdown for Selecting Prov, Kab/Kota, Kec and Kel/Desa
 */
class LocationDropdown extends Widget
{
	/**
	 * @var yii\db\ActiveRecordInterface $locationModel Location Model
	 */
	public $locationModel;
	/**
	 * @var yii\db\ActiveRecordInterface $countryModel Country Model
	 */
	public $countryModel;
	/**
	 * @var yii\db\ActiveRecordInterface $model
	 */
	public $model;
	/**
	 * @var string $attribure Model attribure
	 */
	public $attribute;
	/**
	 * @var string $apiUrl Api location. Default to 'api/location'
	 */
	public $apiUrl = '/api/location';
	/**
	 * @var string $apiRequestType Api Request Type. Default to 'GET'
	 */
	public $apiRequestType = 'GET';
	/**
	 * @var string $attributeCountry Attribute name for country
	 */
	public $attributeCountry = 'country_id';
	/**
	 * @var string $attributeAddress Attribute name for address
	 */
	public $attributeAddress = 'address';
	/**
	 * @var string $attributeLocation1 Attribute name for location_id 1
	 */
	public $attributeLocation1 = 'location_id_1';
	/**
	 * @var string $attributeLocation2 Attribute name for location_id 2
	 */
	public $attributeLocation2 = 'location_id_2';
	/**
	 * @var string $attributeLocation3 Attribute name for location_id 3
	 */
	public $attributeLocation3 = 'location_id_3';
	/**
	 * @var string $attributeLocation4 Attribute name for location_id 4
	 */
	public $attributeLocation4 = 'location_id_4';
	/**
	 * @var string $attributeSalt For two or more dropdowns per page
	 */
	public $attributeSalt = 'd5x7y3z0f1';
	/**
	 * @var string $colspanText Class name for label grid
	 */
	public $colspanText = 'col-sm-6';
	/**
	 * @var string $colspanLabel Class name for label grid
	 */
	public $colspanLabel = 'col-md-3';
	/**
	 * @var bool $useCountry If you want to use country input
	 */
	public $useCountry = false;
	/**
	 * @var bool $allRequired If you want to check required all
	 */
	public $allRequired = false;
	/**
	 * @var bool $readOnly If you want to make read only
	 */
	public $readOnly = false;
	/**
	 * @var bool $isHorizontal If you want to make it form horizontal
	 */
	public $isHorizontal = true;

	/**
	 * @var string[] $dataLocation1 Array of options for location 1
	 */
	protected $dataLocation1 = [];
	/**
	 * @var string[] $dataLocation2 Array of options for location 2
	 */
	protected $dataLocation2 = [];
	/**
	 * @var string[] $dataLocation3 Array of options for location 3
	 */
	protected $dataLocation3 = [];
	/**
	 * @var string[] $dataLocation4 Array of options for location 4
	 */
	protected $dataLocation4 = [];
	/**
	 * @var string[] $dataCountry Array of options for country
	 */
	protected $dataCountry = [];

	public function init()
	{
		parent::init();

		$validLocation = ($this->locationModel instanceof ActiveRecordInterface) 
			&& strpos(get_class($this->locationModel), 'Location');
		$validCountry = ($this->countryModel instanceof ActiveRecordInterface)
			&& strpos(get_class($this->countryModel), 'Country');

		if (!$validLocation || !$validCountry) {
			throw new \yii\base\InvalidConfigException("Model Location/Country mismatch");
		}

		$this->view->attachBehavior('addfun', new ViewBehavior);
	}

	public function run()
	{
		$this->prepareData();
		$this->registerJs();
		return $this->createDropdown();
	}

	public function prepareData()
	{
		$val_attr1 = $this->model->{$this->attributeLocation1};
		$val_attr2 = $this->model->{$this->attributeLocation2};
		$val_attr3 = $this->model->{$this->attributeLocation3};

		$locationModel = $this->locationModel;
		$countryModel = $this->countryModel;

		$dataLocation1 = $locationModel::find()->where('parent_id=0')->all();
		$this->dataLocation1 = ArrayHelper::map($dataLocation1, 'id', 'location_name');

		$this->dataLocation2 = [];
		if (!empty($val_attr1)) {
			$dataLocation2 = $locationModel::find()->where(['parent_id'=>$val_attr1])->all();
			$this->dataLocation2 = ArrayHelper::map($dataLocation2, 'id', 'location_name');
		}

		$this->dataLocation3 = [];
		if (!empty($val_attr2)) {
			$dataLocation3 = $locationModel::find()->where(['parent_id'=>$val_attr2])->all();
			$this->dataLocation3 = ArrayHelper::map($dataLocation3, 'id', 'location_name');
		}

		$this->dataLocation4 = [];
		if (!empty($val_attr3)) {
			$dataLocation4 = $locationModel::find()->where(['parent_id'=>$val_attr3])->all();
			$this->dataLocation4 = ArrayHelper::map($dataLocation4, 'id', 'location_name');
		}

		$this->dataCountry = [];
		if ($this->useCountry) {
			$dataCountry = $countryModel::find()->orderBy('country_name ASC')->all();
			$this->dataCountry = ArrayHelper::map($dataCountry, 'id', 'country_name');
		}
	}

	public function createDropdown()
	{
		$activeLabel = Html::activeLabel($this->model, $this->attribute, [
			'class'=>'control-label '.($this->isHorizontal?$this->colspanLabel:null),
			'required'=>$this->model->isAttributeRequired($this->attribute),
		]);
		$dropdown = $this->selectCountry() .
			$this->textAddress() .
			Html::beginTag('div', ['class'=>'id_area']) .
				$this->selectProvince() .
				$this->selectDistrict() .
				$this->selectSubDistrict() .
				$this->selectVillage() .
			Html::endTag('div');
		$errorBlock = null; //TODO

		if ($this->isHorizontal) {
			return $this->templateHorizontal($activeLabel, $dropdown, $errorBlock);
		} else {
			return $this->templateVertical($activeLabel, $dropdown, $errorBlock);
		}
	}

	public function registerJs()
	{
		$script = '
		Array.prototype.clone = function() {
			return this.slice(0);
		};

		var rebuildSelect2 = function(selector, data, placeholder) {
			var options = { allowClear:true, data:data, language:"id", placeholder:placeholder, theme:"krajee", width:"100%" };
			var selectorId = "#" + selector;

			jQuery(selectorId).select2("destroy");
			jQuery(selectorId).html("");
			jQuery.when(jQuery(selectorId).select2(options)).done(initS2Loading(selector, "s2options_d6851687"));
		};

		var addPlaceHolder = function(data, placeholder) {
			var newdata = data.clone();
			newdata.unshift({id: "", text: ""});
			return newdata;
		};

		jQuery("#country_id").on("change", function(e){
			var country = jQuery(this);
			if (country.val()==100) { //Indonesia
				jQuery(".id_area").show(500);
			} else {
				jQuery(".id_area").hide(500);
			}
		});

		jQuery("#'.$this->attr_location1_id.'").on("change", function(){
			var val = jQuery(this).val();
			jQuery.ajax({
				type: "'.$this->apiRequestType.'",
				url: "'.$this->apiUrl.'",
				dataType: "json",
				data: { Location: { lev:3, parent_id:val } },
				success: function(data, st, xhr) {
					data = addPlaceHolder(data, "")
					rebuildSelect2("'.$this->attr_location2_id.'", data, "--- Pilih Kota/Kabupaten ---");
					rebuildSelect2("'.$this->attr_location3_id.'", [], "--- Pilih Kecamatan ---");
					rebuildSelect2("'.$this->attr_location4_id.'", [], "--- Pilih Kelurahan/Desa ---");
				}
			})
		});

		jQuery("#'.$this->attr_location2_id.'").on("change", function(){
			var val = jQuery(this).val();
			jQuery.ajax({
				type: "'.$this->apiRequestType.'",
				url: "'.$this->apiUrl.'",
				dataType: "json",
				data: { Location: { lev:4, parent_id:val } },
				success: function(data, st, xhr) {
					data = addPlaceHolder(data, "")
					rebuildSelect2("'.$this->attr_location3_id.'", data, "--- Pilih Kecamatan ---");
					rebuildSelect2("'.$this->attr_location4_id.'", [], "--- Pilih Kelurahan/Desa ---");
				}
			})
		});

		jQuery("#'.$this->attr_location3_id.'").on("change", function(){
			var val = jQuery(this).val();
			jQuery.ajax({
				type: "'.$this->apiRequestType.'",
				url: "'.$this->apiUrl.'",
				dataType: "json",
				data: { Location: { lev:5, parent_id:val } },
				success: function(data, st, xhr) {
					data = addPlaceHolder(data, "")
					rebuildSelect2("'.$this->attr_location4_id.'", data, "--- Pilih Kelurahan/Desa ---");
				}
			})
		});';

		$this->view->addJsFuncReady($script);
	}

	protected function templateHorizontal($activeLabel, $dropdown, $errorBlock)
	{
		return Html::beginTag('div', ['class'=>'form-group']) .
				$activeLabel .
				Html::beginTag('div', ['class'=>$this->colspanText]) .
					$dropdown .
					$errorBlock . 
				Html::endTag('div') .
			Html::endTag('div');
	}

	protected function templateVertical($activeLabel, $dropdown, $errorBlock)
	{
		return Html::beginTag('div', ['class'=>'form-group']) .
				$activeLabel .
				$dropdown .
				$errorBlock . 
			Html::endTag('div');
	}

	protected function getAttr_location1_id()
	{
		return $this->attributeLocation1 . $this->attributeSalt;
	}

	protected function getAttr_location2_id()
	{
		return $this->attributeLocation2 . $this->attributeSalt;
	}

	protected function getAttr_location3_id()
	{
		return $this->attributeLocation3 . $this->attributeSalt;
	}

	protected function getAttr_location4_id()
	{
		return $this->attributeLocation4 . $this->attributeSalt;
	}

	protected function selectCountry()
	{
		$dropdown = null;
		if ($this->useCountry) {
			$isRequired = $this->model->isAttributeRequired($this->attributeCountry);

			$dropdown = Html::beginTag('div', ['style'=>'padding-top:5px;']);
			$dropdown .= 'Negara';
			$dropdown .= ($isRequired ? Html::tag('span', '*', ['class'=>'required']) : null);
			$dropdown .= Html::endTag('div');
			$dropdown .= Select2::widget([
				'model'=>$this->model,
				'attribute'=>$this->attributeCountry,
				'data'=>$this->dataCountry,
				'options'=>[
					'id'=>'country_id',
					'prompt'=>'--- Pilih Negara (Jika Luar Negeri) ---',
				],
				'pluginOptions'=>['allowClear'=>true],
			]);
		}
		return $dropdown;
	}

	protected function textAddress()
	{
		return Html::beginTag('div', ['style'=>'padding-top:5px;']) .
				'Alamat (<small>hanya menuliskan Nama jalan, No rumah, Rt/Rw</small>)' .
				($this->allRequired ? Html::tag('span', '*', ['class'=>'required']) : null) .
			Html::endTag('div') .
			Html::activeTextarea($this->model, $this->attributeAddress, array(
				'class'=>'form-control',
				'rows'=>2,
				'placeholder'=>$this->model->getAttributeLabel($this->attributeAddress),
				'readonly'=>$this->readOnly,
			));
	}

	protected function selectProvince()
	{
		return Html::beginTag('div', ['class'=>'padding-top:5px;']) .
				'Provinsi' .
				($this->allRequired ? Html::tag('span', '*', ['class'=>'required']) : null) .
			Html::endTag('div') .
			Select2::widget([
				'model'=>$this->model,
				'attribute'=>$this->attributeLocation1,
				'data'=>$this->dataLocation1,
				'options'=>[
					'id'=>$this->attr_location1_id,
					'prompt'=>'--- Pilih Provinsi ---',
				],
				'pluginOptions'=>['allowClear'=>true],
			]);
	}

	protected function selectDistrict()
	{
		return Html::beginTag('div', ['class'=>'padding-top:5px;']) .
				'Kota/Kabupaten' .
				($this->allRequired ? Html::tag('span', '*', ['class'=>'required']) : null) .
			Html::endTag('div') .
		 	Select2::widget([
				'model'=>$this->model,
				'attribute'=>$this->attributeLocation2,
				'data'=>$this->dataLocation2,
				'options'=>[
					'id'=>$this->attr_location2_id,
					'prompt'=>'--- Pilih Kota/Kabupaten ---',
				],
				'pluginOptions'=>['allowClear'=>true],
			]);
	}

	protected function selectSubDistrict()
	{
		return Html::beginTag('div', ['class'=>'padding-top:5px;']) .
				'Kecamatan' .
				($this->allRequired ? Html::tag('span', '*', ['class'=>'required']) : null) .
			Html::endTag('div') .
			Select2::widget([
				'model'=>$this->model,
				'attribute'=>$this->attributeLocation3,
				'data'=>$this->dataLocation3,
				'options'=>[
					'id'=>$this->attr_location3_id,
					'prompt'=>'--- Pilih Kecamatan ---',
				],
				'pluginOptions'=>['allowClear'=>true],
			]);
	}

	protected function selectVillage()
	{
		return Html::beginTag('div', ['class'=>'padding-top:5px;']) .
				'Kelurahan/Desa' .
				($this->allRequired ? Html::tag('span', '*', ['class'=>'required']) : null) .
			Html::endTag('div') .
			Select2::widget([
				'model'=>$this->model,
				'attribute'=>$this->attributeLocation4,
				'data'=>$this->dataLocation4,
				'options'=>[
					'id'=>$this->attr_location4_id,
					'prompt'=>'--- Pilih Kelurahan/Desa ---',
				],
				'pluginOptions'=>['allowClear'=>true],
			]);
	}
}