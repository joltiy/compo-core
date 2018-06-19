<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\UserBundle\DependencyInjection\Compiler;

use Compo\Sonata\UserBundle\Form\Type\SecurityRolesType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SecurityRolesCompilerPass.
 */
class SecurityRolesCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('sonata.user.form.type.security_roles');
        $definition->setClass(SecurityRolesType::class);
    }
}
