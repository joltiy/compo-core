<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;


/**
 * {@inheritDoc}
 */
class DescriptionFormatterExtension extends AbstractAdminExtension
{
    /**
     * {@inheritdoc}
     */
    public function prePersist(AdminInterface $admin, $object)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if ($admin->descriptionFormatterEnabled) {
            try {
                /** @noinspection PhpUndefinedFieldInspection */
                $object->setDescription($admin->formatterPool->transform($object->getDescriptionFormatter(), $object->getRawDescription()));
            } catch (\Exception $e) {

            }
        }
    }


}