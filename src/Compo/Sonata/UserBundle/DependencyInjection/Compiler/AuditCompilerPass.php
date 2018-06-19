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

/**
 * Class AuditCompilerPass.
 */
class AuditCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->enableAudit($container, 'sonata.user.admin.user');
        $this->enableAudit($container, 'sonata.user.admin.group');
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $service
     */
    public function enableAudit($container, $service)
    {
        $definition = $container->getDefinition($service);
        $tags = $definition->getTags();

        $tags['sonata.admin'][0]['trash'] = true;
        $tags['sonata.admin'][0]['audit'] = true;

        $definition->setTags($tags);
    }
}
