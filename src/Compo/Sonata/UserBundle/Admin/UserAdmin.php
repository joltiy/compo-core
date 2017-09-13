<?php

/*
 * COMPO Форма редактирования пользователя
 */

namespace Compo\Sonata\UserBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;


/**
 * Class UserAdmin
 * @package Compo\Sonata\UserBundle\Admin
 */
class UserAdmin extends \Sonata\UserBundle\Admin\Model\UserAdmin
{

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->tab('User')
            ->with('Profile', array('class' => 'col-md-6'))->end()
            ->with('General', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Security')
            ->with('Status', array('class' => 'col-md-6'))->end()
            ->with('Groups', array('class' => 'col-md-6'))->end()

            //->with('Keys', array('class' => 'col-md-4'))->end()
            //->with('Roles', array('class' => 'col-md-12'))->end()
            ->end();

        $formMapper
            ->tab('User')
            ->with('General')
            ->add('username')
            ->add('email')
            ->add('plainPassword', 'text', array(
                'required' => !$this->getSubject() || null === $this->getSubject()->getId(),
            ))
            ->end()
            ->with('Profile')
            ->add('firstname', null, array('required' => false))
            ->add('lastname', null, array('required' => false))
            ->add('gender', 'sonata_user_gender', array(
                'required' => true,
                'translation_domain' => $this->getTranslationDomain(),
            ))
            ->add('dateOfBirth', 'sonata_type_date_picker', array(
                'format' => 'dd.MM.y',
                'required' => false,
            ))
            //->add('timezone', 'timezone', array('required' => false))
            ->add('phone', null, array('required' => false))
            ->end()
            ->end();

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Security')
                ->with('Status')
                //->add('locked', null, array('required' => false))
                //->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                //->add('credentialsExpired', null, array('required' => false))
                ->end()
                ->with('Groups')
                ->add('groups', 'sonata_type_model', array(
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ))
                ->end()

                /*
                ->with('Roles', array('name' => false))
                ->add('realRoles', 'sonata_security_roles', array(
                    'label' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                ))
                ->end()
                */

                ->end();
        }


    }
}