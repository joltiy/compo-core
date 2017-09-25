<?php

namespace Compo\FeedbackBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class FeedbackAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setSortBy('createdAt');
        $this->setSortOrder('DESC');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('createdAt')
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add(
                '_action',
                null,
                array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                    )
                )
            );
    }

    /**
     * {@inheritDoc}
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
            ->with('main', array('name' => false, 'class' => 'col-lg-6'))
            ->add('id')
            ->add('createdAt')
            ->add(
                'type',
                'choice',
                array(
                    'choices' => $feedbackManager->getTypesChoice(),
                    'choice_translation_domain' => 'CompoFeedbackBundle',
                )
            )
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('page')
            ->add('message')
            ->add(
                'tags',
                'sonata_type_model',
                array(
                    'by_reference' => false,
                    'multiple' => true,
                    'expanded' => false,
                    'compound' => false,
                    'required' => false,
                    'query' => $tagsQb,
                )
            )
            ->end()
            ->end();
    }

    /**
     * {@inheritDoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('createdAt')
            ->add('updatedAt');
    }
}
