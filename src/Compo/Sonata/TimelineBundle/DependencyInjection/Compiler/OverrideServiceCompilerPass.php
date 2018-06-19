<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\TimelineBundle\DependencyInjection\Compiler;

use Compo\Sonata\TimelineBundle\Twig\Extension\SonataTimelineExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * OverrideServiceCompilerPass.
 *
 * SonataTimelineExtension - перехват исключения при генерации ссылки, когда объект удалён окончательно
 */
class OverrideServiceCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('sonata.timeline.twig.extension');
        $definition->setClass(SonataTimelineExtension::class);
    }
}
