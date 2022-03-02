<?php

namespace pkpudev\components\widgets;

use app\models\Photo;
use pkpudev\components\widgets\StandardBox;
use yii\base\Widget;
use yii\helpers\Html;

class ImageListView extends Widget
{
    public $title = 'Foto';
    public $boxType = 'box-default';
    public $withCollapse = true;
    public $model;
    public $typeMeta;

    protected $photoWidth = 100;
    protected $photoHeight = 100;

    public function run()
    {
        $query = $this->model->getPhotos($this->typeMeta);
        $count = $query->count();
        $photos = $query->all();

        $title = $count ? "$this->title <small>($count)</small>" : $this->title;
        $btnGroup = \yii\helpers\Html::a('<i class="fa fa-upload"></i> Upload Foto',
            ['photo', 'id' => $this->model->id, 'pid' => $this->view->projectId],
            ['class' => 'btn btn-default', 'role' => 'modal-remote']
        );?>

        <?php StandardBox::begin([
            'title' => $title,
            'boxType' => $this->boxType,
            'withCollapse' => $this->withCollapse,
            'footer' => $btnGroup,
        ])?>
                <ul class="users-list clearfix">
                <?php foreach ($photos as $photo): ?>
                    <?php $desc = $photo->description?>
                    <li>
                        <?=$this->getImg($photo->photo, $desc)?>
                        <a class="users-list-name" href="#"><?=$desc?></a>
                        <!-- <span class="users-list-date">Today</span> -->
                    </li>
                <?php endforeach;?>
                </ul>
        <?php StandardBox::end();
    }

    protected function getImg(Photo $photo, $desc)
    {
        $attributes = [
            'width' => $this->photoWidth,
            'height' => $this->photoHeight,
            'alt' => $desc,
        ];

        return Html::img('/document' . $photo->location, $attributes);
    }
}