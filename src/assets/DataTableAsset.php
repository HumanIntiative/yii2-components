<?php
/**
 * @copyright Copyright (c) 2014 Serhiy Vinichuk
 * @license MIT
 * @author Serhiy Vinichuk <serhiyvinichuk@gmail.com>
 */

namespace pkpudev\components\assets;

use yii\web\AssetBundle;

class DataTableAsset extends AssetBundle
{
    const STYLING_DEFAULT = 'default';
    const STYLING_BOOTSTRAP = 'bootstrap';
    const STYLING_JUI = 'jqueryui';

    public $styling = self::STYLING_DEFAULT;
    public $fontAwesome = false;
    public $sourcePath = '@bower';

    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        parent::init();

        if (empty($this->js)) {
            $this->js = ['datatables.net/js/jquery.dataTables' . (YII_ENV_DEV ? '' : '.min') . '.js'];
        }
        switch ($this->styling) {
            case self::STYLING_JUI:
                $this->depends[] = 'yii\jui\JuiAsset';
                $this->css[] = 'datatables.net-plugins/integration/jqueryui/dataTables.jqueryui.css';
                $this->js[] = 'datatables.net-plugins/integration/jqueryui/dataTables.jqueryui.min.js';
                break;
            case self::STYLING_BOOTSTRAP:
                $this->depends[] = 'yii\bootstrap\BootstrapAsset';
                $this->css[] = 'datatables.net-plugins/integration/bootstrap/3/dataTables.bootstrap.css';
                $this->js[] = 'datatables.net-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js';
                break;
            case self::STYLING_DEFAULT:
                $this->css[] = 'datatables.net/css/jquery.dataTables' . (YII_ENV_DEV ? '' : '.min') . '.css';
                break;
            default;
        }

        if ($this->fontAwesome) {
            $this->css[] = 'dataTables.fontAwesome.css';
        }
    }
} 