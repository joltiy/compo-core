<?php

namespace Compo\ContactsBundle\Manager;

use Compo\ContactsBundle\Entity\Contacts;
use Compo\ContactsBundle\Repository\ContactsRepository;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\Sonata\AdminBundle\Entity\ViewsRepositoryTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritDoc}
 */
class ContactsManager extends BaseEntityManager
{
    use ContainerAwareTrait;
    use ViewsRepositoryTrait;


    public function getContacts()
    {

        /** @var ContactsRepositor $repository */
        return $this->getRepository()->findById(1);


    }


}