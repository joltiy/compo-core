<?php

namespace Compo\Sonata\SeoBundle\Block\Breadcrumb;


use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;

class BaseBreadcrumbMenuBlockService extends \Sonata\SeoBundle\Block\Breadcrumb\BaseBreadcrumbMenuBlockService
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