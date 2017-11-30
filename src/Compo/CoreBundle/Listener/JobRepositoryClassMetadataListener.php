<?php

namespace Compo\CoreBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class JobRepositoryClassMetadataListener implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
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

        if ('JMS\JobQueueBundle\Entity\Job' !== $classMetadata->getName()) {
            return;
        }

        $classMetadata->customRepositoryClassName = 'Compo\CoreBundle\Doctrine\JobRepository';
    }
}
