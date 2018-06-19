<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Extension;

use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * {@inheritdoc}
 */
class SettingsExtension extends AbstractAdminExtension
{
    /**
     * @param AdminInterface $admin
     *
     * @return array
     */
    public function getAccessMapping(AdminInterface $admin)
    {
        return [
            'settings' => 'SETTINGS',
        ];
    }
}
