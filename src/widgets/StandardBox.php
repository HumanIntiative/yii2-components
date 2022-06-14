<?php

namespace pkpudev\components\widgets;

/**
 * Bootstrap Box Standard Widget
 */
class StandardBox extends \yii\base\Widget
{
    /**
     * @var string $boxType
     */
    public $boxType = 'box-info';
    /**
     * @var string $withBorder
     */
    public $withBorder = 'with-border';
    /**
     * @var bool $withCollapse
     */
    public $withCollapse = false;
    /**
     * @var bool $withFooter
     */
    public $withFooter = false;
    /**
     * @var string $title
     */
    public $title;
    /**
     * @var string $footer
     */
    public $footer;
    /**
     * @var bool $visible
     */
    public $visible = true;
    /**
     * @var bool $isCollapse
     */
    public $isCollapse = false;
    /**
     * @var bool $isResponsive
     */
    public $isResponsive = false;

    public function init()
    {
        if (!$this->visible) {
            return;
        }

        if (!empty($this->footer)) {
            $this->withFooter = true;
        }

        $classCollapse = $this->isCollapse ? 'collapsed-box' : null;?>

        <div class="box <?=$this->boxType?> <?=$classCollapse?>">
            <?php if (!empty($this->title)): ?>
            <div class="box-header <?=$this->withBorder?>">
                <h3 class="box-title"><?=$this->title?></h3>
                <?php if ($this->withCollapse): ?>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <!-- <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                </div>
                <?php endif?>
            </div><!-- /.box-header -->
            <?php endif?>
            <div class="box-body">
                <?php if ($this->isResponsive): ?>
                <div class="table-responsive">
                <?php endif?>
        <?php
}

    public function run()
    {
        ?>
                <?php if ($this->isResponsive): ?>
                </div><!-- /.table-responsive -->
                <?php endif?>
            </div><!-- /.box-body -->
            <?php if ($this->withFooter): ?>
            <div class="box-footer clearfix">
                <?php echo $this->footer; ?>
            </div><!-- /.box-footer -->
            <?php endif?>
        </div>
        <?php
}
}