<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritdoc}
 */
class IdExtension extends AbstractAdminExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\IdEntityTrait',
        ])) {
            return;
        }

        if (!$datagridMapper->has('id')) {
            $datagridMapper->add('id');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if ($listMapper->has('id') && $listMapper->get('id')) {
            $listMapper->get('id')->setTemplate('SonataIntlBundle:CRUD:list_integer.html.twig');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if (!$this->isUseEntityTraits($formMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\IdEntityTrait',
        ])) {
            return;
        }

        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $formMapper->getAdmin();

        if ($admin->isCurrentRoute('create')) {
            if ($formMapper->has('id')) {
                $formMapper->remove('id');
            }
        } else {
            $this->replaceFormField(
                $formMapper,
                'id',
                'text',
                [
                    'required' => false,
                    'attr' => ['readonly' => true],
                    'disabled' => true,
                ]
            );
        }
    }
}
