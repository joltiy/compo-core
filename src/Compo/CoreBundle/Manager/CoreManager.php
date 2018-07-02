<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

/**
 * Class CoreManager.
 */
class CoreManager
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $updatedAtCacheKeys = [];

    /**
     * @var array
     */
    protected $settings;

    /**
     * @return array
     */
    public function getSettings()
    {
        if (null === $this->settings) {
            $this->settings = $this->getContainer()->get('sylius.settings_manager')->load('compo_core_settings');
        }

        return $this->settings;
    }

    /**
     * @param $class
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function clearUpdatedAtCache($class)
    {
        $key = $this->getUpdatedAtCacheKey($class);

        if (!$key) {
            return;
        }

        $cache = $this->getContainer()->get('cache.app');

        $cache->deleteItem($key);
    }

    /**
     * @param $class
     *
     * @return string
     */
    public function getUpdatedAtCacheKey($class)
    {
        if (isset($this->updatedAtCacheKeys[$class])) {
            return $this->updatedAtCacheKeys[$class];
        }

        return '';
    }

    /**
     * @param $class
     *
     * @return string
     */
    public function getUpdatedAtCacheAsString($class)
    {
        return $this->getUpdatedAtCache($class)->format('U');
    }

    /**
     * @param $class
     *
     * @throws \Psr\Cache\InvalidArgumentException
     *
     * @return \DateTime
     */
    public function getUpdatedAtCache($class)
    {
        $key = $this->getUpdatedAtCacheKey($class);

        if (!$key) {
            return new \DateTime();
        }

        $cache = $this->getContainer()->get('cache.app');

        $updatedAtCache = $cache->getItem($key);

        if ($updatedAtCache->isHit()) {
            return $updatedAtCache->get();
        }

        $updatedAt = new \DateTime();

        $updatedAtCache->set($updatedAt);

        $cache->save($updatedAtCache);

        return $updatedAt;
    }

    /**
     * @return array
     */
    public function getUpdatedAtCacheKeys(): array
    {
        return $this->updatedAtCacheKeys;
    }

    /**
     * @param array $updatedAtCacheKeys
     */
    public function setUpdatedAtCacheKeys(array $updatedAtCacheKeys): void
    {
        foreach ($updatedAtCacheKeys as $item) {
            $this->updatedAtCacheKeys[$item['entity']] = 'compo_core_updated_at_cache_' . $item['key'];
        }
    }

    /**
     * @return bool
     */
    public function isDisplayNotify()
    {
        $settings = $this->getSettings();

        $request = $this->getContainer()->get('request_stack')->getCurrentRequest();

        if ($request->get('notify_test')) {
            return true;
        }

        if ($settings['popup_notify_enabled'] && $settings['popup_notify']) {
            $md5 = md5($settings['popup_notify']);

            $session = $this->getContainer()->get('session');

            $hash = $session->get('popup_notify');

            if (!$hash) {
                $session->set('popup_notify', $md5);

                return true;
            }
            if ($hash && $hash !== $md5) {
                $session->set('popup_notify', $md5);

                return true;
            }

            return false;
        }

        return false;
    }
}
