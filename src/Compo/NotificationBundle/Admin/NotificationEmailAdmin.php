<?php

namespace Compo\NotificationBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Form\Type\HelpType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

class NotificationEmailAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setTranslationDomain('CompoNotificationBundle');
        $this->setSortBy('event');
        $this->setSortOrder('ASC');
        $this->configureProperties(true);
        $this->configureSettings(true, 'compo_notification_email_settings');

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
            ->add('note')
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
            ->addIdentifier('event', 'trans', array(
                'catalogue' => 'CompoNotificationBundle',
            ))
            ->add('recipient')
            ->add('note')
            ->add('enabled')
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
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
            ->tab('form.tab_main')
            ->with('form.group_main', array('name' => false, 'class' => 'col-lg-12'));

        $formMapper->add('id')
            ->add('enabled')
            ->add('event', 'choice', array(
                'choices' => $notificationManager->getEventsChoice(),
                'choice_translation_domain' => 'CompoNotificationBundle',
            ))
            ->add('note')
            ->add('sender', null, array('required' => false))
            ->add('recipient', null, array('attr' => array('class' => 'highlight-src'), 'required' => false))
            ->add('subject', null, array('attr' => array('class' => 'highlight-src'), 'required' => false) )
            ->add('body', null, array('attr' => array('class' => 'highlight-src'), 'required' => false));

        $formMapper->add('help', HelpType::class, array(
            'template' => $help

        ));
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
            ->add('subject')
            ->add('body')
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('deletedAt')
        ;
    }

    public function prePersist($object)
    {
        $this->fixData($object);
    }

    public function preUpdate($object)
    {
        $this->fixData($object);
    }


    public function fixData($object)
    {
        $notificationManager = $this->getContainer()->get('compo_notification.manager.notification');

        $event = $notificationManager->getEvent($object->getEvent());

        if (!$object->getSubject()) {
            $object->setSubject($notificationManager->getTemplateSource($event['subject']));
        }

        if (!$object->getRecipient()) {
            $object->setRecipient($notificationManager->getTemplateSource($event['recipient']));
        }

        if (!$object->getBody()) {
            $object->setBody($notificationManager->getTemplateSource($event['body']));
        }
    }
}
