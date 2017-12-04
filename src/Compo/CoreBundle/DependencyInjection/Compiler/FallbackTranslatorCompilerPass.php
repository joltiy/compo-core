<?php

namespace Compo\CoreBundle\DependencyInjection\Compiler;

use Compo\Sonata\AdminBundle\Admin\BreadcrumbsBuilder;
use Compo\Sonata\AdminBundle\Model\AuditReader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * {@inheritdoc}
 */
class FallbackTranslatorCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('translator.default');
        $definition->setClass(\Compo\CoreBundle\Translation\FallbackTranslator::class);

        $definition = $container->getDefinition('sonata.admin.audit.orm.reader');
        $definition->setClass(AuditReader::class);
        $definition->addMethodCall('setContainer', array(new Reference('service_container')));

        $definition = $container->getDefinition('sonata.admin.breadcrumbs_builder');
        $definition->setClass(BreadcrumbsBuilder::class);

        $definition = $container->getDefinition('sonata.page.service.default');
        $definition->addMethodCall('setContainer', array(new Reference('service_container')));
    }
}
