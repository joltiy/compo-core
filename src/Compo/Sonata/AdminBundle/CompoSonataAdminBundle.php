<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle;

use Compo\Sonata\AdminBundle\DependencyInjection\Compiler\AdminLabelTranslationDomainCompilerPass;
use Compo\Sonata\AdminBundle\DependencyInjection\Compiler\AdminLabelTranslatorStrategyCompilerPass;
use Compo\Sonata\AdminBundle\DependencyInjection\Compiler\AuditOrmReaderCompilerPass;
use Compo\Sonata\AdminBundle\DependencyInjection\Compiler\BreadcrumbsBuilderCompilerPass;
use Compo\Sonata\AdminBundle\DependencyInjection\Compiler\FallbackTranslatorCompilerPass;
use Compo\Sonata\AdminBundle\DependencyInjection\Compiler\LogRevisionsListenerCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * This file has been generated by the EasyExtends bundle ( https://sonata-project.org/easy-extends ).
 *
 * References :
 *   bundles : http://symfony.com/doc/current/book/bundles.html
 */
class CompoSonataAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataAdminBundle';
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FallbackTranslatorCompilerPass());

        //$container->addCompilerPass(new AdminLabelCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new AdminLabelTranslationDomainCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new AdminLabelTranslatorStrategyCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);

        $container->addCompilerPass(new AuditOrmReaderCompilerPass());
        $container->addCompilerPass(new LogRevisionsListenerCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);

        $container->addCompilerPass(new BreadcrumbsBuilderCompilerPass());
    }
}
