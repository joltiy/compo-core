<?php

namespace Compo\CoreBundle\Listener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RedirectListener.
 */
class UtmListener
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

        $session = $this->getContainer()->get('session');

        $utm_params = array(
            'utm_source',
            'utm_campaign',
            'utm_medium',
            'utm_term',
        );

        foreach ($utm_params as $param) {
            if ($request->get($param)) {
                $session->set($param, $request->get($param));
            }
        }
    }
}
