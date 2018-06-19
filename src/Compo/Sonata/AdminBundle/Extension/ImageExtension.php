<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class ImageExtension extends AbstractAdminExtension
{
    /**
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isUseEntityTraits($formMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\ImageEntityTrait',
        ])) {
            return;
        }

        $this->replaceFormField(
            $formMapper,
            'image',
            'sonata_type_model_list',
            [
                'required' => false,
                'by_reference' => true,
                'translation_domain' => 'SonataAdminBundle',
            ],
            [
                'translation_domain' => 'SonataAdminBundle',

                'link_parameters' => [
                    'context' => 'image',
                    'hide_context' => true,
                ],
            ]
        );
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\ImageEntityTrait',
        ])) {
            return;
        }

        if ($datagridMapper->has('without_image')) {
            $datagridMapper->remove('without_image');

            $datagridMapper->add(
                'without_image',
                'doctrine_orm_callback',
                [
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return false;
                        }

                        /* @var QueryBuilder $queryBuilder */
                        $queryBuilder->andWhere($queryBuilder->getRootAliases()[0] . '.image IS NULL');

                        return true;
                    },
                    'field_type' => 'checkbox',
                ]
            );
        }
    }
}
