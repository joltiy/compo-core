<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * {@inheritdoc}
 */
class CloneSlugExtension extends AbstractAdminExtension
{
    public function preUpdate(AdminInterface $admin, $object)
    {
        if (method_exists($object, 'setSlug') && method_exists($object, 'getName')) {
            if (false !== mb_strpos($object->getSlug(), 'clone-slug-')) {
                if (false === mb_strpos($object->getName(), '(Копия)')) {
                    $object->setSlug(null);
                }
            }
        }
    }
}
