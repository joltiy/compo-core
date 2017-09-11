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

    /**
     * @param $items
     */
    public function setSeoPages($items) {
        foreach ($items as $item) {
            $this->seoPages[$item['context']] = $item;
        }
    }

    /**
     * @return array
     */
    public function getSeoPages() {
        return $this->seoPages;
    }

    /**
     * @param $context
     * @return mixed
     */
    public function getSeoPageItem($context) {
        return $this->seoPages[$context];
    }

    /**
     * @return array
     */
    public function getChoices() {
        $choices = array();

        foreach ($this->seoPages as $items) {
            $choices[$items['context']] = $items['context'];
        }

        return $choices;
    }
}