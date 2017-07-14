<?php

namespace Compo\SeoBundle\Manager;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class SeoPage extends BaseEntityManager
{
    use ContainerAwareTrait;

    public $seoPages = array();

    public function setSeoPages($items) {
        foreach ($items as $item) {
            $this->seoPages[$item['context']] = $item;
        }
    }

    public function getSeoPages() {
        return $this->seoPages;
    }

    public function getSeoPageItem($context) {
        return $this->seoPages[$context];
    }

    public function getChoices() {
        $choices = array();

        foreach ($this->seoPages as $items) {
            $choices[$items['context']] = $items['context'];
        }

        return $choices;
    }
}