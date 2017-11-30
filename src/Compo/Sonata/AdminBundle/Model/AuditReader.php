<?php

namespace Compo\Sonata\AdminBundle\Model;

use Symfony\Component\DependencyInjection\Container;

class AuditReader extends \Sonata\DoctrineORMAdminBundle\Model\AuditReader
{
    /** @var Container */
    public $container;

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function revert($object, $revision)
    {
        $revision = $this->auditReader->find(get_class($object), $object->getId(), $revision);
        $newValues = $this->auditReader->getEntityValues(get_class($object), $revision);

        foreach ($newValues as $key => $value) {
            $key = preg_replace_callback('/(?:^|_)([a-z])/', function ($matches) {
//       Start or underscore    ^      ^ lowercase character
                return strtoupper($matches[1]);
            }, $key);

            $setter = sprintf('set%s', ucfirst($key));

            $object->{$setter}($value);
        }

        $this->getContainer()->get('doctrine')->getManager()->persist($object);
        $this->getContainer()->get('doctrine')->getManager()->flush();
    }
}
