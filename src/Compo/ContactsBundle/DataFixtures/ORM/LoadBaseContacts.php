<?php

namespace Compo\ContactsBundle\DataFixtures\ORM;

use Compo\ContactsBundle\Entity\Contacts;
use Compo\ContactsBundle\Repository\ContactsRepository;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * {@inheritDoc}
 */
class LoadBaseContacts implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var ContactsRepository $contactsRepository */
        $contactsRepository = $manager->getRepository('CompoContactsBundle:Contacts');

        if (count($contactsRepository->findAll()) == 0) {
            $contacts = new Contacts();
            $contacts->setEmail('test@test.com');
            $contacts->setAddress('test address');
            $contacts->setPhone('test phone');

            $manager->persist($contacts);
            $manager->flush();
        }
    }

}