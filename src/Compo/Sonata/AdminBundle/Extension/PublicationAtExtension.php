<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class PublicationAtExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $listMapper->getAdmin();

        if ($this->isUseEntityTraits($admin, [
                'Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait',
            ]) && $listMapper->has('publicationAt')) {
                $publicationAt = $listMapper->get('publicationAt');
                $publicationAt->setOption('sortable', true);
                $publicationAt->setOption('pattern', 'dd.MM.y HH:mm:ss');
            }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $datagridMapper->getAdmin();

        if ($this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait',
        ])) {
            if ($datagridMapper->has('publicationAt')) {
                $datagridMapper->remove('publicationAt');
            }

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
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $formMapper->getAdmin();

        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\PublicationAtEntityTrait',
        ])) {
            return;
        }

        if ($formMapper->has('publicationAt')) {
            $this->replaceFormField($formMapper, 'publicationAt', 'sonata_type_datetime_picker',
                [
                    'format' => 'dd.MM.y HH:mm:ss',
                    'required' => false,
                ],
                [
                    'translation_domain' => 'SonataAdminBundle',
                ]
            );
        }
    }
}
