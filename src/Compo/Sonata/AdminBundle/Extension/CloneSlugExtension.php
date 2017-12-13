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
class CloneSlugExtension extends AbstractAdminExtension
{
    public function preUpdate(AdminInterface $admin, $object)
    {
        if (method_exists($object, 'setSlug') && method_exists($object, 'getName')) {
            if (strpos($object->getSlug(), 'clone-slug-') !== false) {
                if (strpos($object->getName(), '(Копия)') === false) {
                    $object->setSlug(null);
                }
            }
        }
    }
}
