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
 * {@inheritDoc}
 */
class NotificationSmsAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setSortBy('event');
        $this->setSortOrder('ASC');
    }

    /**
     * @param mixed $object
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
        $notificationManager = $this->getContainer()->get('compo_notification.manager.notification');

        $event = $notificationManager->getEvent($object->getEvent());

        if (!$object->getRecipient()) {
            $object->setRecipient($notificationManager->getTemplateSource($event['recipient_sms']));
        }

        if (!$object->getBody()) {
            $object->setBody($notificationManager->getTemplateSource($event['body_sms']));
        }
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
            ->add('body')
            ->add('note')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }

    /**
     * {@inheritDoc}
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
            ->add('recipient')
            ->add('note')
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
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $notificationManager = $this->getContainer()->get('compo_notification.manager.notification');

        $subject = $this->getSubject();

        if ($this->isCurrentRoute('create')) {
            $help = '';
        } else {
            $event = $notificationManager->getEvent($subject->getEvent());
            $help = $event['help'];
        }

        $formMapper
            ->tab('main')
            ->with('main', array('name' => false, 'class' => 'col-lg-12'));

        $formMapper->add('id')
            ->add('enabled')
            ->add(
                'event',
                'choice',
                array(
                    'choices' => $notificationManager->getEventsChoice(),
                    'choice_translation_domain' => 'CompoNotificationBundle',
                )
            )
            ->add('note')
            ->add('sender', null, array('required' => true, 'label' => 'sms_sender'))
            ->add('recipient', null, array('attr' => array('class' => 'highlight-src'), 'required' => false))
            ->add('body', null, array('attr' => array('class' => 'highlight-src'), 'required' => false));

        $formMapper->add(
            'help',
            HelpType::class,
            array(
                'template' => $help

            )
        );
        $formMapper->end()
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
            ->add('body')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt');
    }
}
