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
 * Class XlsxFileLoader.
 */
class XlsxFileLoader implements FileLoaderInterface
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

        $headers = [];

        $i = 0;

        foreach ($sheet->getRowIterator() as $row) {
            $row_item = [];

            $col_i = 1;

            foreach ($row->getCellIterator() as $cell) {
                if ($i > 0 && $col_i > \count($headers)) {
                    break;
                }

                if (0 === $i) {
                    if (null === $cell->getValue()) {
                        break;
                    }

                    $headers[] = $cell->getValue();
                }

                $row_item[] = $cell->getValue();

                ++$col_i;
            }

            $rows[] = $row_item;

            ++$i;
        }

        return $rows;
    }

    /**
     * @return \Generator
     */
    public function getIteration()
    {
        if (!$this->file) {
            throw new InvalidArgumentException('File not found');
        }

        $objPHPExcel = $this->getContainer()->get('phpoffice.spreadsheet')->createSpreadsheet($this->file->getRealPath());

        $sheet = $objPHPExcel->getActiveSheet();

        $headers = [];

        foreach ($sheet->getRowIterator(1, 1) as $row) {
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
                if ($col_i > \count($headers)) {
                    break;
                }

                $row_item[] = $cell->getValue();

                ++$col_i;
            }

            yield $row_item;
        }
    }
}
