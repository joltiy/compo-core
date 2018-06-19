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

/**
 * Изменение прироритета для sonata.page.kernel.exception_listener.
 */
class ExceptionListenerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('sonata.page.kernel.exception_listener');
        $definition->setClass(\Compo\Sonata\PageBundle\Listener\ExceptionListener::class);

        $tags = $definition->getTags();

        $tag = $definition->getTag('kernel.event_listener');
        $tag[0]['priority'] = -50;

        $tags['kernel.event_listener'] = $tag;

        $definition->setTags($tags);
    }
}
