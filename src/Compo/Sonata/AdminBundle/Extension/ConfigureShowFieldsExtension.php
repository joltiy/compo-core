<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * {@inheritdoc}
 */
class ConfigureShowFieldsExtension extends AbstractAdminExtension
{
    /**
     * Дополняем show элементы из формы редактирования.
     *
     * @param ShowMapper $showMapper
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $array = $showMapper->getAdmin()->getFormFieldDescriptions();

        foreach ($array as $item) {
            if (!$showMapper->has($item->getName())) {
                $showMapper->add($item->getName());
            }
        }
    }
}
