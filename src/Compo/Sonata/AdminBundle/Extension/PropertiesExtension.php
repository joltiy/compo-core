<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class PropertiesExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait',
            'Gedmo\Timestampable\Traits\TimestampableEntity',
        ])) {
            return;
        }

        if (!$datagridMapper->has('createdAt')) {
            $datagridMapper->add('createdAt');
        }

        if (!$datagridMapper->has('updatedAt')) {
            $datagridMapper->add('updatedAt');
        }

        if (!$datagridMapper->has('createdBy')) {
            $datagridMapper->add('createdBy');
        }

        if (!$datagridMapper->has('updatedBy')) {
            $datagridMapper->add('updatedBy');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $formMapper->getAdmin();

        if ($admin->isCurrentRoute('create')) {
            return;
        }

        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait',
            'Gedmo\Timestampable\Traits\TimestampableEntity',
        ])) {
            return;
        }

        if (!$admin->isPropertiesEnabled()) {
            return;
        }

        $formMapper->tab('properties')
            ->with('properties_created', ['name' => false, 'class' => 'col-lg-6'])
            ->add(
                'createdBy',
                'sonata_type_model_list',
                [
                    'required' => false,
                ],
                [
                    'link_parameters' => [
                        'context' => 'default',
                        'hide_context' => true,
                    ],
                    'translation_domain' => 'SonataAdminBundle',
                ]
            )
            ->add(
                'createdAt',
                'sonata_type_datetime_picker',
                [
                    'format' => 'dd.MM.y HH:mm:ss',
                    'required' => true,
                ],
                [
                    'translation_domain' => 'SonataAdminBundle',
                ]
            )
            ->end()
            ->with('properties_updated', ['name' => false, 'class' => 'col-lg-6'])
            ->add(
                'updatedBy',
                'sonata_type_model_list',
                [
                    'required' => false,
                ],
                [
                    'link_parameters' => [
                        'context' => 'default',
                        'hide_context' => true,
                    ],
                    'translation_domain' => 'SonataAdminBundle',
                ]
            )
            ->add(
                'updatedAt',
                'sonata_type_datetime_picker',
                [
                    'format' => 'dd.MM.y HH:mm:ss',
                    'required' => true,
                ],
                [
                    'translation_domain' => 'SonataAdminBundle',
                ]
            )
            ->end()
            ->end();
    }
}
