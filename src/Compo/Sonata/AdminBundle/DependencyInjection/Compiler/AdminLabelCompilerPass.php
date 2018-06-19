<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Добавление label для админки, если отсутствует
 */
class AdminLabelCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('sonata.admin');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);
            $tags = $definition->getTags();

            $tag = $definition->getTag('sonata.admin');

            if (isset($tag[0]) && !isset($tag[0]['label'])) {
                $tag[0]['label'] = $id;
                $tags['sonata.admin'] = $tag;
            }

            $definition->setTags($tags);
        }
    }
}
