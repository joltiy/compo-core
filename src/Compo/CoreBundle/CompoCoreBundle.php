<?php

namespace Compo\CoreBundle;

use Compo\CoreBundle\DependencyInjection\Compiler\AdminLabelCompilerPass;
use Compo\CoreBundle\DependencyInjection\Compiler\AdminLabelTranslationDomainCompilerPass;
use Compo\CoreBundle\DependencyInjection\Compiler\AdminLabelTranslatorStrategyCompilerPass;
use Compo\CoreBundle\DependencyInjection\Compiler\ExceptionListenerCompilerPass;
use Compo\CoreBundle\DependencyInjection\Compiler\FallbackTranslatorCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class CompoCoreBundle
 *
 * @package Compo\CoreBundle
 */
class CompoCoreBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FallbackTranslatorCompilerPass());
        $container->addCompilerPass(new ExceptionListenerCompilerPass());
        $container->addCompilerPass(new AdminLabelTranslatorStrategyCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new AdminLabelTranslationDomainCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        //$container->addCompilerPass(new AdminLabelCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
    }
}
