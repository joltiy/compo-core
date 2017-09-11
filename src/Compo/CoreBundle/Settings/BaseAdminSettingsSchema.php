<?php

namespace Compo\CoreBundle\Settings;

use Sonata\BlockBundle\Util\OptionsResolver;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritDoc}
 */
class BaseAdminSettingsSchema implements SchemaInterface
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
     * @param SettingsBuilderInterface $builder
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
    }

    /**
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {


    }

    /**
     * @param OptionsResolver $resolver
     * @throws \Exception
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'action' => $this->getContainer()->get('router')->generate($this->getBaseRouteName() . '_update') . '?',
            'label_format' => 'form.label_settings_%name%',
            'translation_domain' => $this->getTranslationDomain(),
        ));
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getDefaultOptions()
    {
        return array(
            'action' => $this->getContainer()->get('router')->generate($this->getBaseRouteName() . '_update') . '?',
            'label_format' => 'form.label_settings_%name%',
            'translation_domain' => $this->getTranslationDomain(),
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