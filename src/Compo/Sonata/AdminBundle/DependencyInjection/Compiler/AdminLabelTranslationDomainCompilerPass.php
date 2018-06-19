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
 * Замена TranslationDomain для админок.
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

                if (\count($baseControllerNameArray) > 1) {
                    $definition->addMethodCall('setTranslationDomain', [$baseControllerNameArray[0]]);
                }
            }
        }
    }
}
