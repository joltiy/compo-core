<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Doctrine\ORM\QueryBuilder;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritDoc}
 */
class ImageExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;


    public function configureFormFields(FormMapper $formMapper)
    {
        $this->replaceFormField($formMapper,'image','sonata_type_model_list',
            array(
                'required' => false,
                'by_reference' => true,
                'translation_domain' => 'SonataAdminBundle'
            ),
            array(
                'translation_domain' => 'SonataAdminBundle',

                'link_parameters' => array(
                    'context' => 'default',
                    'hide_context' => true,
                ),
            )
        );
    }
}