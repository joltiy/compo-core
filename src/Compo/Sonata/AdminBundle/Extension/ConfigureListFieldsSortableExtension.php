<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritdoc}
 */
class ConfigureListFieldsSortableExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $list = $listMapper->getAdmin()->getList()->getElements();

        /** @var FieldDescriptionInterface $fieldDescription */
        foreach ($list as $key => $fieldDescription) {
            if (\in_array($key, ['batch', '_action'], true)) {
                continue;
            }

            if (false !== $fieldDescription->getOption('sort_parent_association_enable', false)) {
                $fieldDescription->setOption('sortable', $fieldDescription->getOption('sortable', true));
                $fieldDescription->setOption('sort_parent_association_mappings', [['fieldName' => $fieldDescription->getName()]]);
                $fieldDescription->setOption('sort_field_mapping', ['fieldName' => 'name']);
            }
        }
    }
}
