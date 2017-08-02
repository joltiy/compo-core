<?php
/**
 * Created by PhpStorm.
 * User: jivoy1988
 * Date: 07.02.17
 * Time: 14:57
 */

namespace Compo\RedirectBundle\Listener;

use Compo\RedirectBundle\Entity\Redirect;
use Compo\RedirectBundle\Repository\RedirectRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RedirectListener
{
    private $router;

    /** @var  Container */
    private $container;

    public function __construct($router, $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request_stack')->getCurrentRequest();
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request   = $event->getRequest();

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

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }
}