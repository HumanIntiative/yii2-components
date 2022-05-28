<?php

namespace pkpudev\components\excel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yii;
use yii\base\Component;

class ArrayDataToExcel extends Component
{
    public $headers = [];
    public $data = [];
    public $options = [];

    /**
     * @var Worksheet $worksheet
     */
    protected $worksheet;
    /**
     * @var Spreadsheet $spreadsheet
     */
    protected $spreadsheet;

    public function init()
    {
        // ini_set('memory_limit', '512M');
        parent::init();

        // Get Options
        $options = $this->mergeOptions($this->options);

        // Create Instance
        $this->spreadsheet = new Spreadsheet();

        // Set properties
        $this->spreadsheet->getProperties()
            ->setCreator($options['creator'])
            ->setLastModifiedBy($options['lastModifiedBy'])
            ->setTitle($options['title'])
            ->setSubject($options['subject'])
            ->setDescription($options['desc'])
            ->setKeywords($options['keywords'])
            ->setCategory($options['category']);

        // Select Worksheet
        $this->worksheet = $this->spreadsheet->getActiveSheet();
        $this->worksheet->setTitle($options['first_sheet_name']);

        // Page Setup
        $this->worksheet->getPageSetup()
            ->setOrientation($options['page_orientation'])
            ->setPaperSize($options['page_size'])
            ->setRowsToRepeatAtTopByStartAndEnd(1, 2);

        // Page Margins
        $this->worksheet->getPageMargins()
            ->setRight($options['margin_right'])
            ->setLeft($options['margin_left']);

        // Set Footer
        $this->worksheet->getHeaderFooter()
            ->setOddHeader("&L&G&C&H" . $options['company'])
            ->setOddFooter("&L&B" . $options['title'] . "&RPage &P of &N");

        // Range Style
        $rangeStyle = $this->worksheet->getStyle($options['style_fillRange']);
        $rangeStyle->applyFromArray($options['style_fontBold']);
        $rangeStyle->getFill()->setFillType($options['header_fillType']);
        $rangeStyle->getFill()->getStartColor()->setRGB($options['header_fillColor']);
        unset($rangeStyle);

        // Header Width
        $this->worksheet->getColumnDimension($options['first_col'])->setWidth(4);
        foreach ($this->headers as $header) {
            $this->worksheet->getColumnDimension($header['column'])->setWidth($header['width']);
        }

        // Header Title
        $firstRow = (int) $options['first_row'];
        $firstCell = $options['first_col'] . $firstRow;
        $this->worksheet->setCellValue($firstCell, $options['first_cell_label']);
        foreach ($this->headers as $header) {
            $this->worksheet->setCellValue($header['column'] . $firstRow, $header['title']);
        }

        // Freeze panes
        $this->worksheet->freezePane($options['cell_freezePane']);

        // Other Variables
        $a = $firstRow + 1;
        $no = 1;
        $this->worksheet->getRowDimension($a)->setRowHeight($options['data_rowHeight']);
        $range = sprintf('%s%s:%s%s', $options['first_col'], $a, $options['column_lastChar'], $a);
        $rangeStyle = $this->worksheet->getStyle($range);
        $rangeStyle->getAlignment()->setWrapText(true);
        $rangeStyle->getAlignment()->setVertical($options['data_verticalAlignment']);
        $rangeStyle->applyFromArray($options['style_borderBottom']);

        // Fill Data
        foreach ($this->data as $row) {
            $this->worksheet->setCellValue($options['first_col'] . $a, $no);
            foreach ($this->headers as $index => $header) {
                $fieldname = $header['fieldname'];
                $this->worksheet->setCellValue($header['column'] . $a, $row[$fieldname]);
            }
            $a++;
            $no++;
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->spreadsheet->setActiveSheetIndex(0);
    }

    public function create($saveAsFile = true, $filename = null)
    {
        $dateFormatOption = $this->options['date_format'] ?? $this->defaultOptions['date_format'];
        $filenamePrependOption = $this->options['filename_prepend'] ?? $this->defaultOptions['filename_prepend'];
        $writerOption = $this->options['writer'] ?? $this->defaultOptions['writer'];

        $today = date($dateFormatOption);

        if (is_null($filename)) {
            $filename = "{$filenamePrependOption} {$today}.xls";
        }

        if ($saveAsFile) {
            $output = $filename;
        } else {
            $response = Yii::$app->response;
            $headers = $response->headers;
            // Redirect output to a client's web browser (Excel5)
            $headers->set('Content-Type', 'application/vnd.ms-excel');
            $headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
            $headers->set('Cache-Control', 'max-age=0');
            // If you're serving to IE 9, then the following may be needed
            // header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            // header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            // header('Pragma: public'); // HTTP/1.0
            $output = 'php://output';
        }

        // Save Output
        $writer = $this->createWriter($this->spreadsheet, $writerOption);
        ob_start();
        $writer->save($output);
        $content = ob_get_clean();
        return $content;
    }

    public function getSpreadsheet()
    {
        return $this->spreadsheet;
    }

    public function getWorksheet()
    {
        return $this->worksheet;
    }

    //
    //

    protected function getDefaultOptions()
    {
        return [
            // General Options
            'company' => 'PKPU - Lembaga Kemanusiaan Nasional.',
            'creator' => 'IT Dev',
            'title' => 'Download - Mulia Project v2',
            'subject' => null,
            'desc' => null,
            'keywords' => null,
            'category' => 'Download',
            'lastModifiedBy' => 'IT Dev Team',
            'filename_prepend' => 'Export Ipp',
            'date_format' => 'Y-m-d_his',
            // Worksheet Options
            'first_sheet_name' => 'Sheet1',
            // Cell Options
            'first_col' => 'A',
            'first_row' => 1,
            'first_cell' => 'A1',
            'first_cell_label' => 'No',
            // Component Options
            'style_fontBold' => ['font' => ['bold' => true]],
            'style_borderBottom' => [
                'borders' => [
                    'bottom' => ['style' => Border::BORDER_THIN, 'color' => ['argb' => 'F555753']],
                ],
            ],
            'style_fillRange' => 'A1:BP1',
            'column_lastChar' => 'BP',
            'page_orientation' => PageSetup::ORIENTATION_LANDSCAPE,
            'page_size' => PageSetup::PAPERSIZE_A4,
            'margin_left' => 0.3,
            'margin_right' => 0,
            'header_fillType' => Fill::FILL_SOLID,
            'header_fillColor' => 'EAEAEA',
            'cell_freezePane' => 'A2',
            'data_rowHeight' => 20,
            'data_verticalAlignment' => Alignment::VERTICAL_CENTER,
            // Writer Options
            'writer' => 'Xls',
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

    protected function validateWriter($item)
    {
        $validWriters = ['Csv', 'Html', 'Ods', 'Pdf', 'Xls', 'Xlsx'];
        return in_array($item, $validWriters);
    }

    protected function createWriter($spreadsheet, $item = 'Xls')
    {
        if (!$this->validateWriter($item)) {
            throw new \yii\base\InvalidConfigException("Writer type '{$item}' not found!", 400);
        }

        return IOFactory::createWriter($spreadsheet, $item);
    }
}
