<?php

namespace pkpudev\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecordInterface;

/**
 * Location Model Behavior for usage with LocationDropdown widget
 */
class LocationData extends Behavior
{
	// Data Access
	public $locationModel;
	public $countryModel;

	// For Model (Owner)
	public $attribute   = 'location_id';
	public $attribute1  = 'location_id_1';
	public $attribute2  = 'location_id_2';
	public $attribute3  = 'location_id_3';
	public $attribute4  = 'location_id_4';
	public $attrAddress = 'address';
	public $attrCountry = 'country_id';

	// For Location Model
	protected $_attributeId     = 'id';
	protected $_attributeName   = 'location_name';
	protected $_attributeLevel  = 'lev';
	protected $_attributeParent = 'parent_id';

	// Attributes
	protected $_negara;
	protected $_provinsi;
	protected $_kabupaten;
	protected $_kecamatan;
	protected $_kelurahan;

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
	}

	public function events()
	{
		$model = $this->owner;
		return [
			$model::EVENT_BEFORE_VALIDATE => 'beforeValidate',
			$model::EVENT_AFTER_FIND => 'loadLocation'
		];
	}

	public function beforeValidate($event)
	{
		$modelName = (new \ReflectionClass($this->owner))->getShortName();
		$post = \Yii::$app->request->post($modelName);

		if ($attr4 = $post[$this->attribute4]) { // Kelurahan
			$this->owner->{$this->attribute} = $attr4;
		} elseif ($attr3 = $post[$this->attribute3]) { // Kecamatan
			$this->owner->{$this->attribute} = $attr3;
		} elseif ($attr2 = $post[$this->attribute2]) { // Kabupaten
			$this->owner->{$this->attribute} = $attr2;
		} elseif ($attr1 = $post[$this->attribute1]) { // Provinsi
			$this->owner->{$this->attribute} = $attr1;
		} else {
			$this->owner->{$this->attribute} = null;
		}
	}

	public function loadLocation($event)
	{
		$countryModel = $this->countryModel;
		$locationModel = $this->locationModel;

		if (isset($this->owner->{$this->attrCountry}))
			if (null !== ($country = $countryModel::findOne($this->owner->{$this->attrCountry})))
				$this->_negara = $country->country_name;

		if (empty($this->owner->{$this->attribute})) return;
		if (null === ($location = $locationModel::findOne($this->owner->{$this->attribute}))) return;

		$parent = $location;
		while ($parent) {
			switch ($parent->{$this->_attributeLevel}) {
				case 5: //Kelurahan
					$this->owner->{$this->attribute4} = $parent->{$this->_attributeId};
					$this->_kelurahan = $parent->{$this->_attributeName};
					break;
				case 4: // Kecamatan
					$this->owner->{$this->attribute3} = $parent->{$this->_attributeId};
					$this->_kecamatan = $parent->{$this->_attributeName};
					break;
				case 3: // Kabupaten
					$this->owner->{$this->attribute2} = $parent->{$this->_attributeId};
					$this->_kabupaten = $parent->{$this->_attributeName};
					break;
				case 2: // Provinsi
					$this->owner->{$this->attribute1} = $parent->{$this->_attributeId};
					$this->_provinsi = $parent->{$this->_attributeName};
			}
			$parent = $locationModel::findOne($parent->{$this->_attributeParent});
		}
	}

	public function getLocation()
	{
		$locationModel = $this->locationModel;

		if (empty($this->owner->{$this->attribute})) return;
		if (null === ($location = $locationModel::findOne($this->owner->{$this->attribute}))) return;

		$parent = $location;
		while ($parent) {
			switch ($parent->{$this->_attributeLevel}) {
				case 5:
					$kelurahan = $parent->{$this->_attributeName};
					if (empty($kelurahan)) break;
				case 4:
					$kecamatan = $parent->{$this->_attributeName};
					if (empty($kecamatan)) break;
				case 3:
					$kabupaten = $parent->{$this->_attributeName};
					if (empty($kabupaten)) break;
				case 2:
					$provinsi = $parent->{$this->_attributeName};
			}
			$parent = $locationModel::findOne($parent->{$this->_attributeParent});
		}

		switch ($location->{$this->_attributeLevel}) {
			case 5:
				return $lokasi = "Kel. ".$kelurahan." Kec. ".$kecamatan." Kab. ".$kabupaten.", ".$provinsi; 
				break;
			case 4:
				return $lokasi = " Kec. ".$kecamatan." Kab. ".$kabupaten.", ".$provinsi; 
				break;
			case 3:
				return $lokasi = " Kab. ".$kabupaten.", ".$provinsi; 
				break;
			case 2:
				return $lokasi = $provinsi;  
		}
	}

	public function getAddressComplete()
	{
		if (isset($this->owner->{$this->attrCountry})) {
			$address .= '<em>'.$this->getNegara().'</em>';
			$address .= '<br />';
		}
		$address .= $this->owner->{$this->attrAddress};
		$address .= '<br />';
		$address .= $this->getLocation();

		return $address;
	}

	public function getNegara()
	{
		return $this->_negara ? $this->_negara : 'Indonesia';
	}

	public function getProvinsi()
	{
		return $this->_provinsi;
	}

	public function getKabupaten()
	{
		return $this->_kabupaten;
	}

	public function getKecamatan()
	{
		return $this->_kecamatan;
	}

	public function getKelurahan()
	{
		return $this->_kelurahan;
	}
}