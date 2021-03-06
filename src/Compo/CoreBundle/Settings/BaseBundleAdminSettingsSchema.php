<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Settings;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Mopa\Bundle\BootstrapBundle\Form\Type\TabType;
use Sonata\AdminBundle\Form\FormMapper;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritdoc}
 */
class BaseBundleAdminSettingsSchema implements SchemaInterface
{
    /**
     * @var Container
     */
    public $container;

    /**
     * @var SettingsBuilderInterface
     */
    public $settingsBuilder;

    /**
     * @var string
     */
    public $translationDomain = 'messages';

    /**
     * @var AbstractAdmin
     */
    public $mediaAdmin;

    /**
     * @var string
     */
    protected $baseRouteName = 'compo_core_settings';

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @return SettingsBuilderInterface
     */
    public function getSettingsBuilder(): SettingsBuilderInterface
    {
        return $this->settingsBuilder;
    }

    /**
     * @param SettingsBuilderInterface $settingsBuilder
     */
    public function setSettingsBuilder(SettingsBuilderInterface $settingsBuilder)
    {
        $this->settingsBuilder = $settingsBuilder;
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getMediaBuilder($formMapper)
    {
        $admin_pool = $this->getContainer()->get('sonata.admin.pool');

        $admin = $admin_pool->getAdminByAdminCode('compo_core.admin.settings');
        // simulate an association ...
        $fieldDescription = $this->getMediaAdmin()->getModelManager()->getNewFieldDescriptionInstance(
            $this->mediaAdmin->getClass(),
            'media',
            [
                'translation_domain' => 'SonataMediaBundle',
            ]
        );
        $fieldDescription->setAssociationAdmin($this->getMediaAdmin());
        $fieldDescription->setAdmin($admin);
        $fieldDescription->setOption('edit', 'list');
        $fieldDescription->setAssociationMapping(
            [
                'fieldName' => 'media',
                'type' => ClassMetadataInfo::MANY_TO_ONE,
            ]
        );

        return $formMapper->add(
            'mediaId',
            'sonata_type_model_list',
            [
                'sonata_field_description' => $fieldDescription,
                'class' => $this->getMediaAdmin()->getClass(),
                'model_manager' => $this->getMediaAdmin()->getModelManager(),
                'label' => 'form.label_media',
                'required' => false,
            ]
        );
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @throws \Exception
     *
     * @return \Compo\Sonata\MediaBundle\Admin\MediaAdmin
     */
    public function getMediaAdmin()
    {
        if (!$this->mediaAdmin) {
            $this->mediaAdmin = $this->container->get('sonata.media.admin.media');
        }

        return $this->mediaAdmin;
    }

    /**
     * @throws \Exception
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $this->setFormBuilder($builder);

        $this->buildFormSettings();
    }

    /**
     * @return array
     */
    public function getDefaultSettings()
    {
        return [];
    }

    /**
     * Создание формы настроек.
     */
    public function buildFormSettings()
    {
    }

    /**
     * Добавление вкладки.
     *
     * @param $name
     *
     * @return FormBuilderInterface
     */
    public function addTab($name)
    {
        $tab = $this->createTab($name);

        $this->getFormBuilder()->add($tab);

        return $tab;
    }

    /**
     * Создание вкладки.
     *
     * @param $name
     *
     * @return FormBuilderInterface
     */
    public function createTab($name)
    {
        return $this->getFormBuilder()->create(
            'tab_label_settings_' . $name,
            TabType::class,
            [
                'label' => 'tab.label_settings_' . $name,
                'inherit_data' => true,
            ]
        );
    }

    /**
     * @return FormBuilderInterface
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     */
    public function setFormBuilder($formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $this->setSettingsBuilder($builder);

        $items = $this->getDefaultSettings();

        $this->getSettingsBuilder()->setDefaults($items);

        foreach ($items as $item_name => $types) {
            $this->getSettingsBuilder()->addAllowedTypes($item_name, ['null', 'boolean', 'integer', 'object', 'string', 'array']);
        }
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'action' => $this->getContainer()->get('router')->generate($this->getBaseRouteName() . '_settings') . '?',
            'label_format' => 'form.label_settings_%name%',
            'translation_domain' => $this->getTranslationDomain(),
        ];
    }

    /**
     * @return string
     */
    public function getBaseRouteName()
    {
        return $this->baseRouteName;
    }

    /**
     * @param string $baseRouteName
     */
    public function setBaseRouteName($baseRouteName)
    {
        $this->baseRouteName = $baseRouteName;
    }

    /**
     * @return string
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
    }
}
