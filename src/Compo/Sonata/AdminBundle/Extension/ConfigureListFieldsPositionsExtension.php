<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * {@inheritdoc}
 */
class ConfigureListFieldsPositionsExtension extends AbstractAdminExtension
{
    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        return;
        if ($listMapper->has('_action')) {
            $keys = $listMapper->keys();

            usort($keys, function ($a, $b) {
                if ('_action' === $a) {
                    return 1;
                }

                if ('_action' === $b) {
                    return -1;
                }

                return 0;
            });

            $listMapper->reorder($keys);
        }

        if ($listMapper->has('id')) {
            $keys = $listMapper->keys();

            usort($keys, function ($a, $b) {
                if (\in_array($a, ['batch', '_action'], true) || \in_array($b, ['batch', '_action'], true)) {
                    return 0;
                }

                if ('id' === $a) {
                    return -1;
                }

                if ('id' === $b) {
                    return 1;
                }

                return 0;
            });

            $listMapper->reorder($keys);
        }
    }
}
