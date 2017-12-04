<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritdoc}
 */
class SortableExtension extends AbstractAdminExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $list = $listMapper->getAdmin()->getList()->getElements();

        /** @var FieldDescriptionInterface $fieldDescription */
        foreach ($list as $fieldDescription) {
            if (false !== $fieldDescription->getOption('sort_parent_association_enable', false)) {
                $fieldDescription->setOption('sortable', $fieldDescription->getOption('sortable', true));
                $fieldDescription->setOption('sort_parent_association_mappings', array(array('fieldName' => $fieldDescription->getName())));
                $fieldDescription->setOption('sort_field_mapping', array('fieldName' => 'name'));
            }
        }
    }
}
