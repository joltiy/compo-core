<?php

namespace Compo\NotificationBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritDoc}
 */
class NotificationEmailAccountAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoNotificationBundle');
        $this->setSortBy('id');
        $this->setSortOrder('ASC');
        $this->configureProperties(true);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('username')
            ->add('password')
            ->add('hostname')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('description');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('username')
            ->add('description')
            ->add(
                '_action',
                null,
                array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                    ),
                )
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $notificationManager = $this->getContainer()->get('compo_notification.manager.notification');

        $formMapper
            ->tab('form.tab_main')
            ->with('form.group_main', array('name' => false, 'class' => 'col-lg-12'))
            ->add('id')
            ->add('name')
            ->add('description', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false))
            ->add(
                'transport',
                'choice',
                array(
                    'choices' => $notificationManager->getEmailTransport(),
                )
            )
            ->add('username')
            ->add('password')
            ->add('hostname')
            ->add('port')
            ->add(
                'encryption',
                'choice',
                array(
                    'choices' => $notificationManager->getEmailEncryption(),
                )
            )
            ->add(
                'authMode',
                'choice',
                array(
                    'choices' => $notificationManager->getEmailAuthMode(),
                )
            )
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
            ->add('username')
            ->add('password')
            ->add('hostname')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('description');
    }
}
