<?php

namespace Compo\Sonata\AdminBundle\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritDoc}
 */
class SortableExtension extends AbstractAdminExtension
{


    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $list = $listMapper->getAdmin()->getList()->getElements();

        foreach ($list as $fieldDescription) {

            if ($fieldDescription->getOption('sort_parent_association_enable', false) !== false) {
                $fieldDescription->setOption('sortable', $fieldDescription->getOption('sortable', true));
                $fieldDescription->setOption('sort_parent_association_mappings', array(array('fieldName' => $fieldDescription->getName())));
                $fieldDescription->setOption('sort_field_mapping', array('fieldName' => 'name'));
            }
        }

    }
}