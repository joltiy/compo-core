<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\UserBundle;

use Compo\Sonata\UserBundle\DependencyInjection\Compiler\AuditCompilerPass;
use Compo\Sonata\UserBundle\DependencyInjection\Compiler\ChildAdminCompilerPass;
use Compo\Sonata\UserBundle\DependencyInjection\Compiler\SecurityRolesCompilerPass;
use Compo\Sonata\UserBundle\Form\Type\SecurityRolesType;
use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class CompoSonataUserBundle.
 */
class CompoSonataUserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataUserBundle';
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ChildAdminCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new AuditCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new SecurityRolesCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
    }

    /**
     * Register form mapping information.
     */
    public function registerFormMapping(): void
    {
        FormHelper::registerFormTypeMapping([
            'sonata_security_roles' => SecurityRolesType::class,
        ]);
    }
}
