<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\UserBundle\Admin;

use Compo\Sonata\AdminBundle\Admin\Traits\BaseAdminTrait;
use Compo\Sonata\UserBundle\Form\Type\SecurityRolesType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class GroupAdmin.
 */
class GroupAdmin extends \Sonata\UserBundle\Admin\Model\GroupAdmin
{
    use BaseAdminTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        parent::configureListFields($listMapper);

        $listMapper->remove('roles');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        parent::configureFormFields($formMapper);

        $formMapper->tab('Security');
        $formMapper->with('Roles');

        $formMapper->add('roles', SecurityRolesType::class, [
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'label' => false,
        ]);

        $formMapper->end();
        $formMapper->end();

        $formGroups = $this->getFormGroups();

        $formGroups['Security.Roles']['label'] = false;

        $this->setFormGroups($formGroups);
    }
}
