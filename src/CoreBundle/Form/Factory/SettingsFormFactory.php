<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 14.11.16
 * Time: 6:58
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
     * @param FormFactoryInterface $formFactory
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


        /** @noinspection PhpUndefinedMethodInspection */
        $builder = $this->formFactory->createBuilder('form', $data, array_merge_recursive(
            ['data_class' => null], $options, $schema->getDefaultOptions()
        ));

        $schema->buildForm($builder);

        return $builder->getForm();
    }
}
