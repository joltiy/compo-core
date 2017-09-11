<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritDoc}
 */
class IdExtension extends AbstractAdminExtension
{


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
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        /** @var \Compo\Sonata\AdminBundle\Admin\AbstractAdmin $admin */
        $admin = $formMapper->getAdmin();

        if ($admin->isCurrentRoute('create')) {
            if ($formMapper->has('id')) {
                $formMapper->remove('id');
            }
        } else {
            $this->replaceFormField($formMapper,'id', 'text',
                array(
                    'required' => false,
                    'attr' => array('readonly' => true),
                    'disabled' => true,
                )
            );
        }




    }
}