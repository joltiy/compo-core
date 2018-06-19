<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\NotificationBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Traits\BaseAdminTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * Class MessageAdmin.
 */
class MessageAdmin extends \Sonata\NotificationBundle\Admin\MessageAdmin
{
    use BaseAdminTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);

        $listMapper->get('createdAt')->setOption('pattern', 'dd.MM.y HH:mm:ss');
        $listMapper->get('startedAt')->setOption('pattern', 'dd.MM.y HH:mm:ss');
        $listMapper->get('completedAt')->setOption('pattern', 'dd.MM.y HH:mm:ss');
    }

    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        parent::configureDatagridFilters($datagridMapper);

        if ($datagridMapper->has('createdAt')) {
            $datagridMapper->remove('createdAt');
        }

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

        if ($datagridMapper->has('startedAt')) {
            $datagridMapper->remove('startedAt');
        }

        $datagridMapper->add(
            'startedAt',
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

        if ($datagridMapper->has('completedAt')) {
            $datagridMapper->remove('completedAt');
        }

        $datagridMapper->add(
            'completedAt',
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
