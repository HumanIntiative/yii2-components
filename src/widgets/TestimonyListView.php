<?php

namespace pkpudev\components\widgets;

use pkpudev\components\widgets\StandardBox;
use yii\base\Widget;
use yii\helpers\Html;

class TestimonyListView extends Widget
{
    public $title = 'Testimoni';
    public $boxType = 'box-default';
    public $withCollapse = true;
    public $model;

    public function run()
    {
        $query = $this->model->getTestimonies();
        $count = $query->count();
        $testimonies = $query->all();

        $title = $count ? "$this->title <small>($count)</small>" : $this->title;
        $btnGroup = \yii\helpers\Html::a('<i class="fa fa-upload"></i> Upload Testimoni',
            ['testimony', 'id' => $this->model->id, 'pid' => $this->view->projectId],
            ['class' => 'btn btn-default', 'role' => 'modal-remote']
        );?>

    <?php StandardBox::begin([
            'title' => $title,
            'boxType' => $this->boxType,
            'withCollapse' => $this->withCollapse,
            'footer' => $btnGroup,
        ])?>
      <table class="table no-margin">
        <thead>
          <tr>
            <th colspan="3">Testimoni</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($testimonies as $testimony): ?>
          <?php $name = $testimony->person_name?>
          <?php $desc = $testimony->person_description?>
          <?php $imgUrl = ['/image/view', 'id' => $testimony->photo_id, 'w' => 80, 'h' => 100]?>
          <tr>
            <td width="82"><?=Html::img($imgUrl, ['alt' => $name])?></td>
            <td>
              <blockquote>
                <p><?=$testimony->testimony?></p>
                <small><?=$name?> <cite title="Sbg">(<?=$desc?>)</cite></small>
              </blockquote>
            </td>
          </tr>
        <?php endforeach;?>
        </tbody>
      </table>
    <?php StandardBox::end();
    }
}