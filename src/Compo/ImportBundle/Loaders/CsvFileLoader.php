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
 * Class CsvFileLoader.
 */
class CsvFileLoader implements FileLoaderInterface
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
     * @return \Generator
     */
    public function getIteration()
    {
        if (!$this->file) {
            throw new InvalidArgumentException('File not found');
        }

        $file = fopen($this->file->getRealPath(), 'rb');
        while (false !== ($line = fgetcsv($file, 0, ';'))) {
            foreach ($line as $key => $col) {
                $col = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $col);
                $line[$key] = trim($col);
            }

            yield $line;
        }
    }
}
