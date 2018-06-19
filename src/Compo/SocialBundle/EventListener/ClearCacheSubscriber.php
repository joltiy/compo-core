<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SocialBundle\EventListener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\SocialBundle\Entity\Social;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * {@inheritdoc}
 */
class ClearCacheSubscriber implements EventSubscriber
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'postUpdate',
            'postRemove',
            'postPersist',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->clearCache($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function clearCache(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if ($object instanceof Social) {
            $this->getContainer()->get('compo_social.manager.social')->clearUpdatedAtCacheKey();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->clearCache($args);
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->clearCache($args);
    }
}
