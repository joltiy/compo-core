<?php

/*
 * This file is part of the CompoSymfonyCms package.
 * (c) Compo.ru <info@compo.ru>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->getRepository();

        if (method_exists($repository, 'getChoices')) {
            return $repository->getChoices();
        }

        throw new \Exception('Not found method getChoices for: ' . $repository->getClassName());
    }
}
