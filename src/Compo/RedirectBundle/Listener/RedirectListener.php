<?php

namespace Compo\RedirectBundle\Listener;

use Compo\CoreBundle\DependencyInjection\ContainerAwareTrait;
use Compo\RedirectBundle\Entity\Redirect;
use Compo\RedirectBundle\Repository\RedirectRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RedirectListener
 * @package Compo\RedirectBundle\Listener
 */
class RedirectListener
{
    use ContainerAwareTrait;

    /**
     * @var
     */
    private $router;

    /**
     * RedirectListener constructor.
     * @param $router
     * @param $container
     */
    public function __construct($router)
    {
        $this->router = $router;
    }

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
        $request = $event->getRequest();

        $uri = $request->getRequestUri();

        /** @var RedirectRepository $redirectRepository */
        $redirectRepository = $this->getContainer()->get('doctrine')->getManager()->getRepository('CompoRedirectBundle:Redirect');

        /** @var Redirect $redirect */
        $redirect = $redirectRepository->findOneBy(array(
            'urIn' => $uri,
            'enabled' => true
        ), array(
                'id' => 'ASC'
            )
        );

        if ($redirect) {
            $event->setResponse(new RedirectResponse($redirect->getUrOut()));
            $event->stopPropagation();
        }
    }
}