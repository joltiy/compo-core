<?php

namespace Compo\Sonata\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * OverrideServiceCompilerPass
 *
 * SonataTimelineExtension - перехват исключения при генерации ссылки, когда объект удалён окончательно
 *
 * @package Compo\Sonata\TimelineBundle\DependencyInjection\Compiler
 */
class OverrideServiceCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('sonata.timeline.twig.extension');
        $definition->setClass('Compo\Sonata\TimelineBundle\Twig\Extension\SonataTimelineExtension');
    }
}