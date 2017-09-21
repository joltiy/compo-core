<?php

namespace Compo\SmsProviderBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * {@inheritDoc}
 */
class SmsProviderAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setSortBy('id');
        $this->setSortOrder('DESC');
    }

    /**
     * @param $type
     * @return mixed
     */
    public function transformListType($type)
    {
        $types = $this->getContainer()->get('compo_sms_provider.manager.sms_provider')->getTypesChoices();

        return $types[$type];
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('description')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt');
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
            ->add(
                'type',
                'html',
                array(
                    'template' => 'CompoSmsProviderBundle:Admin:list_type.html.twig'
                )
            )
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
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $manager = $this->getContainer()->get('compo_sms_provider.manager.sms_provider');

        $smsTypesChoices = $manager->getTypesChoices();

        $formMapper
            ->tab('form.tab_main')
            ->with('form.group_main', array('name' => false, 'class' => 'col-lg-6'))
            ->add('id')
            ->add('name', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false))
            ->add('description')
            ->add(
                'type',
                ChoiceType::class,
                array(
                    'required' => true,
                    'choices' => $smsTypesChoices
                )
            )
            ->add('login')
            ->add('password')
            ->end()
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('description')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }
}
