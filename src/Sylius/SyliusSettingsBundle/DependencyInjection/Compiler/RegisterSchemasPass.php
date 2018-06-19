<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RegisterSchemasPass implements CompilerPassInterface
{
    const CLASS_REGEX =
        '@
        (?:([A-Za-z0-9]*)\\\)?        # vendor name / app name
        (Bundle\\\)?                  # optional bundle directory
        ([A-Za-z0-9]+?)(?:Bundle)?\\\ # bundle name, with optional suffix
        (
            Entity|Document|Model|PHPCR|CouchDocument|Phpcr|
            Doctrine\\\Orm|Doctrine\\\Phpcr|Doctrine\\\MongoDB|Doctrine\\\CouchDB
        )\\\(.*)@x';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.settings_schema')) {
            return;
        }

        $schemaRegistry = $container->getDefinition('sylius.registry.settings_schema');
        $taggedServicesIds = $container->findTaggedServiceIds('sylius.settings_schema');

        foreach ($taggedServicesIds as $id => $tags) {
            $schema = $container->getDefinition($id);

            foreach ($tags as $tagKey => $attributes) {
                if (!isset($attributes['alias'])) {
                    /*
                    throw new \InvalidArgumentException(
                        sprintf('Service "%s" must define the "alias" attribute on "sylius.settings_schema" tags.', $id)
                    );
                    */
                    $attributes['alias'] = $attributes['namespace'];
                }

                $tags[$tagKey] = $attributes;

                if (isset($attributes['admin'])) {
                    $admin = $container->getDefinition($attributes['admin']);
                    $arguments = $admin->getArguments();

                    if (isset($arguments[2])) {
                        if (0 === mb_strpos($arguments[1], '%')) {
                            $arguments[1] = $container->getParameter(str_replace('%', '', $arguments[1]));
                        }

                        if (0 === mb_strpos($arguments[2], '%')) {
                            $arguments[2] = $container->getParameter(str_replace('%', '', $arguments[2]));
                        }

                        $baseControllerNameArray = explode(':', $arguments[2]);

                        $schema->addMethodCall('setTranslationDomain', [$baseControllerNameArray[0]]);

                        preg_match(self::CLASS_REGEX, $arguments[1], $matches);

                        $schema->addMethodCall('setBaseRouteName', [sprintf('admin_%s%s_%s',
                            empty($matches[1]) ? '' : $this->urlize($matches[1]) . '_',
                            $this->urlize($matches[3]),
                            $this->urlize($matches[5])
                        )]);

                        $admin->addMethodCall('setSettingsEnabled', [true]);
                        $admin->addMethodCall('setSettingsNamespace', [$attributes['namespace']]);
                    }
                }

                $schemaRegistry->addMethodCall('register', [$attributes['alias'], new Reference($id)]);
            }

            $tags = [
                'sylius.settings_schema' => $tags,
            ];

            $schema->setTags($tags);
        }
    }

    /**
     * urlize the given word.
     *
     * @param string $word
     * @param string $sep  the separator
     *
     * @return string
     */
    public function urlize($word, $sep = '_')
    {
        return mb_strtolower(preg_replace('/[^a-z0-9_]/i', $sep . '$1', $word));
    }
}
