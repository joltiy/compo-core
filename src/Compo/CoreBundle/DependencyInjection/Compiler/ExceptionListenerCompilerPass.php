<?php

namespace Compo\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * {@inheritdoc}
 */
class ExceptionListenerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // TODO: Вынести в отдельный CompilerPass
        $definition2 = $container->getDefinition('sonata.dashboard.admin.block');
        $definition2->setClass(\Compo\Sonata\DashboardBundle\Admin\BlockAdmin::class);

        $definition3 = $container->getDefinition('sonata.dashboard.admin.dashboard');
        $definition3->setClass(\Compo\Sonata\DashboardBundle\Admin\DashboardAdmin::class);

        $definition = $container->getDefinition('sonata.page.kernel.exception_listener');
        $definition->setClass(\Compo\Sonata\PageBundle\Listener\ExceptionListener::class);

        $tags = $definition->getTags();

        $tag = $definition->getTag('kernel.event_listener');
        $tag[0]['priority'] = -50;

        $tags['kernel.event_listener'] = $tag;

        $definition->setTags($tags);
    }
}
