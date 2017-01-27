<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritDoc}
 */
class TimestampableExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;


    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if ($datagridMapper->has('createdAt')) {
            $datagridMapper->remove('createdAt');

            $datagridMapper->add('createdAt', 'doctrine_orm_date_range', array('field_type' => 'sonata_type_date_range_picker',
                    'field_options' => [
                        'field_options' => [
                            'format' => 'dd.MM.yyyy'
                        ]
                    ]
                )
            );
        }

        if ($datagridMapper->has('updatedAt')) {
            $datagridMapper->remove('updatedAt');

            $datagridMapper->add('updatedAt', 'doctrine_orm_date_range', array('field_type' => 'sonata_type_date_range_picker',
                    'field_options' => [
                        'field_options' => [
                            'format' => 'dd.MM.yyyy'
                        ]
                    ]
                )
            );
        }

    }
}