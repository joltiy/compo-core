<?php

namespace Compo\CoreBundle\Listener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RedirectListener.
 */
class ClientIdListener
{
    use ContainerAwareTrait;

    /**
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\Request
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

        if ($request->cookies->get('_ga')) {
            $_ga_array = explode('.', $request->cookies->get('_ga'));

            if (isset($_ga_array[2], $_ga_array[3])) {
                $session->set('google_analytics_client_id', $_ga_array[2] . '.' . $_ga_array[3]);

            }
        }

        if ($request->cookies->get('_ym_uid')) {
            $session->set('yandex_metrika_client_id', $request->cookies->get('_ym_uid'));
        }
    }
}
