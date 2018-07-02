<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\FeedbackBundle\Admin;

use Compo\FeedbackBundle\Entity\FeedbackTag;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class FeedbackAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setSortBy('createdAt');
        $this->setSortOrder('DESC');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('createdAt')
            ->add('updatedAt');

        $datagridMapper
            ->add(
                'tags',
                'doctrine_orm_model_autocomplete',
                [],
                null,
                [
                    'multiple' => true,
                    'property' => 'name',
                    'minimum_input_length' => 0,
                    'cache' => true,
                    'items_per_page' => 0,
                    'callback' => function ($admin, $property, $value) {
                        /** @var AbstractAdmin $admin */
                        $datagrid = $admin->getDatagrid();

                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder = $datagrid->getQuery();

                        $queryBuilder->orderBy($queryBuilder->getRootAliases()[0] . '.name', 'ASC');
                        $datagrid->setValue($property, null, $value);
                    },
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('createdAt')
            ->add('type', 'trans')
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add('tags', null, ['editable' => true])

            ->add(
                '_action',
                null,
                [
                    'actions' => [
                        'delete' => [],
                    ],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $feedbackManager = $this->getContainer()->get('compo_feedback.manager.feedback');

        $formMapper->tab('main');
        $formMapper->with('main', ['name' => false, 'class' => 'col-lg-6']);

        $formMapper->add('id')
            ->add('createdAt')
            ->add(
                'type',
                'sonata_type_choice_field_mask',
                [
                    'choices' => $feedbackManager->getTypesChoice(),
                    'choice_translation_domain' => 'CompoFeedbackBundle',
                    'map' => [
                        'compo_feedback.product_want_lower_cost' => ['product', 'product_url'],
                    ],
                ]
            )
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('message')
        ;

        $formMapper
            ->add('product', 'text', [
                'required' => false,
                'property_path' => 'data[product]',
            ])
            ->add('product_url', 'text', [
                    'required' => false,
                    'property_path' => 'data[product_url]',
                ]
            )
        ;

        $formMapper->end();
        $formMapper->with('extra', ['name' => false, 'class' => 'col-lg-6']);

        $formMapper->add(
            'tags',
            'sonata_type_model_autocomplete',
            [
                'required' => false,
                'multiple' => true,
                'property' => 'name',
                'minimum_input_length' => 0,
                'cache' => true,
                'items_per_page' => 50,
                'callback' => function ($admin, $property, $value) {
                    /** @var AbstractAdmin $admin */
                    $datagrid = $admin->getDatagrid();

                    /** @var QueryBuilder $queryBuilder */
                    $queryBuilder = $datagrid->getQuery();

                    $queryBuilder->orderBy($queryBuilder->getRootAliases()[0] . '.name', 'ASC');
                    $datagrid->setValue($property, null, $value);
                },
                'to_string_callback' => function ($entity, $property) {
                    /** @var $entity FeedbackTag */
                    return $entity->getName();
                },
            ]
        );
        $formMapper->add('page');

        $formMapper->end();
        $formMapper->end();
    }
}
