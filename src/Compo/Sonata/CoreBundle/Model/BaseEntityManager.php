<?php

namespace Compo\Sonata\CoreBundle\Model;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

class BaseEntityManager extends \Sonata\CoreBundle\Model\BaseEntityManager
{
    use ContainerAwareTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    public function getChoices() {
        return $this->getRepository()->getChoices();
    }
}