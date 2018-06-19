<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\PageBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AuditCompilerPass.
 */
class ChildAdminCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definitionSite = $container->getDefinition('sonata.page.admin.site');
        $definitionSite->addMethodCall('addChild', [new Reference('sonata.page.admin.page')]);
        $definitionSite->addMethodCall('addChildAdminMenuItem', ['sonata.page.admin.page']);

        $definitionPage = $container->getDefinition('sonata.page.admin.page');
        $definitionPage->addMethodCall('setParentParentAssociationMapping', ['site']);
    }
}
