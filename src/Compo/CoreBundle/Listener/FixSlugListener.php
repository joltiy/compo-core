<?php

namespace Compo\CoreBundle\Listener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\RedirectBundle\Entity\Redirect;
use Compo\RedirectBundle\Repository\RedirectRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RedirectListener
 * @package Compo\RedirectBundle\Listener
 */
class FixSlugListener
{
    use ContainerAwareTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     * @throws \Exception
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    /**
     * @param GetResponseEvent $event
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();

        $requestUri = $request->getRequestUri();

        $requestUriNew =  preg_replace('/-{2,}/','-', $requestUri);

        if ($requestUri != $requestUriNew){
            $event->setResponse(new RedirectResponse($requestUriNew));
            $event->stopPropagation();
        }
    }
}