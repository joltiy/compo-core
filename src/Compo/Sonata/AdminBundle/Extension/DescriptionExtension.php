<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;
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

            $this->replaceFormField($formMapper,'description', SimpleFormatterType::class, $options);
        }

        if (false && $formMapper->has('body')) {
            $field = $formMapper->getFormBuilder()->get('body');

            $options = $field->getOptions();
            $options['required'] = false;
            $options['format'] = "richhtml";
            $options['ckeditor_context'] = "default";

            $this->replaceFormField($formMapper,'body', SimpleFormatterType::class, $options);
        }
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