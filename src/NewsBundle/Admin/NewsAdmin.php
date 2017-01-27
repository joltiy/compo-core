<?php

namespace Compo\NewsBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class NewsAdmin extends Admin
{
    /**
     * Конфигурация админки
     */
    public function configure()
    {
        // Домен переводов
        $this->setTranslationDomain('CompoNewsBundle');

    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('description')

            ->add('enabled', null, array(
                'editable' => true,
                'required' => true
            ))
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('enabled', null, array('required' => false))
            ->add('name')
        ;

        $this->createDescriptionFormatterFormType($formMapper);
        $formMapper->add('publicationAt');


        $formMapper->add('image', 'sonata_type_model_list', array(
            'required' => false,
            'by_reference' => true,
        ),
            array(
                'link_parameters' => array(
                    'context' => 'default',
                    'hide_context' => true,
                ),
            ));

    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
        ;
    }
}
