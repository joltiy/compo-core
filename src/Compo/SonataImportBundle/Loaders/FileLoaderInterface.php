<?php

namespace Compo\SonataImportBundle\Loaders;

use Symfony\Component\HttpFoundation\File\File;

interface FileLoaderInterface
{
    public function setFile(File $file): self;

    public function getIteration();
}
