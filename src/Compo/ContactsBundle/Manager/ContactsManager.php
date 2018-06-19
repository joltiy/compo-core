<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ContactsBundle\Manager;

use Compo\ContactsBundle\Entity\Contacts;
use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Sonata\CoreBundle\Model\BaseEntityManager;

/**
 * {@inheritdoc}
 */
class ContactsManager extends BaseEntityManager
{
    use ContainerAwareTrait;

    /**
     * @return Contacts
     */
    public function getContacts()
    {
        $repository = $this->getEntityManager()->getRepository(Contacts::class);

        $contacts = $repository->findBy([], ['id' => 'ASC'], 1);

        if (isset($contacts[0])) {
            return $contacts[0];
        }

        return null;
    }
}
