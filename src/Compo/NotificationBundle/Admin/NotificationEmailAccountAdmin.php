<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\NotificationBundle\Admin;

use Compo\NotificationBundle\Manager\NotificationManager;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * {@inheritdoc}
 */
class NotificationEmailAccountAdmin extends AbstractAdmin
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
            ->add('hostname')
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
        $notificationManager = $this->getNotificationManager();

        $formMapper->tab('main');
        $formMapper->with('main', ['name' => false, 'class' => 'col-lg-12']);

        $formMapper
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
                ChoiceType::class,
                [
                    'choices' => NotificationManager::getEmailAuthModeList(),
                    'choice_label' => function ($value, $key, $index) {
                        return 'form.choice.auth_mode.' . $key;
                    },
                    'choice_translation_domain' => $this->getTranslationDomain(),
                ]
            );

        $formMapper->end();
        $formMapper->end();
    }

    /**
     * @return \Compo\NotificationBundle\Manager\NotificationManager
     */
    public function getNotificationManager()
    {
        return $this->getContainer()->get('compo_notification.manager.notification');
    }
}
