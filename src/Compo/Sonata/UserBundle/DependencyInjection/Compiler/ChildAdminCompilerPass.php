<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\UserBundle\DependencyInjection\Compiler;

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
        $definitionGroup = $container->getDefinition('sonata.user.admin.group');
        $definitionGroup->addMethodCall('addChild', [new Reference('sonata.user.admin.user')]);
        $definitionGroup->addMethodCall('addChildAdminMenuItem', ['sonata.user.admin.user']);

        $definitionUser = $container->getDefinition('sonata.user.admin.user');
        $definitionUser->addMethodCall('setParentParentAssociationMapping', ['groups']);
    }
}
