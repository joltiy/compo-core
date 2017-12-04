<?php

namespace Compo\Sonata\CoreBundle\Model;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * {@inheritdoc}
 */
class BaseEntityManager extends \Sonata\CoreBundle\Model\BaseEntityManager
{
    use ContainerAwareTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        $request_stack = $this->getContainer()->get('request_stack');

        if ($request_stack) {
            $request = $request_stack->getCurrentRequest();

            if ($request) {
                return $request;
            }
        }

        return new Request();
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        /** @var \Compo\CoreBundle\Doctrine\ORM\EntityRepository $repository */
        $repository = $this->getRepository();

        return $repository->getChoices();
    }
}
