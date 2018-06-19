<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\TimelineBundle\Twig\Extension;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;

/**
 * Class SonataTimelineExtension.
 */
class SonataTimelineExtension extends \Sonata\TimelineBundle\Twig\Extension\SonataTimelineExtension
{
    /**
     * COMPO Перехват исключения, когда объект удалён окончательно, при генерации ссылки на редактирование объекта.
     *
     * @param ComponentInterface   $component
     * @param ActionInterface|null $action
     *
     * @return string
     */
    public function generateLink(ComponentInterface $component, ActionInterface $action = null)
    {
        try {
            return parent::generateLink($component, $action);
        } catch (\Exception $e) {
            return '';
        }
    }
}
