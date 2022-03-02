<?php

namespace pkpudev\components\widgets;

use Yii;
use yii\base\Widget;

class UserPanel extends Widget
{
    public $asset;

    protected $photoUrl = 'https://intranet.c27g.com/uploads/photo/';

    public function run()
    {
        $imgUrl = $this->photoUrl . Yii::$app->user->id . '.jpg';
        $img = @get_headers($imgUrl)[0] == 'HTTP/1.1 200 OK' ? $imgUrl : '/img/users.jpg';
        $userName = !Yii::$app->user->isGuest ? Yii::$app->user->identity->full_name : 'Guest'

        ?>
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?=$img?>" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?=$userName?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div><?php
}
}