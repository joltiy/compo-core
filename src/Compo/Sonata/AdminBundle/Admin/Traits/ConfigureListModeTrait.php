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
use Symfony\Component\HttpFoundation\Request;

/**
 * Конфигурация шаблонов в расширениях админки.
 */
trait ConfigureListModeTrait
{
    public function configureListMode()
    {
        /** @var AbstractAdmin $admin */
        $admin = $this;

        /** @var AbstractAdminExtension $extension */
        foreach ($admin->getExtensions() as $extension) {
            if (method_exists($extension, 'configureListMode')) {
                $extension->configureListMode($admin);
            }
        }
    }

    public function setListMode($mode)
    {
        if (!$this->hasRequest()) {
            throw new \RuntimeException(sprintf('No request attached to the current admin: %s', $this->getCode()));
        }

        if ($this->getCurrentLeafChildAdmin()) {
            $this->getRequest()->getSession()->set(sprintf('%s.list_mode', $this->getCurrentLeafChildAdmin()->getCode()), $mode);
        } else {
            $this->getRequest()->getSession()->set(sprintf('%s.list_mode', $this->getCode()), $mode);
        }
    }

    /**
     * @return mixed
     */
    public function getListMode()
    {
        if (!$this->hasRequest()) {
            return 'list';
        }

        /** @var Request $request */
        $request = $this->getRequest();

        $sessionListMode = $request->getSession()->get(sprintf('%s.list_mode', $this->getCode()));

        $requestListMode = $request->get('_list_mode');

        if ($requestListMode) {
            $listMode = $requestListMode;
        } elseif ($sessionListMode) {
            $listMode = $sessionListMode;
        } else {
            $listModes = $this->getListModes();

            $listModesKeys = array_keys($listModes);

            $listMode = $listModesKeys[0];
        }

        $this->setListMode($listMode);

        return $listMode;
    }

    /**
     * @param $listModes
     */
    public function setListModes($listModes)
    {
        $key = 'listModes';

        $this->{$key} = $listModes;
    }
}
