<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Form\FormMapper;

class DescriptionFormatterExtension extends AdminExtension
{
    /**
     * {@inheritdoc}
     */
    public function prePersist(AdminInterface $admin, $object)
    {
        if ($admin->descriptionFormatterEnabled) {
            try {
                $object->setDescription($admin->formatterPool->transform($object->getDescriptionFormatter(), $object->getRawDescription()));
            } catch (\Exception $e) {

            }
        }
    }


}