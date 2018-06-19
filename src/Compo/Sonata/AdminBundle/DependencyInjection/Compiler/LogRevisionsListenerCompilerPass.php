<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\DependencyInjection\Compiler;

use Compo\Sonata\AdminBundle\Listener\LogRevisionsListenerExtend;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AuditOrmReaderCompilerPass.
 */
class LogRevisionsListenerCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $audited_entities_ignore_columns = $container->getParameter('audited_entities_ignore_columns');

        $definition = $container->getDefinition('simplethings_entityaudit.log_revisions_listener');

        $definition->setClass(LogRevisionsListenerExtend::class);

        $definition->addMethodCall('setAuditedEntitiesIgnoreColumns', [$audited_entities_ignore_columns]);
    }
}
