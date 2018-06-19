<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\DependencyInjection\Compiler;

use Compo\Sonata\AdminBundle\Admin\BreadcrumbsBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class BreadcrumbsBuilder.
 */
class BreadcrumbsBuilderCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('sonata.admin.breadcrumbs_builder');
        $definition->setClass(BreadcrumbsBuilder::class);
    }
}
