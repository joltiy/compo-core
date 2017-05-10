<?php

namespace Compo\Sonata\TimelineBundle\Twig\Extension;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Model\ComponentInterface;

class SonataTimelineExtension extends \Sonata\TimelineBundle\Twig\Extension\SonataTimelineExtension
{
    /**
     * COMPO Перехват исключения, когда объект удалён окончательно, при генерации ссылки на редактирование объекта
     *
     * @param ComponentInterface $component
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