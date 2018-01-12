<?php

namespace Compo\AdvantagesBundle\Manager;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * {@inheritdoc}
 */
class AdvantagesManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    public function deleteUpdatedAt()
    {
        $cache = new FilesystemAdapter('app.cache');
        $cache->deleteItem('advantages_updated_at');
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        $cache = new FilesystemAdapter('app.cache');
        $updatedAtCache = $cache->getItem('advantages_updated_at');

        if ($updatedAtCache->isHit()) {
            return $updatedAtCache->get();
        }
        $updatedAt = new \DateTime();

        $updatedAtCache->set($updatedAt);

        $cache->save($updatedAtCache);

        return $updatedAt;
    }
}
