<?php

namespace pkpudev\components\widgets;

use kartik\select2\Select2;
use pkpudev\components\helpers\Select2Options;
use yii\base\Widget;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

/**
 * Using Dropdown for Selecting Person
 */
class PersonDropdown extends Widget
{
    /**
     * @var ActiveRecordInterface $model
     */
    public $model;
    /**
     * @var string $apiUrl Default to 'api/employee'
     */
    public $apiUrl = '/api/employee';
    /**
     * @var string $attribute Default to 'employee_id'
     */
    public $attribute = 'employee_id';
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
    public $placeholder = '--- Pilih Karyawan ---';
    /**
     * @var string $codeField
     */
    public $codeField = null;
    /**
     * @var string $codeValue
     */
    public $codeValue = null;
    /**
     * @var string $textField
     */
    public $textField = null;
    /**
     * @var string $textValue
     */
    public $textValue = null;

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

        /*if (null === ($donatur = $this->model->donatur)) {
            $placeholder = $this->placeholder;
        } else {
            $placeholder = (object)['id'=>$idValue, 'text'=>$donatur->full_name];
        }*/
        $placeholder = '--- Pilih '.$this->model->getAttributeLabel($this->attribute).' ---';
        $options = $idValue ? [
            'codeValue'=>$this->codeValue,
            'textValue'=>$this->textValue,
        ] : [];

        return Select2::widget([
            'model'=>$this->model,
            'attribute'=>$this->attribute,
            'options'=>['placeholder'=>$placeholder],
            'pluginOptions'=>Select2Options::toArray(ArrayHelper::merge($options, [
                'url'=>$this->apiUrl,
                'placeholder'=>$placeholder,
                'idValue'=>$idValue,
                'codeField'=>$this->codeField,
                'textField'=>$this->textField,
                'ajax.result'=>new JsExpression('function(data) {
                    var results = [];
                    jQuery.each(data, function (index, item) {
                        results.push({
                            id: item.id,
                            text: item.'.$this->textField.'
                        })
                    })
                    return { results: results }
                }'),
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