<?php

namespace Compo\Sonata\AdminBundle\Extension;

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
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if ($listMapper->has('enabled')) {
            $listMapper->get('enabled')->setOption('editable', true);
            $listMapper->get('enabled')->setOption('required', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if ($formMapper->has('enabled')) {
            $formMapper->get('enabled')->setRequired(false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureBatchActions(AdminInterface $admin, array $actions)
    {
        if ($admin->getList()->has('enabled')) {
            if (
                $admin->hasRoute('edit') && $admin->isGranted('EDIT')
            ) {
                $actions['enable'] = [
                ];

                $actions['disable'] = [
                ];
            }
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
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
