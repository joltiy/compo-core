<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @todo Админка
 * @todo Даты создания, редактирования, удаления
 * @todo Создал/обновил
 * @todo Изменения
 * @todo Корзина
 * @todo Шаблон виджета
 * @todo Контексты
 * @todo Шаблон мозайки
 * @todo Массовая загрузка
 */
class CompoSonataMediaBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataMediaBundle';
    }
}
