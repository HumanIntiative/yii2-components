<?php

namespace pkpudev\components\widgets;

use yii\base\Widget;
use yii\data\BaseDataProvider;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;

class ListStatusHistory extends Widget
{
	/**
	 * @var string $gridId Grid ID
	 */
	public $gridId = 'history-grid';
	/**
	 * @var GridView $gridView
	 */
	public $gridView = null;
	/**
	 * @var string $serialColumn
	 */
	public $serialColumnClass = null;
	/**
	 * @var BaseDataProvider $dataProvider
	 */
	public $dataProvider;
	/**
	 * @var string $statusColumn
	 */
	public $statusColumn = 'status';
	/**
	 * @var string $byColumn
	 */
	public $byColumn = 'by';
	/**
	 * @var string $stampColumn
	 */
	public $stampColumn = 'stamp';
	/**
	 * @var string $commentColumn
	 */
	public $commentColumn = 'comment';
	/**
	 * @var string $summaryText
	 */
	public $summaryText = '';

	public function init()
	{
		parent::init();

		if (is_null($this->gridView)) {
			$this->gridView = new GridView;
		}
		if (is_null($this->serialColumnClass)) {
			$this->serialColumnClass = SerialColumn::className();
		}
	}

	public function run()
	{
		return $this->gridView::widget([
			'id'=>$this->gridId,
			'dataProvider' => $this->dataProvider,
			'columns' => $this->columns,
			'striped' => true,
			'condensed' => true,
			'responsive' => true,
		]);
	}

	public function getColumns()
	{
		$columns = [
			['class'=>$this->serialColumnClass]
		];

		if ($this->statusColumn == 'status_id') {
			array_push($columns, [
				'attribute'=>$this->statusColumn,
				'value'=>function($model) {
					return $model->status->status;
				},
			]);
		} else {
			array_push($columns, $this->statusColumn);
		}

		array_push($columns, [
			'attribute'=>$this->byColumn,
			'value'=>function($model) {
				return $model->creator->name;
			},
		]);
		array_push($columns, [
			'attribute'=>$this->stampColumn,
			'format'=>'datetime',
		]);
		array_push($columns, $this->commentColumn);

		return $columns;
	}
}