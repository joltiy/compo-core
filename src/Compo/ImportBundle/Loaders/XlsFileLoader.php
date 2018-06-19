<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Loaders;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * Class XlsFileLoader.
 */
class XlsFileLoader implements FileLoaderInterface
{
    use ContainerAwareTrait;

    /** @var File $file */
    protected $file;

    /**
     * @param File $file
     *
     * @return FileLoaderInterface
     */
    public function setFile(File $file): FileLoaderInterface
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        if (!$this->file) {
            throw new InvalidArgumentException('File not found');
        }

        $objPHPExcel = $this->getContainer()->get('phpoffice.spreadsheet')->createSpreadsheet($this->file->getRealPath());

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
