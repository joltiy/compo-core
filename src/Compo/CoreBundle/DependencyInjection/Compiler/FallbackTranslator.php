<?php

namespace Compo\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * {@inheritDoc}
 */
class FallbackTranslator implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('translator.default');
        $definition->setClass(\Compo\CoreBundle\Translation\FallbackTranslator::class);

        $definition = $container->getDefinition('sonata.page.kernel.exception_listener');
        $definition->setClass(\Compo\Sonata\PageBundle\Listener\ExceptionListener::class);

        $tags = $definition->getTags();

        $tag = $definition->getTag('kernel.event_listener');
        $tag[0]['priority'] = -50;

        $tags['kernel.event_listener'] = $tag;

        $definition->setTags($tags);
    }
}
