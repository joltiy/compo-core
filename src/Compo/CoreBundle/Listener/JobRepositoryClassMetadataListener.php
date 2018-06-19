<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\CoreBundle\Listener;

use Compo\CoreBundle\Doctrine\JobRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class JobRepositoryClassMetadataListener.
 */
class JobRepositoryClassMetadataListener implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /**
         * @var \Doctrine\ORM\Mapping\ClassMetadata
         */
        $classMetadata = $eventArgs->getClassMetadata();

        if (\JMS\JobQueueBundle\Entity\Job::class !== $classMetadata->getName()) {
            return;
        }

        $classMetadata->customRepositoryClassName = JobRepository::class;
    }
}
