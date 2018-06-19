<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class EnabledExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        /** @var AbstractAdmin $admin */
        $admin = $listMapper->getAdmin();

        if (!$this->isUseEntityTraits($admin, [
            'Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait',
        ])) {
            return;
        }

        if (!$listMapper->has('enabled')) {
            $listMapper->add('enabled');
        }

        $enabled = $listMapper->get('enabled');

        $enabled->setOption('editable', true);
        $enabled->setOption('required', true);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if ($formMapper->has('enabled')) {
            $formMapper->get('enabled')->setRequired(false);
        }
    }

    /**
     * @param AdminInterface $admin
     * @param array          $actions
     *
     * @return array
     */
    public function configureBatchActions(AdminInterface $admin, array $actions)
    {
        /** @var AbstractAdmin $admin */
        if ($admin->hasRoute('edit') && $admin->hasAccess('edit') && $admin->getList()->has('enabled')) {
            $actions['enable'] = [
            ];

            $actions['disable'] = [
            ];
        }

        return $actions;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\EnabledEntityTrait',
        ])) {
            return;
        }

        if (!$datagridMapper->has('enabled')) {
            $datagridMapper->add('enabled');
        }
    }
}
