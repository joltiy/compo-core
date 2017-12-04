<?php

namespace Compo\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * {@inheritdoc}
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
