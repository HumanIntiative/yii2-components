<?php

namespace pkpudev\components\widgets;

use app\models\Ipp;
use app\models\Project;
use app\models\Proposal;
use kartik\widgets\ActiveForm;
use pkpudev\components\helpers\Html;
use Yii;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;

// TODO: Refactor All
class CommentBox extends Widget
{
    /** @var ActiveRecordInterface $parent */
    public $parent;
    /** @var ActiveRecordInterface $model */
    public $model;
    /** @var string $title */
    public $title = 'Project Comment';
    /** @var string $tooltip */
    public $tooltip;

    public $withRecipients = true;
    public $withActions;
    public $withForms;

    protected $data = [];
    protected $recipients = [];

    protected function getComments()
    {
        $photoLocation = 'http://intranet.c27g.com/uploads/photo/';
        if ($this->parent instanceof Project) {
            $sql = "SELECT
					t.id, t.parent_id, t.content, t.created_stamp,
                    r.project_id, t.created_by, cr.full_name,
                    '{$photoLocation}' || t.created_by || '.jpg' AS photo_url,
                    cr.sex as gender
				FROM pdg.comment t
					JOIN pdg.project__comment r ON t.id = r.comment_id
					JOIN sdm_employee cr ON cr.id = t.created_by
                WHERE r.project_id = " . $this->parent->id;
        } elseif ($this->parent instanceof Ipp) {
            $sql = "SELECT
					t.id, t.parent_id, t.content, t.created_stamp,
                    r.ipp_id, t.created_by, cr.full_name,
                    '{$photoLocation}' || t.created_by || '.jpg' AS photo_url,
                    cr.sex as gender
				FROM pdg.comment t
					JOIN pdg.ipp__comment r ON t.id = r.comment_id
					JOIN sdm_employee cr ON cr.id = t.created_by
                WHERE r.ipp_id = " . $this->parent->id;
        } elseif ($this->parent instanceof Proposal) {
            $sql = "SELECT
                    t.id, t.parent_id, t.content, t.created_stamp,
                    r.proposal_id, t.created_by, cr.full_name,
                    '{$photoLocation}' || t.created_by || '.jpg' AS photo_url,
                    cr.sex as gender
                FROM pdg.comment t
                    JOIN pdg.proposal__comment r ON t.id = r.comment_id
                    JOIN sdm_employee cr ON cr.id = t.created_by
                WHERE r.proposal_id = " . $this->parent->id;
        }

        return \Yii::$app->db->createCommand($sql)->queryAll();
    }

    protected function getRecipients()
    {
        if ($this->parent instanceof Project) {
            return $this->parent->commentRecipients;
        } elseif ($this->parent instanceof Ipp) {
            return $this->parent->commentRecipients;
        }

        return [];
    }

    public function init()
    {
        parent::init();
        $this->data = $this->getComments();
        $this->recipients = ArrayHelper::map($this->getRecipients(), 'id', 'full_name');
    }

    public function run()
    {
        $count = count($this->data);?>
        <div class="box box-primary direct-chat direct-chat-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?=$this->title?></h3>
                <div class="box-tools pull-right">
                    <span data-toggle="tooltip" title="<?=$this->tooltip?>" class="badge bg-blue"><?=$count?></span>
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <!-- In box-tools add this button if you intend to use the contacts pane -->
                    <!-- <button class="btn btn-box-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle"><i class="fa fa-comments"></i></button> -->
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages" style="height: 400px !important;">
                    <?php if ($count == 0): ?>
                    <div class="text-center"><i>Belum Ada Komentar.</i></div>
                    <?php endif?>

                    <?php foreach ($this->data as $comment): ?>
                        <?php if ($this->withActions) {
            $action = ($comment['created_by'] == Yii::$app->user->id) ? Html::a(
                '<i class="fa fa-trash"></i> ',
                ['delete-comment', 'id' => $comment['id']],
                [
                    'role' => 'modal-remote',
                    'data-modal-size' => 'large',
                    'data-confirm' => false,
                    'data-method' => false, // for overide yii data api
                    'data-request-method' => 'post',
                    'data-confirm-title' => 'Konfirmasi',
                    'data-confirm-message' => 'Apakah Anda yakin untuk menghapus komentar ini?',
                ]
            ) : null;
        }?>
                    <?php $leftOrient = $comment['created_by'] != Yii::$app->user->id ? 'left' : 'right'?>
                    <?php $rightOrient = $comment['created_by'] == Yii::$app->user->id ? 'left' : 'right'?>
                    <div class="direct-chat-msg <?=$leftOrient?>">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name pull-<?=$leftOrient?>"><?=$comment['full_name']?></span>
                            <span class="direct-chat-timestamp pull-<?=$rightOrient?>"><?=date('d F Y g:i A', strtotime($comment['created_stamp']))?></span>
                        </div><!-- /.direct-chat-info -->
                        <i class="fa fa-comments-o fa-2x direct-chat-img"></i>
                        <!-- img class="direct-chat-img" src="<?=$comment['photo_url']?>" alt="<?=$comment['full_name']?>" --><!-- /.direct-chat-img -->
                        <div class="direct-chat-text">
                            <?=nl2br($comment['content'])?>
                        </div><!-- /.direct-chat-text -->
                        <?=$action?>
                    </div><!-- /.direct-chat-msg -->
                    <?php endforeach;?>
                </div>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <?php if ($this->withForms): ?>
                    <?php $form = ActiveForm::begin([
            'id' => 'comment-form',
            // 'type'=>ActiveForm::TYPE_HORIZONTAL,
            'enableAjaxValidation' => false,
        ]);?>
                        <?=$form->errorSummary($this->model);?>

                        <?=$form->field($this->model, 'content')->textArea([
            'class' => 'form-control',
            'placeholder' => 'Isi Komentar ...',
            'rows' => 3,
        ]);?>

                        <?php if ($this->withRecipients): ?>
                            <?=$form->field($this->model, 'sent_to')->dropDownList($this->recipients, [
            'empty' => '--- Pilih Penerima ---',
            'class' => 'form-control',
        ])?>
                        <?php endif;?>

                        <?=Html::submitButton('<i class="fa fa-comment"></i> Komentar', [
            'class' => "btn btn-primary btn-flat",
        ]);?>
                    <?php ActiveForm::end();?>
                <?php endif;?>
            </div><!-- /.box-footer-->
        </div><!--/.direct-chat -->
        <?php
}
}