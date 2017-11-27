<?php

namespace Compo\CoreBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class JobRepositoryClassMetadataListener implements EventSubscriber
{
    /**
     * @inheritdoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /**
         * @var \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
         */
        $classMetadata = $eventArgs->getClassMetadata();

        if ($classMetadata->getName() !== 'JMS\JobQueueBundle\Entity\Job') {
            return;
        }

        $classMetadata->customRepositoryClassName = 'Compo\CoreBundle\Doctrine\JobRepository';
    }
}
