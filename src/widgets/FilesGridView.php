<?php

namespace pkpudev\components\widgets;

use kartik\grid\GridView;
use pkpudev\components\helpers\FileHelper;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\Url;

class FilesGridView extends Widget
{
    public $gridId = 'file-grid';
    public $dataProvider;
    public $summaryText = '';
    public $withModal = false;
    public $withView = false;
    public $withDelete = false;
    public $withDownload = true;
    public $showFooter = false;
    public $basePathDownload;
    public $projectId;
    public $suffixUrl = null;

    public function run()
    {
        $this->dataProvider->sort = [
            'defaultOrder' => ['id' => SORT_ASC],
        ];
        $this->dataProvider->pagination = [
            'defaultPageSize' => 10,
        ];

        return GridView::widget([
            'id' => $this->gridId,
            'dataProvider' => $this->dataProvider,
            'columns' => $this->columns,
            'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
            'pjax' => $this->withModal ? true : false,
            'panel' => [
                'before' => false,
                'after' => false,
            ],
        ]);
    }

    public function getColumns()
    {
        $suffixUrl = $this->suffixUrl;
        $projectId = $this->projectId;

        return [
            [
                'class' => 'kartik\grid\SerialColumn',
                'vAlign' => 'top',
                'width' => '30px',
            ],
            [
                'attribute' => 'file_type',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'file.file_name',
            ],
            [
                'attribute' => 'file.byte_size',
                'value' => function ($model) {
                    return FileHelper::formatReadable($model->file->byte_size);
                },
            ],
            [
                'header' => 'Waktu',
                'attribute' => 'file.created_stamp',
                'value' => function ($model) {
                    return date("Y-m-d H:i:s", strtotime($model->file->created_stamp));
                },
            ],
            [
                'header' => 'Pembuat',
                'attribute' => 'file.created_by',
                'value' => function ($model) {
                    return $model->file->creator->full_name;
                },
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{view} {download} {delete}',
                'width' => '80px',
                'visibleButtons' => [
                    'view' => $this->withView,
                    'download' => $this->withDownload,
                    'delete' => $this->withDelete,
                ],
                'urlCreator' => function ($action, $model, $key, $index) use ($suffixUrl, $projectId) {
                    $action = $suffixUrl ? "{$action}-{$suffixUrl}" : $action;
                    return Url::to([$action, 'id' => $key, 'pid' => $projectId]);
                },
                'buttons' => [
                    'download' => function ($url, $model) {
                        $location = $model->file->location;
                        $loc = substr($location, 0, 4);

                        if (getenv('YII_ENV') == 'dev') {
                            $subproject = 'projectx.';
                            $subsigma = 'sigmax.';
                        } else {
                            $subproject = 'project.';
                            $subsigma = 'sigma.';
                        }

                        $domain = getenv('DOMAIN_URL');
                        $checkUrl = 'https://' . $subproject . $domain . '/document' . $location;

                        $file_headers = @get_headers($checkUrl);
                        if ($file_headers[0] == 'HTTP/1.1 200 OK') {
                            $downloadUrl = $checkUrl;
                        } else {
                            $downloadUrl = 'https://' . $subsigma . $domain . '/documents' . $location;
                        }

                        if ($loc == 'http') {
                            return Html::a('<i class="fa fa-download"></i>', Url::to($location), [
                                'title' => 'Download',
                                'data-toggle' => 'tooltip',
                                'target' => '_blank',
                                'data-pjax' => 0,
                            ]);
                        } elseif ($loc == '/ipp') {
                            return Html::a('<i class="fa fa-download"></i>', Url::to($downloadUrl), [
                                'title' => 'Download',
                                'data-toggle' => 'tooltip',
                                'target' => '_blank',
                                'data-pjax' => 0,
                            ]);
                        } else {
                            return Html::a('<i class="fa fa-download"></i>', $url, [
                                'title' => 'Download',
                                'data-toggle' => 'tooltip',
                                'target' => '_blank',
                                'data-pjax' => 0,
                            ]);
                        }
                    },
                ],
                'viewOptions' => [
                    'data-toggle' => 'tooltip',
                    'label' => '<i class="fa fa-eye"></i>',
                    'title' => 'View',
                ],
                'deleteOptions' => [
                    'role' => $this->withModal ? 'modal-remote' : false,
                    'title' => 'Delete',
                    'label' => '<i class="fa fa-trash"></i>',
                    'data-method' => false, // for overide yii data api
                    'data-confirm' => false,
                    'data-toggle' => 'tooltip',
                    'data-request-method' => 'post',
                    'data-confirm-title' => 'Anda yakin?',
                    'data-confirm-message' => 'Anda yakin akan menghapus file ini?',
                ],
            ],
        ];
    }
}
