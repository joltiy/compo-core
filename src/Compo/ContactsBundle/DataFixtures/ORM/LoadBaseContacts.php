<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\ContactsBundle\DataFixtures\ORM;

use Compo\ContactsBundle\Entity\Contacts;
use Compo\ContactsBundle\Repository\ContactsRepository;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * {@inheritdoc}
 */
class LoadBaseContacts implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var ContactsRepository $contactsRepository */
        $contactsRepository = $manager->getRepository('CompoContactsBundle:Contacts');

        if (0 === \count($contactsRepository->findAll())) {
            $contacts = new Contacts();
            $contacts->setEmail('test@test.com');
            $contacts->setAddress('test address');
            $contacts->setPhone('test phone');

            $manager->persist($contacts);
            $manager->flush();
        }
    }
}
