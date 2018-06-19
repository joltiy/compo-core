<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Admin\Traits;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\AbstractAdminExtension;

/**
 * Конфигурация шаблонов в расширениях админки.
 */
trait ConfigureTemplatesTrait
{
    public function configureTemplates()
    {
        /** @var AbstractAdmin $admin */
        $admin = $this;

        /** @var AbstractAdminExtension $extension */
        foreach ($admin->getExtensions() as $extension) {
            if (method_exists($extension, 'configureTemplates')) {
                $extension->configureTemplates($admin);
            }
        }
    }
}
