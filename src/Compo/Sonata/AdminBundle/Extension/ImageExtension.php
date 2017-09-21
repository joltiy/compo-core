<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * {@inheritDoc}
 */
class ImageExtension extends AbstractAdminExtension
{
    use ContainerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $this->replaceFormField(
            $formMapper,
            'image',
            'sonata_type_model_list',
            array(
                'required' => false,
                'by_reference' => true,
                'translation_domain' => 'SonataAdminBundle'
            ),
            array(
                'translation_domain' => 'SonataAdminBundle',

                'link_parameters' => array(
                    'context' => 'image',
                    'hide_context' => true,
                ),
            )
        );
    }
}