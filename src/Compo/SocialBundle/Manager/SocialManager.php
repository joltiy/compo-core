<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\SocialBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritdoc}
 */
class SocialManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function clearUpdatedAtCacheKey()
    {
        $this->getContainer()->get('compo_core.manager')->clearUpdatedAtCacheKey('social_updated_at');
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAtCacheKey()
    {
        return $this->getContainer()->get('compo_core.manager')->getUpdatedAtCacheKey('social_updated_at');
    }
}
