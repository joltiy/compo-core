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
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritdoc}
 */
class ColorExtension extends AbstractAdminExtension
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (!$this->isUseEntityTraits($datagridMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\ColorEntityTrait',
        ])) {
            return;
        }

        if (!$datagridMapper->has('color')) {
            $datagridMapper->add('color');
        }
    }

    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        if (!$this->isUseEntityTraits($listMapper->getAdmin(), [
            'Compo\Sonata\AdminBundle\Entity\ColorEntityTrait',
        ])) {
            return;
        }

        if ($listMapper->has('color')) {
            $keys = $listMapper->keys();

            $listMapper->remove('color');
            $listMapper->add('color', 'html');
            $listMapper->reorder($keys);
        }
    }
}
