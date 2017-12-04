<?php

namespace Compo\SeoBundle;

use Compo\SeoBundle\Compiler\SeoServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * {@inheritdoc}
 */
class CompoSeoBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SeoServicePass());
    }
}
