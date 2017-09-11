<?php

namespace Compo\CoreBundle\Settings;

use Mopa\Bundle\BootstrapBundle\Form\Type\TabType;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * {@inheritDoc}
 */
class BaseBundleAdminSettingsSchema implements SchemaInterface
{
    /**
     * @var Container
     */
    public $container;

    /**
     * @var string
     */
    public $translationDomain = 'messages';

    /**
     * @var string
     */
    protected $baseRouteName = 'compo_core_settings';

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var
     */
    public $mediaAdmin;

    /**
     * @return \Compo\Sonata\MediaBundle\Admin\MediaAdmin|object
     * @throws \Exception
     */
    public function getMediaAdmin()
    {
        if (!$this->mediaAdmin) {
            $this->mediaAdmin = $this->container->get('sonata.media.admin.media');
        }
        return $this->mediaAdmin;
    }

    /**
     * @param $formMapper
     * @return mixed
     * @throws \Exception
     */
    public function getMediaBuilder($formMapper)
    {
        $admin_pool = $this->getContainer()->get('sonata.admin.pool');

        $admin = $admin_pool->getAdminByAdminCode('compo_core.admin.settings');
        // simulate an association ...
        $fieldDescription = $this->getMediaAdmin()->getModelManager()->getNewFieldDescriptionInstance($this->mediaAdmin->getClass(), 'media', array(
            'translation_domain' => 'SonataMediaBundle',
        ));
        $fieldDescription->setAssociationAdmin($this->getMediaAdmin());
        $fieldDescription->setAdmin($admin);
        $fieldDescription->setOption('edit', 'list');
        $fieldDescription->setAssociationMapping(array(
            'fieldName' => 'media',
            'type' => ClassMetadataInfo::MANY_TO_ONE,
        ));

        return $formMapper->add('mediaId', 'sonata_type_model_list', array(
            'sonata_field_description' => $fieldDescription,
            'class' => $this->getMediaAdmin()->getClass(),
            'model_manager' => $this->getMediaAdmin()->getModelManager(),
            'label' => 'form.label_media',
            'required' => false,

        ));
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry|object
     * @throws \Exception
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
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
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $this->setFormBuilder($builder);

        $this->buildFormSettings();
    }

    /**
     * Создание формы настроек
     */
    public function buildFormSettings()
    {

    }

    /**
     * Добавление вкладки
     *
     * @param $name
     * @return FormBuilderInterface
     */
    public function addTab($name)
    {
        $tab = $this->createTab($name);

        $this->getFormBuilder()->add($tab);

        return $tab;
    }

    /**
     * Создание вкладки
     *
     * @param $name
     * @return FormBuilderInterface
     */
    public function createTab($name)
    {
        return $this->getFormBuilder()->create('tab_label_settings_' . $name, TabType::class, array(
            'label' => 'tab.label_settings_' . $name,
            'inherit_data' => true,
        ));
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

    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getDefaultOptions()
    {
        return array(
            'action' => $this->getContainer()->get('router')->generate($this->getBaseRouteName() . '_settings') . '?',
            'label_format' => 'form.label_settings_%name%',
            'translation_domain' => $this->getTranslationDomain(),
        );
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