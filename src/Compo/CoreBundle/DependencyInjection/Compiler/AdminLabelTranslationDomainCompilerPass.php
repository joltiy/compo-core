<?php

namespace Compo\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * {@inheritdoc}
 */
class AdminLabelTranslationDomainCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('sonata.admin');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);

            $arguments = $definition->getArguments();

            if (isset($arguments[2])) {
                $baseControllerNameArray = explode(':', $arguments[2]);

                if (count($baseControllerNameArray) > 1) {
                    $definition->addMethodCall('setTranslationDomain', [$baseControllerNameArray[0]]);
                }
            }
        }
    }
}
