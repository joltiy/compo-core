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
        $this->setSortBy('id');
        $this->setSortOrder('ASC');
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $notificationManager = $this->getNotificationManager();

        $formMapper
            ->tab('main')
            ->with('main', ['name' => false, 'class' => 'col-lg-12'])
            ->add('id')
            ->add('name')
            ->add('description', CKEditorType::class, ['attr' => ['class' => ''], 'required' => false])
            ->add(
                'transport',
                'choice',
                [
                    'choices' => $notificationManager->getEmailTransport(),
                ]
            )
            ->add('username')
            ->add('password')
            ->add('hostname')
            ->add('port')
            ->add(
                'encryption',
                'choice',
                [
                    'choices' => $notificationManager->getEmailEncryption(),
                ]
            )
            ->add(
                'authMode',
                'choice',
                [
                    'choices' => $notificationManager->getEmailAuthMode(),
                ]
            )
            ->end()
            ->end();
    }

    /**
     * @return \Compo\NotificationBundle\Manager\NotificationManager
     */
    public function getNotificationManager()
    {
        return $this->getContainer()->get('compo_notification.manager.notification');
    }

    /**
     * {@inheritDoc}
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
