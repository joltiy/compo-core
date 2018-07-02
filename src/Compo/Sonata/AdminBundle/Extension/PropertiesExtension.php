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
class PropertiesExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $listMapper->getAdmin();

        if ($this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait',
        ])) {
            if ($listMapper->has('createdBy')) {
                $listMapper->get('createdBy')->setOption('sortable', true);
            }

            if ($listMapper->has('updatedBy')) {
                $listMapper->get('updatedBy')->setOption('sortable', true);
            }
        }

        if ($this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait',
        ])) {
            if ($listMapper->has('createdAt')) {
                $createdAt = $listMapper->get('createdAt');

                $createdAt->setOption('sortable', true);
                $createdAt->setOption('pattern', 'dd.MM.y HH:mm:ss');
            }

            if ($listMapper->has('updatedAt')) {
                $updatedAt = $listMapper->get('updatedAt');

                $updatedAt->setOption('sortable', true);
                $updatedAt->setOption('pattern', 'dd.MM.y HH:mm:ss');
            }
        }

        if ($this->isUseEntityTraits($admin, [
                'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity',
            ]) && $listMapper->has('deletedAt')) {
                $deletedAt = $listMapper->get('deletedAt');

                $deletedAt->setOption('sortable', true);
                $deletedAt->setOption('pattern', 'dd.MM.y HH:mm:ss');
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
            'Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait',
        ])) {
            if (!$datagridMapper->has('createdBy')) {
                $datagridMapper->add('createdBy');
            }

            if (!$datagridMapper->has('updatedBy')) {
                $datagridMapper->add('updatedBy');
            }
        }

        if ($this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait',
        ])) {
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

            if ($datagridMapper->has('updatedAt')) {
                $datagridMapper->remove('updatedAt');
            }
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

        if ($this->isUseEntityTraits($admin, [
            'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity',
        ])) {
            if ($datagridMapper->has('deletedAt')) {
                $datagridMapper->remove('deletedAt');
            }

            $datagridMapper->add(
                'deletedAt',
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

        if ($admin->isCurrentRoute('create')) {
            return;
        }

        if (
        !(
            $this->isUseEntityTraits($admin, [
                'Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait',
            ])
            ||
            $this->isUseEntityTraits($admin, [
                'Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait',
            ])
        )
        ) {
            return;
        }

        if ($formMapper->hasOpenTab()) {
            $formMapper->end();
        }

        if ($formMapper->hasOpenTab()) {
            $formMapper->end();
        }

        $formMapper->tab('properties', ['translation_domain' => 'SonataAdminBundle']);
        $formMapper->with('properties_created', ['name' => false, 'class' => 'col-lg-6']);

        if ($this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait',
        ])) {
            if ($formMapper->has('createdBy')) {
                $formMapper->remove('createdBy');
            }

            $formMapper->add(
                'createdBy',
                'sonata_type_model_list',
                [
                    'required' => false,
                    'attr' => [
                        'readonly' => true,
                    ],
                    'btn_add' => false,
                    'btn_edit' => false,
                    'btn_list' => false,
                    'btn_delete' => false,
                ],
                [
                    'link_parameters' => [
                        'context' => 'default',
                        'hide_context' => true,
                    ],
                    'translation_domain' => 'SonataAdminBundle',
                ]
            );
        }

        if ($this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait',
        ])) {
            if ($formMapper->has('createdAt')) {
                $formMapper->remove('createdAt');
            }

            $formMapper->add(
                'createdAt',
                'sonata_type_datetime_picker',
                [
                    'format' => 'dd.MM.y HH:mm:ss',
                    'required' => false,
                    'attr' => [
                        'readonly' => true,
                    ],
                ],
                [
                    'translation_domain' => 'SonataAdminBundle',
                ]
            );
        }

        $formMapper->end();
        $formMapper->with('properties_updated', ['name' => false, 'class' => 'col-lg-6']);

        if ($this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\BlameableEntityTrait',
        ])) {
            if ($formMapper->has('updatedBy')) {
                $formMapper->remove('updatedBy');
            }

            $formMapper->add(
                'updatedBy',
                'sonata_type_model_list',
                [
                    'required' => false,
                    'attr' => [
                        'readonly' => true,
                    ],
                    'btn_add' => false,
                    'btn_edit' => false,
                    'btn_list' => false,
                    'btn_delete' => false,
                ],
                [
                    'link_parameters' => [
                        'context' => 'default',
                        'hide_context' => true,
                    ],
                    'translation_domain' => 'SonataAdminBundle',
                ]
            );
        }

        if ($this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\TimestampableEntityTrait',
        ])) {
            if ($formMapper->has('updatedAt')) {
                $formMapper->remove('updatedAt');
            }

            $formMapper->add(
                'updatedAt',
                'sonata_type_datetime_picker',
                [
                    'format' => 'dd.MM.y HH:mm:ss',
                    'required' => false,
                    'attr' => [
                        'readonly' => true,
                    ],
                ],
                [
                    'translation_domain' => 'SonataAdminBundle',
                ]
            );
        }

        $formMapper->end();

        if ($this->isUseEntityTraits($admin, [
                'Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity',
            ]) && $admin->getSubject()->getDeletedAt()) {
            if ($formMapper->has('deletedAt')) {
                $formMapper->remove('deletedAt');
            }

            $formMapper->with('properties_deleted', ['name' => false, 'class' => 'col-lg-6']);
            $formMapper
                ->add(
                    'deletedAt',
                    'sonata_type_datetime_picker',
                    [
                        'format' => 'dd.MM.y HH:mm:ss',
                        'required' => false,
                        'attr' => [
                            'readonly' => true,
                        ],
                    ],
                    [
                        'translation_domain' => 'SonataAdminBundle',
                    ]
                );

            $formMapper->end();
        }

        $formMapper->end();
    }
}
