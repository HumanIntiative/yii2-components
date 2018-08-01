<?php

namespace pkpudev\components\excel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xls as ExcelWriter;
use yii\base\Component;

class ArrayDataToExcel extends Component
{
	public $headers = [];
	public $data = [];
	public $options = [];

	public function init()
	{
		// ini_set('memory_limit', '512M');
		parent::init();
	}

	public function create($saveAsFile=true, $filename=null)
	{
		// Get Options
		$options = $this->mergeOptions($this->options);

		// Create Instance
		$spreadsheet = new Spreadsheet();

		// Set properties
		$spreadsheet->getProperties()
			->setCreator($options['creator'])
			->setLastModifiedBy($options['lastModifiedBy'])
			->setTitle($options['title'])
			->setSubject($options['subject'])
			->setDescription($options['desc'])
			->setKeywords($options['keywords'])
			->setCategory($options['category']);

		// Select Worksheet
		$worksheet = $spreadsheet->getActiveSheet();

		// Page Setup
		$worksheet->getPageSetup()
			->setOrientation($options['page_orientation'])
			->setPaperSize($options['page_size'])
			->setRowsToRepeatAtTopByStartAndEnd(1, 2);

		// Page Margins
		$worksheet->getPageMargins()
			->setRight($options['margin_right'])
			->setLeft($options['margin_left']);

		// Set Footer
		$worksheet->getHeaderFooter()
			->addImage(new HeaderFooterDrawing, $options['header_imagePosition'])
			->setOddHeader("&L&G&C&H".$options['company'])
			->setOddFooter("&L&B".$options['title']."&RPage &P of &N");

		// Range Style
		$rangeStyle = $worksheet->getStyle($options['style_fillRange']);
		$rangeStyle->applyFromArray($options['style_fontBold']);
		$rangeStyle->getFill()->setFillType($options['header_fillType']);
		$rangeStyle->getFill()->getStartColor()->setRGB($options['header_fillColor']);
		unset($rangeStyle);

		// Header Width
		$worksheet->getColumnDimension($options['first_col'])->setWidth(4);
		foreach ($this->headers as $header) {
			$worksheet->getColumnDimension($header['column'])->setWidth($header['width']);
		}

		// Header Title
		$worksheet->setCellValue($options['first_cell'], $options['first_cell_label']);
		foreach ($this->headers as $header) {
			$worksheet->setCellValue($header['column'].'1', $header['title']);
		}

		// Freeze panes
		$worksheet->freezePane($options['cell_freezePane']);

		// Other Variables
		$a=2; $no=1;
		$worksheet->getRowDimension($a)->setRowHeight($options['data_rowHeight']);
		$range = sprintf('%s%s:%s%s', $options['first_col'], $a, $options['column_lastChar'], $a);
		$rangeStyle = $worksheet->getStyle($range);
		$rangeStyle->getAlignment()->setWrapText(true);
		$rangeStyle->getAlignment()->setVertical($options['data_verticalAlignment']);
		$rangeStyle->applyFromArray($options['style_borderBottom']);

		// Fill Data
		foreach ($this->data as $row) {
			$worksheet->setCellValue($options['first_col'].$a, $no);
			foreach ($this->headers as $index => $header) {
				$fieldname = $header['fieldname'];
				$worksheet->setCellValue($header['column'].$a, $row[$fieldname]);
			}
			$a++; $no++;
		}

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);
		$today = date($options['date_format']);

		if (is_null($filename))
			$filename = "{$options[filename_prepend]} {$today}.xls";

		if ($saveAsFile) {
			$output = $filename;
		} else {
			// Redirect output to a client's web browser (Excel5)
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment;filename=\"{$filename}\"");
			header("Cache-Control: max-age=0");
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');
			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			$output = 'php://output';
		}

		// Save Output
		$writer = new ExcelWriter($spreadsheet);
		$writer->save($output);
	}

	protected function getDefaultOptions()
	{
		return [
			// General Options
			'company'=>'PKPU - Lembaga Kemanusiaan Nasional.',
			'creator'=>'IT Dev',
			'title'=>'Download - Mulia Project v2',
			'subject'=>null,
			'desc'=>null,
			'keywords'=>null,
			'category'=>'Download',
			'lastModifiedBy'=>'IT Dev Team',
			'filename_prepend'=>'Export Ipp',
			'date_format'=>'Y-m-d_his',
			// Cell Options
			'first_col'=>'A',
			'first_cell'=>'A1',
			'first_cell_label'=>'No',
			// Component Options
			'style_fontBold'=>['font'=>['bold'=>true]],
			'style_borderBottom'=>[
				'borders'=>[
					'bottom'=>['style'=>Border::BORDER_THIN, 'color'=>['argb'=>'F555753']],
				],
			],
			'style_fillRange'=>'A1:BP1',
			'column_lastChar'=>'BP',
			'page_orientation'=>PageSetup::ORIENTATION_LANDSCAPE,
			'page_size'=>PageSetup::PAPERSIZE_A4,
			'margin_left'=>0.3,
			'margin_right'=>0,
			'header_imagePosition'=>HeaderFooter::IMAGE_HEADER_LEFT,
			'header_fillType'=>Fill::FILL_SOLID,
			'header_fillColor'=>'EAEAEA',
			'cell_freezePane'=>'A2',
			'data_rowHeight'=>20,
			'data_verticalAlignment'=>Alignment::VERTICAL_CENTER,
		];
	}

	protected function mergeOptions()
	{
		$defOptions = $this->defaultOptions;

		foreach ($this->options as $optK => $optV) {
			$defOptions[$optK] = $optV;
		}

		return $defOptions;
	}
}