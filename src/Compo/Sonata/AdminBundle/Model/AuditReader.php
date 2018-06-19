<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Compo\Sonata\AdminBundle\Model;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class AuditReader.
 */
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

    /**
     * @param $object
     * @param $revision
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \SimpleThings\EntityAudit\Exception\DeletedException
     * @throws \SimpleThings\EntityAudit\Exception\NoRevisionFoundException
     * @throws \SimpleThings\EntityAudit\Exception\NotAuditedException
     */
    public function revert($object, $revision)
    {
        if (!method_exists($object, 'getId')) {
            throw new \Exception('Revert failed: not method_exists: getId');
        }

        $id = $object->getId();

        $revision = $this->auditReader->find(\get_class($object), $id, $revision);
        $newValues = $this->auditReader->getEntityValues(\get_class($object), $revision);

        foreach ($newValues as $key => $value) {
            $key = preg_replace_callback('/(?:^|_)([a-z])/', function ($matches) {
                //       Start or underscore    ^      ^ lowercase character
                return mb_strtoupper($matches[1]);
            }, $key);

            $setter = sprintf('set%s', ucfirst($key));

            $object->{$setter}($value);
        }

        $em = $this->getContainer()->get('doctrine')->getManager();

        $em->persist($object);
        $em->flush();
    }
}
