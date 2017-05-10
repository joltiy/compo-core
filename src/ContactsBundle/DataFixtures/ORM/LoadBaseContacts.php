<?php
namespace Compo\ContactsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Compo\ContactsBundle\Entity\Contacts;

class LoadBaseContacts implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {


        $qb = $manager->getRepository('CompoContactsBundle:Contacts')
                        ->createQueryBuilder('t')
                        ->select('count(t.id)');

        $count = $qb->getQuery()->getSingleScalarResult();

        if($count === '0')
        {
            $contacts = new Contacts();
            $contacts->setEmail('test@test.com');
            $contacts->setAddress('test address');
            $contacts->setPhone('test phone');

            $manager->persist($contacts);
            $manager->flush();

        }


    }

}