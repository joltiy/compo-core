<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Exporter\Writer;

use Exporter\Writer\TypedWriterInterface;

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
    protected $filename = null;

    /**
     * @var resource|null
     */
    protected $file = null;

    /**
     * @var bool
     */
    protected $showHeaders;

    /**
     * @var mixed|null
     */
    protected $columnsType = null;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var string
     */
    protected $header = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet"><OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office"></OfficeDocumentSettings><ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"></ExcelWorkbook><Worksheet ss:Name="Sheet 1"><Table>';
    protected $footer = '</Table></Worksheet></Workbook>';




    public const LABEL_COLUMN = 1;
    /** @var  \PHPExcel */
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
     * Create PHPExcel object and set defaults
     */
    public function open()
    {
        $this->phpExcelObject = new \PHPExcel();
    }


    /**
     * {@inheritdoc}
     */
    public function write(array $data)
    {
        $this->init($data);
        foreach ($data as $header => $value) {
            $this->setCellValue($this->getColumn($header), $value);
        }
        ++$this->position;
    }
    /**
     *  Set labels
     * @param $data
     *
     * @return void
     */
    protected function init($data)
    {
        if ($this->position > 1) {
            return;
        }
        $i = 0;
        foreach ($data as $header => $value) {
            $column = self::formatColumnName($i);
            $this->setHeader($column, $header);
            $i++;
        }
        $this->setBoldLabels();
    }
    /**
     * Save Excel file
     */
    public function close()
    {
        $writer = \PHPExcel_IOFactory::createWriter($this->phpExcelObject, 'Excel2007');
        $writer->save($this->filename);
    }
    /**
     * Returns letter for number based on Excel columns
     * @param int $number
     * @return string
     */
    public static function formatColumnName($number)
    {
        for ($char = ""; $number >= 0; $number = intval($number / 26) - 1) {
            $char = chr($number%26 + 0x41) . $char;
        }
        return $char;
    }
    /**
     * @return \PHPExcel_Worksheet
     */
    private function getActiveSheet()
    {
        return $this->phpExcelObject->getActiveSheet();
    }
    /**
     * Makes header bold
     */
    private function setBoldLabels()
    {
        $this->getActiveSheet()->getStyle(
            sprintf(
                "%s1:%s1",
                reset($this->headerColumns),
                end($this->headerColumns)
            )
        )->getFont()->setBold(true);
    }
    /**
     * Sets cell value
     * @param string $column
     * @param string $value
     */
    private function setCellValue($column, $value)
    {
        $this->getActiveSheet()->setCellValue($column, $value);
    }
    /**
     * Set column label and make column auto size
     * @param string $column
     * @param string $value
     */
    private function setHeader($column, $value)
    {
        $this->setCellValue($column.self::LABEL_COLUMN, $value);
        $this->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
        $this->headerColumns[$value] = $column;
    }
    /**
     * Get column name
     * @param string $name
     * @return string
     */
    private function getColumn($name)
    {
        return $this->headerColumns[$name].$this->position;
    }



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
