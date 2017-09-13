<?php

namespace Compo\SeoBundle\Manager;

use Compo\Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class SeoPage extends BaseEntityManager
{
    /**
     * @var array
     */
    public $seoPages = array();

    /**
     * @return array
     */
    public function getSeoPages()
    {
        return $this->seoPages;
    }

    /**
     * @param $items
     */
    public function setSeoPages($items)
    {
        foreach ($items as $item) {
            $this->seoPages[$item['context']] = $item;
        }
    }

    /**
     * @param $context
     * @return mixed
     */
    public function getSeoPageItem($context)
    {
        return $this->seoPages[$context];
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = array();

        foreach ($this->seoPages as $items) {
            $choices[$items['context']] = $items['context'];
        }

        return $choices;
    }
}