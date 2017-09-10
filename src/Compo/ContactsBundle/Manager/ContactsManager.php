<?php

namespace Compo\ContactsBundle\Manager;

use Compo\ContactsBundle\Repository\ContactsRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class ContactsManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    public function getContacts()
    {
        /** @var ContactsRepository $repository */
        $repository = $this->getRepository();

        return $this->getRepository()->findById(1);
    }
}