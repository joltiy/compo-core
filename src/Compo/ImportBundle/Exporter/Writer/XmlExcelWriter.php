<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Exporter\Writer;

use Exporter\Writer\TypedWriterInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Generate a Xml Excel file.
 *
 * @author Vincent Touzet <vincent.touzet@gmail.com>
 */
class XmlExcelWriter implements TypedWriterInterface
{
    /**
     * @var string|null
     */
    protected $filename;

    /**
     * @var resource|null
     */
    protected $file;

    /**
     * @var bool
     */
    protected $showHeaders;

    /**
     * @var mixed|null
     */
    protected $columnsType;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var string
     */
    protected $header = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"><OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"></OfficeDocumentSettings><ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"></ExcelWorkbook><Worksheet ss:Name="Sheet 1"><Table>';
    /**
     * @var string
     */
    protected $footer = '</Table></Worksheet></Workbook>';

    /**
     * LABEL_COLUMN.
     */
    public const LABEL_COLUMN = 1;

    /** @var Spreadsheet */
    private $phpExcelObject;

    /** @var array */
    private $headerColumns = [];

    /**
     * @param string $filename
     * @param bool   $showHeaders
     * @param mixed  $columnsType Define cells type to use
     *                            If string: force all cells to the given type. e.g: 'Number'
     *                            If array: force only given cells. e.g: array('ean'=>'String', 'price'=>'Number')
     *                            If null: will guess the type. 'Number' if value is numeric, 'String' otherwise
     */
    public function __construct($filename, $showHeaders = true, $columnsType = null)
    {
        $this->filename = $filename;
        $this->showHeaders = $showHeaders;
        $this->columnsType = $columnsType;

        if (is_file($filename)) {
            throw new \RuntimeException(sprintf('The file %s already exist', $filename));
        }
    }

    /**
     * Create PHPExcel object and set defaults.
     */
    public function open()
    {
        $this->phpExcelObject = new Spreadsheet();
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $data)
    {
        //dump($this->position);

        if (0 === $this->position && $this->showHeaders) {
            $this->init($data);

            $this->position = 2;
        }

        foreach ($data as $header => $value) {
            $this->setCellValue($this->getColumn($header), $value);
        }

        ++$this->position;
    }

    /**
     *  Set labels.
     *
     * @param array $data
     */
    protected function init($data)
    {
        $i = 0;

        foreach ($data as $header => $value) {
            $column = self::formatColumnName($i);
            $this->setHeader($column, $header);
            ++$i;
        }
        $this->setBoldLabels();
    }

    /**
     * Save Excel file.
     */
    public function close()
    {
        $writer = IOFactory::createWriter($this->phpExcelObject, 'Xlsx');
        //\PHPExcel_IOFactory::createWriter($this->phpExcelObject, 'Excel2007');
        $writer->save($this->filename);
    }

    /**
     * Returns letter for number based on Excel columns.
     *
     * @param int $number
     *
     * @return string
     */
    public static function formatColumnName($number)
    {
        for ($char = ''; $number >= 0; $number = (int) ($number / 26) - 1) {
            $char = \chr($number % 26 + 0x41) . $char;
        }

        return $char;
    }

    /**
     * @return Worksheet
     */
    private function getActiveSheet()
    {
        return $this->phpExcelObject->getActiveSheet();
    }

    /**
     * Makes header bold.
     */
    private function setBoldLabels()
    {
        $this->getActiveSheet()->getStyle(
            sprintf(
                '%s1:%s1',
                reset($this->headerColumns),
                end($this->headerColumns)
            )
        )->getFont()->setBold(true);
    }

    /**
     * Sets cell value.
     *
     * @param string      $column
     * @param string|bool $value
     */
    private function setCellValue($column, $value)
    {
        $cellValue = $value;

        if (\is_bool($value)) {
            if ($value) {
                $cellValue = 1;
            } else {
                $cellValue = 0;
            }
        }

        $this->getActiveSheet()->setCellValue($column, $cellValue);
    }

    /**
     * Set column label and make column auto size.
     *
     * @param string $column
     * @param string $value
     */
    private function setHeader($column, $value)
    {
        $this->setCellValue($column . self::LABEL_COLUMN, $value);
        $this->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        $this->headerColumns[$value] = $column;
    }

    /**
     * Get column name.
     *
     * @param string $name
     *
     * @return string
     */
    private function getColumn($name)
    {
        return $this->headerColumns[$name] . $this->position;
    }

    /**
     * @return string
     */
    public function getDefaultMimeType()
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'xlsx';
    }
}
