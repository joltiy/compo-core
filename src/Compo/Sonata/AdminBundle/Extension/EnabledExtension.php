<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        if ($formMapper->has('enabled')) {
            $formMapper->get('enabled')->setRequired(false);
        }
    }


    public function configureBatchActions(AdminInterface $admin, array $actions)
    {
        if ($admin->getList()->has('enabled')) {
            if (
                $admin->hasRoute('edit') && $admin->isGranted('EDIT')
            ) {
                $actions['enable'] = array(
                    'label' => 'batch_actions.label_enable',
                    'ask_confirmation' => true,
                    'translation_domain' => $admin->getTranslationDomain()
                );

                $actions['disable'] = array(
                    'label' => 'batch_actions.label_disable',
                    'ask_confirmation' => true,
                    'translation_domain' => $admin->getTranslationDomain()
                );
            }
        }

        return $actions;
    }
}