<?php

namespace Compo\FeedbackBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

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
            ->add(
                '_action',
                null,
                [
                    'actions' => [
                        'edit' => [],
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

        /** @var QueryBuilder $tagsQb */
        $tagsQb = $this->getDoctrine()->getManager()->createQueryBuilder('c');
        $tagsQb->select('c')
            ->from('CompoFeedbackBundle:FeedbackTag', 'c')
            ->orderBy('c.name', 'ASC');

        $formMapper
            ->tab('main')
            ->with('main', ['name' => false, 'class' => 'col-lg-6'])
            ->add('id')
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
            ->add('message');

        $formMapper
            ->add('product', 'text', [
                'property_path' => 'data[product]',
            ])
            ->add('product_url', 'text', [
                    'property_path' => 'data[product_url]',
                ]
            )
        ;

        $formMapper->end();
        $formMapper->with('extra', ['name' => false, 'class' => 'col-lg-6']);

        $formMapper->add(
                'tags',
                'sonata_type_model',
                [
                    'by_reference' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'compound' => false,
                    'required' => false,
                    'query' => $tagsQb,
                ]
            );
        $formMapper->add('page');

        $formMapper->end()
            ->end();
    }
}
