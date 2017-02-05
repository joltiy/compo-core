<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

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
        if ($formMapper->has('description')) {
            $field = $formMapper->getFormBuilder()->get('description');

            $options = $field->getOptions();
            $options['required'] = false;
            $options['format'] = "richhtml";
            $options['ckeditor_context'] = "default";

            $formMapper->getFormBuilder()->add('description', SimpleFormatterType::class, $options);
        }
    }
}