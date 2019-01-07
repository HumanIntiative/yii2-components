<?php

namespace pkpudev\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord as AR;
use yii\helpers\Json;

class MetadataBehavior extends Behavior
{
  const JSON_OPTIONS = 320; //JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE

  public $name = 'metadata';
  public $fields = 'extraFields';

  public function events()
  {
    return [
      AR::EVENT_AFTER_FIND => 'afterFind',
      AR::EVENT_BEFORE_INSERT => 'beforeSave',
      AR::EVENT_BEFORE_UPDATE => 'beforeSave',
    ];
  }

  public function loadMetadata($data, $formName)
  {
    // Dont use $model->load($data, '');
    $scope = $formName === null ? $this->owner->formName() : $formName;
    foreach ($this->owner->{$this->fields} as $field) {
      if (!is_null($data[$scope][$field])) { // Dont change, please
        $this->owner->{$field} = $data[$scope][$field];  
      }
    }
  }

  public function beforeSave($event)
  {
    $metadata = [];
    foreach ($this->owner->{$this->fields} as $field) {
      $metadata[$field] = $this->owner->{$field};
    }
    $this->owner->{$this->name} = json_encode($metadata, self::JSON_OPTIONS); //Json::encode();
  }

  public function afterFind($event)
  {
    $metadata = $this->owner->{$this->name} ?: [];
    $metadata = is_array($metadata) ? $metadata : json_decode($metadata, true); //Json::decode();
    foreach ($this->owner->{$this->fields} as $field) {
      $this->owner->{$field} = @$metadata[$field];
    }
  }
}