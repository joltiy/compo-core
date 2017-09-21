<?php

namespace Compo\CoreBundle\DependencyInjection\Compiler;

use Compo\Sonata\AdminBundle\Model\AuditReader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * {@inheritDoc}
 */
class FallbackTranslatorCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('translator.default');
        $definition->setClass(\Compo\CoreBundle\Translation\FallbackTranslator::class);

        $definition = $container->getDefinition('sonata.admin.audit.orm.reader');
        $definition->setClass(AuditReader::class);
        $definition->addMethodCall('setContainer', array(new Reference('service_container')));
    }
}
