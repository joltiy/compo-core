<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\NotificationBundle;

use Compo\Sonata\NotificationBundle\DependencyInjection\Compiler\AdminCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @todo Подробный просмотр задач
 * @todo Кнопка перезапуска
 * @todo Удаление
 * @todo Автоматический перезапуск
 * @todo Автоматическая очистка старых
 */
class CompoSonataNotificationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataNotificationBundle';
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AdminCompilerPass());
    }
}
