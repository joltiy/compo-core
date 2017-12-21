<?php

namespace Compo\SonataImportBundle\Loaders;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Exception\InvalidArgumentException;

class XlsFileLoader implements FileLoaderInterface{

    /** @var File $file  */
    protected $file = null;

    public function setFile(File $file) : FileLoaderInterface {
        $this->file = $file;
        return $this;
    }
    public function getRows() {
        if(!$this->file){
            throw new InvalidArgumentException('File not found');
        }


        $objPHPExcel = \PHPExcel_IOFactory::load($this->file->getRealPath());

        $sheet = $objPHPExcel->getActiveSheet();

        $rows = array();

        foreach ($sheet->getRowIterator() as $row) {
            $row_item = array();

            foreach ($row->getCellIterator() as $cell) {

                $row_item[] = $cell->getValue();
            }

            $rows[] = $row_item;
        }

        return $rows;
    }

    public function getIteration() {

    }

}
