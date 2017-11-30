<?php

namespace Compo\SeoBundle\Manager;

use Compo\Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritdoc}
 */
class SeoPage extends BaseEntityManager
{
    /**
     * @var
     */
    protected $settings;

    /**
     * @var array
     */
    public $seoPages = array();

    /**
     * @return object|\Sylius\Bundle\SettingsBundle\Model\SettingsInterface
     */
    public function getSettings()
    {
        if (null === $this->settings) {
            $this->settings = $this->getContainer()->get('sylius.settings_manager')->load('seo');
        }

        return $this->settings;
    }

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
     *
     * @return mixed
     */
    public function getSeoPageItem($context)
    {
        if (isset($this->seoPages[$context])) {
            return $this->seoPages[$context];
        }

        return null;
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
