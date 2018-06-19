<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ImportBundle\Loaders;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Interface FileLoaderInterface.
 */
interface FileLoaderInterface
{
    /**
     * @param File $file
     *
     * @return FileLoaderInterface
     */
    public function setFile(File $file): self;

    public function getIteration();

    /**
     * @param ContainerInterface|null $container
     *
     * @return mixed
     */
    public function setContainer(ContainerInterface $container = null);

    public function getContainer();
}
