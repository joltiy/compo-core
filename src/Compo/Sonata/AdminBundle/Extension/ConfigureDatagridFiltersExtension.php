<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\DoctrineORMAdminBundle\Admin\FieldDescription;

/**
 * {@inheritdoc}
 */
class ConfigureDatagridFiltersExtension extends AbstractAdminExtension
{
    /**
     * Дополняем фильтры из столбцов.
     *
     * @param DatagridMapper $datagridMapper
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        return;
        $items = $datagridMapper->getAdmin()->getListFieldDescriptions();

        /** @var FieldDescription $item */
        foreach ($items as $item) {
            $name = $item->getName();

            if ('batch' === $name || '_action' === $name || 'select' === $name) {
                continue;
            }

            if (!$item->getMappingType()) {
                continue;
            }

            if (!$datagridMapper->has($name)) {
                $datagridMapper->add($name);
            }
        }
    }
}
