<?php

namespace Compo\NotificationBundle\Admin;

use Compo\NotificationBundle\Entity\NotificationEmail;
use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Form\Type\HelpType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

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
                array(
                    'catalogue' => 'CompoNotificationBundle',
                )
            )
            ->add('name')
            ->add('recipient')
            ->add('enabled')
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
            ->with('main', array('name' => false, 'class' => 'col-lg-12'));

        $formMapper->add('id')
            ->add('enabled')
            ->add('name')
            ->add(
                'event',
                'choice',
                array(
                    'choices' => $notificationManager->getEventsChoice(),
                    'choice_translation_domain' => 'CompoNotificationBundle',
                )
            )
            ->add('sender', null, array('required' => false))
            ->add('recipient', null, array('attr' => array('class' => 'highlight-src'), 'required' => false))
            ->add('subject', null, array('attr' => array('class' => 'highlight-src'), 'required' => false))
            ->add('body', null, array('attr' => array('class' => 'highlight-src'), 'required' => false));

        $formMapper->add(
            'help',
            HelpType::class,
            array(
                'template' => $help,
            )
        );

        $formMapper
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
            ->add('event')
            ->add('recipient')
            ->add('subject')
            ->add('body')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }
}
