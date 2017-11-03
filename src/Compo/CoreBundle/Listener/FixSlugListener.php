<?php

namespace Compo\CoreBundle\Listener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
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

        if ($requestUri !== $requestUriNew){
            $event->setResponse(new RedirectResponse($requestUriNew));
            $event->stopPropagation();
        }
    }
}
