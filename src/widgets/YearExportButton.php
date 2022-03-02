<?php

namespace pkpudev\components\widgets;

use pkpudev\components\helpers\ArrayHelper;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

class YearExportButton extends Widget
{
    public $from;
    public $to;
    public $companyId = 1; //PKPU
    public $prefixFilepath = '/export/pmp/export-pmp-c';
    public $buttonId = 'btnDownload';
    public $dropdownId = 'downloadYear';
    public $placeholder = '--- Pilih Tahun ---';

    public function run()
    {
        $this->registerScript();

        $rangeYears = ArrayHelper::yearRangeOptions(range($this->to, $this->from), $this->placeholder);
        $downloadButton = Html::beginTag('div', ['class' => 'pull-left']) .
        Html::beginTag('div', ['class' => 'input-group input-group-sm']) .
        Html::dropdownList($this->dropdownId, null, $rangeYears, ['class' => 'form-control', 'id' => $this->dropdownId]) .
        Html::beginTag('span', ['class' => 'input-group-btn']) .
        Html::button('<i class="fa fa-file-excel-o"></i> Export', ['class' => 'btn btn-info', 'id' => $this->buttonId]) .
        Html::endTag('span') .
        Html::endTag('div') .
        Html::endTag('div');

        return $downloadButton;
    }

    protected function registerScript()
    {
        $script = "
			var getYear = function() {
				return (new Date()).getFullYear();
			};
			var getToday = function() {
				var today = new Date();
				var year = today.getFullYear();
				var month = String(today.getMonth() + 1).padStart(2, '0');
				var day = String(today.getDate()).padStart(2, '0');

				return year + month + day;
			};
			jQuery(document).on('click', '#{$this->buttonId}', function(event) {
				var year = jQuery('#{$this->dropdownId}').val();
				if (year == getYear()) {
					year = getToday();
				}
				var	url = '{$this->prefixFilepath}' + {$this->companyId} + '-' + year + '.xls'
				location.href = url
			})";
        $this->view->registerJs($script, View::POS_READY);
    }
}
