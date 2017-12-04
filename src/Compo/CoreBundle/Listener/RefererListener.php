<?php

namespace Compo\CoreBundle\Listener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RedirectListener.
 */
class RefererListener
{
    use ContainerAwareTrait;

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     *
     * @throws \Exception
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws \Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();

        $referer = $request->headers->get('referer');

        if ($referer && false === strpos($referer, $request->getHost())) {
            $session = $this->getContainer()->get('session');

            $session->set('referer', $referer);
        }
    }
}
