<?php

namespace Compo\SeoBundle\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SeoServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sonata.seo.page')) {
            return;
        }

        $definition = $container->findDefinition('sonata.seo.page');

        $taggedServices = $container->findTaggedServiceIds('compo.seo_service');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addService', array(
                    new Reference($id),
                    $attributes["alias"],
                    $attributes["context"]
                ));
            }
        }
    }
}