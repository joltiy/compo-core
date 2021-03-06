<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Form\Factory;

use Sylius\Bundle\SettingsBundle\Form\Factory\SettingsFormFactoryInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SettingsFormFactory implements SettingsFormFactoryInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $schemaRegistry;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param ServiceRegistryInterface $schemaRegistry
     * @param FormFactoryInterface     $formFactory
     */
    public function __construct(ServiceRegistryInterface $schemaRegistry, FormFactoryInterface $formFactory)
    {
        $this->schemaRegistry = $schemaRegistry;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($schemaAlias, $data = null, array $options = [])
    {
        /** @var SchemaInterface $schema */
        $schema = $this->schemaRegistry->get($schemaAlias);

        $builder = $this->formFactory->createBuilder(
            'form',
            $data,
            array_merge_recursive(
                ['data_class' => null],
                $options,
                $schema->getDefaultOptions()
            )
        );

        $schema->buildForm($builder);

        return $builder->getForm();
    }
}
