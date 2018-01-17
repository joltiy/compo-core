<?php

namespace Compo\SonataImportBundle\Loaders;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class XlsxFileLoader implements FileLoaderInterface
{
    /** @var File $file */
    protected $file = null;

    public function setFile(File $file): FileLoaderInterface
    {
        $this->file = $file;

        return $this;
    }

    public function getRows()
    {
        if (!$this->file) {
            throw new InvalidArgumentException('File not found');
        }

        $reader = \PHPExcel_IOFactory::createReader('Excel2007');

        $objPHPExcel = $reader->load($this->file->getRealPath());

        $sheet = $objPHPExcel->getActiveSheet();

        $rows = [];

        $headers = array();

        $i = 0;

        foreach ($sheet->getRowIterator() as $row) {
            $row_item = [];

            $col_i = 1;

            foreach ($row->getCellIterator() as $cell) {
                if ($i > 0 && $col_i > count($headers)) {
                    break;
                }

                if ($i === 0) {

                    if (is_null($cell->getValue())) {
                        break;
                    }

                    $headers[] = $cell->getValue();
                }

                $row_item[] = $cell->getValue();

                $col_i++;
            }

            $rows[] = $row_item;

            $i++;
        }

        return $rows;
    }

    public function getIteration()
    {
        if (!$this->file) {
            throw new InvalidArgumentException('File not found');
        }

        $reader = \PHPExcel_IOFactory::createReader('Excel2007');

        $objPHPExcel = $reader->load($this->file->getRealPath());

        $sheet = $objPHPExcel->getActiveSheet();

        $headers = [];

        foreach ($sheet->getRowIterator(1,1) as $row) {

            /** @var \PHPExcel_Cell $cell */
            foreach ($row->getCellIterator() as $cell) {
                if (null === $cell->getValue()) {
                    break;
                }

                $headers[] = $cell->getValue();
            }
        }

        foreach ($sheet->getRowIterator() as $row) {
            $row_item = [];

            $col_i = 1;

            foreach ($row->getCellIterator() as $cell) {
                if ($col_i > count($headers)) {
                    break;
                }

                $row_item[] = $cell->getValue();

                $col_i++;
            }

            yield $row_item;
        }
    }
}
