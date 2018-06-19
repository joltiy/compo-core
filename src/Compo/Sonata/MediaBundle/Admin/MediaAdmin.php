<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\MediaBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Traits\BaseAdminTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;

/**
 * Class MediaAdmin.
 */
class MediaAdmin extends \Sonata\MediaBundle\Admin\ORM\MediaAdmin
{
    use BaseAdminTrait;

    public function configure()
    {
        $listModes = [
            'mosaic' => ['class' => 'fa fa-th-large fa-fw'],
        ];

        $this->setListModes(array_merge($listModes, $this->getListModes()));

        $this->setSortBy('createdAt');
        $this->setSortOrder('DESC');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);

        $listMapper->add('createdAt', null, [
            'sortable' => true,
            'pattern' => 'dd.MM.y HH:mm:ss',
        ]);
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
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $media = $this->getSubject();

        $pool = $this->pool;

        if (!$media) {
            $media = $this->getNewInstance();
        }

        if (!$media || !$media->getProviderName()) {
            return;
        }

        $formMapper->add('providerName', 'hidden');

        $formMapper->getFormBuilder()->addModelTransformer(new ProviderDataTransformer($pool, $this->getClass()), true);

        $provider = $pool->getProvider($media->getProviderName());

        if ($media->getId()) {
            $provider->buildEditForm($formMapper);
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }
}
