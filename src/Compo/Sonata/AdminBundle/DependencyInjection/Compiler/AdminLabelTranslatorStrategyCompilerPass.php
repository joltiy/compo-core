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
 * Замена label_translator_strategy.
 */
class AdminLabelTranslatorStrategyCompilerPass implements CompilerPassInterface
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

            if (isset($tag[0]) && !isset($tag[0]['label_translator_strategy'])) {
                $tag[0]['label_translator_strategy'] = 'sonata.admin.label.strategy.underscore';
                $tags['sonata.admin'] = $tag;
            }

            $definition->setTags($tags);
        }
    }
}
