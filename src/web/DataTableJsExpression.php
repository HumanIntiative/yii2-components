<?php 

namespace pkpudev\components\web;

use pkpudev\components\assets\DataTableAsset;
use yii\base\BaseObject;
use yii\web\View;

class DataTableJsExpression extends BaseObject
{
    /**
     * @var yii\web\View $view
     */
    public $view;
    /**
     * @var string $divSelector
     */
    public $divSelector;
    /**
     * @var string $addSelector
     */
    public $addSelector;
    /**
     * @var string $delSelector
     */
    public $delSelector;
    /**
     * @var integer $delIndex
     */
    public $delIndex;
    /**
     * @var yii\web\JsExpression $createdCallback
     */
    public $createdCallback;
    /**
     * @var yii\web\JsExpression $addedCallback
     */
    public $addedCallback;
    /**
     * @var yii\web\JsExpression $deletedCallback
     */
    public $deletedCallback;

    public function init()
    {
        DataTableAsset::register($this->view);

        $irand = rand(0, 100);
        $jsScript = <<<JS
            // Var definitions
            var divSelector = '{$this->divSelector}';
            var addSelector = '{$this->addSelector}';
            var delSelector = '{$this->delSelector}';
            var delIndex    = {$this->delIndex} || 0;

            // Fn definitions
            var createdCallback = function(row, data, index) {
                {$this->createdCallback}
            };
            var addedCallback   = function() {
                {$this->addedCallback}
            };
            var deletedCallback = function() {
                {$this->deletedCallback}
            };
        JS;
        $jsScript .= file_get_contents(__DIR__.'/datatable.js');

        $this->view->registerJs($jsScript, View::POS_READY, "tabular-input-js{$irand}");
    }
}