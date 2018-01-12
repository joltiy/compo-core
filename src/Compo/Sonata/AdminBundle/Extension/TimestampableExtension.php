<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class TimestampableExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if ($datagridMapper->has('createdAt')) {
            $datagridMapper->remove('createdAt');

            $datagridMapper->add(
                'createdAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'field_options' => [
                        'field_options' => [
                            'format' => 'dd.MM.yyyy',
                        ],
                    ],
                ]
            );
        }

        if ($datagridMapper->has('updatedAt')) {
            $datagridMapper->remove('updatedAt');

            $datagridMapper->add(
                'updatedAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'field_options' => [
                        'field_options' => [
                            'format' => 'dd.MM.yyyy',
                        ],
                    ],
                ]
            );
        }

        if ($datagridMapper->has('publicationAt')) {
            $datagridMapper->remove('publicationAt');

            $datagridMapper->add(
                'publicationAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'field_options' => [
                        'field_options' => [
                            'format' => 'dd.MM.yyyy',
                        ],
                    ],
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $this->replaceFormField(
            $formMapper,
            'publicationAt',
            'sonata_type_datetime_picker',
            [
                'format' => 'dd.MM.y HH:mm:ss',
                'required' => true,
            ],
            [
                'translation_domain' => 'SonataAdminBundle',
            ]
        );

        $this->replaceFormField(
            $formMapper,
            'createdAt',
            'sonata_type_datetime_picker',
            [
                'format' => 'dd.MM.y HH:mm:ss',
                'required' => true,
            ],
            [
                'translation_domain' => 'SonataAdminBundle',
            ]
        );

        $this->replaceFormField(
            $formMapper,
            'updatedAt',
            'sonata_type_datetime_picker',
            [
                'format' => 'dd.MM.y HH:mm:ss',
                'required' => true,
            ],
            [
                'translation_domain' => 'SonataAdminBundle',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $keys = $listMapper->keys();

        if ($listMapper->has('createdAt')) {
            $field = $listMapper->get('createdAt');

            $field->setOption('pattern', 'dd.MM.y HH:mm:ss');
        }

        if ($listMapper->has('updatedAt')) {
            $field = $listMapper->get('updatedAt');

            $field->setOption('pattern', 'dd.MM.y HH:mm:ss');
        }

        if ($listMapper->has('publicationAt')) {
            $field = $listMapper->get('publicationAt');

            $field->setOption('pattern', 'dd.MM.y HH:mm:ss');
        }

        $listMapper->reorder($keys);
    }
}
