<?php

namespace Compo\NotificationBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritdoc}
 */
class NotificationSmsAccountAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setSortBy('id');
        $this->setSortOrder('ASC');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('username')
            ->add('password')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('description');
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $notificationManager = $this->getContainer()->get('compo_notification.manager.notification');

        $formMapper
            ->tab('main')
            ->with('main', array('name' => false, 'class' => 'col-lg-12'))
            ->add('id')
            ->add('name')
            ->add('description', CKEditorType::class, array('attr' => array('class' => ''), 'required' => false))
            ->add(
                'transport',
                'choice',
                array(
                    'choices' => $notificationManager->getSmsTransport(),
                )
            )
            ->add('username')
            ->add('password')
            ->add(
                'sender',
                null,
                array(
                    'label' => 'sms_sender',
                )
            )
            ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('username')
            ->add('password')
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
            ->add('description');
    }
}
