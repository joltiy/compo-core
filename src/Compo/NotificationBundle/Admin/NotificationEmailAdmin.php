<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\NotificationBundle\Admin;

use Compo\NotificationBundle\Entity\NotificationEmail;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Form\Type\HelpType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class NotificationEmailAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setSortBy('event');
        $this->setSortOrder('ASC');
    }

    /**
     * @param NotificationEmail $object
     */
    public function prePersist($object)
    {
        $this->fixData($object);
    }

    /**
     * @param $object NotificationEmail
     */
    public function fixData($object)
    {
        $notificationManager = $this->getNotificationManager();

        $event = $notificationManager->getDefaultEventByName($object->getCode());

        if (!$event) {
            $events = $notificationManager->getDefaultEventsByEvent($object->getEvent());

            foreach ($events as $item) {
                $event = $item;
            }
        }

        if ($event && !$object->getSubject()) {
            $object->setSubject($notificationManager->getTemplateSource($event['subject']));
        }

        if ($event && !$object->getRecipient()) {
            $object->setRecipient($notificationManager->getTemplateSource($event['recipient']));
        }

        if ($event && !$object->getBody()) {
            $object->setBody($notificationManager->getTemplateSource($event['body']));
        }
    }

    /**
     * @return \Compo\NotificationBundle\Manager\NotificationManager
     */
    public function getNotificationManager()
    {
        return $this->getContainer()->get('compo_notification.manager.notification');
    }

    /**
     * @param mixed $object
     */
    public function preUpdate($object)
    {
        $this->fixData($object);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('event')
            ->add('recipient')
            ->add('subject')
            ->add('body')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier(
                'event',
                'trans',
                [
                    'catalogue' => 'CompoNotificationBundle',
                ]
            )
            ->add('name')
            ->add('recipient')
            ->add('enabled')
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

        $subject = $this->getSubject();

        $help = '';

        if (!$this->isCurrentRoute('create')) {
            $events = $notificationManager->getDefaultEventsByEvent($subject->getEvent());

            foreach ($events as $event) {
                $help = $event['help'];
            }
        }

        $formMapper
            ->tab('main')
            ->with('main', ['name' => false, 'class' => 'col-lg-12']);

        $formMapper->add('id')
            ->add('enabled')
            ->add('name')
            ->add(
                'event',
                'choice',
                [
                    'choices' => $notificationManager->getEventsChoice(),
                    'choice_translation_domain' => 'CompoNotificationBundle',
                ]
            )
            ->add('sender', null, ['required' => false])
            ->add('recipient', null, ['attr' => ['class' => 'highlight-src'], 'required' => false])
            ->add('subject', null, ['attr' => ['class' => 'highlight-src'], 'required' => false])
            ->add('body', null, ['attr' => ['class' => 'highlight-src'], 'required' => false]);

        $formMapper->add(
            'help',
            HelpType::class,
            [
                'template' => $help,
            ]
        );

        $formMapper
            ->end()
            ->end();
    }
}
