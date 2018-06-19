<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\DashboardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class OverrideAdminClassCompilerPass.
 */
class OverrideAdminClassCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definitionBlock = $container->getDefinition('sonata.dashboard.admin.block');
        $definitionBlock->setClass(\Compo\Sonata\DashboardBundle\Admin\BlockAdmin::class);

        $definitionDashboard = $container->getDefinition('sonata.dashboard.admin.dashboard');
        $definitionDashboard->setClass(\Compo\Sonata\DashboardBundle\Admin\DashboardAdmin::class);

        $this->enableAudit($container, 'sonata.dashboard.admin.dashboard');
        $this->enableAudit($container, 'sonata.dashboard.admin.block');

        $this->setLabelTranslatorStrategy($container, 'sonata.dashboard.admin.dashboard');
        $this->setLabelTranslatorStrategy($container, 'sonata.dashboard.admin.block');
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

    /**
     * @param ContainerBuilder $container
     * @param string           $service
     */
    public function setLabelTranslatorStrategy($container, $service)
    {
        $definition = $container->getDefinition($service);
        $tags = $definition->getTags();

        $tags['sonata.admin'][0]['label_translator_strategy'] = 'sonata.admin.label.strategy.underscore';

        $definition->setTags($tags);
    }
}
