<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;


/**
 * {@inheritDoc}
 */
class DescriptionExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if ($listMapper->has('description')) {
            $keys = $listMapper->keys();

            $listMapper->remove('description');
            $listMapper->add('description', 'html');
            $listMapper->reorder($keys);
        }

        if ($listMapper->has('body')) {
            $keys = $listMapper->keys();

            $listMapper->remove('body');
            $listMapper->add('body', 'html');
            $listMapper->reorder($keys);
        }
    }
}