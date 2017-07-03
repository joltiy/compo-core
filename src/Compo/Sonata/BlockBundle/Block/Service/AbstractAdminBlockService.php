<?php


namespace Compo\Sonata\BlockBundle\Block\Service;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

class AbstractAdminBlockService extends \Sonata\BlockBundle\Block\Service\AbstractAdminBlockService
{
    use ContainerAwareTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }
}