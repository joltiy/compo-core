<?php

namespace Compo\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * {@inheritDoc}
 */
class AdminLabelTranslationDomainCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('sonata.admin');

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);

            $arguments = $definition->getArguments();

            if (isset($arguments[2])) {
                $baseControllerNameArray = explode(':', $arguments[2]);

                $definition->addMethodCall('setTranslationDomain', array($baseControllerNameArray[0]));
            }
        }
    }
}
