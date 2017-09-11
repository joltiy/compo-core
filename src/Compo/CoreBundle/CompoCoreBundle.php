<?php

namespace Compo\CoreBundle;

use Compo\CoreBundle\DependencyInjection\Compiler\FallbackTranslator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class CompoCoreBundle
 *
 * @package Compo\CoreBundle
 */
class CompoCoreBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FallbackTranslator());
    }
}
