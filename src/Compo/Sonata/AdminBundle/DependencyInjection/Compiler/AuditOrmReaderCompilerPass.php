<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\DependencyInjection\Compiler;

use Compo\Sonata\AdminBundle\Model\AuditReader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AuditOrmReaderCompilerPass.
 */
class AuditOrmReaderCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('sonata.admin.audit.orm.reader');
        $definition->setClass(AuditReader::class);
        $definition->addMethodCall('setContainer', [new Reference('service_container')]);
    }
}
