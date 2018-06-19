<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Controller\Traits;

use Compo\Sonata\AdminBundle\Admin\AbstractAdmin;
use Compo\Sonata\AdminBundle\Admin\Traits\IsUseEntityTraitsTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Расширение функционала CRUD контроллера админки.
 */
trait BaseCRUDControllerTrait
{
    use IsUseEntityTraitsTrait;
    use EditActionTrait;
    use ListActionTrait;
    use BatchActionFormsTrait;
    use BatchActionEnabledTrait;

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Return the admin related to the given $class.
     *
     * @param string $class
     *
     * @return \Sonata\AdminBundle\Admin\AdminInterface
     */
    public function getAdminByClass($class)
    {
        /** @var AbstractAdmin $admin */
        $admin = $this->getAdmin();

        return $admin->getConfigurationPool()->getAdminByClass($class);
    }

    /**
     * @return AbstractAdmin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param AbstractAdmin $admin
     */
    public function setAdmin(AbstractAdmin $admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator()
    {
        /** @var Container $container */
        $container = $this->getContainer();

        return $container->get('translator');
    }
}
