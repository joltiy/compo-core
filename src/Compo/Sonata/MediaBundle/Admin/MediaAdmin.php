<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 17.11.16
 * Time: 5:49.
 */

namespace Compo\Sonata\MediaBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Traits\ConfigureTabMenuTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;

/**
 * Class MediaAdmin.
 */
class MediaAdmin extends \Sonata\MediaBundle\Admin\ORM\MediaAdmin
{
    use ConfigureTabMenuTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $options = array(
            'choices' => array(),
        );

        foreach ($this->pool->getContexts() as $name => $context) {
            $options['choices'][$name] = $name;
        }

        $datagridMapper
            ->add('name')
            ->add('providerReference')
            ->add('enabled')
            ->add('width')
            ->add('height')
            ->add('contentType');

        $providers = array();

        $providerNames = (array) $this->pool->getProviderNamesByContext($this->getPersistentParameter('context', $this->pool->getDefaultContext()));
        foreach ($providerNames as $name) {
            $providers[$name] = $name;
        }

        $datagridMapper->add(
            'providerName',
            'doctrine_orm_choice',
            array(
                'field_options' => array(
                    'choices' => $providers,
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false,
                ),
                'field_type' => 'choice',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('description')
            ->add('size')
            ->add('createdAt')
            ->add('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $media = $this->getSubject();

        if (!$media) {
            $media = $this->getNewInstance();
        }

        if (!$media || !$media->getProviderName()) {
            return;
        }

        $formMapper->add('providerName', 'hidden');

        $formMapper->getFormBuilder()->addModelTransformer(new ProviderDataTransformer($this->pool, $this->getClass()), true);

        $provider = $this->pool->getProvider($media->getProviderName());

        if ($media->getId()) {
            $provider->buildEditForm($formMapper);
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }
}
