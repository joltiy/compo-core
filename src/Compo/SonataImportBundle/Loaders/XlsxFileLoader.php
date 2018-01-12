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

        foreach ($sheet->getRowIterator() as $row) {
            $row_item = [];

            foreach ($row->getCellIterator() as $cell) {
                $row_item[] = $cell->getValue();
            }

            $rows[] = $row_item;
        }

        return $rows;
    }

    public function getIteration()
    {
    }
}
