<?php

namespace Compo\Sonata\UserBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class GroupAdmin.
 */
class GroupAdmin extends \Sonata\UserBundle\Admin\Model\GroupAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('Group')
            ->with('General', array('class' => 'col-md-6'))
            ->add('name')
            ->end()
            ->end()
            ->tab('Security')
            ->with('Roles', array('class' => 'col-md-12', 'name' => false))
            ->add(
                'roles',
                'sonata_security_roles',
                array(
                    'label' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                )
            )
            ->end()
            ->end();
    }
}
